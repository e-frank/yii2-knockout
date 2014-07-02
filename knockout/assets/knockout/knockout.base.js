ko.observable.fn.toString = function() {
	return "KO.OBSERVABLE: " + ko.toJSON(this(), null, 2);
};

ko.computed.fn.toString = function() {
	return "KO.COMPUTED: " + ko.toJSON(this(), null, 2);
};

function viewmodelBase() {

	var self 		= this;
	this.options    = {};
	// this.assign     = function(o) { self.instance = o; }
	this.setOptions = function(o) { self.options = o; }

	this.logOptions = function() { console.log (self.options); }
	this.get 		= function() { 
		var result = {};
		if (self._attributes && self._attributes.length > 0) {
			$.each(self._attributes, function(key, value) {
				if (ko.isObservable(self[value])) {
					if (self[value].get)
						result[value] = self[value].get();
					else
						result[value] = self[value]();
				}
			})
		} else {
			$.each(self, function(key, value) {
				if (ko.isObservable(self[key]))
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
	this.set 		= function(data, errors) { 

		if (self._attributes) {
			$.each(self._attributes, function(key, value) {
				var p = self[value];
				d = data ? data[value] : null;
				if (ko.isObservable(p)) {
					p.set ? p.set(d) : p(d);
					p.errors ? p.errors([]) : null;
				}
			})
		} else {
			$.each(data, function(key, value) {
				var p = self[key];
				if (ko.isObservable(p)) {
					p.set ? p.set(value) : p(value);
					p.errors ? p.errors([]) : null;
				}
			})
		}
		errors = errors || [];
		$.each(errors, function(key, value) {
			var p = self[key];
			p.errors ? p.errors(value) : null;
		});
		return self;
	};

	this.modal 		= function(show) { 
		// console.log('modal options', show, self.options, self.get());
		if(self.options.modal)
			$('#' + self.options.modal).modal(show);	
	}

	this.update = function() {
		if (self.options.url)
			self.post(self.options.url, self.getModel(), function(data) {
				if (self.options.grid)
					baseViewModel.pjax(self.options.grid);
				if (data.error == false) {
					self.modal('hide');
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
			console.log('base post result', data);
			self.set(data.model, data.errors);
			if (callback)
				callback(data);
		}, 'json');
		console.log(p);		
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
}