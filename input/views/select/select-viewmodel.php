ko.components.register(<?= \yii\helpers\Json::encode($component) ?>, {
	viewModel: function(params) {
		// console.log(<?= \yii\helpers\Json::encode($component) ?> + ' viewmodel', params.item(), params);
		params           = params || {};

		var self          = this;
		this.label        = params.label || 'undefined';
		this.id           = params.id || null;
		this.name         = params.name || null;

		this.items          = params.items;
		this.item           = params.item;
		this.optionsText    = params.optionsText || null;
		this.optionsValue   = params.optionsValue || null;
		this.optionsCaption = params.optionsCaption || null;

		this.hint         = params.hint || null;
		this.errors       = ko.observableArray([]);

		this.value 		  = params.value;


		if (!params.value)
        this.value        = ko.computed({
            owner: self,
            read: function() { 
                var o = ko.unwrap(this.optionsValue);
                return this.item() ? ((o == null) ? this.item() : (this.item())[o]) : null;
            },
            write: function(v) {

                // find item by key
                var o        = ko.unwrap(this.optionsValue);
                var selected = (o != null) ? 
                    ko.utils.arrayFirst(ko.unwrap(this()), function(item){ return (ko.unwrap(item))[o] == v; })
                    : 
                    ko.utils.arrayFirst(ko.unwrap(this()), function(item){ return (ko.unwrap(item)) == v; });

                // assign only if different; EXACT order of properties!
                if (this.item() !== selected) {
                    this.item(selected);
                // if (JSON.stringify(this.item()) !== JSON.stringify(selected)) {
                //     this.item(selected);
                }
            }

        });


	},
	template: { element: <?= \yii\helpers\Json::encode($component) ?> }
});
