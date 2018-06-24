<div class="form-inline">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text"><i class="fa fa-calendar"></i></span>
			</div>
<?php
if (empty($model) || empty($attribute)) {
	echo \yii\helpers\Html::input('date', $name, $value, array_merge($options, ['id' => $id]));
} else {
	echo \yii\helpers\Html::activeTextInput($model, $attribute, array_merge($options, ['id' => $id]));
}
?>

<!-- 			<span class="input-group-addon" data-bind="click: <?= $attribute ?>.current">
				<a href="javascript: false;" class="text-primary" ><i class="fa fa-clock-o"></i>OO</a>
			</span>
 -->

<code data-bind="text: startdate"></code>
		</div>
</div>
