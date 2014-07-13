<?
namespace efrank\knockout\widgets;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\base\InvalidConfigException;
use yii\web\View;
use yii\web\JsExpression;


class ActiveForm extends \yii\bootstrap\ActiveForm {

	const TYPE_INPUT    = 1;

	public $dateFormat = 'yy-mm-dd';

	public $enableClientValidation = false;
	public $validateOnSubmit       = false;
	public $validateOnChange       = false;

	private $labels = [];

	// public $fieldConfig = [
	// 'template' => "{label}\n{beginWrapper}\n{input}\n{hidden}ZZZ\n{hint}\n{error}\n{endWrapper}",
	// ];

	public function field222($model, $attribute, $options = []) {
		$inputOptions                 = ArrayHelper::getValue($options, 'inputOptions', []);
		$inputOptions                 = ArrayHelper::merge($inputOptions, ['name' => '', 'data-bind' => sprintf('value: %s.display', $attribute)]);
		$options['inputOptions']      = $inputOptions;
		$options['options']           = ['class' => 'control-group', 'data-bind' => sprintf("css:{'%s':%s.hasError,'%s':%s.validated}", $this->errorCssClass, $attribute, $this->successCssClass, $attribute)];
		$options['errorOptions']      = ['data-bind' => sprintf("foreach:%s.errors", $attribute)];
		$options['parts']['{error}']  = sprintf('<!-- ko foreach: %1$s.errors --><p class="help-block help-block-error" data-bind="text:$data"></p><!-- /ko --><!-- ko if: (%1$s.errors || []).length == 0 --><p class="help-block help-block-error"></p><!-- /ko -->', $attribute);
		$options['parts']['{hidden}'] = Html::hiddenInput(sprintf('%s[%s]', basename(get_class($model)), $attribute), null, ['data-bind' => sprintf('value: %s', $attribute)]);
		return parent::field($model, $attribute, $options);
	}


	public function display($model, $attribute, $options = []) {
		$inputOptions                 = ArrayHelper::getValue($options, 'inputOptions', []);
		$inputOptions['data-bind']    = ArrayHelper::getValue($inputOptions, 'data-bind', sprintf('value: %s.display', $attribute));
		$options['inputOptions']      = $inputOptions;
		$options['options']           = ['class' => 'control-group', 'data-bind' => sprintf("css:{'has-error':%s.hasError}", $attribute)];
		// $options['errorOptions']   = ['data-bind' => sprintf("foreach:%s.errors", $attribute)];
		$options['parts']['{error}']  = sprintf('<!-- ko foreach: %1$s.errors --><p class="help-block help-block-error" data-bind="text:$data"></p><!-- /ko --><!-- ko if: %1$s.errors.length == 0 --><p class="help-block help-block-error"></p><!-- /ko -->', $attribute);
		return parent::field($model, $attribute, $options);
	}


	public function label($content) {
		return sprintf('<label class="control-label">%s</label>', $content);
	}

	public function hint($content) {
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


	public function run()
	{
		parent::run();
		// echo Html::endTag('div');
	}


	public function init()
	{

		if (!isset($this->options['id'])) {
			$this->options['id'] = $this->getId();
		}

		if (!isset($this->fieldConfig['class'])) {
			$this->fieldConfig['class'] = ActiveField::className();
		}

		// if (!isset($this->beforeSubmit))
		// 	$this->beforeSubmit = new JsExpression('alert(1)');

		$this->beforeSubmit = new JsExpression('function($form) {
			console.log("beforeSubmit", $form);
			return false;
		}');

		$view = $this->getView();
		$view->registerJs(sprintf('$("#%1$s button[type=submit]").click(function(e) {
			alert(222);
			e.preventDefault();
			var vm = ko.dataFor(document.getElementById("%1$s"));
			if (vm && vm.update) vm.update(%2$s);
			console.log("vm", vm.getModel());
			return false;
		});', $this->getId(), Json::encode($this->beforeSubmit)), View::POS_READY);

		// echo Html::beginTag('div', $this->options);
		parent::init();
	}


}

?>