ko.bindingHandlers.typeahead	= {
	init :	function (element, valueAccessor, allBindings, context) {
		var v = $.extend({}, {
			value	:	null,
			decimal	:	2,
			zero	:	true,
			percent	:	false
		}, valueAccessor());

		var value = parseFloat(ko.utils.unwrapObservable(v.value));
		if (isNaN(value))
			value = 0;

		var result = 0;
		if (!v.zero && value == 0)
			result = '';
		else if (v.percent)
			result = number_format((value * 100), v.decimal, x1_decimal, x1_thousands) + ' %';
		else
			result = number_format(value, v.decimal, x1_decimal, x1_thousands);

		$(element).text(result);

		// ko.applyBindingsToNode(element, { checked : valueAccessor.boolean });
	},
	update :	function (element, valueAccessor, allBindings, context) {
		var v = $.extend({}, {
			value	:	null,
			decimal	:	2,
			zero	:	true,
			percent	:	false
		}, valueAccessor());

		var value = parseFloat(ko.utils.unwrapObservable(v.value));
		if (isNaN(value))
			value = 0;

		var result = 0;
		if (!v.zero && value == 0)
			result = '';
		else if (v.percent)
			result = number_format((value * 100), v.decimal, x1_decimal, x1_thousands) + ' %';
		else
			result = number_format(value, v.decimal, x1_decimal, x1_thousands);

		$(element).text(result);

		// ko.applyBindingsToNode(element, { checked : valueAccessor.boolean });
	}
}
