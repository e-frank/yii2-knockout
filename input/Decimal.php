<?
namespace x1\knockout\input;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\web\View;
use yii\helpers\Url;


class Decimal extends BaseDecimalInput {
	public static $autoIdPrefix = 'x1Decimal';

	public $size      = null;
	public $maxlength = 15;

	public function run() {
		return $this->view->render('@x1/knockout/input/views/decimal/decimal', [
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

	public function init() {
		parent::init();
	}
}

?>