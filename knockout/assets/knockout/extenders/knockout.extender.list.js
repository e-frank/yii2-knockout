ko.extenders.list = function (target, options) {

	//	default options
	options	=	$.extend({
		name:      'undefiendListName',
		viewmodel: false,
	}, options);
	
	target.get = function() {
		var result = {};
		if (options.viewmodel) {
			var t = target() || [];
			return ko.utils.arrayMap(t, function(item) { return item.getModel(); })
		} else {
			return (target() || []);
		}
	}

	target.set = function(value) {
		value = value || [];
		if (options.viewmodel) {
			eval("var f=" + options.viewmodel);
			target(ko.utils.arrayMap(value, function(item) { return new f(item); }));
		} else {
			target(value);
		}
	}

	return target;
}
