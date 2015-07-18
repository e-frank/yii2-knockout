<template id="<?= $component ?>">

	<div xxxclass="form-group" data-bind="css: {'has-error': errors() && errors().length > 0 }">
		<input class="form-control text-right" data-bind="'attr': {'id': id, 'maxlength': maxlength }, 'value': value.display" />
		<input type="hidden" data-bind="attr: {'name': name }, 'value': value">
	</div>

</template>


