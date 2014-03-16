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

	var hasChange = false;
	var saveTimeout = null;

	$('[data-dexeditable]').on('input', function(e) {
		hasChange = true;
		clearTimeout(saveTimeout);
		saveTimeout = setTimeout(save, 1000);
	});

	$('[data-dexeditable]').on('change', function(e) {
		console.log('change');
		if (hasChange)
		{
			clearTimeout(saveTimeout);
			save();
			hasChange = false;
		}
	});

	function save() {
		hasChange = false;
		$.event.trigger({
			type: 'save'
		});
	}

	$('#log-out').click(function() {
		api('/' + base_url + 'api/core/users/', {
			'action': 'logout'
		}, function(data) {
			$('#api_fatal').fadeOut().remove();
			$('#api_status').fadeOut().remove();
			$('#admin-bar').slideUp(function() {
				this.remove();
			});
			$('[data-editable]').attr('contenteditable', 'false');

			$('body').animate({
				'padding-top': '0'
			});
		});
	});
});
