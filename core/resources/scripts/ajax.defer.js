function ajax(element, method, data, success, error) {
    $.ajax({
        type: method,
        url: window.location.href,
        data: data,
        success: function(data) {
            if (typeof success !== 'undefined')
                success(element, data);
        },
        error: function(data) {
            if (typeof error !== 'undefined')
                error(element, data);
            else
            {
                var text = JSON.stringify(data);
                if (typeof data['responseText'] !== 'undefined')
                    text = data['responseText'];
                else if (typeof data['statusText'] !== 'undefined')
                    text = data['statusText'];

                $('#ajax_error, #ajax_error_link').remove();
                $('body').append('<a href="#ajax_error" id="ajax_error_link" class="hidden fancybox"></a>\
                    <div id="ajax_error" class="hidden">' + text + '</div>');
                $('#ajax_error_link').fancybox().click();
            }
        },
        dataType: 'json'
    });
}