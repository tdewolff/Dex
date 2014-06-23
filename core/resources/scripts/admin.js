$('html').on('click', 'a[href="#"]', function (e) {
	e.preventDefault();
});

// responsive
$('html').on('click', '#links > a.admin-links-expand', function () {
	$('#links li').removeClass('mobile-hide').css('display', 'block');
	$(this).removeClass('admin-links-expand').addClass('admin-links-collapse').find('i').removeClass('fa-caret-left').addClass('fa-caret-down');
	positionMenu();

	$('html, body').animate({
		'scrollTop': $('#links').offset().top - $('.dex-admin-bar').outerHeight(true)
	}, 0);
});

$('html').on('click', '#links > a.admin-links-collapse', function () {
	$('#links li:not(.selected)').addClass('mobile-hide').css('display', '');
	$(this).removeClass('admin-links-collapse').addClass('admin-links-expand').find('i').removeClass('fa-caret-down').addClass('fa-caret-left');
	positionMenu();
});

function positionMenu() {
	if (window.screen.width <= 1205 || document.documentElement.clientWidth <= 767) {
		var fixed = (scrollY >= $('h1').outerHeight(true) && $('#links > a').hasClass('admin-links-expand'));
		var position = (fixed ? 'fixed' : '');
		if ($('#links').css('position') !== position) {
			$('#links').css('position', position);
			$('#links').css('marginTop', (fixed ? '-' + $('h1').outerHeight(true) + 'px' : ''));
			$('#main').css('marginTop', (fixed ? $('#links li.selected').height() + 'px' : ''));
		}
	}
}

$(window).scroll(function () {
	positionMenu();
});

var mobile = false;
$(window).resize(function () {
	positionMenu();

	if (mobile === (window.screen.width <= 1205 || document.documentElement.clientWidth <= 767)) {
		return;
	}
	mobile = !mobile;

	if (!mobile) {
		$('#main').css('marginTop', '');
		$('#links > a.admin-links-collapse').click();
	}
});

// dropdown
$('html').click(function () {
	$('.dropdown-menu').fadeOut(100);
});

$('html').on('click', '.dropdown-menu', function (e) {
	e.stopPropagation();
});

$('html').on('click', '.dropdown-toggle', function (e) {
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
$('html').on('click', 'a.halt', function (e) {
	e.preventDefault();
	var display = $(this).css('display');
	$(this).hide();
	$(this).parent().find('a.sure').show().css('display', display);
});

$('html').on('mouseleave', 'a.sure', function (e) {
	e.preventDefault();
	$(this).fadeOut(100, function() {
		$(this).parent().find('a.halt').fadeIn(100);
	});
});

// inline form
function inlineFormError(element, error) {
	element.addClass('invalid');

	var inlineError = element.find('.inline-error-' + element.attr('data-error-position'));
	if (!inlineError.length) {
		inlineError = $('<div class="inline-error-' + element.attr('data-error-position') + '">\
			<div class="box">\
				<div class="arrow"></div>\
				<div class="arrow-border"></div>\
				<p>\
					<i class="fa fa-exclamation-circle"></i>&ensp;<span></span>\
				</p>\
			</div>\
		</div>').appendTo(element.parent());
	}

	if (inlineError.find('span').text() != error) {
		inlineError.hide();
		inlineError.find('span').text(error);
	}
	inlineError.fadeIn();
}

function hideInlineFormErrors(element) {
	element.find('.inline-error-left, .inline-error-right, .inline-error-above, .inline-error-below').hide().parent().find('.invalid').removeClass('invalid');
}

$('html').on('click', '.inline-error-left, .inline-error-right, .inline-error-above, .inline-error-below', function () {
	$(this).hide();
});

function titleToUrl(title) {
	var url = title.toLowerCase().replace(/[^a-z0-9\-_\s]+/g, '').trim().replace(/\s/g, '-');
	if (url.length) {
		url += '/';
	}
	return url;
}