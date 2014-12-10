ko.extenders.list = function (target, options) {

	//	default options
	options	=	$.extend({
		name:      'undefiendListName',
		viewmodel: false,
		key:       {},
		id:        ['id'],
		update: 	true, 
	}, options);
	

	console.log('list', options);

	if (options.viewmodel && options.parent) {
		target.assign = function() {
			var items = target() || [];
			if (items.length > 0 && options.key != {}) {
				$.each(items, function(i, t) {
					$.each(options.key, function(index, val) {
						if ($.inArray(val, t._attributes) < 0)
							t._attributes.push(val);
						t[val] = ko.computed({
							owner: target,
							read:  function() { return options.parent[index](); },
							write: function(v) {},
						});
					});
				});
			}
		}
		target.subscribe(function(v) {
			target.assign();
		})
		target.assign();
	}

	target.get = function() {
		var result = {};
		if (options.viewmodel) {
			var t = target() || [];
			return ko.utils.arrayMap(t, function(item) { return item.get(); })
		} else {
			return (target() || []);
		}
	}

	target.set = function(value) {
		value = value || [];
		if (options.viewmodel) {
			// eval("var f=" + options.viewmodel);
			var f = options.viewmodel;
			target(ko.utils.arrayMap(value, function(item) { return new f(item); }));
		} else {
			target(value);
		}
	}

	target.add = function(value) {
		value = value || {};
		var v = value.get ? value.get() : value;
		options.viewmodel ? target.push(new options.viewmodel(v)) : target.push(v);
	}

	target.edit = function($data) {
		if (options.viewmodel) {
			target.editItem.set($data ? $data.get() : options.defaults || {});
		} else {
			target.editItem($data ? $data : options.defaults);
		}
	}

	target.update = function() {
		var ei = target.editItem();
		if (options.update) {
			ei.update(function(data) { return options.redirect || false; });
		}
	}

	target.validate = function() {
		var items = target() || [];
		console.log('list val');
		$.each(items, function(index, value) {
			console.log(value);
			if (value && value.validate)
				value.validate();
		})
	}

	target.getDefaults = function() {
		return options.viewmodel ? new options.viewmodel(options.defaults || {}) : options.defaults || {};
	}
	target.editItem = ko.observable(target.getDefaults());
	target.addItem  = ko.observable(target.getDefaults());

	return target;
}
