ko.bindingHandlers.select2 = {
	init: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
		var e    = $(element);
		var v    = valueAccessor();
		var dont = false;

		var bindings  = ko.unwrap(allBindings());
		var selectedX = bindings.select2Value;
		var items     = (bindings.items && ko.isObservable(bindings.items)) ? bindings.items : null;
		var idProp    = ko.unwrap(bindings.optionsValue) || 'id';
		var textProp  = ko.unwrap(bindings.optionsText) || 'text';

		var options  = $.extend({
				data:  ko.unwrap(items || []), 
				value: ko.unwrap(selectedX)
			}, v);


		this.attachEvents = function() {
			if (bindings.open && bindings.open == true) {
				e.select2('open');
			}
		}

		this.getData = function() {
			var s2   = e.data('select2');
			var data = s2.data();
			var v    = (data.length && data.length > 0) ? data[0][idProp] : null
			return v;
		}

		// set current selection by id
		this.setCurrent = function() {
			var s2       = e.data('select2');
			var selected = ko.utils.arrayFirst(ko.unwrap(items) || [], function(item) {
	            return (item[idProp]) == (ko.unwrap(selectedX));
	        });

			if (selected) {
				var old = getData();
				var v   = selected[idProp];

				if (v !== old) {
					s2.trigger('select', {data: selected});
					e.trigger('change');
				}
			} else {
				e.select2('val', null);
			}
		}


		// cleanup
		ko.utils.domNodeDisposal.addDisposeCallback(element, function() {
    		e.select2('destroy');
  		});


		// rebuild data on changed items
		if (items !== null) {
			items.subscribe(function(items) {
				var isOpen = e.data('select2').isOpen()
				// e.select2('destroy');
				e.empty();

				options.data = ko.unwrap(items || []);
				e.select2(options);
				setCurrent();
				if (isOpen)
					e.select2('open');
			});
		}

		e.select2(options);
		setCurrent();
		attachEvents();


		// listen to changed event
		if (selectedX) {
			if (ko.isObservable(selectedX)) {
				this.changed = function(ev) {
					var v = getData();
					var o = selectedX();
					if (o !== v)
						selectedX(v);
				}
			} else {
				this.changed = function(ev) {
					var v     = getData();
					selectedX = v;
				}
			}

			e.on('change', changed)
		}

		// if selected value has changed, update select2
		if (selectedX && ko.isObservable(selectedX)) {
			selectedX.subscribe(function(newvalue) {
				setCurrent();
			});
		}


	}

}
