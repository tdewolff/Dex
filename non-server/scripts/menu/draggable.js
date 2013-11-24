var Draggable = function(ul) {
    var self = this;

    this.ul = $(ul);
    this.needsSave = false;

    this.draggee = false;
    this.draggee_x_start = 0;
    this.draggee_y_offset = 0;
    this.placeholder = false;

    this.drag = function(e) {
        if (self.draggee !== false) {
            var elements = self.ul.find('li:not(:first)').filter(function() {
                return $(this).css('position') == 'static';
            });

            // restrain Y movement within the list
            var top = e.pageY - self.draggee_y_offset;
            var min_top = elements.first().offset().top + 1;
            var max_top = elements.last().offset().top + 1;
            if (top < min_top)
                top = min_top;
            else if (top > max_top)
                top = max_top;

            // placement
            var places = elements.filter(function() {
                 return !$(this).hasClass('placeholder');
            });

            if (places.length) {
                var y = top + 0.5 * places.first().outerHeight();
                places.each(function() {
                    var place = $(this);
                    if (y >= place.offset().top && y < place.offset().top + place.outerHeight()) {
                        if (top < self.placeholder.offset().top)
                            self.placeholder.insertBefore(place);
                        else
                            self.placeholder.insertAfter(place);
                        return false;
                    }
                });
            }

            // X movement determines level of the item
            var level = 0;
            var previous = false;
            elements.each(function() {
                var element = $(this);
                if (element.hasClass('placeholder'))
                    return false;
                previous = element;
            });

            if (previous !== false && typeof previous.attr('data-level') != 'undefined')
            {
                level = Math.floor((e.pageX - self.draggee_x_start + 20.0) / 40.0);
                var previous_level = previous.attr('data-level');
                if (level >= previous_level + 2)
                    level = previous_level + 1;
            }

            self.draggee.find('.icon-long-arrow-right').hide();
            for (var i = 0; i < level; i++)
                self.draggee.find('.icon-long-arrow-right').eq(i).show();

            // apply CSS
            self.draggee.css('top', top + 'px');
            self.draggee.attr('data-level', level);
        }
        else
            $(document).unbind('mousemove', self.drag);
    };

    this.ul.on('mousedown', '.icon-eye-open', function(e) {
        e.preventDefault();

        var li = $(this).closest('li');
        li.toggleClass('unused');
        li.find('input').toggleClass('unused');

        var level = li.attr('data-level');
        var elements = li.nextAll('li').filter(function() {
            return !$(this).hasClass('placeholder');
        }).each(function() {
            var element = $(this);
            if (element.attr('data-level') <= level)
                return false;

            if (element.hasClass('unused') != li.hasClass('unused'))
            {
                element.toggleClass('unused');
                element.find('input').toggleClass('unused');
            }
        });
        self.needsSave = true;
    });

    this.ul.on('mousedown', '.icon-reorder', function(e) {
        e.preventDefault();

        if (self.draggee === false && e.which == 1) {
            self.draggee = $(this).closest('li');

            self.draggee_x_start = e.pageX - $('.icon-long-arrow-right:visible', self.draggee).length * 40.0;
            self.draggee_y_offset = e.pageY - (self.draggee.offset().top + 1);

            self.placeholder = $('<li>').addClass('placeholder').insertAfter(self.draggee);
            self.draggee.addClass('draggee').css({
                'top': (self.draggee.offset().top + 1) + 'px',
                'left': self.draggee.offset().left + 'px'
            });

            $(document).bind('mousemove', self.drag);
        }
    });

    $('html').mouseup(function(e) {
        if (self.draggee !== false) {
            e.preventDefault();
            $(document).unbind('mousemove', self.drag);

            self.draggee.insertAfter(self.placeholder).animate({
                'top': (self.placeholder.offset().top + 1) + 'px'
            }, 'fast', function() {
                self.placeholder.remove();
                $(this).removeClass('draggee').css({
                    'top': '',
                    'left': ''
                });
            });

            // toggle unused class
            if (self.draggee.prev('li').attr('data-level') < self.draggee.attr('data-level') && self.draggee.prev('li').hasClass('unused') && !self.draggee.hasClass('unused'))
            {
                self.draggee.toggleClass('unused');
                self.draggee.find('input').toggleClass('unused');
            }
            self.draggee = false;
            self.needsSave = true;
        }
    });

    this.ul.on('keyup', 'input', function() {
        self.needsSave = true;
    });

    this.intervalSave = function() {
        if (self.needsSave == true) {
            self.needsSave = false;
            self.save();
        }
    };
    setInterval(self.intervalSave, 1000);

    this.save = function() {
        $('.response > .success, .reponse > .error').hide();
        $('.response > .loading').fadeIn('fast').css('display', 'inline-block');

        var i = 0;
        var data = {};
        var elements = $('li:not(:first)', self.ul).filter(function() {
            return !$(this).hasClass('placeholder');
        }).each(function() {
            var element = $(this);
            data[i] = {
                link_id: element.attr('data-link-id'),
                level: element.attr('data-level'),
                name: element.find('input').val(),
                enabled: (element.hasClass('unused') ? '0' : '1')
            };
            i++;
        });

        api('/' + base_url + 'api/module/menu/index.php', {
            action: 'modify_menu',
            menu: data
        }, function() {
            $('.response > .loading').hide();
            $('.response > .success').fadeIn('fast');
        }, function() {
            $('.response > .loading').hide();
            $('.response > .error').fadeIn('fast');
        });
    };
}

$('ul.draggable').each(function(i, ul) {
    new Draggable(ul);
});