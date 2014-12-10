
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

