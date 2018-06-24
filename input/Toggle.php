<?
namespace x1\knockout\input;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\web\View;
use yii\helpers\Url;


class Toggle extends \yii\widgets\InputWidget {
	public static $autoIdPrefix = 'x1Toggle';

	public $size  = '';
	public $label = null;

	public function run() {
		$this->options['type'] = 'checkbox';
		Html::addCssClass($this->options, 'switch');
		Html::removeCssClass($this->options, 'form-control');

		return $this->view->render('@x1/knockout/input/views/toggle/toggle', [
			'id'        => $this->id,
			'model'     => $this->model,
			'attribute' => $this->attribute,
			'name'      => $this->name,
			'value'     => $this->value,
			'size'      => $this->size,
			'label'     => $this->label,
			'options'   => array_merge(array_filter([]), $this->options),
		]);
	}

}

?>