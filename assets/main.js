console.log('first line');

require.config({
	// baseUrl: './asd',

	// packages: [
	// 	{
	// 		name:     'mapping',
	// 		main:     'knockout-mapping',
	// 		location: './',
	// 		deps:     ['knockout', 'asd']
	// 	},
	// ],

	paths: {
		'ko':      'knockout-asset/knockout-3.3.0',
		'mapping': 'knockout-asset/knockout-mapping'
	},

	// deps: ['knockout', 'mapping'],

	shim: {
		'ko': {
			exports: 'ko'
		},
		'mapping': {
			deps:    ['ko'],
			exports: 'mapping'
		},
	}

});



define('ko', ['ko', 'mapping'], function(ko, mapping) {
	console.log('knockout is here', ko, mapping)
	ko.mapping = require(['mapping']);
	return ko;
});



// requirejs(['ko', 'ko/knockout.mapping'], function(ko, mapping, ae) {
// 	console.log('!!!!!!!!!!!!!!!!!!!ae', ae);
// 	ko.mapping = mapping;
// 	return ko;
// });



console.log('knockout main loaded', requirejs.s.contexts._.config);