function ajax(method, data, success, error) {
    $.ajax({
        type: method,
        url: window.location.href,
        data: data,
        success: success,
        error: error,
        dataType: 'json'
    });
}