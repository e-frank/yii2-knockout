ko.bindingHandlers.initial = {
	init :	function (element, valueAccessor, allBindings, context) {
		var current = null

		if (allBindings().checkedValue) {
			current  = ko.unwrap(allBindings().checkedValue);
		} else if (allBindings().value) {
			current  = ko.unwrap(allBindings().value);
		}
		
		if (current !== null) {
			var value    = valueAccessor();

			if (value.assign)
				value.assign(current);
			else
				value(current);
		}
	}
}