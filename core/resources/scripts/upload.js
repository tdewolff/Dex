$(function () {
	$(document).on('drop dragover', function (e) {
		e.preventDefault();
	});
});

// adding directories, assets or images
function addAlphabetically(list, item, name) {
	item = $(item).hide();

	var added = false;
	list.each(function () {
		if ($(this).attr('data-name') > name) {
			item.insertBefore(this).slideDown('fast');
			added = true;
			return false;
		}
	});

	if (!added) {
		item.insertAfter(list.last()).slideDown('fast');
	}
}

function initUpload(upload, append, progress, done, error) {
	upload = $(upload);

	var upload_i = 0;
	var done_n = 0;
	var total_loaded = 0, total_size = 0;

	if (!upload.fileupload) {
		return false;
	}

	upload.fileupload({
		dropZone: upload,
		dataType: 'json',
		sequentialUploads: true,

		add: function (e, data) {
			if (upload_i == done_n) {
				total_loaded = 0;
				total_size = 0;
			}
			total_size += data.files[0].size;
			data.i = upload_i;
			upload_i++;

			append(data.i, data.files[0].name, data.i == done_n);
			var jqXHR = data.submit();
		},
		progress: function (e, data) {
			var fileProgress = parseInt(data.loaded / data.total * 100, 10);
			var totalProgress = (total_size > 0 ? parseInt((total_loaded + data.loaded) / total_size * 100, 10) : 100);
			progress(totalProgress, fileProgress);
		},
		always: function (e, data) {
			total_loaded += data.total;
			done_n++;
		},
		done: function (e, data) {
			done(data.i, data.files[0].name, data.response().result);
		},
		fail: function (e, data) {
			error(data.i, data.response().jqXHR['responseText']);
		}
	});
}

function initAdminUpload(_upload, success) {
	var upload = $(_upload);

	 function addMask() {
		upload.addClass('drag');
		upload.find('#drop-mask').css({
			width: upload.width() + 2,
			height: upload.height() + 2
		}).show();
	}

	function removeMask() {
		upload.removeClass('drag');
		upload.find('#drop-mask').hide();
	}

	upload.on('dragenter', addMask);
	upload.find('#drop-mask').on('dragleave', removeMask);
	upload.find('#drop-mask').on('drop', removeMask);

	upload.find('#big-knob input, #small-knob input').knob();
	upload.find('#drop a').click(function () {
		$(this).parent().find('input').click();
	});

	initUpload(_upload, function (i, name, refresh) {
		removeMask();
		upload.find('#knob').stop().fadeIn();
		if (refresh) {
			upload.find('ul li').remove();
		}
		$('<li id="upload_' + i + '"><i class="fa fa-fw fa-cog fa-spin"></i>' + name + '</li>').hide().appendTo('#upload ul').slideDown();
	}, function (totalProgress, progress) {
		upload.find('#big-knob input').val(totalProgress).change();
		upload.find('#small-knob input').val(progress).change();
	}, function (i, name, result) {
		if (typeof result['upload_error'] !== 'undefined') {
			upload.find('#upload_' + i).addClass('fail').append(' (' + result['upload_error'] + ')').find('i').attr('class', 'fa fa-fw fa-times');
			upload.find('#knob').stop().hide();
		} else {
			upload.find('#upload_' + i).addClass('done').find('i').attr('class', 'fa fa-fw fa-check');
			if (name !== result['file']['title']) {
				$('#upload_' + i).append('&emsp;&#8658;&emsp;' + result['file']['title']);
			}
			success(result);
		}
	}, function (i, response) {
		response = $.parseJSON(response);
		if (typeof response['error'] !== 'undefined') { // PHP error but still handled by API
			apiFatal(response['error'].join('<br>'));
		} else {
			apiFatal(escapeHtml(response));
		}

		upload.find('#upload_' + i).addClass('fail').append(' (Unknown error)').find('i').attr('class', 'fa fa-fw fa-times');
		upload.find('#knob').stop().hide();
	});
}