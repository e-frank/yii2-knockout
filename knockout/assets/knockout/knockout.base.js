ko.observable.fn.toString = function() {
	return "KO.OBSERVABLE: " + ko.toJSON(this(), null, 2);
};

ko.computed.fn.toString = function() {
	return "KO.COMPUTED: " + ko.toJSON(this(), null, 2);
};

function viewmodelBase() {

	var self 		= this;
	this.options    = {};
	this.setOptions = function(o) { self.options = o; }
	this.errors 	= ko.observableArray([]);
	this.hasError	= ko.computed(function() { return this.errors.length > 0}, this);
	this.hasErrors	= this.hasError;

	this.logOptions = function() { console.log (self.options); }
	this.get 		= function() { 
		var result = {};
		if (self._attributes && self._attributes.length > 0) {
			$.each(self._attributes, function(key, value) {
				if (ko.isWriteableObservable(self[value])) {
					if (self[value].get)
						result[value] = self[value].get();
					else
						result[value] = self[value]();
				}
			})
		} else {
			$.each(self, function(key, value) {
				if (ko.isWriteableObservable(self[key]))
					result[key] = self[key]();
			})
		}
		return result;
	};
	this.getModel	= function() {
		var o = {};
		o[self._name] = self.get();
		return o;
	}

	this._isSetting = ko.observable(false);
	this.set       = function(data, errors) { 
		errors = errors || {};
		self._isSetting(true);
		if (self._attributes) {
			$.each(self._attributes, function(key, value) {
				var p = self[value];
				d = data ? data[value] : null;
				if (ko.isWriteableObservable(p)) {
					p.set ? p.set(d, errors[value]) : p(d);
					p.errors ? p.errors([]) : null;
					p.validated ? p.validated(false) : null;
				}
			})
		} else {
			$.each(data, function(key, value) {
				var p = self[key];
				if (ko.isWriteableObservable(p)) {
					p.set ? p.set(value) : p(value);
					p.errors ? p.errors([]) : null;
					p.validated ? p.validated(false) : null;
				}
			})
		}
		var eee = [];
		$.each(errors, function(key, value) {
			var p = self[key];
			if (p && p.errors) {
				p.errors(value);
				eee.concat(value);
			} else {
				if (!p)
					console.log('setting undefined: ', key)
			}
		});
		self.errors(eee);
		self._isSetting(false);
		// console.log('setting done', self);
		return self;
	};

	this.modal 		= function(show) { 
		// console.log('modal options', show, self.options, self.get());
		if(self.options.modal)
			$('#' + self.options.modal).modal(show);	
	}

	this.update = function(callback) {
		if (self.options.url)
			self.post(self.options.url, self.getModel(), function(data) {
				// console.log('update post result' ,data);
				if (self.options.grid)
					baseViewModel.pjax(self.options.grid);
				if (((data.errors && data.errors.length == 0) || (!data.errors))) {
					self.modal('hide');
					var ok = true;
					if (callback)
						ok = callback(data) || true;
					if (ok && self.options.redirect) {
						window.location.href = self.options.redirect;
					}
				}
			});
	}

	this.post 		= function(url, postdata, callback) {
		var opt = {};
		if (self.classname)
			opt[self.classname] = postdata;
		else
			opt = postdata;
		var p = $.post(url, opt, function(data) {
			// console.log('base post result', data);
			self.set(data.model, data.errors);
			if (callback)
				callback(data);
		}, 'json');
		console.log('post return', p);		
	}

	this.formSubmit	= function() {
		if (self.options.form) {
			console.log('capturing form submit', self.options.form);
			$('#' + self.options.form).on('submit', function(event, data) {
				event.preventDefault();
				console.log('captured form submit');
				self.update();
				return false;
			});
		}
	}

	this.finish 	= function() {
		self.formSubmit();
	}

	this.validate 	= function() {
		$.each(self._validators, function(key, value) {
			var p = self[value];
			if (p.validated && p.validated() != true && p.validate) {
				p.validate();
			}
		})

		$.each(self._lists, function(key, value) {
			var p = self[value];
			if (p.validate) {
				p.validate();
			}
		})
	}
}