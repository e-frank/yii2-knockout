
ko.observableArray.fn.toString = function() {
	return "KO.ARRAY: " + ko.toJSON(this(), null, 2);
};

ko.observableArray.fn.isObservableArray = true;

ko.observable.fn.toString = function() {
	return "KO.OBSERVABLE: " + ko.toJSON(this(), null, 2);
};

ko.computed.fn.toString = function() {
	return "KO.COMPUTED: " + ko.toJSON(this(), null, 2);
};

ko.observable.fn.withPausing = function() {
    this.notifySubscribers = function() {
       if (!this.pauseNotifications) {
          ko.subscribable.fn.notifySubscribers.apply(this, arguments);
       }
    };

    this.sneakyUpdate = function(newValue) {
        this.pauseNotifications = true;
        this(newValue);
        this.pauseNotifications = false;
    };

    return this;
};