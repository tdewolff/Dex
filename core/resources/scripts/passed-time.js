function passed_time() {
    $('span.passed-time').change(function() {
        $(this).attr('data-time', new Date().getTime());
    }).each(function () {
        if (typeof $(this).attr('data-time') == 'undefined')
            $(this).attr('data-time', new Date().getTime());

        var diff = Math.round((new Date().getTime() - $(this).attr('data-time')) / 1000),
            then = new Date(parseInt($(this).attr('data-time'))),
            value;

        if (diff < 10)
            value = '';
        else if (diff < 30)
            value = ' seconds ago';
        else if (diff < 60)
            value = ' half a minute ago';
        else if (diff < 120)
            value = ' 1 minute ago';
        else if (diff < 600)
            value = ' ' + Math.round(diff / 60) + ' minutes ago';
        else
            value = ' ' + then.getHours() + ':' + then.getMinutes();

        if ($('span', this).text() !== value)
            $(this).fadeOut(function () {
                $('span', this).text(value);
            }).fadeIn();
    });
}
setInterval(passed_time, 5000);
passed_time();