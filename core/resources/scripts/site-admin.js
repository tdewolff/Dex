function padZero(value) {
	return (value < 10 ? '0' + value : value);
}

$(function () {
	// init
	if ($('.logged-in').length) {
		if (typeof DexEdit !== 'undefined') {
			DexEdit.init();
			$('[contenteditable="true"]').each(function () {
				var bg = $(this).css('backgroundColor');
				$(this).animate({'backgroundColor': '#A9CC66'}, 50, function () {
					$(this).animate({'backgroundColor': bg}, 2000);
				});
			});

			if (typeof author !== 'undefined' && typeof last_save !== 'undefined') {
				apiStatusSuccess(author + ' (' + padZero(last_save.getDate()) + '-' + padZero(last_save.getMonth() + 1) + ' ' + last_save.getHours() + ':' + padZero(last_save.getMinutes()) + ')');
			}
		}
	}

	// saving
	new Save($('[data-dexeditable]')[0]);

	$('[data-dexeditable]').on('input', function (e) {
		apiStatusClear();
	});

	$('[data-dexeditable]').on('save', function (e) {
		var time = new Date();
		apiStatusSuccess(time.getHours() + ':' + padZero(time.getMinutes()));
	});
});

$('html').on('click', 'a[href="#"]', function (e) {
	e.preventDefault();
});

var currentPopupFrame = 0;
function switchPopupFrame(popup) {
	$('.fancybox-inner').animate({'scrollTop': 0});

	var frames = popup.find('> div');
	frames.eq(1).css('display', 'inline-block');
	popup.animate({'margin-left': '-' + frames.eq(0).width() + 'px'}, function () {
		popup.css({
			'margin-left': '0'
		});
		frames.eq(0).css('display', 'none');
		parent.$.fancybox.update();
	});
	currentPopupFrame++;
}

function switchBackPopupFrame(popup) {
	$('.fancybox-inner').animate({'scrollTop': 0});

	var frames = popup.find('> div');
	frames.eq(0).css('display', 'inline-block');
	popup.css('margin-left', '-' + frames.eq(1).width() + 'px').animate({'margin-left': '0px'}, function () {
		frames.eq(1).css('display', 'none');
		parent.$.fancybox.update();
	});
	currentPopupFrame--;
}

function preSwitchPopupFrame(popup) {
	var frames = popup.find('> div');
	frames.eq(1).css('display', 'inline-block');
	frames.eq(0).css('display', 'none');
	parent.$.fancybox.update();
}