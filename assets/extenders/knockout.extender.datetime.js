ko.extenders.hours = [
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

ko.extenders.minutes = [
					{	key:	00,	value:	'00'},
					{	key:	15,	value:	'15'},
					{	key:	30,	value:	'30'},
					{	key:	45,	value:	'45'}
				];

ko.extenders.date_db     = 'YYYY-MM-DD';

ko.extenders.datetime = function (target, options) {

	//	default options
	options	=	$.extend({
		utc:      false,
		date:     true,
		datetime: false,
		year:     false,
		month:    false,
		day:      false,
		hour:     false,
		hours:    ko.extenders.hours,
		minute:   false,
		time:     false,
		round:    true,
		format:   x1.config.date,
		timespan: 15,
		language: x1.config.language,
		null:     true
	}, options);


	if ((options.minute || options.time) && options.minutes == undefined)	 {
		options.minutes = [];
		for (var i = 0; i < 60; i=i+options.timespan) {
			options.minutes.push({ key: i, value: i.toString().lpad('0', 2) });
		};
	}


	target.minutes  = options.minutes;
	target.hours    = options.hours;



	if (options.time == true) {
		target.times = [];
		for (var h = 0; h < target.hours.length;  h++) {
			for (var m = 0; m < target.minutes.length;  m++) {
				target.times.push(target.hours[h].value + ':' + target.minutes[m].value)
			}
		}
	}

	//	moment display format
	var date_db     = ko.extenders.date_db + ((options.time || options.hour || options.minute) ? ' HH:mm:ss' : '');
	

	// get moment and choose UTC or LOCAL
	function getMoment(t) {
		var result = null;
		if (t !== undefined && t !== null && t !== '') {
			var m = ((options.utc == true) ? moment.utc(t, date_db).local() : moment(t, date_db));
			m.locale(options.language);

			//	round to quarters
			if (options.round && m && m.isValid()) {
				m.minute(m.minute() - m.minute() % options.timespan);
				m.second(0);
			}
			
			result = m;
		}
		return result;
	}
	

	target.moment = ko.observable(getMoment(target()));
	target.moment.subscribe(function(v) {
		if (v == null) {
			target(v);
		} else {
			target(target.moment().format(date_db));
		}
	});
	target.subscribe(function(v) {
		target.moment(getMoment(v));
	});


	target.date		=	ko.computed({
		owner	:	target,
		read	:	function() {
			var m = target.moment();
			if (m != null)
				return m.format(options.format);
			else
				return '';
		},
		write	:	function(v) {
			if (v !== undefined && v !== null && v !== '') {
				var i	=	parseInt(v);

				//	smart date
				if (i == v)
					var m	=	moment().add(v, 'days');
				else
					var m	=	moment(v, options.format);

				//	round to quarters
				if (options.round && m && m.isValid()) {
					m.minute(m.minute() - m.minute() % options.timespan);
					m.second(0);
				}
				
				n = target.moment();
				if (n != null) {
					n.year(m.year());
					n.month(m.month());
					n.date(m.date());
				} else {
					n = m;
				}

				target(n.format(date_db));
			}
			else {
				target(null);
			}
		}
	});


	if (options.time || options.datetime)
		target.time	=	ko.computed({
			owner	:	target,
			read	:	function() {
				var m = target.moment();
				if (m !== null)
					return m.hours() + ':' + m.minutes();
				else
					return '';
			},
			write	:	function(v) {
				if (v != undefined && v !== null && v !== '') {
					var m = target.moment();
					if (m == null)
					{
						m = moment();
						m.seconds(0);
					}
					var split = v.split(':');
					m.hours(split[0]);
					m.minutes(split[1]);
					//	round to quarters
					if (options.round) {
						m.minute(m.minute() - m.minute() % options.timespan);
					}
					target(m.format(date_db));
				} else {
					target(null);
				}
			}
		});

	if (options.hour)
		target.hour		=	ko.computed({
			owner	:	target,
			read	:	function() {
				var m = target.moment();
				if (m !== null)
					return m.hours();
				else
					return '';
			},
			write	:	function(v) {
				if (v !== undefined && v !== null && v !== '') {
					var m = target.moment();
					if (m == null)
					{
						m = moment();
						m.minutes(0);
						m.seconds(0);
					}
					m.hours(v);
					target(m.format(date_db));
				}
				else {
					target(null);
				}
			}
		});

	if (options.minute)
		target.minute	=	ko.computed({
			owner	:	target,
			read	:	function() {
				var m = target.moment();
				if (m !== null)
					return m.minutes();
				else
					return '';
			},
			write	:	function(v) {
				if (v !== undefined && v !== null && v !== '') {
					var m = target.moment();
					if (m == null)
					{
						m = moment();
						m.seconds(0);
					}
					m.minutes(v);
					//	round to quarters
					if (options.round) {
						m.minute(m.minute() - m.minute() % options.timespan);
					}

					target(m.format(date_db));
				} else {
					target(null);
				}
			}
		});


	
	target.year	=	ko.computed({
		owner	:	target,
		read	:	function() {
			var m = target.moment();
			if (m != null)
				return m.year();
			else
				return '';
		},
		write	:	function(v) {
			if (v != undefined && v !== null && v !== '') {
				var m = target.moment();
				if (m == null)
				{
					m = moment();
					m.seconds(0);
				}
				m.year(v);
				target(m.format(date_db));
			} else
			target(null);
		}
	});

	target.month	=	ko.computed({
		owner	:	target,
		read	:	function() {
			var m = target.moment();
			if (m != null)
				return m.month() + 1;
			else
				return '';
		},
		write	:	function(v) {
			if (v != undefined && v !== null && v !== '') {
				var m = target.moment();
				if (m == null)
				{
					m = moment();
					m.seconds(0);
				}
				m.month(v - 1);
				target(m.format(date_db));
			} else
			target(null);
		}
	});
	

	target.current = function() {
		var m = getMoment(moment().format(date_db));
		target(m.format(date_db));
		console.log('current', m.format(date_db), m, target());
	}
	
	target.clear = function() {
		target(null);
	}
	
	target.display = target.date;
	return target;
}
