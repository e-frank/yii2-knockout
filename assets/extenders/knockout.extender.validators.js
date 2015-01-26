ko.extenders.validators = function (target, options) {
	// //	default options
	// options	=	$.extend({
	// 	abortValidation: false,
	// 	fn:              null,
	// }, options);

	target.isSetting = false;
	target.validated = ko.observable(false);
	target.errors    = ko.observableArray([]);
	target.hasError  = ko.computed(function() {
		return target.errors().length > 0;
	}, target);

	// call validation function
	target.validate = function() {
		var value    = target();
		var messages = [];

		if (options)
			options(value, messages);
		
		target.validated(true);
		target.errors(messages);
	}

	//	if assigning, skip validating and reset errors
	target.assign = function(v) {
		target.isSetting = true;
		target(v);
		target.validated(false);
		target.errors([]);
		target.isSetting = false;
	}

	// validate only if not setting object
	target.subscribe(function(v) {
		if (!target.isSetting) {
			target.validate();
		}
	});

	return target;
}
