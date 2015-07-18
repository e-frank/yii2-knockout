<template id="<?= $component ?>">

	<div class="form-group" data-bind="css: {'has-error': errors() && errors().length > 0 }">
		<select class="form-control" data-bind="'attr': {'id': id}, 'options': items, 'value': item, 'optionsText': optionsText, 'optionsCaption': optionsCaption"></select>
		<div class="hint" data-bind="text: hint"></div>

		<!-- ko if: errors() && errors().length > 0 -->
		<ul data-bind="foreach: errors">
			<li data-bind="text: $data"></li>
		</ul>
		<!-- /ko -->

		<input type="hidden" data-bind="attr: {'name': name}, 'value': value">
	</div>

</template>


