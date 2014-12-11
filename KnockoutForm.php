<?
namespace x1\knockout;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\web\View;
use yii\helpers\Url;


class KnockoutForm extends \yii\base\Widget {

    public static $autoIdPrefix = 'x1Knockout';

    public $name      = '';
    public $model     = null;
    public $class     = null;
    public $scenario  = 'default';
    public $base      = [];
    public $viewmodel = null;

    public static function begin($config = [])
    {
        $w    = parent::begin($config);
        $view = $w->getView();
        echo $view->render('@x1/knockout/views/knockout-form/begin', [
            'id' => $w->id,
            ]);
        $view->registerJs('// knockoutForm');
        return $w;
    }


    private function makePath($attribute, $hasMany, &$model, &$base) {
        if (!isset($base[$attribute])) {
            $base[$attribute] = [$attribute => 1];
        }

        return $base[$attribute];
    }

    public function getPath($attribute, $hasMany = null) {
        $splits = explode('.', $attribute);

        $base  =& $this->base;
        $model = $this->model;
        foreach ($splits as $split) {
            var_dump($split);
            if (!isset($base[$split])) {
                // $base['_model'] = $model;
                $base['_class'] = get_class($model);
                $base[$split] = [];
            }

            $base =& $base[$split];
        }

        var_dump($splits);
        var_dump($this->base);
    }


    public static function end() {
    	$w 		= self::$stack[count(self::$stack) - 1];
        $view 	= $w->getView();
        echo $view->render('@x1/knockout/views/knockout-form/end');
        parent::end();
    }



    private function endsWith($haystack, $needle) {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }
        return (substr($haystack, -$length) === $needle);        
    }

    public function field($model, $attribute, $options = []) {
        $splits = explode('.', $attribute);

        $c        = 0;
        $splits_r = array_reverse($splits);
        $idx      = 0;

        foreach ($splits_r as $key => $value) {
            $idx++;
            if ($this->endsWith($value, '[]')) {
                $c++;
                $s              = (($c > 1) ? str_repeat('$parentContext.', $c - 1) : '') . '$index()';
                $value          = '[' . str_replace('[]', '', $value) . ']' . '[\' + '.$s. ' + \']';
                $splits_r[$key] = $value;
            } else {
                if ($idx < count($splits_r))
                    $splits_r[$key] = '[' . $value . ']';
            }


        }
        $name = '\'' . implode('', array_reverse($splits_r)) . '\'';
        $field = array_pop($splits);

        echo sprintf('

<div class="input-group" data-bind="css: {\'has-error\': %1$s.errors && %1$s.errors().length > 0}">
    <input class="form-control" data-bind="value: (%1$s.display) ? %1$s.display : %1$s " />
    <input type="hidden" data-bind="value: %1$s, attr: {name:%2$s}">
    <!-- ko if: %1$s.errors && %1$s.errors().length > 0 -->
    <ol class="has-error clearfix list-unstyled" data-bind="foreach: %1$s.errors"><li data-bind="text: $data"></li></ol>
    <!-- /ko -->
</div>
            ', $field, $name);
    }


    public function init() {
        if (empty($this->model) && !empty($this->class)) {
            $this->model = new $this->class(['scenario' => $this->scenario]);
        } else if (empty($this->class) && !empty($this->model)) {
            $this->class    = get_class($this->model);
            $this->scenario = $this->model->getScenario();
        }

        if (empty($this->name) && !empty($this->model))
            $this->name = $this->model->formName();

        // if (!empty($this->createContent) && empty($this->createButton))
        //     throw new \yii\base\InvalidConfigException("Expecting 'createButton' property of " . get_class($this));

        if (!empty($this->viewmodel)) {
            $view = $this->view;
            if (empty($this->model))
                $data = [];
            else  
                $data = $this->model->toArray();
            $view->registerJs(sprintf('//ko.applyBindings(ko.mapping.fromJS(%s, %s), document.getElementById("%s"));', Json::encode($data, JSON_FORCE_OBJECT), $this->viewmodel, $this->id));
            $view->registerJs(sprintf('//ko.applyBindings(ko.mapping.fromJS(%s, %s));', Json::encode($data, JSON_FORCE_OBJECT), $this->viewmodel, $this->id));
            $view->registerJs(sprintf('console.log(ko.dataFor(document.getElementById("%s")));', $this->id));
        }
    }


}

?>