$(function() {
	if ($.fn.fancybox) {
		$(".fancybox").fancybox({
			arrows: false,
			closeClick: true,
			closeBtn: false,

			openEffect : 'elastic',
			openSpeed  : 150,

			closeEffect : 'elastic',
			closeSpeed  : 150,

			helpers:  {
				overlay: {
					locked: false
				}
			}
		});
	}
});

function switchPopupFrame(popup) {
	$('.fancybox-inner').animate({'scrollTop': 0});

	var frames = popup.find('> div');
	frames.eq(1).css('display', 'inline-block');
	popup.animate({'margin-left': '-' + frames.eq(0).width() + 'px'}, function() {
		popup.css({
			'margin-left': '0'
		});
		frames.eq(0).css('display', 'none');
		parent.$.fancybox.update();
	});
}