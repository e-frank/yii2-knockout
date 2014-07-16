<?
namespace efrank\knockout\widgets;

use Yii;
use yii\property\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;


class ko extends \yii\base\Widget
{
	const PREFIX      = 'viewmodel';
	const COMPONENT   = 'component';
	public $prefix    = self::PREFIX;
	public $model     = null;
	public $viewmodel = null;
	public $bind      = true;
	public $options   = [];

	public static $formats = array(
		'date'               => 'YYYY-MM-DD',
		'thousandsSeparator' => '.',
		'decimalSeparator'   => ',',
		);

	private $models        = [];
	private $config        = [];

	private static function getClassName($obj) {
		return basename(get_class($obj));
		$classname = get_class($obj);

		if (preg_match('@\\\\([\w]+)$@', $classname, $matches)) {
			$classname = $matches[1];
		}

		return $classname;
	}


	private static function getName($params) {
		if (array_key_exists('name', $params)) {
			return $params['name'];
		} elseif (array_key_exists('model', $params)) {
			return self::getClassName($params['model']);
		} elseif (array_key_exists('class', $params)) {
			return basename($params['class']);
		}
		return false;
	}

	public static function viewmodel(&$params, $view, &$viewmodels = null) {
		if ($viewmodels == null) {
			$viewmodels = [];
		}
		if (!is_array($params) && $params instanceof \yii\base\Model)  {
			$params = self::fromModel($params, $view);
		}

		$hasModel = array_key_exists('model', $params);
		if (!$hasModel && array_key_exists('class', $params)) {
			$params['model'] = new $params['class'];
			$hasModel        = true;
		}

		if ($hasModel) {
			$params = array_merge($params, self::fromModel($params['model'], $view, $params));
		}

		if (!array_key_exists('name', $params)) {
			throw new \yii\base\InvalidConfigException('viewmodel "name" not set');
		}

		if (!array_key_exists('attributes', $params)) {
			throw new \yii\base\InvalidConfigException('viewmodel "attributes" not set');
		}

		$name   = $params['name'];
		$class  = ArrayHelper::getValue($params, 'class', array_key_exists('model', $params) ? get_class($params['model']) : '');
		$prefix = ArrayHelper::getValue($params, 'prefix', self::PREFIX);


		$validators = ArrayHelper::getValue($params, 'validators', []);
		$attr       = ArrayHelper::getValue($params, 'attributes', []);

		$attributes = array();
		foreach ($attr as $key => $value) {
			if (is_array($value)) {
				if (array_key_exists('model', $value)) {
					$value['name']  = ArrayHelper::getValue($value, 'name', self::getClassName($model));
					$value['class'] = get_class($model);
				} else {
					$value['name']  = ArrayHelper::getValue($value, 'name', $key);
				}
				$attributes[$key] = $value;
			} else {
				$attributes[$value]['name'] = $value;
			}
		}


		// remove unwanted
		$remove = array_flip(ArrayHelper::getValue($params, 'remove', []));
		if (!empty($remove)) {
			$attributes = array_diff_key($attributes, $remove);
		}
		$extenders = array_intersect_key(ArrayHelper::getValue($params, 'extenders', []), $attributes);
		
		if (array_key_exists('key', $params)) {
			$key = $params['key'];
			foreach ($key as $key => $value) {
				$attributes[$value] = ['name' => $value];
				$extenders[$value] = [];
			}
		} else {
			$key = [];
		}



		$lines          = array();
		$options        = ArrayHelper::getValue($params, 'options', []);
		$options['url'] = ArrayHelper::getValue($options, 'url', Url::to(''));

		$lines[] = '';
		$lines[] = sprintf('function %s%s(obj, options) {', $prefix, $name);
		$lines[] = "\tthis.prototype = new viewmodelBase();";
		$lines[] = "\tviewmodelBase.call(this);";
		$lines[] = "\tvar self = this;";
		$lines[] = sprintf("\tthis._name       = %s;", Json::encode($name));
		$lines[] = sprintf("\tthis._class      = %s;", Json::encode($class));

		$lines[] = "\tobj = obj || {};";
		$lines[] = sprintf("\tthis.options = $.extend(this.options || {}, options, %s);\r\n", Json::encode($options, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT));

		// component attributes
		$components = ArrayHelper::getValue($params, 'components', []);
		foreach ($components as $key => $value) {
			$keys = ArrayHelper::getValue($value, 'key', []);
			foreach ($keys as $componentKey => $componentTarget) {
				if (!in_array($componentKey, $attributes)) {
					$attributes[$componentKey]  = ['name' => $componentKey];
				}
			}
		}

		$_validators = [];
		foreach ($attributes as $key => $value) {
			$e             = ArrayHelper::getValue($extenders, $value['name'], []);
			$v             = ArrayHelper::getValue($validators, $value['name'], []);
			$_validators[] = $value['name'];

			if (!empty($v)) {
				$e['validators'] = new JsExpression(sprintf("{abortValidation:self._isSetting,fn:function(value,messages){%s}}", implode('', $validators[$value['name']])));
			}

			$lines[] = "\t" .sprintf('this.%1$s = ko.observable().extend(%2$s);', $value['name'], Json::encode($e, JSON_FORCE_OBJECT));
		}


		$components_ = '';
		foreach ($components as $key => $value) {
			$p = ArrayHelper::getValue($value, 'prefix', self::COMPONENT);
			$value['prefix'] = $p;

			$e = [];
			$ex = '';
			if ($n = self::getName($value)) {
				$e = ArrayHelper::getValue($extenders, $n, []);

				$e['component'] = new JsExpression(sprintf('{viewmodel:%s,parent:self,key:%s}', $p.$n, Json::encode(ArrayHelper::getValue($value, 'key', []), JSON_FORCE_OBJECT)));

				if (!in_array($p . $n, $viewmodels)) {
					$viewmodels[] = $p . $n;
					$components_ .= self::viewmodel($value, $view, $viewmodels);
				}


				$new = sprintf('new %s%s()', $p, $n);
			} else {
				$e['component'] = new JsExpression('true');
				$new = '{}';
			}

			$attributes[$value['attribute']]  = ['name' => $value['attribute']];
			$lines[] = "\t" .sprintf('this.%1$s = ko.observable(%3$s).extend(%2$s);', $value['attribute'], Json::encode($e), $new);

		}



		// lists
		$lists_ = '';
		if (array_key_exists('lists', $params)) {
			foreach ($params['lists'] as $key => $value) {
				$lines[] = '// ' . serialize($value);
				$e = [];
				$p = array_key_exists('prefix', $value) ? $value['prefix'] : self::PREFIX;
				$ex = '';
				if ($n = self::getName($value)) {
					// $e['list'] = ['viewmodel' => $p . $n];
					$ex = sprintf('list:{ viewmodel:%s, parent: self, key: %s }', $p.$n, json_encode($value['key']));

					if (!in_array($p . $n, $viewmodels)) {
						$viewmodels[] = $p . $n;
						$lists_ .= self::viewmodel($value, $view, $viewmodels);
					}
				} else {
					$e['list'] = true;
				}
				$attributes[$value['attribute']]  = ['name' => $value['attribute']];
				$ext = substr_replace(json_encode($e, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT), $ex, 1, 0);
				$lines[] = "\t" .sprintf('this.%1$s = ko.observableArray().extend(%2$s);', $value['attribute'], $ext);

			}
		}


		$lines[] = sprintf("\tthis._attributes = %s;\r\n", Json::encode(array_keys($attributes)));
		$lines[] = sprintf("\tthis._validators = %s;\r\n", Json::encode($_validators));
		$lines[] = sprintf("\tthis.validate = function() {
			$.each(self._validators, function(index, value) {
				self[value].validate();
			})
	};\r\n");

		$extensions = ArrayHelper::getValue($params, 'extensions', []);
		if (!empty($extensions)) {
			foreach ($extensions as $key => $value) {
				$e_ext = [];
				if (is_array($value)) {
					foreach ($value as $k => $v) {
						if (is_int($k))
							$e_ext[$v] = [];
						else
							$e_ext[$k] = $v;
					}
				} else {
					$e_ext = [$value => new JsExpression('true')];
				}
				$lines[] = "\t" .sprintf('this.%1$s = ko.observable(self).extend(%2$s);', $key, Json::encode($e_ext, JSON_FORCE_OBJECT));
			}
		}

		$computed = ArrayHelper::getValue($params, 'computed', []);
		if (!empty($computed)) {
			foreach ($computed as $key => $value) {
				$e_comp = ['owner' => new JsExpression('self')];
				if (is_array($value)) {
					$e_comp = ArrayHelper::merge($e_comp, $value);
				} else {
					$e_comp = ['read' => $value];
				}

				$e_comp_ext = ArrayHelper::remove($e_comp, 'extenders', []);
				$lines[] = "\t" .sprintf('this.%s = ko.computed(%s).extend(%s);', $key, Json::encode($e_comp, JSON_FORCE_OBJECT), Json::encode($e_comp_ext, JSON_FORCE_OBJECT));
			}
		}

		if (array_key_exists('code', $params)) {
			$code = $params['code'];
			if (is_array($code)) 
				$lines[] = implode("", $code);
			else
				$lines[] = $code;
		}

		$lines[] = "\tthis.set(obj);";
		$lines[] = "\tthis.finish();";
		$lines[] = '}';
		$lines[] = '';
		$lines[] = '';
		return $components_ . "\r\n" . $lists_ . "\r\n" . implode("\r\n", $lines);
	}

	public static function convertDateFormat($date) {
		return strtoupper($date);
	}

	public static function fromModel($model, $view, $params = []) {
		$class  = self::getClassName($model);
		$lines  = array();

		$options = ArrayHelper::getValue($params, 'options', []);
		$formats = ArrayHelper::merge(self::$formats, ArrayHelper::getValue($params, 'formats', []));
		
		if (!array_key_exists('key', $options)) {
			if (method_exists($model, 'primaryKey')) {
				$pk = $model->primaryKey();
				$options['key'] = $pk;
			} else {

			}
		}

		$attributes = ArrayHelper::getValue($params, 'attributes', array_keys($model->getAttributes()));
		$extenders  = ArrayHelper::getValue($params, 'extenders', []);

		if (method_exists($model, 'getTableSchema')) {

			$columns = $model->getTableSchema()->columns;
			foreach ($attributes as $key => $value) {
				if (!array_key_exists($value, $extenders)) {
					switch($columns[$value]->type) {
						case 'integer':
						{					
							$extenders[$value]['decimal'] = ['decimals' => 0, 'thousandsSeparator' => $formats['thousandsSeparator']];
							break;
						}
						case 'decimal':
						{
							preg_match('|^decimal\(\d+,(\d+)\)$|i', $columns[$value]->dbType, $matches);
							$extenders[$value]['decimal'] = ['decimals' => $matches[1], 'thousandsSeparator' => $formats['thousandsSeparator'], 'decimalSeparator' => $formats['decimalSeparator']];
							break;
						}
						case 'date':
						{
							$extenders[$value]['date'] = ['format' => ($formats['date']), 'time' => false];
							break;
						}
						case 'datetime':
						{
							$extenders[$value]['datetime'] = ['format' => ($formats['date']), 'time' => true];
							break;
						}
						default:
						{
							$extenders[$value]['display'] = true;
							break;
						}
					}
				}
			}
		}

		$validators = [];
		if (method_exists($model, 'getValidators')) {
			$v = $model->getValidators();
			foreach ($v as $validator) {
				foreach ($validator->attributes as $attribute) {
					$js = $validator->clientValidateAttribute($model, $attribute, $view);
					if (!empty($js)) {
						if (!isset($validators[$attribute]))
							$validators[$attribute] = [];
						$validators[$attribute][] = $js;
					}
				}
			}
		}

		$result               = array_merge(['name' => $class, 'attributes' => $attributes], $params);
		$result['extenders']  = array_merge($extenders, ArrayHelper::getValue($params, 'extenders', []));
		$result['validators'] = array_merge($validators, ArrayHelper::getValue($params, 'validators', []));
		$result['model']      = $model;
		return $result;
	}


	public static function getModelData($viewmodel) {
		$data = [];
		$model = ArrayHelper::getValue($viewmodel, 'model', null);
		if ($model) {
			$data = $model->toArray();
		}
		$components=  ArrayHelper::getValue($viewmodel, 'components', []);
		foreach ($components as $key => $value) {
			$data[$value['attribute']] = self::getModelData($value);
		}
		return $data;
	}

	public function run() {
		$view = $this->getView();

		\efrank\knockout\assets\KnockoutAsset::register($view);

		if (isset($this->viewmodel)) {
			$view->registerJs($this::viewmodel($this->viewmodel, $view), \yii\web\View::POS_END);
		} elseif (isset($this->model)) {
			$view->registerJs($this::viewmodel($this->model, $view), \yii\web\View::POS_END);
		}

		$load = Json::encode(self::getModelData($this->viewmodel), JSON_FORCE_OBJECT);
		if ($model = ArrayHelper::getValue($this->viewmodel, 'model', false)) {
		}

		if ($this->bind) {
			$p               = ArrayHelper::getValue($this->viewmodel, 'prefix', $this::PREFIX);
			$t               = '';
			if ($this->bind !== true) {
				$t = sprintf(', document.getElementById(\'%s\')', $this->bind);
			}
			$view->registerJs(sprintf('ko.applyBindings(new %s%s(%s,%s)%s);', $p, $this::getName($this->viewmodel), $load, Json::encode($this->options, JSON_FORCE_OBJECT), $t) , \yii\web\View::POS_READY);
		}


		return;
	}
}
