$(function() {
	// select first form element
	$('form:first *:input[type!=hidden]:first').focus();

	$('textarea.bottom').each(function () {
		$(this).scrollTop(this.scrollHeight - $(this).height());
	});

	// for images like in assets
    if ($.fn.fancybox) {
        $('.fancybox').fancybox({
            closeClick: true,
            closeBtn: false,

            openEffect : 'elastic',
            openSpeed  : 150,

            closeEffect : 'elastic',
            closeSpeed  : 150,

            helpers:  {
                overlay: {
                    locked: false
                }
            }
        });
    }

	applyTooltips();
});

$('html').on('click', 'a[href="#"]', function (e) {
	e.preventDefault();
});

// dropdown
$('html').click(function() {
	$('.dropdown-menu').fadeOut('fast');
});

$('html').on('click', '.dropdown-menu', function(e) {
	e.stopPropagation();
});

$('html').on('click', '.dropdown-toggle', function(e) {
	e.stopPropagation();

	var dropdown = $(this).parent();
	$('.dropdown-menu').not($('.dropdown-menu', dropdown)).hide(150);
	$('.dropdown-menu', dropdown).toggle();
	if ($('.dropdown-menu', dropdown).is(':visible')) {
		$('.dropdown-menu', dropdown).css({
			overflow: 'visible'
		});
	}
});

// halt
$('html').on('click', 'a.halt', function(e) {
	e.preventDefault();
	var display = $(this).css('display');
	$(this).hide();
	$(this).parent().find('a.sure').show().css('display', display);
});

$('html').on('mouseleave', 'a.sure', function(e) {
	e.preventDefault();
	$(this).fadeOut('fast', function() {
		$(this).parent().find('a.halt').fadeIn('fast');
	});
});

function applyTooltips() {
	$('[data-tooltip]').tooltip({
		position: {
			my: 'center top',
			at: 'center bottom+5',
			collision: 'fit',
			using: function(position, feedback) {
				$(this).css(position);
				$('<div>').addClass('ui-tooltip-arrow').appendTo(this);
			}
		},
		items: "[data-tooltip]",
		content: function() {
			return $(this).attr('data-tooltip');
		},
		show: {
			duration: 100
		},
		hide: {
			duration: 100
		}
	});
}