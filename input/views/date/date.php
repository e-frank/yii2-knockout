<div class="form-inline">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text"><i class="fa fa-calendar"></i></span>
			</div>
<?php
if (empty($model) || empty($attribute)) {
	echo \yii\helpers\Html::input('text', $name, $value, $options + ['id' => $id]);
} else {
	echo \yii\helpers\Html::activeTextInput($model, $attribute, $options + ['id' => $id]);
}
?>

<!-- 			<span class="input-group-addon" data-bind="click: <?= $attribute ?>.current">
				<a href="javascript: false;" class="text-primary" ><i class="fa fa-clock-o"></i>OO</a>
			</span>
 -->
		</div>
</div>