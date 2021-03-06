$('html').on('click', 'a[href="#"]', function (e) {
	e.preventDefault();
});

// responsive
$('html').on('click', '#links a.admin-links-expand', function () {
	var height = 0;
	$("#links li").each(function() {
	   height += $(this).outerHeight();
	});

	$('#links li').removeClass('mobile-hide').slideDown(100);
	$(this).removeClass('admin-links-expand').addClass('admin-links-collapse').find('i').removeClass('fa-caret-left').addClass('fa-caret-down');

	$('#main').animate({
		'marginTop': '+' + height
	}, 100);
});

$('html').on('click', '#links a.admin-links-collapse', function () {
	$('#links li:not(.selected)').slideUp(100, function () {
		$(this).addClass('mobile-hide').attr('style', '');
	});
	$(this).removeClass('admin-links-collapse').addClass('admin-links-expand').find('i').removeClass('fa-caret-down').addClass('fa-caret-left');

	$('#main').animate({
		'marginTop': '+' + $('#links li.selected').height() + 'px'
	}, 100);
});

function positionMenu() {
	var h1 = $('h1');
	var scrollY = window.scrollY || document.documentElement.scrollTop;
	$('#links').css('top', Math.max(33, h1.position().top + h1.outerHeight(true) - scrollY) + 'px');
}

$(window).scroll(function () {
	if ($('#links').css('position') === 'fixed') {
		positionMenu();
	}
});

$(window).resize(function () {
	if ($('#links').css('position') !== 'fixed') {
		if ($('#links').css('top') !== '') {
			$('#links').css('top', '');
			$('#main').css('marginTop', '');
			$('#links li:not(.selected)').addClass('mobile-hide').attr('style', '');
			$('#links a.admin-links-collapse').removeClass('admin-links-collapse').addClass('admin-links-expand').find('i').removeClass('fa-caret-down').addClass('fa-caret-left');
		}
	} else {
		positionMenu();
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

function escapeHtml(s) {
	return s
		 .replace(/&/g, "&amp;")
		 .replace(/</g, "&lt;")
		 .replace(/>/g, "&gt;")
		 .replace(/"/g, "&quot;")
		 .replace(/'/g, "&#039;");
 }