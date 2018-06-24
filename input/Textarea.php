<?php
namespace x1\knockout\input;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\web\View;
use yii\helpers\Url;


class Textarea extends \yii\widgets\InputWidget {

	public static $autoIdPrefix = 'x1Textarea';
	
	public $cols = null;
	public $rows = null;

	public function run() {
		return $this->view->render('@x1/knockout/input/views/textarea/textarea', [
			'id'        => $this->id,
			'model'     => $this->model,
			'attribute' => $this->attribute,
			'name'      => $this->name,
			'value'     => $this->value,
			'options'   => array_merge(array_filter(['rows' => $this->rows, 'cols' => $this->cols]), $this->options),
		]);
	}

}
