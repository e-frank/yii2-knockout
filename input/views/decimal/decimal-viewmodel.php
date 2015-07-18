ko.components.register(<?= \yii\helpers\Json::encode($component) ?>, {
	viewModel: function(params) {
		// console.log(< ?= \yii\helpers\Json::encode($component) ?> + ' viewmodel', params.item(), params);

		params      = params || {};
		var self    = this;
		this.id     = params.id || null;
		this.name   = params.name || null;
		this.hint   = params.hint || null;
		this.errors = ko.observableArray(params.errors || []);
        this.maxlength = params.maxlength || 9;
        this.percent   = params.percent || false;

        if (this.percent == true && !params.decimals) {
            params.decimals = 0;
        }

        this.value = ko.isObservable(params.value) ? params.value : ko.observable(params.value || null);
        this.value = this.value.extend({ decimal: { decimals: params.decimals, decimalSeparator: params.decimalSeparator, thousandsSeparator: params.thousandsSeparator, percent: self.percent } });

	},
	template: { element: <?= \yii\helpers\Json::encode($component) ?> }
});
