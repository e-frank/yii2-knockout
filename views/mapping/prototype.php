var <?= $namespace ?> = <?= $namespace ?> || { 
	prototype: function() {
		var self = this;

		// add errors for obserable arrays
		for (key in this) {
			if (this[key] && this[key].isObservableArray) {
				this[key] = this[key].extend({arrayError: true});
			}
		}


		// return error for property
		this.hasError = ko.pureComputed(function() {
			for (key in this) {
				if (this[key] && this[key].hasError && this[key].hasError() == true) {
					return true;
				}
			}
			return false;
		}, self);


		// set errors (from ajax response)
		this.setErrors =  function(data) {
			for (key in data) {
			    if (data.hasOwnProperty(key)) {
			        if (this[key]) {
				        if (this[key].isObservableArray) {
				        	var a = ko.unwrap(this[key]);
				        	if (a.length == data[key].length) {
								for (i=0; i < a.length; i++) {
									a[i].setErrors(data[key][i]);
								}
				        	} else {
				        		// wrong item count
				        	}
				        } else {
							this[key].errors(data[key]);
				        }
			        }
			    }
			}
		}


	},
};
