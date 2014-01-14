$('#publish-site').on('click', 'a', function() {
    $.fancybox.open({
        content: '<textarea id="console" readonly></textarea>'
    });

    apiStatusWorking('Publishing site...');
    apiUpdateConsole($('#console'));
    api('/' + base_url + 'api/core/publish-site/', {
    }, function(data) {
        apiStopConsole();
        apiStatusSuccess('Published site');
        $('#publish-site').addClass('fade');
    }, function() {
        apiStopConsole();
        apiStatusError('Publishing site failed');
        return false;
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
            'top': '0'
        });
    }, function() {
        apiStatusError('Logging out failed');
        return false;
    });
});