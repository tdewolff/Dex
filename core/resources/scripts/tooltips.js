$(function () {
	applyTooltips();
});

function applyTooltips() {
	$('[data-tooltip]').tooltip({
		position: {
			my: 'center top',
			at: 'center bottom+5',
			collision: 'fit',
			using: function (position, feedback) {
				$(this).css(position);
				$('<div>').addClass('ui-tooltip-arrow').appendTo(this);
			}
		},
		items: "[data-tooltip]",
		content: function () {
			return $(this).attr('data-tooltip');
		},
		show: {
			duration: 100
		},
		hide: {
			duration: 100
		}
	});
}