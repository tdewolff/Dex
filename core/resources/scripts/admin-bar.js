$('#publish-site').on('click', 'a', function() {
    $.fancybox.open({
        content: '<textarea id="console" readonly></textarea>'
    });

    apiStatusWorking('Publishing site...');
    apiUpdateConsole($('#console'));
    api('/' + base_url + 'api/core/index.php', {
        action: 'publish_site'
    }, function(data) {
        apiStopConsole();
        apiStatusSuccess('Published site');
        $('#publish-site').remove();
    }, function() {
        apiStopConsole();
        apiStatusError('Publishing site failed');
        return false;
    });
});