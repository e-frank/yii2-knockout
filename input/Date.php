<?
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

	public $size      = 5;
	public $maxlength = 10;

	public function run() {
		return $this->view->render('@x1/knockout/input/views/date/date', [
			'id'        => $this->id,
			'model'     => $this->model,
			'attribute' => $this->attribute,
			'name'      => $this->name,
			'value'     => $this->value,
			'maxlength' => $this->maxlength,
			'size'      => $this->size,
			'options'   => $this->options,
		]);
	}

}

?>