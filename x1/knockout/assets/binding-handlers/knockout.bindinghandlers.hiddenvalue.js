//
//	triggers change event, if bound value is changed
//	used by yiiActiveForm validation to work on data-bound hidden fields
//
ko.bindingHandlers.hiddenValue	= {
	init :	function (element, valueAccessor, allBindings, context) {
		var current  = $(element).val();
		var value    = valueAccessor();

		value(current);
		ko.applyBindingsToNode(element, { value : value });

		value.subscribe(function () {
            $(element).trigger('change');
            console.log('change');
        });
	}
}
