<?php
namespace x1\knockout\input;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\web\View;
use yii\helpers\Url;


class BootstrapMaterialDatetimePicker extends \yii\widgets\InputWidget {
	public static $autoIdPrefix = 'x1DatetimePicker';

	public $time          = true;
	public $size          = null;
	public $maxlength     = 10;
	public $pickerOptions = [];

	public function run() {

		// $this->view->registerCssFile('https://fonts.googleapis.com/icon?family=Material+Icons');
		$this->view->registerCssFile('https://fonts.googleapis.com/icon?family=Material+Icons');

		\x1\assets\BootstrapMaterialDatetimePicker\BootstrapMaterialDatetimePickerAsset::register($this->view);

		return $this->view->render('@x1/knockout/input/views/bootstrap-material-datetime-picker', [
			'id'            => $this->id,
			'model'         => $this->model,
			'attribute'     => $this->attribute,
			'name'          => $this->name,
			'value'         => $this->value,
			'options'       => array_merge(array_filter(['size' => $this->size, 'maxlength' => $this->maxlength]), $this->options),
			'pickerOptions' => $this->pickerOptions,
			'time'          => $this->time,
		]);
	}


	public function init() {
		if (!isset($this->pickerOptions['lang']))
			$this->pickerOptions['lang'] = new JsExpression('x1.config.language');
		if (!isset($this->pickerOptions['format']))
			$this->pickerOptions['format'] = new JsExpression('x1.config.date');
	}

}
