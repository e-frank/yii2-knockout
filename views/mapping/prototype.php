var <?= $namespace ?> = <?= $namespace ?> || { 
	prototype: function() {
		var self = this;

		self.init = function() {
			// add errors for obserable arrays
			for (key in self) {
				if (self.hasOwnProperty(key) && self[key] && self[key].isObservableArray) {
					self[key] = self[key].extend({arrayError: true});
				}
			}
		}


		// return error for property
		self.hasError = ko.pureComputed(function() {
			for (key in self) {
				if (self[key] && self[key].hasError && self[key].hasError() == true) {
					return true;
				}
			}
			return false;
		}, self);


		// set errors (from ajax response)
		self.setErrors =  function(data) {

			for (var key in data) {
			    if (data.hasOwnProperty(key)) {
			        if (self[key]) {
				        if (self[key].isObservableArray) {
				        	var a = ko.unwrap(self[key]);
				        	if (a.length == data[key].length) {
								for (i=0; i < a.length; i++) {
									if (a[i].setErrors)
										a[i].setErrors(data[key][i]);
								}
				        	} else {
				        		// wrong item count
				        	}
				        } else {
				        	if (self[key].errors)
								self[key].errors(data[key]);
				        }
			        }
			    }
			}
		}


		self.test = function() {
			console.log(self.__ko_mapping__)
		}

	},
};
