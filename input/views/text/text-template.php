<template id="<?= $component ?>">

	<div class="form-group" data-bind="css: {'has-error': errors() && errors().length > 0 }">
		<input class="form-control" data-bind="'attr': {'id': id, 'maxlength': maxlength, 'name': name }, 'value': value" />

		<!-- ko if: errors() && errors().length > 0 -->
		<ul data-bind="foreach: errors" class="error-block">
			<li data-bind="text: $data"></li>
		</ul>
		<!-- /ko -->
	</div>

</template>


