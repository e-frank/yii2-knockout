//
//	triggers change event, if bound value is changed
//	used by yiiActiveForm validation to work on data-bound hidden fields
//
ko.bindingHandlers.hiddenValue	= {
	init :	function (element, valueAccessor, allBindings, context) {
		var current  = $(element).val();
		var value    = valueAccessor();

		if (value.assign)
			value.assign(current);
		else
			value(current);
		
		ko.applyBindingsToNode(element, { 'value' : value });
	},

}
