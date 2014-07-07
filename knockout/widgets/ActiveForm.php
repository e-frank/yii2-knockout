<?
namespace efrank\knockout\widgets;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use yii\web\View;


class ActiveForm extends \yii\bootstrap\ActiveForm {

    const TYPE_INPUT    = 1;

    public $dateFormat = 'yy-mm-dd';

    private $labels = [];

	public function field($model, $attribute, $options = []) {
		$inputOptions              = ArrayHelper::getValue($options, 'inputOptions', []);
		$inputOptions['data-bind'] = ArrayHelper::getValue($inputOptions, 'data-bind', sprintf('value: %s', $attribute));
		$options['inputOptions']   = $inputOptions;
		return parent::field($model, $attribute, $options);
	}


	public function display($model, $attribute, $options = []) {
		$inputOptions              = ArrayHelper::getValue($options, 'inputOptions', []);
		$inputOptions['data-bind'] = ArrayHelper::getValue($inputOptions, 'data-bind', sprintf('value: %s.display', $attribute));
		$options['inputOptions']   = $inputOptions;
		return parent::field($model, $attribute, $options);
	}

	public function label($content) {
		return sprintf('<label class="control-label">%s</label>', $content);
	}

	public function datepicker($model, $attribute, $options = []) {
		$inputOptions              = ArrayHelper::getValue($options, 'inputOptions', []);
		$inputOptions['data-bind'] = ArrayHelper::getValue($inputOptions, 'data-bind', sprintf('value: %s.display', $attribute));
		$options['inputOptions']   = $inputOptions;


		// $a = parent::label($model, $attribute);
		// var_dump($a);
    	echo $this->label($model->getAttributeLabel($attribute)) . sprintf('<div id="%1$s_wrapper" class="input-group">
    		<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
    		<input class="form-control" data-provide="datepicker" data-date-format="%4$s" id="%1$s_disp" data-bind="value: %3$s.display" />
    		<span class="input-group-addon" data-bind="click: %3$s.clear"><i class="glyphicon glyphicon-remove"></i></span>
    		<span class="input-group-addon" data-bind="click: %3$s.current"><i class="glyphicon glyphicon-time"></i></span>
    		<input type="hidden" id="%1$s" name="%2$s[%3$s]" data-bind="value: %3$s" />
		</div>', $this->options['id'], basename($model->className()), $attribute, ArrayHelper::getValue($options, 'format', $this->dateFormat));


		// return parent::field($model, $attribute, $options);
	}

    // public function init()
    // {
    // 	parent::init();

    //     echo $this->renderInput();
    // }


}

?>