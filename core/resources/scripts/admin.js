$(function() {
	$('form:first *:input[type!=hidden]:first').focus();

	if ($('.markdown').length > 0) {
		$('.markdown').markItUp(mySettings);

		$('a[title="Preview"]').trigger('mouseup');
		$('.markdown').on('keyup', function() {
			$('a[title="Preview"]').trigger('mouseup');
		});
	}

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

	$('textarea.bottom').each(function () {
		$(this).scrollTop(this.scrollHeight - $(this).height());
	});

	applyTooltips();
});

$('html').on('click', 'a[href="#"]', function (e) {
	e.preventDefault();
});

// dropdown
$('html').click(function() {
	$('.dropdown-menu').fadeOut('fast');
});

$('html').on('click', '.dropdown-menu', function(e) {
	e.stopPropagation();
});

$('html').on('click', '.dropdown-toggle', function(e) {
	e.stopPropagation();

	var dropdown = $(this).parent();
	$('.dropdown-menu').not($('.dropdown-menu', dropdown)).hide(150);
	$('.dropdown-menu', dropdown).toggle();
	if ($('.dropdown-menu', dropdown).is(':visible')) {
		$('.dropdown-menu', dropdown).css({
			overflow: 'visible'
		});
	}
});

// halt
$('html').on('click', 'a.halt', function(e) {
	e.preventDefault();
	var display = $(this).css('display');
	$(this).hide();
	$(this).parent().find('a.sure').show().css('display', display);
});

$('html').on('mouseleave', 'a.sure', function(e) {
	e.preventDefault();
	$(this).fadeOut('fast', function() {
		$(this).parent().find('a.halt').fadeIn('fast');
	});
});

// adding directories, assets or images
function addAlphabetically(list, item, name) {
	item = $(item).hide();

	var added = false;
	list.each(function() {
		if ($(this).attr('data-name') > name)
		{
			item.insertBefore(this).slideDown('fast');
			added = true;
			return false;
		}
	});

	if (!added)
		item.insertAfter(list.last()).slideDown('fast');
}

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

function applyTooltips() {
	$('[data-tooltip]').tooltip({
		position: {
			my: 'center top',
			at: 'center bottom+5',
			collision: 'fit',
			using: function(position, feedback) {
				$(this).css(position);
				$('<div>').addClass('ui-tooltip-arrow').appendTo(this);
			}
		},
		items: "[data-tooltip]",
		content: function() {
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

jQuery.fn.extend({
	insertAtCaret: function(myValue){
		return this.each(function(i) {
			if (document.selection) {
				//For browsers like Internet Explorer
				this.focus();
				var sel = document.selection.createRange();
				sel.text = myValue;
				this.focus();
			} else if (this.selectionStart || this.selectionStart == '0') {
				//For browsers like Firefox and Webkit based
				var startPos = this.selectionStart;
				var endPos = this.selectionEnd;
				var scrollTop = this.scrollTop;
				this.value = this.value.substring(0, startPos)+myValue+this.value.substring(endPos,this.value.length);
				this.focus();
				this.selectionStart = startPos + myValue.length;
				this.selectionEnd = startPos + myValue.length;
				this.scrollTop = scrollTop;
			} else {
				this.value += myValue;
				this.focus();
			}
		});
	}
});