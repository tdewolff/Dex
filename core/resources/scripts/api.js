function api(url, data, success, error) {
    if (!url)
        apiFatal('no API URL set');
    else
        $.ajax({
            type: (data == null ? 'GET' : 'POST'),
            url: url,
            data: data,
            dataType: 'json',
            success: function(data) {
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
            error: function(data) {
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
        content: message,
        closeBtn: false,
        beforeShow: function() {
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
    $('#api_status div').stop(true).hide();
}

function apiStatusWorking(message) {
    apiStatusClear();
    $('#api_status div.working').delay(800).fadeIn('fast');
    if (typeof message !== 'undefined')
        $('#api_status div.working span').delay(800).html(message).find('span[data-time]').attr('data-time', new Date().getTime());
}

function apiStatusSuccess(message) {
    apiStatusClear();
    $('#api_status div.success').fadeIn('fast');
    if (typeof message !== 'undefined')
        $('#api_status div.success span').html(message).find('span[data-time]').attr('data-time', new Date().getTime());
}

function apiStatusError(message) {
    apiStatusClear();
    $('#api_status div.error').fadeIn('fast');
    if (typeof message !== 'undefined')
        $('#api_status div.error span').html(message).find('span[data-time]').attr('data-time', new Date().getTime());
}

function apiStatusTime() {
    $('span[data-time]').each(function () {
        var self = $(this),
            time = parseInt(self.attr('data-time'));
        if (!isNaN(time)) {
            var diff = Math.round((new Date().getTime() - time) / 1000),
                then = new Date(time),
                value;

            if (diff < 10)
                value = '';
            else if (diff < 30)
                value = ' seconds ago';
            else if (diff < 60)
                value = ' half a minute ago';
            else if (diff < 120)
                value = ' 1 minute ago';
            else if (diff < 600)
                value = ' ' + Math.round(diff / 60) + ' minutes ago';
            else
                value = ' ' + then.getHours() + ':' + then.getMinutes();

            if (self.text() !== value)
                self.fadeOut(function () {
                    self.text(value);
                }).fadeIn();
        }
    });
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

$(function() {
    setInterval(apiStatusTime, 5000);
    apiStatusTime();
});

var apiUpdateConsoleTimeout;
function apiUpdateConsole(dest) {
    apiUpdateConsoleTimeout = setTimeout(function() {
        api('/' + base_url + 'api/core/index/', {
            action: 'console'
        }, function(data) {
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
    setTimeout(function() {
        clearTimeout(apiUpdateConsoleTimeout);
    }, 1000);
}