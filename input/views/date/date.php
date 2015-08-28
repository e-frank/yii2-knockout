<div class="form-inline">
		<div class="input-group">
			<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
			<input class="form-control" maxlength="<?= $maxlength ?>" size="<?= $size ?>" data-bind="'value': <?= $attribute ?>.display" />
			<span class="input-group-addon" data-bind="click: <?= $attribute ?>.clear">
				<a href="javascript: false;" class="text-danger" ><i class="fa fa-remove"></i></a>
			</span>
			<span class="input-group-addon" data-bind="click: <?= $attribute ?>.current">
				<a href="javascript: false;" class="text-primary" ><i class="fa fa-clock-o"></i></a>
			</span>
		</div>
</div>
