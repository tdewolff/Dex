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

	// logging out
	function adminBarLogOut() {
		$('.dex-api').fadeOut(100).remove();
		$('.dex-admin-bar .logged-in').fadeOut(100, function () {
			$('.dex-admin-bar .logged-out').fadeIn(100);
		});

		if (typeof DexEdit !== 'undefined') {
			DexEdit.destroy();
		}
	}

	function adminBarForget() {
		$('.dex-admin-bar').slideUp(100, function () {
			$(this).remove();
		});

		$('body').animate({
			'padding-top': '0'
		}, 100);
	}

	function session() {
		api('/' + base_url + 'api/core/users/', {
			'action': 'timeleft'
		}, function(data) {
			if (data['timeleft'] > 0) {
				setTimeout(session, data['timeleft'] * 1000);
			} else {
				adminBarLogOut();
			}
		}, function () {
			adminBarLogOut();
		});
	}
	setTimeout(session, session_time * 1000);

	$('.dex-admin-bar .logged-in .current-user a').click(function (e) {
		e.preventDefault();
		var admin = ($(this).attr('data-admin') === '1' ? 1 : 0);
		api('/' + base_url + 'api/core/users/', {
			'action': 'logout',
			'admin': admin
		}, function(data) {
			if (admin === 0) {
				adminBarLogOut();
			} else {
				window.location = '/' + base_url + 'admin/';
			}
		});
	});

	$('.dex-admin-bar .logged-out .current-user a').click(function () {
		api('/' + base_url + 'api/core/users/', {
			'action': 'forget'
		}, function (data) {
			adminBarForget();
		});
	});
});

$('html').on('click', 'a[href="#"]', function (e) {
	e.preventDefault();
});

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
}

function preSwitchPopupFrame(popup) {
	var frames = popup.find('> div');
	frames.eq(1).css('display', 'inline-block');
	frames.eq(0).css('display', 'none');
	parent.$.fancybox.update();
}