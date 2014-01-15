$(function() {
    $('.page-wrapper, .page-wrapper-slim')
        .css({
            display: 'block',
            marginTop: '-10px',
            opacity: '0'
        })
        .animate({
            marginTop: '0',
            opacity: '1'
        }, 400, 'swing');
});