$(function () {
	// logging out
	function adminBarLogOut() {
		$('.dex-api:not(.load-status)').fadeOut(100).remove();
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
		}, function() {
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
		}, function () {
			adminBarForget();
		});
	});
});