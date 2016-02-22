<div class="form-inline">
		<div class="input-group">
			<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
			<?= \yii\helpers\Html::tag('input', null, $options) ?>
			<span class="input-group-addon" data-bind="click: <?= $attribute ?>.clear">
				<a href="javascript: false;" class="text-danger" ><i class="fa fa-remove"></i></a>
			</span>
			<span class="input-group-addon" data-bind="click: <?= $attribute ?>.current">
				<a href="javascript: false;" class="text-primary" ><i class="fa fa-clock-o"></i></a>
			</span>
		</div>
</div>
