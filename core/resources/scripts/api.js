$(function () {
	if ($('.dex.admin-bar').length)
		$('.dex.api .status').css('top', '+=33px');
});

function api(url, data, success, error) {
	if (!url)
		apiFatal('no API URL set');
	else
		$.ajax({
			type: (data == null ? 'GET' : 'POST'),
			url: url,
			data: data,
			dataType: 'json',
			success: function (data) {
				if (typeof data['error'] !== 'undefined')
				{
					if (typeof error !== 'undefined' && error)
						if (error(data) === false)
							return;

					apiFatal(data['error'].join('<br>'));
				}
				else if (typeof success !== 'undefined' && success)
				{
					success(data);
					if (typeof applyTooltips !== 'undefined')
						applyTooltips();
				}
			},
			error: function (data) {
				if (typeof error !== 'undefined' && error)
					if (error(data) === false)
						return;

				if (typeof data['responseJSON'] !== 'undefined' && typeof data['responseJSON']['error'] !== 'undefined') // PHP error but still handled by API
					apiFatal(data['responseJSON']['error'].join('<br>'));
				else if (typeof data['responseText'] !== 'undefined') // Non-JSON response
					apiFatal(data['responseText']);
				else if (typeof data['statusText'] !== 'undefined') // Some XHR thing went wrong
					apiFatal(data['statusText']);
				else // ...shrugs
					apiFatal(data);
			}
		});
}

function apiFatal(message) {
	$.fancybox.open({
		content: '<h2 class="error">Error</h2>' + message,
		beforeShow: function () {
			this.skin.css({
				'background': '#F2DEDE',
				'color': '#B94A48',
				'border': 'solid 1px #EED3D7'
			});
		},
		overlay: {
			closeClick: true,
			locked: false
		}
	});
}

function apiStatusClear() {
	$('.dex.api .status div').stop(true).hide();
}

function apiStatusFade() {
	$('.dex.api .status div').stop(true).fadeOut('fast');
}

function apiStatusWorking(message) {
	apiStatusClear();
	$('.dex.api .status div.working').delay(800).fadeIn('fast');
	if (typeof message !== 'undefined') {
		$('.dex.api .status div.working span').delay(800).show().html(message);
	} else {
		$('.dex.api .status div.working span').hide();
	}
}

function apiStatusSuccess(message) {
	apiStatusClear();
	$('.dex.api .status div.success').fadeIn('fast');
	if (typeof message !== 'undefined') {
		$('.dex.api .status div.success span').show().html(message);
		setTimeout(apiStatusFade, 5000);
	} else {
		$('.dex.api .status div.success span').hide();
	}
}

function apiStatusError(message) {
	apiStatusClear();
	$('.dex.api .status div.error').fadeIn('fast');
	if (typeof message !== 'undefined') {
		$('.dex.api .status div.error span').show().html(message);
	} else {
		$('.dex.api .status div.error span').hide();
	}
}

function apiLoadStatusClear(load) {
	load.find('div').stop(true).hide();
}

function apiLoadStatusWorking(load) {
	apiLoadStatusClear(load);
	load.find('div.working').fadeIn('fast');
}

function apiLoadStatusSuccess(load) {
	load.remove();
}

function apiLoadStatusEmpty(load) {
	apiLoadStatusClear(load);
	load.find('div.empty').fadeIn('fast');
}

function apiLoadStatusError(load) {
	apiLoadStatusClear(load);
	load.find('div.error').fadeIn('fast');
}

var apiUpdateConsoleTimeout;
function apiUpdateConsole(dest) {
	apiUpdateConsoleTimeout = setTimeout(function () {
		api('/' + base_url + 'api/core/console/', {
			action: 'console'
		}, function (data) {
			if (typeof data['status'] !== 'undefined')
			{
				dest.html(data['status']);
				dest[0].scrollTop = dest[0].scrollHeight;
			}
			apiUpdateConsole(dest);
		});
	}, 200);
}

function apiStopConsole() {
	setTimeout(function () {
		clearTimeout(apiUpdateConsoleTimeout);
	}, 1000);
}