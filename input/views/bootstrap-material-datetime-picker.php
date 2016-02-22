<?php 
use yii\helpers\Json;

$options['data-bind'] .= ', datetimepicker: ' . Json::encode($pickerOptions);
?>


<div class="form-inline">
		<div class="input-group">
			<span class="input-group-addon" data-bind="click: <?= $attribute ?>.display.open"><i class="fa fa-calendar"></i></span>
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
			<span class="input-group-addon" data-bind="click: <?= $attribute ?>.clear">
				<a href="javascript: false;" class="text-danger" ><i class="fa fa-remove"></i></a>
			</span>
			<span class="input-group-addon" data-bind="click: <?= $attribute ?>.current">
				<a href="javascript: false;" class="text-primary" ><i class="fa fa-clock-o"></i></a>
			</span>
		</div>

</div>


