<?
namespace x1\knockout;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\web\View;
use yii\helpers\Url;


class FormField extends \yii\base\Component {

    public static $autoIdPrefix = 'knockoutFormField';

    public $model         = null;
    public $attribute     = null;
    public $form          = null;
    public $extend        = [];
    public $options       = ['class' => 'form-group'];
    public $template      = "{label}\n{input}\n{hidden}\n{hint}\n{error}";
    public $inputOptions  = ['class'  => 'form-control', 'name' => null];
    public $hiddenOptions = [];
    public $errorOptions  = ['class'  => 'help-block'];
    public $labelOptions  = ['class'  => 'control-label'];
    public $hintOptions   = ['class'  => 'hint-block'];
    public $parts         = [];


    public function begin() {
            $path     = [];

            foreach ($this->form->structurePath as $key => $value) {
                $path[] = $value['formName'];
                $path[] = '_relations';
                $path[] = $value['relation'];
            }

            $structure =& $this->form->arrayPath($this->form->structure, $path);

            if (!empty($this->extend))
                $structure[$this->model->formName()][$this->attribute] = $this->extend;

            // var_dump($structure);
            return Html::beginTag('div', ArrayHelper::merge($this->options, ['data-bind' => sprintf('css: {\'has-error\': (%1$s && %1$s.errors && %1$s.errors() && %1$s.errors().length > 0) }', $this->attribute)]));
    }

    public function end() {
        // return 'FormField END';
            return Html::endTag('div');
    }

    public function render($content = null)
    {
        $this->inputOptions['name'] = '';

        if ($content === null) {
            if (!isset($this->parts['{input}'])) {
                // $this->inputOptions['data-bind'] = sprintf('value: %s', $this->attribute);
                $this->parts['{input}'] = Html::activeTextInput($this->model, $this->attribute, $this->inputOptions);
            }
            if (!isset($this->parts['{hidden}'])) {
                $this->parts['{hidden}'] = Html::activeHiddenInput($this->model, $this->attribute, $this->hiddenOptions);
            }
            if (!isset($this->parts['{label}'])) {
                $this->parts['{label}'] = Html::activeLabel($this->model, $this->attribute, $this->labelOptions);
            }
            if (!isset($this->parts['{error}'])) {
                $this->parts['{error}'] = Html::error($this->model, $this->attribute, $this->errorOptions);
            }
            if (!isset($this->parts['{hint}'])) {
                $this->parts['{hint}'] = '';
            }
            $content = strtr($this->template, $this->parts);
        } elseif (!is_string($content)) {
            $content = call_user_func($content, $this);
        }

        return $this->begin() . "\n" . $content . "\n" . $this->end();
    }

    public function init() {
        if (!isset($this->extend['validators'])) {
            $model_validators = $this->model->getActiveValidators($this->attribute);
            $validators = [];
            foreach ($model_validators as $validator) {
                $js = $validator->clientValidateAttribute($this->model, $this->attribute, $this->form->view);
                if (!empty($js)) {
                    $validators[] = $js;
                }
            }
            $validators = array_filter($validators);

            if (!empty($validators)) {
                $this->extend['validators'] = new JsExpression(sprintf('function(value, messages) {%s}', implode('', $validators)));
            }
        }

        $this->parts['{error}'] = sprintf('<!-- ko if: %1$s && %1$s.errors --><div class="help-block"><ul class="list-unstyled" data-bind="foreach: %1$s.errors"><li data-bind="text: $data"></li></ul></div><!-- /ko -->', $this->attribute);

        $this->inputOptions['value'] = '';
        $this->inputOptions['id']    = '';
        if (empty($this->inputOptions['data-bind'])) {
            $this->inputOptions['data-bind'] = sprintf('value: %1$s.display ? %1$s.display : %1$s', $this->attribute);
        }
        if (empty($this->hiddenOptions['data-bind'])) {
            $this->hiddenOptions['data-bind'] = sprintf('value: %1$s', $this->attribute);
        }
    }


    public function dropDownList($items = [], $options = []) {
        if (empty($options['data-bind'])) {
            $options['data-bind'] = sprintf('value: %1$s', $this->attribute);
        }

        Html::addCssClass($options, 'form-control');
        $this->parts['{input}'] = Html::activeDropDownList($this->model, $this->attribute, $items, $options);
        return $this;
    }

    public function textInput($options = []) {
        return $this;
    }

    public function textArea($options = []) {
        return $this->widget(\x1\input\Textarea::className(), $options);
        return $this;
    }


    public function date($options = []) {
        $this->extend = ['datetime' => ['time' => false]];
        return $this->widget(\x1\input\Date::className(), $options);
    }

    public function dateTime($options = []) {
        $this->extend = ['datetime' => ['time' => true]];
        return $this->widget(\x1\input\DateTime::className(), $options);
    }

    public function decimal($options = []) {
        if (!isset($this->form->defaults['decimal']))
            $this->form->defaults[__FUNCTION__] = ['decimals' => 2];

        $this->extend = ArrayHelper::merge(['decimal' => ['decimals' => 2]], $this->extend, ['decimal' => $options]);
        return $this->widget(\x1\knockout\input\Decimal::className(), $options);
    }

    
    public function percent($options = []) {
        $this->extend = ArrayHelper::merge(['decimal' => ['decimals' => 0, 'percent' => true]], $this->extend, ['decimal' => $options]);
        return $this->widget(\x1\input\Percent::className(), $options);
    }

    
    public function label($label = null, $options = [])
    {
        if ($label === false) {
            $this->parts['{label}'] = '';
            return $this;
        }

        $options = array_merge($this->labelOptions, $options);
        if ($label !== null) {
            $options['label'] = $label;
        }
        $this->parts['{label}'] = Html::activeLabel($this->model, $this->attribute, $options);

        return $this;
    }


    public function hint($content, $options = [])
    {
        $options               = array_merge($this->hintOptions, $options);
        $tag                   = ArrayHelper::remove($options, 'tag', 'div');
        $this->parts['{hint}'] = Html::tag($tag, $content, $options);

        return $this;
    }


    public function widget($class, $config = [])
    {
        $config['model']        = $this->model;
        $config['attribute']    = $this->attribute;
        $config['view']         = $this->form->getView();
        $this->parts['{input}'] = $class::widget($config);

        return $this;
    }

    public function __toString()
    {
        // __toString cannot throw exception
        // use trigger_error to bypass this limitation
        try {
            return $this->render();
        } catch (\Exception $e) {
            \yii\base\ErrorHandler::convertExceptionToError($e);
            return '';
        }
    }
}

?>