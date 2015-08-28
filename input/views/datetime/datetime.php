<?= var_dump($time) ?>
<div class="form-inline">
		<div class="input-group">
			<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
			<input class="form-control" maxlength="<?= $maxlength ?>" size="<?= $size ?>" data-bind="'value': <?= $attribute ?>.display" />
		</div>

<? if ($time == true) { ?>
		<select class="form-control" data-bind="options: <?= $attribute ?>.times, optionsCaption: '-', value: <?= $attribute ?>.time"></select>
<? } else { ?>
		<select class="form-control" data-bind="options: <?= $attribute ?>.hours, optionsValue: 'key', optionsText: 'value', optionsCaption: '-', value: <?= $attribute ?>.hour"></select>
		<select class="form-control" data-bind="options: <?= $attribute ?>.minutes, optionsValue: 'key', optionsText: 'value', optionsCaption: '-', value: <?= $attribute ?>.minute"></select>
<? } ?>

		<div class="input-group nested-group" style="min-height: 2.45em">
			<span class="input-group-addon" data-bind="click: <?= $attribute ?>.clear">
				<a href="javascript: false;" class="text-danger" ><i class="fa fa-remove"></i></a>
			</span>
			<span class="input-group-addon" data-bind="click: <?= $attribute ?>.current">
				<a href="javascript: false;" class="text-primary" ><i class="fa fa-clock-o"></i></a>
			</span>
		</div>
</div>

<hr>
<ul data-bind="foreach: <?= $attribute ?>.times">
	<li data-bind="text: $data"></li>
</ul>
