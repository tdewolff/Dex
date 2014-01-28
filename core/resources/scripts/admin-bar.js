$('#publish-site').click(function() {
    $.fancybox.open({
        content: '<textarea id="console" readonly></textarea>'
    });

    apiStatusWorking('Publishing site...');
    apiUpdateConsole($('#console'));
    api('/' + base_url + 'api/core/publish-site/', {
    }, function(data) {
        apiStopConsole();
        apiStatusSuccess('Published site');
    }, function() {
        apiStopConsole();
        apiStatusError('Publishing site failed');
        return false;
    });
});

$('#edit').on('click', 'a', function() {
    $('#edit').fadeOut('fast', function() {
        $('#save').fadeIn('fast');
    });

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
    });
});

$('#log-out').click(function() {
    apiStatusWorking('Logging out...');
    api('/' + base_url + 'api/core/users/', {
        'action': 'logout'
    }, function(data) {
        $('#api_fatal').fadeOut().remove();
        $('#api_status').fadeOut().remove();
        $('#admin-bar').slideUp(function() {
            this.remove();
        });
        $('body').animate({
            'padding-top': '0'
        });
    }, function() {
        apiStatusError('Logging out failed');
        return false;
    });
});