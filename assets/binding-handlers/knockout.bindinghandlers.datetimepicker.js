ko.bindingHandlers.datetimepicker = {
    init: function(element, valueAccessor, allBindings) {
        var value = valueAccessor();

        var picker = $(element).bootstrapMaterialDatePicker(value).data("plugin_bootstrapMaterialDatePicker");

        var v = allBindings.get('value');
        v.open = function(e) {
            picker._onFocus();
        }
    },
};