function api(data, success, error) {
    if (typeof apiUrl === 'undefined')
        ajaxError('API URL not set');
    else
        ajax(apiUrl, (data == null ? 'GET' : 'POST'), data, success, error);
}

function ajax(url, method, data, success, error) {
    $.ajax({
        type: method,
        url: url,
        data: data,
        dataType: 'json',
        success: function(data) {
            if (typeof data['error'] !== 'undefined')
                ajaxError(data['error'].join('<br>'));
            else if (typeof success !== 'undefined')
                success(data);
        },
        error: function(data) {
            if (typeof error !== 'undefined')
                if (error(data) === false)
                    return;

            if (typeof data['responseJSON'] !== 'undefined' && typeof data['responseJSON']['error'] !== 'undefined') // PHP error but still handled by API
                ajaxError(data['responseJSON']['error'].join('<br>'));
            else if (typeof data['responseText'] !== 'undefined') // Non-JSON response
                ajaxError(data['responseText']);
            else if (typeof data['statusText'] !== 'undefined') // Some XHR thing went wrong
                ajaxError(data['statusText']);
            else // ...shrugs
                ajaxError(data);
        }
    });
}

function ajaxError(error) {
    $('#ajax_error, #ajax_error_link').remove();
    $('body').append('<a href="#ajax_error" id="ajax_error_link" class="hidden fancybox"></a>\
        <div id="ajax_error" class="hidden">' + error + '</div>');
    $('#ajax_error_link').fancybox().click();
}