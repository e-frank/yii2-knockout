ko.components.register(<?= \yii\helpers\Json::encode('x1-' . $component) ?>, {
	viewModel: function(params) {
		// console.log(< ?= \yii\helpers\Json::encode($component) ?> + ' viewmodel', params.item(), params);

		params         = params || {};
		var self       = this;
		this.viewmodel = params.viewmodel;
		this.id        = params.id || null;
		this.name      = params.name || null;
		this.label     = params.label || null;
		this.hint      = params.hint || null;
		this.errors    = ko.observableArray(params.errors || (params.error ? [params.error] : []));
        this.maxlength = params.maxlength;

        // link observable value
        if (this.viewmodel && params.attribute && this.viewmodel[params.attribute] && ko.isObservable(this.viewmodel[params.attribute])) {
        	this.value = this.viewmodel[params.attribute];
        	this.value(params.value || null)
        } else {
	        this.value = ko.isObservable(params.value) ? params.value : ko.observable(params.value || null);
        }

        if (params.validators) {
	        this.value.subscribe(function(v) {
	        	var messages = [];
	        	params.validators(ko.unwrap(self.value), messages);
	        	self.errors(messages);
	        });
        }

        // this.value = this.value.extend({ decimal: { decimals: params.decimals || 2, decimalSeparator: params.decimalSeparator, thousandsSeparator: params.thousandsSeparator, percent: self.percent } });

	},
	template: { element: <?= \yii\helpers\Json::encode('x1-' . $component) ?> }
});
