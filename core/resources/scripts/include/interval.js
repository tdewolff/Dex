var Interval = function(handler, time) {
    this.save = false;

    setInterval(function() {
        if (this.save == true) {
            this.save = false;
            handler();
        }
    }, time);

    return (function() {
        this.save = true;
    });
}