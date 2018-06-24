<?php
namespace x1\knockout\input;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\web\View;
use yii\helpers\Url;


class Date extends \yii\widgets\InputWidget {
	public static $autoIdPrefix = 'x1Date';

	public $size      = null;
	public $maxlength = 10;
	public $clear     = true;
	public $current   = true;

	public function run() {
		return $this->view->render('@x1/knockout/input/views/date/date', [
			'id'        => $this->id,
			'model'     => $this->model,
			'attribute' => $this->attribute,
			'name'      => $this->name,
			'value'     => $this->value,
			'clear'     => $this->clear,
			'current'   => $this->current,
			'options'   => array_merge(array_filter(['type' => 'date', 'size' => $this->size, 'maxlength' => $this->maxlength]), $this->options),
		]);
	}

}
