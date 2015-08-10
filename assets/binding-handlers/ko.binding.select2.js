ko.bindingHandlers.select2 = {
	init: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
		var e    = $(element);
		var v    = valueAccessor();
		var dont = false;

		var bindings = ko.unwrap(allBindings());
		var items    = (bindings.items && ko.isObservable(bindings.items)) ? bindings.items : null;
		var idProp   = ko.unwrap(bindings.optionsValue) || 'id';
		var textProp = ko.unwrap(bindings.optionsText) || 'text';

		var options  = $.extend({
				data:    ko.unwrap(items || []), 
				value: ko.unwrap(bindings.selectedValue)
			}, v);

		// cleanup
		ko.utils.domNodeDisposal.addDisposeCallback(element, function() {
    		e.select2('destroy');
  		});


		// rebuild data on changed items
		if (items !== null) {
			items.subscribe(function(items) {
				e.select2('destroy');
				e.empty();
				options.data = ko.unwrap(items || []);
				e.select2(options);
				e.trigger('change');
			});
		}

		console.log('select2 options', 	options)
		e.select2(options); 

		// load initial value, listen to changed event
		if (bindings.selectedValue) {
			e.select2('val', ko.unwrap(bindings.selectedValue));
			if (ko.isObservable(bindings.selectedValue)) {
				e.on('change', function(ev) {
					var v = e.select2('val');
					var o = bindings.selectedValue();
					if (o != v)
						bindings.selectedValue(v);
				})
			} else {
				e.on('change', function(ev) {
					var v = e.select2('val');
					bindings.selectedValue = v;
				})
			}
		}


		// if selected value is changed in viewmodel, update select2
		if (bindings.selectedValue && ko.isObservable(bindings.selectedValue)) {
			bindings.selectedValue.subscribe(function(newvalue) {
				if ((newvalue || null) != e.select2('val')) {
					e.select2('val', ko.unwrap(newvalue));
				}
				e.trigger('change');
			});
		}

	}
}
