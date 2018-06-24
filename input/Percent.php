<?php
namespace x1\knockout\input;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\web\View;
use yii\helpers\Url;


class Percent extends BaseDecimalInput {
	public static $autoIdPrefix = 'x1Percent';

	public $size      = null;
	public $maxlength = 10;

	public function run() {
		return $this->view->render('@x1/knockout/input/views/decimal/percent', [
			'id'        => $this->id,
			'model'     => $this->model,
			'attribute' => $this->attribute,
			'name'      => $this->name,
			'value'     => $this->value,
			'options'   => array_merge(array_filter(['size' => $this->size, 'maxlength' => $this->maxlength]), $this->options),
		]);
	}

}
