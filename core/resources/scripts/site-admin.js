$(function () {
	var saveTimeout = null;
	$('[data-dexeditable]').on('input', function (e) {
		apiStatusClear();
		clearTimeout(saveTimeout);
		saveTimeout = setTimeout(save, 2000);
	});

	function save() {
		$.event.trigger({
			type: 'save'
		});
		apiStatusSuccess('Saved');
	}


	function adminBarLogOut() {
		$('.dex.api').fadeOut().remove();
		$('.dex.admin-bar .logged-in').fadeOut(function () {
			$('.dex.admin-bar .logged-out').fadeIn();
		});
		$('[data-dexeditable]').attr('contenteditable', 'false');
	}

	function adminBarForget() {
		$('.dex.admin-bar').slideUp(function () {
			$(this).remove();
		});

		$('body').animate({
			'padding-top': '0'
		});
	}

	setTimeout(function () {
		adminBarLogOut();
	}, session_time * 1000);

	$('.dex.admin-bar .logged-in .current-user a').click(function () {
		api('/' + base_url + 'api/core/users/', {
			'action': 'logout'
		}, function(data) {
			adminBarLogOut();
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