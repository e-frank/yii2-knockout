<?php
namespace efrank\knockout\widgets;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;


class ActiveField extends \yii\bootstrap\ActiveField
{

    public $wrapperOptions = [
#        'data-bind' => "css:{'has-error':true}",
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


    public function __construct222($config = [])
    {
        // $layoutConfig = $this->createLayoutConfig($config);
        // $config = ArrayHelper::merge($layoutConfig, $config);

		$attribute         = ArrayHelper::getValue($config, 'attribute', []);
		$config['options'] = ArrayHelper::getValue($config, 'options', []);
        $config['options']['data-bind'] = ArrayHelper::getValue($config['options'], 'data-bind', sprintf("css:{'has-error':%s}", $attribute));

        // $config['parts']
        return parent::__construct($config);
    }

    /**
     * @param array $instanceConfig the configuration passed to this instance's constructor
     * @return array the layout specific default configuration for this instance
     */
    protected function createLayoutConfig222($instanceConfig)
    {
        $config = [
            'hintOptions' => [
                'tag' => 'p',
                'class' => 'help-block',
            ],
            'errorOptions' => [
                'tag' => 'p',
                'class' => 'help-block help-block-error',
            ],
            'inputOptions' => [
                'class' => 'form-control',
            ],
        ];

        $layout = $instanceConfig['form']->layout;

        if ($layout === 'horizontal') {
            $config['template'] = "{label}\n{beginWrapper}\n{input}\n{error}\n{endWrapper}\n{hint}";
            $cssClasses = [
                'offset' => 'col-sm-offset-3',
                'label' => 'col-sm-3',
                'wrapper' => 'col-sm-6',
                'error' => '',
                'hint' => 'col-sm-3',
            ];
            if (isset($instanceConfig['horizontalCssClasses'])) {
                $cssClasses = ArrayHelper::merge($cssClasses, $instanceConfig['horizontalCssClasses']);
            }
            $config['horizontalCssClasses'] = $cssClasses;
            $config['wrapperOptions'] = ['class' => $cssClasses['wrapper']];
            $config['labelOptions'] = ['class' => 'control-label ' . $cssClasses['label']];
            $config['errorOptions'] = ['class' => 'help-block help-block-error ' . $cssClasses['error']];
            $config['hintOptions'] = ['class' => 'help-block ' . $cssClasses['hint']];
        } elseif ($layout === 'inline') {
            $config['labelOptions'] = ['class' => 'sr-only'];
            $config['enableError'] = false;
        }

        return $config;
    }



}
