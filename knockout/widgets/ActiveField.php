<?php
namespace efrank\knockout\widgets;

use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;


class ActiveField extends \yii\bootstrap\ActiveField
{

    public $template = "{label}\n{beginWrapper}\n{input}\n{hidden}\n{hint}\n{error}\n{endWrapper}";

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
        $layoutConfig = $this->createLayoutConfig($config);
        $config       = ArrayHelper::merge($layoutConfig, $config);
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
        $config    = array(
            'options' => array(
                'class'     => 'control-group',
                'data-bind' => sprintf("css:{'%s':%s.hasError,'%s':%s.validated}", $form->errorCssClass, $attribute, $form->successCssClass, $attribute),
                ),
            'inputOptions' => array(
                'name'      => '',
                'class'     => 'form-control',
                'data-bind' => sprintf('value:%s.display', $attribute),
                ),
            'parts' => array(
                '{attribute}' => $attribute,
                '{hidden}'    => Html::activeHiddenInput($instanceConfig['model'], $attribute, ['id' => null, 'data-bind' => sprintf('value:%s', $attribute)]),
                '{error}'     => sprintf('<!-- ko foreach: %1$s.errors --><p class="help-block help-block-error" data-bind="text:$data"></p><!-- /ko --><!-- ko if: (%1$s.errors || []).length == 0 --><p class="help-block help-block-error"></p><!-- /ko -->', $attribute),
                ),
            );

        return $config;
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


    public function radioButtons($items, $options = []) {
        $s = '<label class="btn" data-bind="css:{active:%2$s()==%3$s}, click: function() {%2$s(%3$s)}"><input type="radio" data-bind="checked: %2$s, checkedValue:%3$s" name="%4$s"> %1$s</label>';
        $x = [];

        foreach ($items as $key => $value) {
            $x[] = sprintf($s, $value, $this->attribute, Json::encode($key), Html::getInputName($this->model, $this->attribute));
        }
        $this->inputTemplate = sprintf('<div class="btn-group" data-toggle="buttons">%s</div>', implode('', $x));
        return $this;
    }

    public function prepend() {

    }
}
