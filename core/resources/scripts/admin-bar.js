$(document).ready(function () {
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
    var article = $('article.main[role="main"] ');
    var articleAbsWidth = article.width() +
        parseInt(article.css('padding-left')) +
        parseInt(article.css('padding-right')) +
        parseInt(article.css('border-left-width')) +
        parseInt(article.css('border-right-width'));
    $('#edit,#save').
        css(article.offset()).
        css({
            'margin-left': articleAbsWidth - $('#save').width(),
            'margin-top': -1 * $('#save').height()
        });

    $('#edit').on('click', 'a', function() {
        $('article').attr('contenteditable', 'true');
        grande.bind(document.querySelectorAll("article"));
        $('#edit').fadeOut('fast', function() {
            $('#save').fadeIn('fast');
        });
    });

    $('#save').on('click', 'a', function() {
        $('#save').fadeOut('fast', function () {
            $('#edit').fadeIn('fast');
        });
        $('article').attr('contenteditable', 'false');
        apiStatusWorking('Saving page...');
        var item = $(this);
        api('/' + base_url + 'api/template/static/index/', {
            action: 'save_page',
            link_id: link_id,
            content: $('article').html()
        }, function() {
            apiStatusSuccess('Saved page <span data-time></span>');
        }, function() {
            apiStatusError('Saving page failed');
        });
    });

    $('article').on('keydown', function() {
        apiStatusClear();
    });

    $('#log-out').click(function() {

        apiStatusWorking('Logging out...');
        api('/' + base_url + 'api/core/users/', {
            'action': 'logout'
        }, function(data) {
            $('article').attr('contenteditable', 'false');
            $('#edit,#save').remove();
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

    if (window.location.hash === "#edit") {
        $('#edit a').trigger('click');
    }
});
