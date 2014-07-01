<?
namespace efrank\knockout\widgets;

use Yii;
use yii\property\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;


class ko extends \yii\base\Widget
{
	
	public $name = 'viewmodel';
	public $model;
	public $viewmodel;
	private static $labels = [];
	private $models        = [];

	private static function get_real_class($obj) {
		$classname = get_class($obj);

		if (preg_match('@\\\\([\w]+)$@', $classname, $matches)) {
			$classname = $matches[1];
		}

		return $classname;
	}

	public function render($view, $params = [])
	{
		$this->viewmodel = $params;
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


	public static function collection($params) {
		$name = array_key_exists('name', $params) ? $params['name'] : 'undefinedCollectionName';
		$output = '';
		$output .= sprintf('// collection %s', $name);

		return $output;
	}


	public static function vm($params = []) {

		$name      = array_key_exists('name', $params) ? $params['name'] : 'undefinedViewModelName';
		$models    = array_key_exists('models', $params) ? $params['models'] : [];
		$relations = array_key_exists('relations', $params) ? $params['relations'] : [];
		$output    = '';

		$lines  = array();
		$lines[] = sprintf('function %s(obj) {', $name);
		$lines[] = sprintf('var self = this;', $name);
		$lines[] = sprintf('// viewmodel %s', $name);

		foreach ($relations as $key => $relation) {
			$lines[] = '// relations';

			foreach ($relation['keys'] as $key => $value) {
				// $lines[] = sprintf('this.%1$s = new viewmodel%1$s();', $key);
			}

		}

		$first = true;
		$firstName = '';
		foreach ($models as $key => $value) {
			$property   = array_key_exists('property', $value) ? $value['property'] : 'undefinedPropertyName';
			if ($first) {
				$firstName = $property;
				$first = false;
			}
			$class   = array_key_exists('class', $value) ? $value['class'] : 'viewmodel'.$property;
			$list    = array_key_exists('list', $value) ? $value['list'] : false;
			$raw     = array_key_exists('raw', $value) ? $value['raw'] : false;
			$options = array_key_exists('options', $value) ? $value['options'] : [];
			$lines[] = sprintf('this.%1$s = new %2$s(undefined, %3$s);', $property, $class, json_encode($options, JSON_FORCE_OBJECT));
			$lines[] = sprintf('console.log(this.%1$s.options);', $property, $class, json_encode($options));
			if ($list) {
				$lines[] = sprintf('this.%1$sList = ko.observableArray().extend({ list: { %3$stargetProperty: self.%2$s }});', $property, $property, ($raw) ? '' : 'viewmodel: '.$class.',' );
			}
		}

		$lines[] = sprintf('this.%1$s.set(obj);', $firstName);

		$lines[] = '}';
		$lines[] = '';
		$lines[] = '';
		return implode("\r\n", $lines);
	}


	public static function model($model, $params = []) {
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

		$lines[] = '';
		$lines[] = sprintf('function viewmodel%s(obj, options) {', $class);
		$lines[] = "\tthis.prototype = new viewmodelBase();";
		$lines[] = "\tviewmodelBase.call(this);";
		$lines[] = "\tvar self = this;";
		$lines[] = sprintf("\tthis._classname = %s;\r\n", json_encode($class));

		if (array_key_exists('attributes', $params)) {
			$lines[] = sprintf("\tthis._attributes = %s;\r\n", json_encode($params['attributes']));
		} else {
			$lines[] = sprintf("\tthis._attributes = [];\r\n");
		}

		$lines[] = "\tobj = obj || {};";
		// $lines[] = sprintf("\tthis.setOptions($.extend(options || {}, this.options, %s));\r\n", json_encode($options, JSON_PRETTY_PRINT || JSON_FORCE_OBJECT));
		$lines[] = sprintf("\tthis.options = $.extend(this.options || {}, options, %s);\r\n", json_encode($options, JSON_PRETTY_PRINT || JSON_FORCE_OBJECT));

		$attrs = $model->getAttributes();
		foreach ($attrs as $key => $value) {
			$lines[] = "\t" .sprintf('this.%1$s = ko.observable().extend({ errors: true });', $key);
			
			// $lines[] = "\t" .sprintf('this.%1$s.errors = ko.observableArray();', $key);
			// $lines[] = "\t" .sprintf('this.%1$s.hasError = ko.computed(function() { return this.%1$s.errors().length > 0; }, this);', $key);
		}

		$lines[] = "\tthis.set(obj);";
		$lines[] = "\tthis.finish();";
		$lines[] = '}';
		$lines[] = '';
		$lines[] = '';
		return implode("\r\n", $lines);
	}

	public static function vmReady($params = []) {
		$lines[] = '';
		$lines[] = sprintf('function cccXXXXXX(obj) {');
		$lines[] = "\tvar self = this;";
		$lines[] = '}';
		$lines[] = '';
		$lines[] = '';
		return implode("\r\n", $lines);

	}

	public function run() {
		if ($this->model instanceof \yii\base\Model) {
			// print "model found";
		}
		return $this::model($this->model, []);
		return 'run widget ZZZZZZ';
	}
}
