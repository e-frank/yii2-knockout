ko.extenders.component = function (target, options) {

	//	default options
	options	=	$.extend({
		name:      'undefiendComponentName',
		viewmodel: false,
		parent:    null,
		key:       {}
	}, options);
	
	// console.log('component', options);

	if (options.viewmodel && options.parent) {
		target.assign = function() {
			console.log('target assign');
			var t = target();
			if (t) {
				$.each(options.key, function(index, val) {
					if ($.inArray(val, t._attributes) == 0)
						t._attributes.push(val);
					t[val] = ko.computed({
						owner: target,
						read:  function() {
							// console.log('comp par', options.parent, index);
							return options.parent[index]();
						},
						write: function(v) {
							options.parent[index](v);
							// console.log('write comp', v);
						},
					});
					// console.log(index, val);
				});
				// console.log('new attr', t._attributes)
			}
		}
		target.subscribe(function(v) {
			// console.log('component changed');
			target.assign();
		})
		target.assign();
	}

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

	target.set = function(value, errors) {
		options.viewmodel ? target().set(value, errors) : target(value);
		// console.log('comp set', target());
	}

	return target;
}
