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
                    apiFatal(data['error'].join('<br>'));
                else if (typeof success !== 'undefined' && success)
                {
                    applyTooltips();
                    success(data);
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

function apiIdle() {
    $('#api_status i.fa-cog, #api_status i.fa-check, #api_status i.fa-times').stop(true).hide();
}

function apiBusy() {
    apiIdle();
    $('#api_status i.fa-cog').delay(800).fadeIn('fast');
}

function apiSuccess(tooltip) {
    apiIdle();
    $('#api_status i.fa-check').fadeIn('fast');
    if (typeof tooltip !== 'undefined')
        $('#api_status i.fa-check').attr('data-tooltip', tooltip);
}

function apiError(tooltip) {
    apiIdle();
    $('#api_status i.fa-times').fadeIn('fast');
    if (typeof tooltip !== 'undefined')
        $('#api_status i.fa-times').attr('data-tooltip', tooltip);
}

function apiFatal(error) {
    $('#api_error_link').fancybox({
        closeBtn: false,
        beforeShow: function() {
            this.skin.css({
                'background': '#F2DEDE',
                'color': '#B94A48',
                'border': 'solid 1px #EED3D7'
            });
        },
        overlay : {
            closeClick : true,
            locked: false
        }
    }).click();
}