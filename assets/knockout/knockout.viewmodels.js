

function viewmodel_datepicker(obj, format) {
	var self = this;
	format = format || 'YYYY-MM-DD';
	var m = new moment(obj, format);

	this.value   = ko.observable(obj || '');
	this.date    = ko.observable(m.format(format));
	this.hour    = ko.observable(m.format("hh"));
	this.minute  = ko.observable(m.format("mm"));
	this.display = ko.computed({
		read: function() {
			var v = this.value();
			if (v)
				return new moment(v).format('YYYY-MM-DD');
			else
				return '';
		},
		write: function(v) {
			this.value(v);
		},
		owner: this
	});

	this.current = function() {
		this.value(moment().format('YYYY-MM-DD'));
	}
	this.remove = function() {
		this.value('');
	}
}



function viewmodel_datetimepicker(obj, format) {
	var self = this;
	var options = { datetime: true }
	if (format)
		options.format = format;
	this.datetime = ko.observable(obj).extend({ x1datetime: options });
	this.hours   = options.hours || [
					{	key:	00,	value:	'00'},
					{	key:	01,	value:	'01'},
					{	key:	02,	value:	'02'},
					{	key:	03,	value:	'03'},
					{	key:	04,	value:	'04'},
					{	key:	05,	value:	'05'},
					{	key:	06,	value:	'06'},
					{	key:	07,	value:	'07'},
					{	key:	08,	value:	'08'},
					{	key:	09,	value:	'09'},
					{	key:	10,	value:	'10'},
					{	key:	11,	value:	'11'},
					{	key:	12,	value:	'12'},
					{	key:	13,	value:	'13'},
					{	key:	14,	value:	'14'},
					{	key:	15,	value:	'15'},
					{	key:	16,	value:	'16'},
					{	key:	17,	value:	'17'},
					{	key:	18,	value:	'18'},
					{	key:	19,	value:	'19'},
					{	key:	20,	value:	'20'},
					{	key:	21,	value:	'21'},
					{	key:	22,	value:	'22'},
					{	key:	23,	value:	'23'}
				];

	this.minutes = options.minutes || [
					{	key:	00,	value:	'00'},
					{	key:	15,	value:	'15'},
					{	key:	30,	value:	'30'},
					{	key:	45,	value:	'45'}
				];

	this.current = function() {
		var m = moment();
		m.minutes(m.minutes() - (m.minutes() % 15));
		m.seconds(0);
		self.datetime.moment(m);
		// self.datetime(m.format(x1.date_db));

		// self.datetime.date(m.format(options.format));
		// self.datetime.moment.hours(m.hours());
		// self.datetime.moment.minutes(m.minutes() - (m.minutes() % 15));
	}
	this.remove = function() {
		self.datetime('');
		// self.value('');
		// self.h('');
		// self.m('');
	}

}















