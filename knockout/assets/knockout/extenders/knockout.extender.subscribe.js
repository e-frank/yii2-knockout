ko.extenders.subscribe = function (target, options) {

	//	default options
	if (typeof options == 'function') {
		options	=	$.extend({
			beforeChange: null,
			afterChange:  options,
		}, options);
	} else {
		options	=	$.extend({
			beforeChange: null,
			afterChange:  null,
		}, options);
	}
	

	if (options.beforeChange != null) {
		target.subscribe(options.beforeChange, null, 'beforeChange');
 	}

	if (options.afterChange != null) {
		target.subscribe(options.afterChange);
	}

	return target;
}
