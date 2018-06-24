<?php
namespace x1\knockout;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\web\View;
use yii\helpers\Url;


class FormField extends \yii\base\Component {

    const TEMPLATE = "{label}\n{input}\n{hidden}\n{hint}\n{error}";

    public static $autoIdPrefix = 'knockoutFormField';

    public $model         = null;
    public $attribute     = null;
    public $form          = null;
    public $extend        = [];
    public $options       = ['class' => 'form-group'];
    public $template      = self::TEMPLATE;
    public $inputOptions  = ['class'  => 'form-control', 'name' => null];
    public $hiddenOptions = [];
    public $errorOptions  = ['class'  => 'help-block'];
    public $labelOptions  = ['class'  => 'control-label'];
    public $hintOptions   = ['class'  => 'hint-block'];
    public $parts         = [];
    public $wrapper       = true;

    private $prefix = null;
    private $label  = null;

    private function getPath() {

    }

    public function begin() {
        $dataBind = sprintf('css: {\'has-error\': (%1$s && %1$s.errors && %1$s.errors() && %1$s.errors().length > 0) }', $this->attribute, ArrayHelper::getValue($this->options, 'data-bind', ''));
        return (($this->prefix == null) ? '' : Html::beginTag('div', ['data-bind' => 'with: ' . strtolower($this->prefix)])) . Html::beginTag('div', ArrayHelper::merge($this->options, ['data-bind' => $dataBind]));
    }

    public function end() {
        // return 'FormField END';
            return (($this->prefix == null) ? '' : Html::endTag('div')) . Html::endTag('div');
    }


    public function extend($extend) {
        $this->extend = $extend;
        return $this;
    }

    public function item() {
        $path  = [];

        if (count($this->form->structurePath) > 0) {
            $path[] = '[' . $this->attribute . ']';
            $parent = '';
            foreach (array_reverse($this->form->structurePath) as $value) {
                if (ArrayHelper::getValue($value, 'multiple', false) == true) {
                    $path[] = '[\'+'.$parent.'$index()+\']';
                }
                $path[] = '[' . $value['relation'] . ']';
                $path[] = $value['formName'];
                $parent .= '$parentContext.';
            }


            // $this->prefix = $this->model->formName();
#            $this->template = sprintf('<div data-bind="with: %s">%s</div>', strtolower($this->model->formName()), $this->template);
        } else {
            $path[] = $this->attribute;
        }

        $hidden = implode('',  array_reverse($path));

        $this->parts['{hidden}'] = sprintf('<input type="hidden" data-bind="value: %s, attr:{name:\'%s\'}" />', $this->attribute, $hidden);
        return $this;
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

        return empty($content) ? '' : ($this->wrapper ? ($this->begin() . "\n" . $content . "\n" . $this->end()) : $content);
    }

    public function init() {

        if (preg_match('|^\[([^\]]*)\](.*)|is', $this->attribute, $matches)) {
            $this->attribute = array_pop($matches);
            // $this->prefix    = '$data.';
            // var_dump($this->inputOptions);
            // $this->hiddenOptions['name'] = '';
        }

        


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
            $this->hiddenOptions['data-bind'] = sprintf('hiddenValue: %1$s', $this->attribute);
        }
    }

    
    private function getDefaults($config, $defaults = [], $maps = []) {
        $result = [];

        foreach ($defaults as $key) {
            if (isset($this->form->defaults[$key])) {
                $result[isset($maps[$key]) ? $maps[$key] : $key] = $this->form->defaults[$key];
            }
        }

        return array_merge($config, $result);
    }

    public function tab($index) {
        $this->inputOptions['tabindex'] = $index;
        return $this;
    }

    public function dropDownList($items = [], $options = []) {
        if (empty($options['data-bind'])) {
            $options['data-bind'] = sprintf('value: %1$s', $this->attribute);
        }
        $options['name'] = '';

        Html::addCssClass($options, 'form-control');
        $this->parts['{input}'] = Html::activeDropDownList($this->model, $this->attribute, $items, $options);
        return $this;
    }


    /**
     * draws a checkbox. set trueValue (default 1) and falseValue (default 0) in options
     * @param  array  $options [description]
     * @return [type]          [description]
     */
    public function checkbox($options = []) {
        $this->extend = ArrayHelper::merge($this->extend, [
            'bool' => $this->getDefaults(['trueValue' => 1, 'falseValue' => 0], [], [])
            ], $options);

        if (empty($options['data-bind'])) {
            $options['data-bind'] = sprintf('checked: %1$s.bool', $this->attribute);
        }

        $this->options['class'] = 'checkbox';
        if ($this->template == self::TEMPLATE)
            $this->template = "<label>{input}\n{hidden}\n{label}</label>\n{hint}\n{error}";
        $this->parts['{label}'] = $this->model->getAttributeLabel($this->attribute);
        $this->inputOptions     = ArrayHelper::merge($this->inputOptions, ['id' => null, 'type' => 'checkbox', 'data-bind' => sprintf('checked: %1$s.bool', $this->attribute), 'value' => $this->extend['bool']['trueValue']]);
        return $this;
    }

    public function textInput($options = []) {
        $this->inputOptions = ArrayHelper::merge($this->inputOptions, $options);
        return $this;
    }

    public function passwordInput($options = []) {
        $this->inputOptions['type']   = 'password';
        $this->hiddenOptions['type']  = 'password';
        $this->hiddenOptions['style'] = 'visibility: hidden; display: none';
        return $this;
    }


    public function noHidden() {
        $this->parts['{hidden}'] = '';
        return $this;
    }

    public function hiddenInput($options = []) {
        $this->wrapper  = false;
        $this->template = "{hidden}";
        return $this;
    }

    public function textArea($options = []) {
        $this->extend = ArrayHelper::merge($this->extend, [
            'textarea' => $this->getDefaults([], ['textarea'], [])
            ], $options);
        return $this->widget(\x1\knockout\input\Textarea::className(), ArrayHelper::merge(['options' => $this->inputOptions], $options));
    }

    public function toggle($options = []) {
        $this->noHidden();
        $this->parts['{label}'] = '';
        // $this->extend           = ArrayHelper::merge($this->extend, [], $options);
        $this->inputOptions['data-bind'] = sprintf('checked: %s, attr:{name:\'%s\',id222:\'%s\'}', $this->attribute, Html::getInputName($this->model, $this->attribute), Html::getInputId($this->model, $this->attribute));
        $this->inputOptions['value']     = $this->model->{$this->attribute};
        $this->inputOptions['value']     = ArrayHelper::getValue($options, 'value', "1");
        return $this->widget(\x1\knockout\input\Toggle::className(), ArrayHelper::merge(['label' => $this->label, 'options' => $this->inputOptions], $options));
    }


    public function date($options = []) {
        $this->noHidden();
        $this->extend = ArrayHelper::merge($this->extend, [
            ], $options);
        $this->inputOptions['data-bind'] = sprintf('hiddenValue: %1$s, attr:{name:\'%2$s\'}', $this->attribute, Html::getInputName($this->model, $this->attribute));
        $this->inputOptions['value']     = $this->model->{$this->attribute};
        return $this->widget(\x1\knockout\input\Date::className(), ArrayHelper::merge(['options' => $this->inputOptions], $options));
    }

    public function datePicker($options = []) {
        $this->extend = ArrayHelper::merge($this->extend, [
            'datetime' => $this->getDefaults(['time' => false], ['date'], ['date' => 'format'])
            ], $options);
        return $this->widget(\x1\knockout\input\BootstrapMaterialDatetimePicker::className(), ArrayHelper::merge(['time' => false, 'options' => $this->inputOptions, 'pickerOptions' => ['time' => false, 'autoOpen' => false]], $options));
        return $this->widget(\x1\knockout\input\Date::className(), ArrayHelper::merge(['options' => $this->inputOptions], $options));
    }

    public function dateTime($options = []) {
        $this->extend = ArrayHelper::merge($this->extend, [
            'datetime' => $this->getDefaults(['time' => true], ['date'], ['date' => 'format'])
            ], $options);
        return $this->widget(\x1\knockout\input\BootstrapMaterialDatetimePicker::className(), ArrayHelper::merge(['time' => true, 'options' => $this->inputOptions, 'pickerOptions' => ['time' => false, 'autoOpen' => false]], $options));
        return $this->widget(\x1\knockout\input\DateTime::className(), ArrayHelper::merge(['options' => $this->inputOptions], $options));
    }

    public function decimal($options = []) {
        $this->extend = ArrayHelper::merge($this->extend, [
            'decimal' => $this->getDefaults([], ['decimals', 'thousandsSeparator', 'decimalSeparator'], [])
            ], $options);
        return $this->widget(\x1\knockout\input\Decimal::className(), ArrayHelper::merge(['options' => $this->inputOptions], $options));
    }

    public function integer($options = []) {
        $this->extend = ArrayHelper::merge($this->extend, [
            'decimal' => $this->getDefaults(['decimals' => 0], ['thousandsSeparator', 'decimalSeparator'], [])
            ], $options);
        return $this->widget(\x1\knockout\input\Decimal::className(), ArrayHelper::merge(['options' => $this->inputOptions], $options));
    }
    
    public function percent($options = []) {
        $this->extend = ArrayHelper::merge($this->extend, [
            'decimal' => $this->getDefaults(['decimals' => 0, 'percent' => true], ['thousandsSeparator', 'decimalSeparator'], [])
            ], $options);
        return $this->widget(\x1\knockout\input\Percent::className(), ArrayHelper::merge(['options' => $this->inputOptions], $options));
    }


    public function focus($focus = false) {
        $this->extend['focus'] = $focus;
        $bind  = ArrayHelper::getValue($this->inputOptions, 'data-bind', '');
        $binds = [$bind, sprintf('hasFocus: %s.focus', $this->attribute)];
        $this->inputOptions['data-bind'] = implode(', ', array_filter($binds));
        return $this;
    }

    public function fileInput($options = []) {
        return Html::activeFileInput($this->model, $this->attribute, $options);
    }

    // public function fileDrop($options = []) {
    //     $this->extend = ArrayHelper::merge([
    //         'filedrop' => $this->getDefaults(['multiple' => false], [], [])
    //         ], $options);
    //     return $this->widget(\x1\knockout\input\FileDrop::className(), $options);
    // }
    
   
    public function label($label = null, $options = [])
    {
        $this->label = $label;
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


    public function placeholder($placeholder = null)
    {
        $this->parts['{label}'] = '';
        $this->inputOptions['placeholder'] = empty($placeholder) ? (empty($this->label) ? $this->model->getAttributeLabel($this->attribute) : $this->label) : $placeholder;
        
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


    private function addField() {
        $path     = [];

        $relation = false;
        foreach ($this->form->structurePath as $key => $value) {
            $path[] = $value['formName'];
            $path[] = '_relations';
            $path[] = $relation = $value['relation'];
        }
        $structure =& $this->form->arrayPath($this->form->structure, $path);

        // if (!empty($this->extend))
        //     $structure[$this->model->formName()][$this->attribute] = $this->extend;
        // $structure[($relation) ? $relation : $this->model->formName()][$this->attribute] = $this->extend;
        $structure[$this->model->formName()][$this->attribute] = $this->extend;

    }

    public function create() {
        $this->addField();

        try {
            return $this->render('');
        } catch (\Exception $e) {
            \yii\base\ErrorHandler::convertExceptionToError($e);
            return '';
        }
    }


    public function __toString()
    {
        $this->addField();

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
