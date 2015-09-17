<?
namespace x1\knockout;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\web\View;
use yii\helpers\Url;


class ActiveForm extends \yii\base\Widget {

    const DATE                = 'date';
    const DECIMAL_SEPARATOR   = 'decimalSeparator';
    const THOUSANDS_SEPARATOR = 'thousandsSeparator';
    const DECIMALS            = 'decimals';

    public static $autoIdPrefix  = 'knockoutActiveForm';

    public $fieldClass           = 'x1\knockout\FormField';
    public $fieldConfig          = [];
    public $action               = null;
    public $namespace            = 'mapping';
    public $structure            = [];
    public $structurePath        = [];
    public $data                 = null;
    public $options              = null;
    public $encodeErrorSummary   = true;
    public $errorSummaryCssClass = 'error-summary';
    public $defaults             = [
        // self::DATE                => 'YYYY-MM-DD',
        // self::DECIMAL_SEPARATOR   => '.',
        // self::THOUSANDS_SEPARATOR => ',',
        self::DECIMALS            => 2,
    ];
    public $method = 'post';

    public static function begin($config = [])
    {
        $defaults    = ArrayHelper::remove($config, 'defaults', []);
        $w           = parent::begin($config);
        var_dump($w->defaults);
        $w->defaults = ArrayHelper::merge($w->defaults, $defaults);

        $view     = $w->getView();

        echo Html::beginForm($w->action, $w->method, ArrayHelper::merge(['id' => 123], ArrayHelper::getValue($config, 'options', []), ['id' => $w->id, 'method' => 'POST', 'enctype' => 'multipart/form-data']));

        KnockoutAsset::register($view);
        $view->registerJs(sprintf(<<<EOD
var %1\$s = $.extend({}, x1.config, %2\$s);
EOD
, lcfirst(\yii\helpers\Inflector::camelize($w->id)), Json::encode($w->defaults)));
        return $w;
    }


    public static function end() {
    	$w 		= self::$stack[count(self::$stack) - 1];
        $view 	= $w->getView();

        if ($c = count($w->structurePath) > 0) {
            throw new \yii\base\Exception(sprintf("%d unclosed relations", $c));
        }

        echo Html::endForm();
        $w->createMappings();

        if (!empty($w->data))
            $w->bind($w->data);

        parent::end();
    }

    public static function &arrayPath(&$array, $path) {
        $offset =& $array;
        if ($path) foreach ($path as $index) {
            $offset =& $offset[$index];
        }
        return $offset;
    }



    public function field($model, $attribute, $options = []) {

        $config = $this->fieldConfig;
        if ($config instanceof \Closure) {
            $config = call_user_func($config, $model, $attribute);
        }
        if (!isset($config['class'])) {
            $config['class'] = $this->fieldClass;
        }

        return Yii::createObject(ArrayHelper::merge($config, ['inputOptions' => ['class' => 'form-control']], $options, [
            'model'     => $model,
            'attribute' => $attribute,
            'form'      => $this,
        ]));
    }

    public function init() {
        parent::init();

    	if ($this->action == null)
    		$this->action = Url::current();
    }


    public function createMappings() {
        $mapping              = [];
        // $mapping['prototype'] = new JsExpression($this->render('active-form/fn-prototype'));
        $mapping['prototype'] = new JsExpression('ko.proto');

        // generate create function for 1st level vs prototype
        $models = [];
        foreach ($this->structure as $key => $value) {
            $models[] = sprintf('ko.mapping.fromJS(options.data, %s.%s, self);', $this->namespace, strtolower($key));
        }

        $mapping['create'] = new JsExpression(sprintf('function(options) {
            var self = new %1$s.prototype(options);
            %2$s
            if (self.init) { self.init(%1$s._arrays); }
            if (%1$s._fn) {
                self = %1$s._fn(self);
            }
            return self;
        }', $this->namespace, implode('', $models)));
        $mapping['_defaults'] = new JsExpression(Json::encode($this->defaults));

        $this->walkLevel($this->structure, [], $mapping);

        $this->view->registerJs(sprintf('var %s=%s;', $this->namespace, Json::encode($mapping)), View::POS_END);
    }

    public function walkLevel($structure, $path = [], &$mapping = []) {
        // walk through models
        foreach ($structure as $key => $value) {

            $key       = strtolower($key);
            $path[]    = $key;
            $m         = & $mapping[$key];
            $relations = ArrayHelper::remove($value, '_relations', []);
            $arrays    = ArrayHelper::remove($value, '_arrays', []);

            foreach ($value as $key => $v2) {
                $m[$key]['create'] = new JsExpression(sprintf('function(options) { return ko.observable(options.data).extend(%s); }', Json::encode($v2)));
            }

            // walk through relations
            if (!empty($relations)) {
                foreach ($relations as $relation => &$level) {
                    $path[]                  = $relation;


                    $p            = $path;
                    $firstModel   = key(reset($relations));
                    $p[]          = strtolower($firstModel);

                    $subrelations = isset($level[$firstModel]['_relations']) ? array_keys($level[$firstModel]['_relations']) : [];

                    $m['_arrays'] = new JsExpression(Json::encode($arrays));
                    $m[$relation]['create']  = new JsExpression(sprintf('function(options) { 
                        var self = new %1$s.prototype(options);
                        ko.mapping.fromJS(options.data, %1$s.%2$s, self);
                        if (self.init) { self.init(%1$s.%5$s._arrays); }
                        if (%1$s.%3$s._fn) {
                            self = %1$s.%3$s._fn(self);
                        }
                        return self;
                    }', $this->namespace, implode('.', $p), implode('.', $path), Json::encode($subrelations), implode('.', array_slice($path, 0, count($path) - 1))));

                    $this->walkLevel($level, $path, $m[$relation]);
                    array_pop($path);
                }
            }

            array_pop($path);
        }
    }

    public function bind($data = null) {
        $this->view->registerJs(sprintf('
            ko.applyBindings(vm = ko.mapping.fromJS(%1$s, %2$s), document.getElementById("%3$s"));
        ', Json::encode($data), $this->namespace, $this->id), View::POS_READY);
    }


    public function beginRelation($model, $relation) {
        $rel = $model->getRelation($relation);
        $this->structurePath[] = ['formName' => $model->formName(), 'relation' => $relation, 'multiple' => $rel->multiple];

        $path     = [];

        foreach ($this->structurePath as $key => $value) {
            $path[] = $value['formName'];
            $path[] = '_relations';
            $path[] = $value['relation'];
        }
        $structure =& $this->arrayPath($this->structure, $path);

        if ($rel !== null && $rel->multiple) {
            array_pop($path);
            array_pop($path);

            $path[]      = '_arrays';
            $structure   =& $this->arrayPath($this->structure, $path);
            $structure[] = $relation;
        }

        return Html::beginTag('div', ['data-bind' => sprintf('%s: %s', ($rel->multiple) ? 'foreach' : 'with', $relation)]);
    }

    public function endRelation() {
        array_pop($this->structurePath);
        return Html::endTag('div');
    }


    public function errorSummary($models, $options = []) {
        Html::addCssClass($options, $this->errorSummaryCssClass);
        $options['encode'] = $this->encodeErrorSummary;
        return Html::errorSummary($models, $options);
    }


    public function submitButton($options = []) {
        $text = ArrayHelper::getValue($options, 'text', Yii::t('app', 'Submit'));
        return Html::submitButton('<i class="glyphicon" data-bind="css: {\'glyphicon-ok\': !hasError(), \'glyphicon-warning-sign\': hasError(), }"></i> ' . $text, ['class' => 'btn btn-primary', 'data-bind' => 'css: { \'btn-danger\': hasError() }, enable: !hasError()']);
    }

}

?>