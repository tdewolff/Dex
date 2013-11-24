$(function() {
    $('.page-wrapper, .page-wrapper-slim')
        .css({
            marginTop: '-10px',
            opacity: '0',
            display: 'block'
        })
        .animate({
            marginTop: '0',
            opacity: '1'
        }, 400, 'swing');
});