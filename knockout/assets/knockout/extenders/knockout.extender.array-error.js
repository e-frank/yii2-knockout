ko.extenders.arrayError = function (target, options) {
	target.hasError  = ko.pureComputed(function() {
		var a = ko.unwrap(target);
		for (i=0; i<a.length; i++)
			if (a[i] && a[i].hasError && a[i].hasError() == true)
				return true;
	}, target);

	return target;
}
