<script id="x1-<?= $component ?>" type="text/html">

	<div class="form-group" data-bind="css: { 'has-error': (errors() && errors().length > 0) }">
		<!-- ko if: label -->
		<label class="control-label" data-bind="attr: {'for': name }, text: label"></label>
		<!-- /ko -->
		<input class="form-control" data-bind="'attr': {'id': id, 'maxlength': maxlength, 'name': name }, 'value': value" />
		<!-- ko if: hint -->
			<div class="hint-block" data-bind="text: hint"></div>
		<!-- /ko -->

		<!-- ko if: errors() && errors().length > 0 -->
		<ul data-bind="foreach: errors" class="help-block list-unstyled">
			<li data-bind="text: $data"></li>
		</ul>
		<!-- /ko -->
	</div>

</script>