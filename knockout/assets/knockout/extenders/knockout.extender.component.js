ko.extenders.component = function (target, options) {

	//	default options
	options	=	$.extend({
		name:      'undefiendComponentName',
		viewmodel: false,
	}, options);
	
	target.get = function() {
		console.log('component getter ', options);
		var result = {};
		if (options.viewmodel) {
			var t = target();
			console.log('found vm ', t);
			if (t)
				result = t.get();
			console.log('component getter found 1 ', result);
		} else {
			var t = target();
			console.log('component getter found 2 ', t);
			if (t) {
				$.each(t, function(key, value) {
					result[key] = t[key];
				})
			}
		}
		return result;
	}

	target.set = function(value) {
		if (options.viewmodel) {
			target().set(value);
			// eval("var f = " + options.viewmodel);
			// target(f(value));
		} else {
			target(value);
		}
	}

	return target;
}
