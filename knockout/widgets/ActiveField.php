<?php
namespace efrank\knockout\widgets;

use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;


class Dummy extends \yii\base\Model {

    public $base = null;
    public $attribute = '';

    // public function formName() {

    // }
}

class ActiveField extends \yii\bootstrap\ActiveField
{
    public $base          = null;
    public $with          = null;
    public $hiddenOptions = [];

    public $template = "{beginLabel}{labelTitle}{endLabel}\n{beginWrapper}\n{input}\n{hidden}\n{hint}\n{error}\n{endWrapper}";

    // public $templateButtons = "{label}\n{beginWrapper}\n{input}\n{hidden}\n{hint}\n{error}\n{endWrapper}";

    public $wrapperOptions = [
    ];


    public function render222($content = null)
    {
        if ($content === null) {
            if (!isset($this->parts['{beginWrapper}'])) {
                $options = $this->wrapperOptions;
                $tag = ArrayHelper::remove($options, 'tag', 'div');
                $this->parts['{beginWrapper}'] = Html::beginTag($tag, $options);
                $this->parts['{endWrapper}'] = Html::endTag($tag);
            }
            if ($this->enableLabel === false) {
                $this->parts['{label}'] = '';
                $this->parts['{beginLabel}'] = '';
                $this->parts['{labelTitle}'] = '';
                $this->parts['{endLabel}'] = '';
            } elseif (!isset($this->parts['{beginLabel}'])) {
                $this->renderLabelParts();
            }
            if ($this->enableError === false) {
                $this->parts['{error}'] = '';
            }
            if ($this->inputTemplate) {
                $input = isset($this->parts['{input}']) ?
                $this->parts['{input}'] : Html::activeTextInput($this->model, $this->attribute, $this->inputOptions);
                $this->parts['{input}'] = strtr($this->inputTemplate, ['{input}' => $input]);
            }
        }
        return parent::render($content);
    }


    public function __construct($config = [])
    {

        $attribute = ArrayHelper::getValue($config, 'attribute', '');
        $with      = '';
        $attrs     = explode('.', $attribute);
        $name      = '';
        $this->with                   = implode('().', $attrs);
        if (count($attrs) > 1) {
            $base                         = ArrayHelper::getValue($config, 'base', null);
            $config['inputOptions']['id'] = ArrayHelper::getValue(ArrayHelper::getValue($config, 'inputOptions', []), 'id', strtolower($base->formName()).'-'.implode('-', $attrs));
            $name                         = sprintf('%s[%s]', $base->formName(), implode('][', $attrs));
            // $config['with']            = implode('().', $attrs);
            $attribute                    = array_pop($attrs);
            // $config['with']            = 'with:'.implode('.', $attrs).',';
        }
        if (!empty($name)) {
            $config['attribute']             = $attribute;
            $config['labelOptions']['for']   = $config['inputOptions']['id'];
            $config['hiddenOptions']['name'] = $name;
        }


        $layoutConfig = $this->createLayoutConfig($config);
        $config       = ArrayHelper::merge($layoutConfig, $config);
        // unset($config['with']);
        return parent::__construct($config);
    }

    /**
     * @param array $instanceConfig the configuration passed to this instance's constructor
     * @return array the layout specific default configuration for this instance
     */
    protected function createLayoutConfig($instanceConfig)
    {

        $attribute = $instanceConfig['attribute'];
        $form      = $instanceConfig['form'];

        $attribute = ArrayHelper::getValue($instanceConfig, 'attribute', '');
        $with      = $this->with; //ArrayHelper::getValue($instanceConfig, 'with', $attribute);

        $config    = array(
            'options' => array(
                'class'     => 'control-group',
                'data-bind' => sprintf("css:{'%s':%s.hasError,'%s':%s.validated}", $form->errorCssClass, $with, $form->successCssClass, $with),
                // 'data-bind' => sprintf("%s", ArrayHelper::getValue($instanceConfig, 'with', ''), $form->errorCssClass, $attribute, $form->successCssClass, $attribute),
                ),
            'inputOptions' => array(
                'name'      => '',
                'class'     => 'form-control',
                'data-bind' => sprintf('value:%s.display', $with),
                ),
            'parts' => array(
                '{attribute}' => $attribute,
                '{hidden}'    => Html::activeHiddenInput($instanceConfig['model'], $attribute, ArrayHelper::merge(['id' => null, 'data-bind' => sprintf('value:%s', $with)], ArrayHelper::getValue($instanceConfig, 'hiddenOptions', []))),
                '{error}'     => sprintf('<!-- ko foreach: %1$s.errors --><p class="help-block help-block-error" data-bind="text:$data"></p><!-- /ko --><!-- ko if: (%1$s.errors || []).length == 0 --><p class="help-block help-block-error"></p><!-- /ko -->', $with),
                ),
            );


        return $config;
    }



    public function prepend($content) {
        if (is_array($content))
            $content = implode('</span><span class="input-group-addon">', $content);
        $this->inputTemplate = '<div class="input-group"><span class="input-group-addon">'.$content.'</span>{input}</div>';
        return $this;
    }


    public function append($content) {
        if (is_array($content))
            $content = implode('</span><span class="input-group-addon">', $content);
        $this->inputTemplate = '<div class="input-group">{input}<span class="input-group-addon">'.$content.'</span></div>';
        return $this;
    }


    public function right() {
        $this->inputOptions['class'] = $this->inputOptions['class'] . ' text-right';
        return $this;
    }

    public function hiddenInput($options = []) {
        $this->template = '{hidden}';
        return $this;
    }

    public function radioButtons($items, $options = []) {
        $s = '<label class="btn" data-bind="css:{active:%2$s()==%3$s}, click: function() {%2$s(%3$s)}"><input type="radio" data-bind="checked: %2$s, checkedValue:%3$s" name="%4$s"> %1$s</label>';
        $x = [];

        $inputName = Html::getInputName($this->model, $this->attribute);
        foreach ($items as $key => $value) {
            $x[] = sprintf($s, $value, $this->attribute, Json::encode($key), $inputName);
        }
        $this->inputTemplate = sprintf('<div class="btn-group" data-toggle="buttons">%s</div>', implode('', $x));
        return $this;
    }

    public function datepicker($options = []) {

        $this->inputOptions['data-provide']     = "datepicker";
        $this->inputOptions['data-date-format'] = ArrayHelper::getValue($options, 'dateFormat', isset($this->form->dateFormat) ? $this->form->dateFormat : 'yyyy-mm-dd');

        $this->inputTemplate = sprintf('<div class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
            {input}
            <span class="input-group-addon" data-bind="click: %1$s.clear"><i class="glyphicon glyphicon-remove"></i></span>
            <span class="input-group-addon" data-bind="click: %1$s.current"><i class="glyphicon glyphicon-time"></i></span>
        </div>', $this->attribute);

        return $this;
    }    

    public function datetimepicker($options = []) {

        $this->inputOptions['data-provide']     = "datepicker";
        $this->inputOptions['data-date-format'] = ArrayHelper::getValue($options, 'dateFormat', isset($this->form->dateFormat) ? $this->form->dateFormat : 'yyyy-mm-dd');

        $this->inputTemplate = sprintf('<div class="row">
            <div class="col-sm-6 form-inline">
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                    {input}
                </div>
            </div>
            <div class="col-sm-6 form-inline">
                <select class="form-control" data-bind="options: %1$s.hours, optionsValue: \'key\', optionsText:\'value\', value:%1$s.hour, optionsCaption: \'--\'"></select>
                :
                <select class="form-control" data-bind="options: %1$s.minutes, optionsValue: \'key\', optionsText:\'value\', value:%1$s.minute, optionsCaption: \'--\'"></select>

                <a href="#" class="btn btn-sm btn-warning" data-bind="click: %1$s.clear"><i class="glyphicon glyphicon-remove"></i></a>
                <a href="#" class="btn btn-sm btn-info" data-bind="click: %1$s.current"><i class="glyphicon glyphicon-time"></i></a>
            </div>
        </div>', $this->attribute);

    return $this;
    }    


    public function email() {
       $this->parts['{beginLabel}'] = sprintf('<label><a data-bind="attr:{href:\'mailto:\'+%s()}">', $this->with);
       $this->parts['{labelTitle}'] = $this->model->getAttributeLabel($this->attribute);
       $this->parts['{endLabel}']   = '</a></label>';
       return $this;
   }

}

