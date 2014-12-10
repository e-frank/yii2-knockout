var mapping = mapping || {};

mapping.errors = {

	setErrors:  function(data) {

			for (key in data) {
			    if (data.hasOwnProperty(key)) {
			        console.log('key', key, self[key]);
			        if (self[key]) {
				        if (self[key].isObservableArray) {
				        	var a = ko.unwrap(self[key]);
				        	if (a.length == data[key].length) {
								for (i=0; i < a.length; i++) {
									console.log('next', data[key][i]);
									a[i].setErrors(data[key][i]);
								}
				        	} else {
				        		// wrong item count
								console.log('wrong item count');
				        	}
							console.log('length', self[key]().length)
				        } else {
					        console.log('item', key, data[key], self[key].errors);
							self[key].errors(data[key]);
				        }
			        }
			    }
			}			
		}

}