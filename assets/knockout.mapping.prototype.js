ko.mapping.prototype = function(options) {
    // console.log('prototype', options);
    options        = options || {};
    options.arrays = options.arrays || [];
    options.fields = options.fields || [];
    var self       = this;

    // return error for property
    self.hasError = ko.pureComputed(function() {

        for (var key in self) {
            if (self.hasOwnProperty(key)) {
                if (self[key].hasError && self[key].hasError())
                    return true;
            }
        }
        return false;

        for (var i = 0; i < options.arrays.length; ++i) {
            if (self[options.arrays[i]].hasError() == true) {
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
                            throw 500;
                        }
                    } else {
                        if (self[key].errors)
                            self[key].errors(data[key]);
                    }
                }
            }
        }

        return self;
    }

    // call init to apply getErrors on arrays
    self.init = function(arrays) {
        for (var x in arrays) {
            if (x && self.hasOwnProperty(x) && self[x].isObservableArray) {
                self[x] = self[x].extend({ arrayError: true });
            }
        }
    }

}
