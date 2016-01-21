ko.extenders.bool = function (target, options) {
	//	default options
	options	=	$.extend({
		trueValue:          1,
		falseValue:         0,
	}, options);
	

	target.bool = ko.computed({
		owner: this,
		read: function() {
			var t = target();
			return t == options.trueValue;
		},
		write: function(v) {
			target(v ? options.trueValue : options.falseValue);
		}
	})

	return target;
}

