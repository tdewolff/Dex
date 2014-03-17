$(function() {
	/*$('[data-editable]').attr('contenteditable', 'true');
	grande.bind(document.querySelectorAll('[data-editable]'));
	initializeUpload('[contenteditable="true"]');

	initializeUploadDone(function(data) {
		if (!data['file'].is_image)
		{
			var item = asset_item(data['file']);
			if (directories_assets.find('li.asset').length)
				addAlphabetically(directories_assets.find('li.asset'), item, data['file']['name']);
			else
				$(item).hide().insertAfter(directories_assets.find('.directory:last')).slideDown('fast');
		}
		else
		{
			var item = image_item(data['file']);
			if (images.find('li').length)
				addAlphabetically(images.find('li'), item, data['file']['name']);
			else
				$(item).hide().appendTo(images).slideDown('fast');
		}
	});*/

	var saveTimeout = null;
	$('[data-dexeditable]').on('input', function(e) {
		clearTimeout(saveTimeout);
		saveTimeout = setTimeout(save, 1000);
	});

	function save() {
		$.event.trigger({
			type: 'save'
		});
	}

	$('.dex.admin-bar .logged-in .current-user a').click(function() {
		api('/' + base_url + 'api/core/users/', {
			'action': 'logout'
		}, function(data) {
			$('.dex.api').fadeOut().remove();
			$('.dex.admin-bar .logged-in').fadeOut(function() {
				$('.dex.admin-bar .logged-out').fadeIn();
			});
			$('[data-dexeditable]').attr('contenteditable', 'false');
		});
	});

	$('.dex.admin-bar .logged-out .current-user a').click(function() {
		api('/' + base_url + 'api/core/users/', {
			'action': 'forget'
		}, function(data) {
			$('.dex.admin-bar').slideUp(function() {
				$(this).remove();
			});

			$('body').animate({
				'padding-top': '0'
			});
		});
	});
});
