function api(url, data, success, error) {
    if (!url)
        apiError('no API URL set');
    else
        $.ajax({
            type: (data == null ? 'GET' : 'POST'),
            url: url,
            data: data,
            dataType: 'json',
            success: function(data) {
                if (typeof data['error'] !== 'undefined')
                    apiError(data['error'].join('<br>'));
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
                    apiError(data['responseJSON']['error'].join('<br>'));
                else if (typeof data['responseText'] !== 'undefined') // Non-JSON response
                    apiError(data['responseText']);
                else if (typeof data['statusText'] !== 'undefined') // Some XHR thing went wrong
                    apiError(data['statusText']);
                else // ...shrugs
                    apiError(data);
            }
        });
}

function apiError(error) {
    $('#api_error').html(error);
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