ko.components.register(<?= \yii\helpers\Json::encode($component) ?>, {
        viewModel: function(params) {
                params         = params || {};
                this.id        = params.id || null;
                this.maxlength = params.maxlength;

                this.updateHidden = function() { $('#'+ this.id).trigger('change'); }

                var extend = {
                        decimal: {
                                decimals:           params.decimals || 2, 
                                percent:            params.percent || false,
                                thousandsSeparator: params.thousandsSeparator, 
                                decimalSeparator:   params.decimalSeparator
                        }
                };

                // connect the value
                this.value = ko.isObservable(params.value) ? params.value.extend(extend) : ko.observable(params.value).extend(extend);

                console.log(params);
        },
        template: { element: <?= \yii\helpers\Json::encode($component) ?> }
});
