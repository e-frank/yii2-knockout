
//  apply on observablearrays for use with select inputs
//  manages items and selected item
ko.extenders.select = function(target, options) {

    if (!target.isObservableArray == true) {
        throw "target must be 'observableArray'";
    }

    target.item         = ko.observable();
    target.optionsText  = options.optionsText || null;
    target.optionsValue = options.optionsValue || null;

    target.value        = ko.computed({
        owner: target,
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

    // selects the item by id
    target.setItemById = function(id) {
        var o = ko.unwrap(target.optionsValue);
        var item = ko.utils.arrayFirst(target(), function(item) { return (o ? item[o] : item) == id; }) || null;
        target.item(item);
        return item;
    };

    return target;
}
