ko.bindingHandlers.select2 = {
	init: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
		var self = this;
		var e    = $(element);
		var v    = valueAccessor();
		var dont = false;

		var bindings  = ko.unwrap(allBindings());
		var selectedX = bindings.select2Value;
		var items     = (bindings.items && ko.isObservable(bindings.items)) ? bindings.items : null;
		var idProp    = ko.unwrap(bindings.optionsValue) || 'id';
		var textProp  = ko.unwrap(bindings.optionsText) || 'text';
		var current   = bindings.current;

		var options  = $.extend({
				// data:  ko.unwrap((items) ? items : []), 
				data:  ko.unwrap((items) ? items : ((current) ? [ko.toJS(current)] : [])), 
				value: ko.unwrap(selectedX)
			}, v);


		this.isSetting = false;

		this.attachEvents = function() {
			if (bindings.open && bindings.open == true) {
				e.select2('open');
			}
		}

		this.getData = function() {
			var v  = null;
			var s2 = e.data('select2');
			if (s2) {
				var data = s2.data();
				v = (data.length && data.length > 0) ? data[0][idProp] : null
			}

			return v;
		}

		// set current selection by id
		this.setCurrentItems = function() {
			var itms = ko.unwrap(items) || null;
			var s2   = e.data('select2');

			if (itms !== null) {
				var selected = ko.utils.arrayFirst(itms || [], function(item) {
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
		}


		// cleanup
		ko.utils.domNodeDisposal.addDisposeCallback(element, function() {
    		e.select2('destroy');
  		});


		e.select2(options);
		setCurrentItems();
		attachEvents();


		// rebuild data on changed items
		if (items !== null) {
			items.subscribe(function(items) {
				var isOpen = e.data('select2').isOpen()
				// e.select2('destroy');
				e.empty();

				options.data = ko.unwrap(items || []);
				e.select2(options);
				setCurrentItems();
				if (isOpen)
					e.select2('open');
			});
		}


		if (selectedX) {

			// listen to changed event
			if (!current) {
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

				e.on('change', this.changed)
			}

		}




		// if selected value has changed, update select2
		if (ko.isObservable(current)) {

			// triggering selection
	        e.on('select2:select', function(ev) {
	        	var data = ev.params.data;

	        	if (ko.isWriteableObservable(current)) {
	        		if (self.isSetting == false) {
	        			self.isSetting = true;
		            	current(data);
	        			self.isSetting = false;
	        		}
	        	} else {
	        		if (ko.isObservable(selectedX)) {
	        			if (ko.isWriteableObservable(selectedX)) {
	        				selectedX(data);
	        			}
	        		} else {
	        			selectedX = data;
	        		}
	        	}
	        });

			current.subscribe(function(v) {
				if (self.isSetting == false) {
					self.isSetting = true;
					var s2         = e.data('select2');
					var selected   = ko.toJS(current) || {};

					selected[idProp]   = selected[idProp] || '';
					selected[textProp] = selected[textProp] || '';

					if (selectedX) {
						if (ko.isWriteableObservable(selectedX)) {
							selectedX(selected[idProp]);
						} else {
							if (!ko.isObservable(selectedX)) {
								selectedX = selected[idProp];
							}
						}
					}

					s2.trigger('select', { data: selected });
					e.trigger('change');
					self.isSetting = false;
				}
			});
		} else {
			if (ko.isObservable(selectedX)) {
				selectedX.subscribe(function(newvalue) {
					setCurrentItems();
				});
			}
		}


	}

}
