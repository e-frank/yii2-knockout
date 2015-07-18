<div class="form-inline">
		<div class="input-group">
			<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
			<input class="form-control" maxlength="10" size="5" data-bind="'value': <?= $attribute ?>.display" />
		</div>
		<select class="form-control" data-bind="options: <?= $attribute ?>.hours, optionsValue: 'key', optionsText: 'value', optionsCaption: '-', value: <?= $attribute ?>.hour"></select>
		<select class="form-control" data-bind="options: <?= $attribute ?>.minutes, optionsValue: 'key', optionsText: 'value', optionsCaption: '-', value: <?= $attribute ?>.minute"></select>
		<div class="btn-group" role="group">
			<a href="" class="btn btn-default" data-bind="click: <?= $attribute ?>.clear"><i class="fa fa-remove"></i></a>
			<a href="" class="btn btn-primary" data-bind="click: <?= $attribute ?>.current"><i class="fa fa-clock-o"></i></a>
		</div>
</div>
