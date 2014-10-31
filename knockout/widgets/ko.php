<?
namespace efrank\knockout\widgets;

use Yii;
use yii\property\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;
use efrank\knockout\helpers\DateFormatConverter;

class ko extends \yii\base\Widget
{
	const PREFIX      = 'viewmodel';
	const COMPONENT   = 'component';
	public $prefix    = self::PREFIX;
	public $model     = null;
	public $viewmodel = null;
	public $bind      = true;
	public $options   = [];
	public $cached	  = true;

	public static $formats = array(
		'date'               => null,
		'thousandsSeparator' => null,
		'decimalSeparator'   => null,
		);

	private $models        = [];
	private $config        = [];

	private static function getClassName($obj) {
		$classname = get_class($obj);

		if (preg_match('@\\\\([\w]+)$@', $classname, $matches)) {
			$classname = $matches[1];
		}

		return $classname;
	}


	public static function getName($params) {
		if (array_key_exists('name', $params)) {
			return $params['name'];
		} elseif (array_key_exists('model', $params)) {
			return self::getClassName($params['model']);
		} elseif (array_key_exists('class', $params)) {
			return self::getClassName($params['class']);
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
		
		// $extenders = array_intersect_key(ArrayHelper::getValue($params, 'extenders', []), $attributes);
		$extenders = ArrayHelper::getValue($params, 'extenders', []);
		
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
		$lists  = [];
		$lists_ = '';
		if (array_key_exists('lists', $params)) {
			foreach ($params['lists'] as $key => $value) {
				// $lines[] = '// ' . serialize($value);
				$e = [];
				$p = array_key_exists('prefix', $value) ? $value['prefix'] : self::PREFIX;
				$ex = '';
				if ($n = self::getName($value)) {
					// $e['list'] = ['viewmodel' => $p . $n];
					$ex = sprintf('list:{ viewmodel:%s, parent: self, key: %s }', $p.$n, Json::encode(ArrayHelper::getValue($value, 'key', [])));

					if (!in_array($p . $n, $viewmodels)) {
						$viewmodels[] = $p . $n;
						$lists_ .= self::viewmodel($value, $view, $viewmodels);
					}
				} else {
					$e['list'] = true;
				}
				if (is_numeric($key))
					$key = $value['attribute'];

				$attributes[$key] = ['name' => $key];
				$lists[]          = $key;
				$ext              = substr_replace(json_encode($e, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT), $ex, 1, 0);
				$lines[]          = "\t" .sprintf('this.%1$s = ko.observableArray().extend(%2$s);', $key, $ext);

			}
		}


		$lines[] = sprintf("\r\n\tthis._attributes = %s;", Json::encode(array_keys($attributes)));
		$lines[] = sprintf("\tthis._lists = %s;", Json::encode($lists));
		$lines[] = sprintf("\tthis._validators = %s;\r\n", Json::encode($_validators));
	// 	$lines[] = sprintf("\tthis.validate = function() {
	// 		$.each(self._validators, function(index, value) {
	// 			self[value].validate();
	// 		})
	// };\r\n");


		$observables = ArrayHelper::getValue($params, 'observables', []);
		if (!empty($observables)) {
			foreach ($observables as $key => $value) {
				$e_ext__ = ArrayHelper::getValue($value, 'extenders', []);
				$e_ext = [];
				if (is_array($e_ext__)) {
					foreach ($e_ext__ as $k => $v) {
						if (is_int($k))
							$e_ext[$v] = [];
						else
							$e_ext[$k] = $v;
					}
				} else {
					$e_ext = [$value => new JsExpression('true')];
				}
				$lines[] = "\t" .sprintf('this.%1$s = ko.observable(%2$s).extend(%3$s);', $key, Json::encode(ArrayHelper::getValue($value, 'value', null, JSON_FORCE_OBJECT)), Json::encode($e_ext, JSON_FORCE_OBJECT));
			}
		}


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

		$arrays = ArrayHelper::getValue($params, 'arrays', []);
		if (!empty($arrays)) {
			foreach ($arrays as $key => $value) {
				$e_ext__ = ArrayHelper::getValue($value, 'extenders', []);
				$e_ext = [];
				if (is_array($e_ext__)) {
					foreach ($e_ext__ as $k => $v) {
						if (is_int($k))
							$e_ext[$v] = [];
						else
							$e_ext[$k] = $v;
					}
				} else {
					$e_ext = [$value => new JsExpression('true')];
				}
				$lines[] = "\t" .sprintf('this.%1$s = ko.observableArray(%2$s).extend(%3$s);', $key, Json::encode(ArrayHelper::getValue($value, 'value', [])), Json::encode($e_ext, JSON_FORCE_OBJECT));
			}
		}

		$computed = ArrayHelper::getValue($params, 'computed', []);
		if (!empty($computed)) {
			foreach ($computed as $key => $value) {
				$e_comp = ['owner' => new JsExpression('this')];
				if (is_array($value)) {
					$e_comp = ArrayHelper::merge($e_comp, $value);
				} else {
					$e_comp['read'] = new JsExpression($value);
				}
				$e_comp_ext = array_merge_recursive(ArrayHelper::remove($extenders, $key, []), ArrayHelper::remove($value, 'extenders', []));
				$lines[] = "\t" .sprintf('this.%s = ko.computed(%s).extend(%s);', $key, Json::encode($e_comp, JSON_FORCE_OBJECT), Json::encode($e_comp_ext, JSON_FORCE_OBJECT));
			}
		}

		$subscriptions = ArrayHelper::getValue($params, 'subscriptions', []);
		if (!empty($subscriptions)) {
			foreach ($subscriptions as $key => $value) {
				$lines[] = "\t" .sprintf('this.%s.subscribe(%s);', $key, Json::encode($value));
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
						case 'smallint':
						case 'long':
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
							$extenders[$value]['date'] = ['format' => DateFormatConverter::convertPhpToMoment($formats['date']), 'time' => false];
							break;
						}
						case 'datetime':
						{
							$extenders[$value]['datetime'] = ['format' => DateFormatConverter::convertPhpToMoment($formats['date']), 'time' => true];
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
			$v = $model->getActiveValidators();
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
		$components =  ArrayHelper::getValue($viewmodel, 'components', []);
		foreach ($components as $key => $value) {
			$data[$value['attribute']] = self::getModelData($value);
		}
		$lists =  ArrayHelper::getValue($viewmodel, 'lists', []);
		foreach ($lists as $k => $list) {
			if (is_numeric($k)) {
				$listname = ArrayHelper::getValue($list, 'attribute', "list{$k}");
			} else {
				$listname = $k;
			}
			$n               = self::getName($list);
			$listvalue       = ArrayHelper::getValue($list, 'value', []);
			$data[$listname] = [];
			if ($n) {
				foreach ($listvalue as $item) {
					$data[$listname][] = self::getModelData(ArrayHelper::merge($list, ['model' => $item]));
				}
			} else {
				foreach ($listvalue as $item) {
					$data[$listname][] = $item->toArray();
				}
			}
		}
		return $data;
	}


	public static function noCache($config = []) {
		$bind = ArrayHelper::getValue($config, 'bind', true);
		$view = ArrayHelper::getValue($config, 'view', Yii::$app->getView());

		\efrank\knockout\assets\KnockoutAsset::register($view);
		\yii\validators\ValidationAsset::register($view);

		if (!empty($bind)) {
			$viewmodel = ArrayHelper::getValue($config, 'viewmodel', []);
			$options   = ArrayHelper::getValue($config, 'options', []);
			$load      = Json::encode(self::getModelData($viewmodel));
			$p         = ArrayHelper::getValue($viewmodel, 'prefix', self::PREFIX);
			$t         = '';

			if ($bind !== true) {
				$t = sprintf(', document.getElementById(\'%s\')', $bind);
			}
			$view->registerJs(sprintf('ko.applyBindings(new %s%s(%s,%s)%s);', $p, self::getName($viewmodel), $load, Json::encode($options, JSON_FORCE_OBJECT), $t) , \yii\web\View::POS_READY);
		}
	}

	public function run() {
		$view   = $this->getView();
		$cached = count($view->cacheStack) > 0;


		if (isset($this->viewmodel)) {
			$vm = $this::viewmodel($this->viewmodel, $view);
		} elseif (isset($this->model)) {
			$vm = $this::viewmodel($this->model, $view);
		} else {
			$vm = '';
		}

		if ($cached) {
			echo '<script>'.$vm.'</script>';
		} else {
			$view->registerJs($vm, \yii\web\View::POS_END);

			self::noCache([
				'bind'      => $this->bind,
				'viewmodel' => $this->viewmodel,
				'view'      => $view,
				'options'   => $this->options
				]);
		}

		return;
	}


	//
	//	get formats from config/locale
	//
	public function init() {

		$decimal  = self::$formats['decimalSeparator'];
		$thousand = self::$formats['thousandsSeparator'];
		$date     = self::$formats['date'];


		$locale = Yii::$app->formatter->locale;
		if (empty($locale))
			$locale = Yii::$app->language;

		if ($decimal == null || $thousand == null) {

			if ($decimal == null)
				$decimal = Yii::$app->formatter->decimalSeparator;
			if ($thousand == null)
				$thousand = Yii::$app->formatter->thousandSeparator;

			if ($decimal == null || $thousand == null) {
				$fmt = numfmt_create($locale, \NumberFormatter::DECIMAL);
				if ($decimal == null)
					$decimal = numfmt_get_symbol($fmt, \NumberFormatter::DECIMAL_SEPARATOR_SYMBOL);
				if ($thousand == null)
					$thousand = numfmt_get_symbol($fmt, \NumberFormatter::GROUPING_SEPARATOR_SYMBOL);
			}

			self::$formats['decimalSeparator']   = $decimal;
			self::$formats['thousandsSeparator'] = $thousand;
		}

		if ($date == null) {

		    $shortFormats = [
		        'short'  => 3, // IntlDateFormatter::SHORT,
		        'medium' => 2, // IntlDateFormatter::MEDIUM,
		        'long'   => 1, // IntlDateFormatter::LONG,
		        'full'   => 0, // IntlDateFormatter::FULL,
		    ];

			$fmt        = new \IntlDateFormatter($locale, $shortFormats[Yii::$app->formatter->dateFormat], \IntlDateFormatter::NONE, Yii::$app->formatter->timeZone);
			$dateFormat = $fmt->getPattern();
			$date       = \yii\helpers\FormatConverter::convertDateIcuToPhp($dateFormat);

			self::$formats['date'] = $date;

		}


		return parent::init();
	}
}
