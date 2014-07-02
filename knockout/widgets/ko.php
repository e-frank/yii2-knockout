<?
namespace efrank\knockout\widgets;

use Yii;
use yii\property\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;


class ko extends \yii\base\Widget
{
	const PREFIX      = 'viewmodel';
	public $prefix    = self::PREFIX;
	public $model     = null;
	public $viewmodel = null;
	public $bind      = true;

	private static $labels = [];
	private $models        = [];
	private $config        = [];


	private static function get_real_class($obj) {
		$classname = get_class($obj);

		if (preg_match('@\\\\([\w]+)$@', $classname, $matches)) {
			$classname = $matches[1];
		}

		return $classname;
	}

	public function render222($view, $params = [])
	{
		$this->config = $params;
		// $view  = $this->getView();

		// $view  = $this->getView();
		$models = "\r\n// initialize models\r\n\r\n";
		if (array_key_exists('models', $params)) {
			foreach ($params['models'] as $value) {
				if (!array_key_exists(get_class($value['model']), $this->models)) {
					$this->models[get_class($value['model'])] = $value['model'];
					$options                                  = array_key_exists('options', $value) ? $value['options'] : [];
					$models                                  .= $this::viewmodel($value['model'], $options);
				}
			}
			$view->registerJs($models, \yii\web\View::POS_BEGIN);
		}
		if (array_key_exists('js', $params)) {
			foreach ($params['js'] as $key => $value) {
				$view->registerJsFile($value, [], [\yii\web\View::POS_END]);
			}
		}

		$view->registerJs(self::vm($params), \yii\web\View::POS_END);
		$view->registerJs(self::vmReady($params), \yii\web\View::POS_READY);

		// echo $models;
		return;
	}	


	private static function getName($params) {
		if (array_key_exists('name', $params)) {
			return $params['name'];
		} elseif (array_key_exists('model', $params)) {
			return self::get_real_class($params['model']);
		} elseif (array_key_exists('class', $params)) {
			return basename($params['class']);
		}
		return false;
	}

	public static function viewmodel($params, $viewmodels = []) {
		if (!is_array($params) && $params instanceof \yii\base\Model)  {
			$params = self::fromModel($params);
		}

		$hasModel = array_key_exists('model', $params);
		if (!$hasModel && array_key_exists('class', $params)) {
			$params['model'] = new $params['class'];
			$hasModel        = true;
		}

		if ($hasModel) {
			$params = array_merge($params, self::fromModel($params['model'], $params));
		}

		if (!array_key_exists('name', $params)) {
			throw new \yii\base\InvalidConfigException('viewmodel "name" not set');
		}

		if (!array_key_exists('attributes', $params)) {
			return '';
			// throw new \yii\base\InvalidConfigException('viewmodel "attributes" not set');
		}

		$name   = $params['name'];
		$class  = (array_key_exists('class', $params)) ? $params['class'] : (array_key_exists('model', $params) ? get_class($params['model']) : '');
		$prefix = array_key_exists('prefix', $params) ? $params['prefix'] : self::PREFIX;
		// if (array_key_exists('remove', $params))
		// 	$attr = array_values(array_diff($params['attributes'], $params['remove']));
		// else

		if (!array_key_exists('extenders', $params))
			$params['extenders'] = [];

		if (array_key_exists('attributes', $params))
			$attr = $params['attributes'];
		else
			$attr = [];

		$attributes = array();
		foreach ($attr as $key => $value) {
			if (is_array($value)) {
				if (array_key_exists('model', $value)) {
					$value['name']  = self::get_real_class($model);
					$value['class'] = get_class($model);
				}
				$attributes[$key] = $value;
			} else {
				$attributes[$value]['name'] = $value;
			}
		}
		if (array_key_exists('extensions', $params)) {

		}

		// remove unwanted
		if (array_key_exists('remove', $params)) {
			$attributes = array_diff_key($attributes, array_flip($params['remove']));
		}
		$extenders = array_intersect_key($params['extenders'], $attributes);



		$lines      = array();

		$options = array_key_exists('options', $params) ? $params['options'] : [];

		$lines[] = '';
		$lines[] = sprintf('function %s%s(obj, options) {', $prefix, $name);
		$lines[] = "\tthis.prototype = new viewmodelBase();";
		$lines[] = "\tviewmodelBase.call(this);";
		$lines[] = "\tvar self = this;";
		$lines[] = sprintf("\tthis._name       = %s;", json_encode($name));
		$lines[] = sprintf("\tthis._class      = %s;", json_encode($class));

		$lines[] = "\tobj = obj || {};";
		$lines[] = sprintf("\tthis.options = $.extend(this.options || {}, options, %s);\r\n", json_encode($options, JSON_PRETTY_PRINT || JSON_FORCE_OBJECT));

		foreach ($attributes as $key => $value) {
			if (array_key_exists($value['name'], $extenders))
				$e = $extenders[$value['name']];
			else
				$e = [];
			$e['errors'] = true;
			$lines[] = "\t" .sprintf('this.%1$s = ko.observable().extend(%2$s);', $value['name'], json_encode($e, JSON_PRETTY_PRINT || JSON_FORCE_OBJECT));
		}


		// components
		$components_ = '';
		if (array_key_exists('components', $params)) {
			$components = $params['components'];
			foreach ($components as $key => $value) {
				$p = array_key_exists('prefix', $value) ? $value['prefix'] : self::PREFIX;
				$e = [];
				if ($n = self::getName($value)) {
					if (array_key_exists($n, $extenders))
						$e = $extenders[$n];
					else
						$e = [];
					$e['component'] = ['viewmodel' => $p . $n];

					if (!in_array($p . $n, $viewmodels)) {
						$viewmodels[] = $p . $n;
						$components_ .= self::viewmodel($value, $viewmodels);
					}

					$new = sprintf('new %s%s()', $p, $n);

				} else {
					$e['component'] = true;
					$new = '{}';
				}

				$attributes[$value['attribute']]  = ['name' => $value['attribute']];
				$lines[] = "\t" .sprintf('this.%1$s = ko.observable(%3$s).extend(%2$s);', $value['attribute'], json_encode($e, JSON_PRETTY_PRINT || JSON_FORCE_OBJECT), $new);

			}
		} else {
			$components = [];
		}


		// lists
		$lists_ = '';
		if (array_key_exists('lists', $params)) {
			foreach ($params['lists'] as $key => $value) {
				$lines[] = '// ' . serialize($value);
				$e = [];
				$p = array_key_exists('prefix', $value) ? $value['prefix'] : self::PREFIX;
				if ($n = self::getName($value)) {
					$e['list'] = ['viewmodel' => $p . $n];

					if (!in_array($p . $n, $viewmodels)) {
						$viewmodels[] = $p . $n;
						$lists_ .= self::viewmodel($value, $viewmodels);
					}
				} else {
					$e['list'] = null;
				}
				$attributes[$value['attribute']]  = ['name' => $value['attribute']];
				$lines[] = "\t" .sprintf('this.%1$s = ko.observableArray().extend(%2$s);', $value['attribute'], json_encode($e, JSON_PRETTY_PRINT || JSON_FORCE_OBJECT));

			}
		}

		$lines[] = sprintf("\tthis._attributes = %s;\r\n", json_encode(array_keys($attributes)));

		if (array_key_exists('code', $params)) {
			$code = $params['code'];
			if (is_array($code)) 
				$lines[] = implode("\r\n", $code);
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


	public static function fromModel($model, $params = []) {
		$class  = self::get_real_class($model);
		$lines  = array();

		$options = array_key_exists('options', $params) ? $params['options'] : [];
		
		if (!array_key_exists('key', $options)) {
			if (method_exists($model, 'primaryKey')) {
				$pk = $model->primaryKey();
				$options['key'] = $pk;
			} else {

			}
		}

		if (array_key_exists('attributes', $params)) {
			$attributes = $params['attributes'];
		} else {
			$attributes = array_keys($model->getAttributes());
		}

		if (array_key_exists('extenders', $params))
			$extenders = $params['extenders'];
		else
			$extenders = [];
		if (method_exists($model, 'getTableSchema')) {

			$columns = $model->getTableSchema()->columns;
			foreach ($attributes as $key => $value) {
				if (!array_key_exists($value, $extenders)) {
					switch($columns[$value]->type) {
						case 'integer':
							$extenders[$value]['decimal'] = ['decimals' => 0];
							break;
						case 'decimal':
							preg_match('|^decimal\(\d+,(\d+)\)$|i', $columns[$value]->dbType, $matches);
							$extenders[$value]['decimal'] = ['decimals' => $matches[1]];
							break;
						case 'date':
							$extenders[$value]['date'] = (object)[];
							break;
						case 'datetime':
							$extenders[$value]['datetime'] = (object)[];
							break;
					}
				}
			}
		}

		if (!array_key_exists('extenders', $params))
			$params['extenders'] = [];

		$result              = array_merge(['name' => $class, 'attributes' => $attributes], $params);
		$result['extenders'] = array_merge($extenders, $params['extenders']);
		$result['model']     = $model;
		return $result;
	}


	public function run() {
		if (isset($this->viewmodel)) {
			$this->getView()->registerJs($this::viewmodel($this->viewmodel), \yii\web\View::POS_END);
		} elseif (isset($this->model)) {
			$this->getView()->registerJs($this::viewmodel($this->model), \yii\web\View::POS_END);
		}
		if ($this->bind) {
			$p = array_key_exists('prefix', $this->viewmodel) ? $this->viewmodel['prefix'] : $this::PREFIX;
			$t = '';
			if ($this->bind !== true) {
				$t = sprintf(', document.getElementById(\'%s\')', $this->bind);
			}
			$this->getView()->registerJs(sprintf('ko.applyBindings(new %s%s()%s);', $p, $this::getName($this->viewmodel), $t) , \yii\web\View::POS_READY);
		}
		return;
	}
}
