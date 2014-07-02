ko.extenders.component = function (target, options) {

	//	default options
	options	=	$.extend({
		name:      'undefiendComponentName',
		viewmodel: false,
	}, options);
	
	target.get = function() {
		var result = {};
		if (options.viewmodel) {
			var t = target();
			if (t)
				result = t.get();
		} else {
			var t = target();
			if (t) {
				$.each(t, function(key, value) {
					result[key] = t[key];
				})
			}
		}
		return result;
	}

	target.set = function(value) {
		options.viewmodel ? target().set(value) : target(value);
	}

	return target;
}
