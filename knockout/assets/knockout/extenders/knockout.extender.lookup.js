ko.extenders.lookup = function (target, options) {

	//	default options
	options	=	$.extend({
		url:     '',
		depends: {},
		cache:   true,
		method:  'get'
	}, options);
	
	target.query = ko.computed(function() {
		var result = {};

		$.each(options.depends, function(index, value) {
			var v = ko.unwrap(value);
			if (v != undefined)
				result[index] = v;
		});

		return result;
	}, target);


	if (options.cache) {
		target.cache = {};
		if ((target() || []).length > 0) {
			target.cache[(ko.toJSON(target.query()))] = target();
		}
	}

	target.lookup = function(q) {
		q = q || {};
		var hash = (ko.toJSON(q));
		if (options.cache && target.cache[hash])
			target(target.cache[hash]);
		else 
		{
			var fn = (options.method == 'get') ? $.get : $.post;
			var p = fn(options.url, q, function(data) {
				if (options.cache)
					target.cache[hash] = data;
				target(data);
			}, 'json');
		}
	}

	target.query.subscribe(function(v) {
		target.lookup(v);
	});


	return target;
}
