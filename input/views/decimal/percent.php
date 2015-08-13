<div class="input-group">
	<input class="form-control text-right" <?= empty($maxlength) ? '' : 'maxlength="'.$maxlength.'"' ?> <?= empty($size) ? '' : 'size="'.$size.'"' ?> data-bind="'value': <?= $attribute ?>.display" />
	<span class="input-group-addon">%</span>
</div>
