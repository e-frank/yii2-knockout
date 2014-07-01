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
				if (ko.isObservable(self[value]))
					result[value] = self[value]();
			})
		} else {
			$.each(self, function(key, value) {
				if (ko.isObservable(self[key]))
					result[key] = self[key]();
			})
		}
		return result;
	};
	this.set 		= function(data, errors) { 

		$.each(data, function(key, value) {
			var p = self[key];
			if (ko.isObservable(p)) {
				p(value);
				// console.log('set2 ', key, value, p());
				if (p.errors)
					p.errors([]);
			}
		})
		errors = errors || [];
		$.each(errors, function(key, value) {
			var p = self[key];
			if (p.errors)
				p.errors(value);
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
			self.post(self.options.url, self.get(), function(data) {
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