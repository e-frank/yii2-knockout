<?php 
use yii\helpers\Json;

$options['data-bind'] .= ', datetimepicker: ' . Json::encode($pickerOptions);
?>

<div class="form-inline">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text" data-bind="click: <?= $attribute ?>.display.open"><i class="fa fa-calendar"></i></span>
			</div>
<?
if (empty($model) || empty($attribute)) {
	echo \yii\helpers\Html::input('text', $name, $value, $options + ['id' => $id]);
} else {
	echo \yii\helpers\Html::activeTextInput($model, $attribute, $options + ['id' => $id]);
}
?>
<? if ($time == true) { ?>
		</div>
		<div class="input-group">
		<select class="form-control" data-bind="options: <?= $attribute ?>.times, optionsCaption: '-', value: <?= $attribute ?>.time"></select>
<? } ?>
			<div class="input-group-append">
				<span class="input-group-text" data-bind="click: <?= $attribute ?>.clear">
					<a href="javascript: false;" class="text-danger" ><i class="fas fa-times"></i></a>
				</span>
				<span class="input-group-text" data-bind="click: <?= $attribute ?>.current">
					<a href="javascript: false;" class="text-primary" ><i class="far fa-clock"></i></a>
				</span>
			</div>
		</div>

</div>


