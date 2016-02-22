ko.extenders.focus = function (target, options) {
	if (ko.isObservable(options)) {
		target.focus = options;
	} else {
		target.focus = ko.observable(options || false);
	}
	return target;
}

