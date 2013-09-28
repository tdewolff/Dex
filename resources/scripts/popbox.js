(function () {
    $.fn.popbox = function (options) {
        var settings = $.extend({
            selector      : this.selector,
            open          : '.open',
            box           : '.box',
            arrow         : '.arrow',
            arrow_border  : '.arrow-border',
            close         : '.close'
        }, options);

        var methods = {
            open: function (event) {
                event.preventDefault();

                var box = $(this).parent().find(settings['box']);
                if (box.css('display') == 'block') {
                    methods.close();
                } else {
                    box.css({'display': 'block'});
                }
            },

            close: function () {
                $(settings['box']).fadeOut("fast");
            }
        };

        $(document).bind('keyup', function (event) {
            if(event.keyCode == 27) {
                methods.close();
            }
        });

        return this.each(function () {
            //$(this).css({'width': $(settings['box']).width()}); // Width needs to be set otherwise popbox will not move when window resized.
            var pop = $(this),
                box = $(settings['box'], pop),
                open = $(settings['open'], this);

            $(settings['arrow'], box).css({'left': box.width()/2 - 13});
            $(settings['arrow_border'], box).css({'left': box.width()/2 - 13});
            box.css({'top': pop.height() + 10, 'left': ((pop.width()/2) -box.width()/2 )});

            open.unbind('click').bind('click', methods.open);
            $(settings['close'], box).unbind('click').bind('click', function (event) {
                event.preventDefault();
                methods.close();
            });
        });
    }
}).call(this);
