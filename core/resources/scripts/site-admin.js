function padZero(value) {
	return (value < 10 ? '0' + value : value);
}

$(function () {
	if ($('.logged-in').length) {
		if (typeof DexEdit.init !== 'undefined') {
			DexEdit.init();
		}
	}

	var saveTimeout = null;
	$('[data-dexeditable]').on('input', function (e) {
		apiStatusClear();
		clearTimeout(saveTimeout);
		saveTimeout = setTimeout(save, 1000);
	});

	function save() {
		$.event.trigger({
			type: 'save'
		});

		var time = new Date();
		apiStatusSuccess(time.getHours() + ':' + padZero(time.getMinutes()));
	}

	$(document).on('keydown', function (e) {
	    if (e.ctrlKey && e.which === 83) {
	        e.preventDefault();
	        save();
	        return false;
	    }
	});


	function adminBarLogOut() {
		$('.dex.api').fadeOut().remove();
		$('.dex.admin-bar .logged-in').fadeOut(function () {
			$('.dex.admin-bar .logged-out').fadeIn();
		});

		if (typeof DexEdit.destroy !== 'undefined') {
			DexEdit.destroy();
		}
	}

	function adminBarForget() {
		$('.dex.admin-bar').slideUp(function () {
			$(this).remove();
		});

		$('body').animate({
			'padding-top': '0'
		});
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

	$('.dex.admin-bar .logged-in .current-user a').click(function (e) {
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

	$('.dex.admin-bar .logged-out .current-user a').click(function () {
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