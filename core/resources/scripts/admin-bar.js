$('a#publish_site').click(function() {
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
    }, function() {
        apiStopConsole();
        apiStatusError('Publishing site failed');
        return false;
    });
});

$(function() {
    // <a href="#" class="small-button" data-tooltip="Publish and optimize the content of the site" data-action="publish_site"><i class="fa fa-magic"></i>&ensp;Publish site</a>
});