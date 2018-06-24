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

			<span class="input-group-append">
				<?php if ($clear) { ?>
				<div class="input-group-text text-danger" data-bind="click: function(vm, e) { $(e.target).closest('div.input-group').find('input[type=date]').val(null); return false; }">
					<i class="fa fa-times"></i>
				</div>
				<?php } ?>
				<?php if ($current) { ?>
				<div class="input-group-text text-primary" data-bind="click: function(vm, e) { $(e.target).closest('div.input-group').find('input[type=date]').val((new Date()).toISOString().split('T')[0]); return false; }">
					<i class="fa fa-clock"></i>
				</div>
				<?php } ?>
			</span>

		</div>
</div>
