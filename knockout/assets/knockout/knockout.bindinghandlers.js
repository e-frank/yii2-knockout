
ko.bindingHandlers.bsChecked = {
	init: function (element, valueAccessor, allBindingsAccessor, viewModel, bindingContext) {
		var value = valueAccessor();
		var newValueAccessor = function () {
			return {
				change: function () {
					value(element.value);
				}
			}
		};
		ko.bindingHandlers.event.init(element, newValueAccessor, allBindingsAccessor, viewModel, bindingContext);
	},
	update: function (element, valueAccessor, allBindingsAccessor,
		viewModel, bindingContext) {
		if ($(element).val() == ko.unwrap(valueAccessor())) {
			$(element).closest('.btn').button('toggle');
		}
	}
}


ko.bindingHandlers.checkbox	= {
	init :	function (element, valueAccessor, allBindings, context) {
		var el = $(element);
		if (!valueAccessor.boolean) {
			valueAccessor.boolean = ko.computed({
				read: function() {
					return this() == 1;
				},
				write: function(v) {
					if (v == 1 || v == true) {
						this(1);
					} else { this(0); }
				},
				owner: valueAccessor()
			});
		}
		ko.applyBindingsToNode(element, { checked : valueAccessor.boolean });
	},

	update :	function (element, valueAccessor, allBindings, context) {
		var el = $(element);
		if (!valueAccessor.boolean) {
			valueAccessor.boolean = ko.computed({
				read: function() {
					return this() == 1;
				},
				write: function(v) {
					if (v == 1 || v == true) {
						this(1);
					} else { this(0); }
				},
				owner: valueAccessor()
			});
		}
		ko.applyBindingsToNode(element, { checked : valueAccessor.boolean });
	},

}

ko.bindingHandlers.number	= {
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
	}
}


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

