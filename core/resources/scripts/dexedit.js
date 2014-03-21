var DexEdit = function (root) {
	var self = this;

	this.isFirefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;

	this.root = $(root).attr('contenteditable', 'true');
	this.menu = $('<div class="dexedit_menu">\
		<div class="dexedit_menu_arrow"></div>\
		<span class="dexedit_menu_b"><i class="fa fa-fw fa-bold"></i></span><span class="dexedit_menu_i"><i class="fa fa-fw fa-italic"></i></span><span class="dexedit_menu_h3">H3</span><span class="dexedit_menu_h4">H4</span><span class="dexedit_menu_blockquote"><i class="fa fa-fw fa-quote-right"></i></span><span class="dexedit_menu_link"><i class="fa fa-fw fa-link"></i></span><span class="dexedit_menu_image"><i class="fa fa-fw fa-picture-o"></i></span>\
	</div>').prependTo('body');

	this.root.find('figure, hr').attr('contenteditable', 'false');
	this.root.find('img').each(function (i, img) {
		new DexEditImg(self.root, img);
	});

	this.getSelection = function () {
		if (window.getSelection) {
			return window.getSelection();
		} else if (document.selection) { // Opera
			return document.selection.createRange();
		}
	}
	this.selection = this.getSelection();

	this.setRange = function (range) {
		self.selection.removeAllRanges();
		self.selection.addRange(range);
		self.range = range;

		if (!range.collapsed && !/^[\s]+$/.test(self.selection.toString())) {
			self.repositionMenu(range);
		} else {
			self.hideMenu();
		}
	}
	this.range = null;

	this.getBlock = function () {
		var node = self.range.commonAncestorContainer;
		while (node.parentNode) {
			if (node === self.root[0]) {
				break;
			} else if (node.nodeName === 'DIV' || node.nodeName === 'UL' || node.nodeName === 'OL' || node.nodeName === 'P' || node.nodeName === 'H3' || node.nodeName === 'H4' || node.nodeName === 'BLOCKQUOTE') {
				return node;
			}
			node = node.parentNode;
		}
		return null;
	}

	this.isBackwards = function () {
		var backwards = false;
		if (self.selection && !self.selection.isCollapsed) {
			var range = document.createRange();
			range.setStart(self.selection.anchorNode, self.selection.anchorOffset);
			range.setEnd(self.selection.focusNode, self.selection.focusOffset);
			backwards = range.collapsed;
			range.detach();
		}
		return backwards;
	}

	this.repositionMenu = function (range) {
		var rect = range.getClientRects()[0];

		var top = window.scrollY + rect.top - self.menu.height() - 6;
		if (top < 38) { // include admin-bar
			top = window.scrollY + rect.bottom + 6;
			self.menu.find('.dexedit_menu_arrow').addClass('dexedit_menu_arrow_upsidedown');
		} else {
			self.menu.find('.dexedit_menu_arrow').removeClass('dexedit_menu_arrow_upsidedown');
		}

		var left = rect.left + rect.width / 2 - self.menu.width() / 2;
		if (left < 5) {
			left = 5;
			self.menu.find('.dexedit_menu_arrow').css({
				left: rect.left + rect.width / 2 - 5
			});
		} else if (left + self.menu.width() > window.innerWidth - 5) {
			left = window.innerWidth - self.menu.width() - 5;
			self.menu.find('.dexedit_menu_arrow').css({
				left: rect.left + rect.width / 2 - 5
			});
		} else {
			self.menu.find('.dexedit_menu_arrow').css({
				left: '50%'
			});
		}

    	if (document.queryCommandState('bold')) {
			self.menu.find('.dexedit_menu_b').addClass('enabled');
		} else {
			self.menu.find('.dexedit_menu_b').removeClass('enabled');
		}

    	if (document.queryCommandState('italic')) {
			self.menu.find('.dexedit_menu_i').addClass('enabled');
		} else {
			self.menu.find('.dexedit_menu_i').removeClass('enabled');
		}

		if (self.hasParentTag(range.commonAncestorContainer, 'H3')) {
			self.menu.find('.dexedit_menu_h3').addClass('enabled');
		} else {
			self.menu.find('.dexedit_menu_h3').removeClass('enabled');
		}

		if (self.hasParentTag(range.commonAncestorContainer, 'H4')) {
			self.menu.find('.dexedit_menu_h4').addClass('enabled');
		} else {
			self.menu.find('.dexedit_menu_h4').removeClass('enabled');
		}

		if (self.hasParentTag(range.commonAncestorContainer, 'BLOCKQUOTE')) {
			self.menu.find('.dexedit_menu_blockquote').addClass('enabled');
		} else {
			self.menu.find('.dexedit_menu_blockquote').removeClass('enabled');
		}

    	if (self.hasParentTag(range.commonAncestorContainer, 'A')) {
			self.menu.find('.dexedit_menu_link').addClass('enabled');
			self.menu.find('.dexedit_menu_link > i').attr('class', 'fa fa-unlink');
		} else {
			self.menu.find('.dexedit_menu_link').removeClass('enabled');
			self.menu.find('.dexedit_menu_link > i').attr('class', 'fa fa-link');
		}

		self.menu.css({
			top: top,
			left: left
		}).stop().fadeIn('fast');
	}

	this.hideMenu = function () {
		self.menu.hide();
	}

	this.getTextProperty = function (node) {
		if (node.nodeType === Node.TEXT_NODE) {
			return 'data';
		} else if (self.isFirefox) {
			return 'textContent';
		} else {
			return 'innerText';
		}
	}

	this.setText = function (node, text) {
		node[self.getTextProperty(node)] = text;
	}

	this.getText = function (node) {
		return node[self.getTextProperty(node)];
	}

	this.getParentTag = function (node, tag) {
		while (node.parentNode) {
			if (node === self.root[0]) {
				return null;
			} else if (node.nodeName === tag) {
				return node;
			}
			node = node.parentNode;
		}
		return null;
	}

	this.hasParentTag = function (node, tag) {
		return !!self.getParentTag(node, tag);
	}

	this.hasParentClass = function (node, classname) {
		while (node.parentNode) {
			if ($(node).hasClass(classname)) {
				return true;
			}
			node = node.parentNode;
		}
		return false;
	}

	this.hasParentTag = function (node, tag) {
		return !!self.getParentTag(node, tag);
	}

	this.toggleFormat = function (tag) {
		var action = '';
		switch (tag) {
		case 'B':
			action = 'bold';
			break;
		case 'I':
			action = 'italic';
			break;
		}

		document.execCommand(action, false);
		self.setRange(self.selection.getRangeAt(0));
	}

	this.toggleFormatBlock = function (tag) {
		var block = self.getBlock();
		if (block) {
			// TODO: removes, was used to select all text in block
			//var parent = block.parentNode;
			//block = block.previousSibling;

			if (!self.hasParentTag(self.range.commonAncestorContainer, tag)) {
				document.execCommand('formatBlock', false, tag);
			} else {
				document.execCommand('formatBlock', false, 'P');
				document.execCommand('outdent', false);
			}
			self.setRange(self.selection.getRangeAt(0));

			/*if (block === null) {
				block = parent.childNodes[0];
			} else {
				block = block.nextSibling;
			}

			var range = document.createRange();
			range.selectNodeContents(block);
			self.setRange(range);*/
		}
	}

	this.toggleLink = function (link) {
		if (self.hasParentTag(self.range.commonAncestorContainer, 'A')) {
			document.execCommand('unlink', false);
		} else {
			document.execCommand('createLink', false, link);
		}
		self.setRange(self.selection.getRangeAt(0));
	}

	this.replaceText = function (text) {
		var startContainer = self.range.startContainer;
		var startOffset = self.range.startOffset;
		var endContainer = self.range.endContainer;
		var endOffset = self.range.endOffset;

		document.execCommand('insertText', false, text);

		var range = document.createRange();
		range.setStart(startContainer, startOffset);
		range.setEnd(endContainer, text.length);
		self.setRange(range);
	}

	this.insertList = function (tag) {
		document.execCommand(tag === 'OL' ? 'insertOrderedList' : 'insertUnorderedList');
		self.setText(self.selection.anchorNode, '');
		self.setRange(self.selection.getRangeAt(0));
	}

	this.root.on('selectstart', function () {
		self.hideMenu();
		$(document).one('mouseup', function () {
			if (!self.selection.isCollapsed) {
				var text = self.selection.toString();
				var beginTrim = text.match(/^(\s*)/)[1].length;
				var endTrim = text.match(/(\s*)$/)[1].length;

				// remove whitespace begin and end of selection
				var range = document.createRange();
				if (!self.isBackwards()) {
					range.setStart(self.selection.anchorNode, self.selection.anchorOffset + beginTrim <= self.getText(self.selection.anchorNode).length ? self.selection.anchorOffset + beginTrim : self.getText(self.selection.anchorNode).length);
					range.setEnd(self.selection.focusNode, self.selection.focusOffset - endTrim > 0 ? self.selection.focusOffset - endTrim : 0);
				} else {
					range.setStart(self.selection.focusNode, self.selection.focusOffset + beginTrim <= self.getText(self.selection.focusNode).length ? self.selection.focusOffset + beginTrim : self.getText(self.selection.focusNode).length);
					range.setEnd(self.selection.anchorNode, self.selection.anchorOffset - endTrim > 0 ? self.selection.anchorOffset - endTrim : 0);
				}
				self.setRange(range);
			}
        });
	});

	// mouse
	$(document).on('mousedown', function (e) {
		if (!self.hasParentClass(e.toElement, 'fancybox-wrap')) {
			self.menu.stop().fadeOut('fast');
		}
	});

	this.root.on('mousedown', function (e) {
		e.stopPropagation();
		if (self.range) {
			self.menu.stop().fadeOut('fast');
		}
	});

	this.menu.on('mousedown', function (e) {
		e.stopPropagation();
		self.setRange(self.range);
	});

	this.menu.on('click', '.arrow', function (e) {
		e.stopPropagation();
		self.setRange(self.range);
	});

	this.menu.on('click', 'span', function (e) {
		e.stopPropagation();

		if (e.which == 1 && self.range) {
			// reselect text after blur due to button click
			self.setRange(self.range);

			var target = $(this);
			if (target.hasClass('dexedit_menu_b')) {
				self.toggleFormat('B');
			} else if (target.hasClass('dexedit_menu_i')) {
				self.toggleFormat('I');
			} else if (target.hasClass('dexedit_menu_h3')) {
				self.toggleFormatBlock('H3');
			} else if (target.hasClass('dexedit_menu_h4')) {
				self.toggleFormatBlock('H4');
			} else if (target.hasClass('dexedit_menu_blockquote')) {
				self.toggleFormatBlock('BLOCKQUOTE');
			} else if (target.hasClass('dexedit_menu_link')) {
				self.setRange(self.range);
				if (self.hasParentTag(self.range.commonAncestorContainer, 'A')) {
					self.toggleLink();
				} else {
					$.fancybox.open({
						'type': 'ajax',
						'href': '/' + base_url + 'admin/auxiliary/insert-link/',
						beforeShow: function () {
							$('.fancybox-skin').css('background', 'white');
							$('#insert_text').val(self.selection.toString());
							applyTooltips();
						},
						beforeClose: function () {
							if ($('#insert_submit').val() == 1 && $('#insert_url').val()) {
								var title = $('#insert_title').val();
								var url = $('#insert_url').val();
								var text = $('#insert_text').val();

								self.setRange(self.range);
								self.toggleLink(url);
								self.replaceText(text);
								self.range.commonAncestorContainer.parentNode.title = title;
							}
						},
						helpers:  {
							overlay: {
								locked: false
							}
						}
					});
				}
			} else if (target.hasClass('dexedit_menu_image')) {
				self.setRange(self.range);
				//if (self.hasParentTag(self.range.commonAncestorContainer, 'A')) {
				//	self.toggleLink();
				//} else {
					$.fancybox.open({
						'type': 'ajax',
						'href': '/' + base_url + 'admin/auxiliary/insert-image/',
						beforeShow: function () {
							$('.fancybox-skin').css('background', 'white');
							$('#insert_text').val(self.selection.toString());
						},
						beforeClose: function () {
							if ($('#insert_submit').val() == 1 && $('#insert_url').val()) {
								var title = $('#insert_title').val();
								var url = $('#insert_url').val();
								var text = $('#insert_text').val();

					            var figure = $('<figure style="float: left;"><img src="' + url + '" title="' + title + '" alt="' + text + '"></figure>');
					            new DexEditImg(self.root, figure.find('img'));
					            figure.insertBefore(self.getBlock());

					            self.selection.removeAllRanges();
								self.menu.stop().fadeOut('fast');
								self.root.trigger('input');


					            // Preserve the selection
					   //          range = range.cloneRange();
					   //          range.setStartAfter(element);
					   //          range.collapse(true);
					   //          sel.removeAllRanges();
					   //          sel.addRange(range);
								// document.execCommand('insertHtml', false, '<figure style="float: left;"><img src="' + url + '" title="' + title + '" alt="' + text + '"></figure>');
								// console.log(self.range.commonAncestorContainer);
								//new DexEditImg(self.range.commonAncestorContainer);
							}
						},
						helpers:  {
							overlay: {
								locked: false
							}
						}
					});
				//}
			}
		}
	});

	// keyboard
    this.root.on('keyup', function (e) {
		if (e.keyCode === 13) {
			// insert horizontal rule
			var paragraph = self.getParentTag(self.selection.anchorNode, 'P');
			if (paragraph && paragraph.previousSibling) {
				var previousParagraph = paragraph.previousSibling;
				if (previousParagraph.nodeName === 'P'	&& !previousParagraph.textContent.length) {
					if (!previousParagraph.previousSibling || previousParagraph.previousSibling.nodeName !== 'HR') {
						var hr = document.createElement("hr");
						hr.contentEditable = false;
						previousParagraph.parentNode.replaceChild(hr, previousParagraph);
					}
				}
			}

			var range = document.createRange();
			if (!self.isBackwards()) {
				range.setStart(self.selection.anchorNode, self.selection.anchorOffset);
				range.setEnd(self.selection.focusNode, self.selection.focusOffset);
			} else {
				range.setStart(self.selection.focusNode, self.selection.focusOffset);
				range.setEnd(self.selection.anchorNode, self.selection.anchorOffset);
			}
			self.setRange(range);

			var block = self.getBlock();
			if (block && (block.nodeName === 'DIV' || block.nodeName === 'BLOCKQUOTE')) {
				self.toggleFormatBlock('P');
			}
		}

    	if (self.selection.isCollapsed) {
			// FF will return sel.anchorNode to be the parentNode when the triggered keyCode is 13
			//if (!self.selection.anchorNode || self.selection.anchorNode.nodeName === 'ARTICLE') {
			//	return;
			//}

			if (self.selection.anchorNode.parentNode.nodeName === 'P') {
				var text = self.getText(self.selection.anchorNode);
				if (text.match(/^[-*]\s/)) {
					self.insertList('UL');
				} else if (text.match(/^1\.\s/)) {
					self.insertList('OL');
				}
			}

			var preCaretText = self.getText(self.selection.anchorNode);//.substring(0, self.selection.anchorOffset);
			var oldPreCaretText = preCaretText;
			preCaretText = preCaretText.replace(/(^|[-\u2013\u2014\s(\["])'|()`/g, "$1\u2018");						// opening singles
			preCaretText = preCaretText.replace(/'/g, "\u2019");													// closing singles & apostrophes
			preCaretText = preCaretText.replace(/(^|[-\u2013\u2014\/\[(\u2018\s])"|()\u2018\u2018/g, "$1\u201C");	// opening doubles
			preCaretText = preCaretText.replace(/"|\u2019\u2019/g, "\u201D");										// closing doubles
			preCaretText = preCaretText.replace(/--/g, "\u2013");													// en-dashes
			preCaretText = preCaretText.replace(/[\u2013-]{2}/g, "\u2014");											// em-dashes
			preCaretText = preCaretText.replace(/\.\.\./g, "\u2026");												// ellipsis
			preCaretText = preCaretText.replace(/,,/g, "\u201E");													// comma quotes
			preCaretText = preCaretText.replace(/[\u2039<]{2}/g, "\u00AB");											// opening double guillemets
			preCaretText = preCaretText.replace(/[\u203A>]{2}/g, "\u00BB");											// opening double guillemets
			preCaretText = preCaretText.replace(/</g, "\u2039");													// opening single guillemets
			preCaretText = preCaretText.replace(/>/g, "\u203A");													// opening single guillemets

			if (preCaretText !== oldPreCaretText) {
				var parent = self.selection.anchorNode.parentNode;
				var offset = self.selection.anchorOffset + (preCaretText.length - oldPreCaretText.length);

				self.setText(self.selection.anchorNode, preCaretText);// + self.getText(self.selection.anchorNode).substring(self.selection.anchorOffset));

				var range = document.createRange();
				range.setStart(parent.childNodes[0], offset);
				range.setEnd(parent.childNodes[0], offset);
				self.setRange(range);
			}
		}
    });
};

var DexEditImg = function (root, img) {
	var self = this;

	this.root = root;
	this.img = $(img).wrap('<div class="dexedit_img" contenteditable="false"></div>');
	this.wrapper = this.img.parent();
	this.figure	= (this.wrapper.parent()[0] && this.wrapper.parent()[0].nodeName === 'FIGURE' ? self.wrapper.parent() : null);
	if (this.figure === null) {
		return;
	}

	this.placeholder = $('<figure class="dexedit_img_placeholder"></figure>').insertBefore(this.figure);
	this.img_resize = $('<div class="dexedit_img_resize">\
		<div class="dexedit_img_resize_tl"></div><div class="dexedit_img_resize_tr"></div><div class="dexedit_img_resize_bl"></div><div class="dexedit_img_resize_br"></div>\
	</div>').prependTo(this.wrapper);
	this.img_menu = $('<div class="dexedit_img_menu">\
		<span class="dexedit_img_menu_left"><i class="fa fa-fw fa-chevron-left"></i></span><span class="dexedit_img_menu_edit"><i class="fa fa-fw fa-edit"></i></span><span class="dexedit_img_menu_center"><i class="fa fa-fw fa-square"></i></span><span class="dexedit_img_menu_trash"><i class="fa fa-fw fa-trash-o"></i></span><span class="dexedit_img_menu_right"><i class="fa fa-fw fa-chevron-right"></i></span>\
	</div>').prependTo(this.wrapper);

	this.resizing = false;
	this.dragging = false;
	this.hovering = false;

	this.drag_start_x = 0;
	this.drag_start_y = 0;
	this.drag_start_w = 0;
	this.drag_start_h = 0;
	this.drag_sign_x = 0;
	this.drag_sign_y = 0;
	this.drag_offset_x = 0;
	this.drag_offset_y = 0;

	this.setDimensions = function (width, height) {
		if (width < 30) {
			width = 30;
			height = width * self.img_ratio;
		}

		if (height < 30) {
			height = 30;
			width = height / self.img_ratio;
		}

		self.img.css({
			width: width,
			height: height
		});

		self.wrapper.css({
			width: width,
			height: height
		});

		self.placeholder.css({
			width: width,
			height: height
		});
	}

	console.log(this.img[0].width);
	console.log(this.img.width());

	if (this.img[0].width == 0 || this.img[0].height == 0) {
		this.img_ratio = 1;
	} else {
		this.img_ratio = this.img[0].height / this.img[0].width;
	}
	this.setDimensions(this.img[0].width, this.img[0].height);

	this.getPreviousBlock = function (node) {
		node = node.previousSibling;
		while (node) {
			if (node.nodeName === 'DIV' || node.nodeName === 'HR' || node.nodeName === 'UL' || node.nodeName === 'OL' || node.nodeName === 'P' || node.nodeName === 'H3' || node.nodeName === 'H4' || node.nodeName === 'BLOCKQUOTE') {
				return node;
			}
			node = node.previousSibling;
		}
		return node;
	};

	this.getNextBlock = function (node) {
		node = node.nextSibling;
		while (node) {
			if (node.nodeName === 'DIV' || node.nodeName === 'HR' || node.nodeName === 'UL' || node.nodeName === 'OL' || node.nodeName === 'P' || node.nodeName === 'H3' || node.nodeName === 'H4' || node.nodeName === 'BLOCKQUOTE') {
				return node;
			}
			node = node.nextSibling;
		}
		return node;
	};

	this.resize = function (e) {
        if (self.resizing === false) {
			$(document).unbind('mousemove', self.resize);
			return;
		}

		var diff_x = (e.pageX - self.drag_start_x) * self.drag_sign_x;
		var diff_y = (e.pageY - window.scrollY - self.drag_start_y) * self.drag_sign_y;

		var width = self.drag_start_w + diff_x;
		var height = self.drag_start_h + diff_y;
		if (height < width * self.img_ratio) { // pick the smallest (width/height) by ratio
			width = height / self.img_ratio;
		} else {
			height = width * self.img_ratio;
		}
		self.setDimensions(width, height);
	};

	this.drag = function (e) {
        if (self.dragging === false) {
			$(document).unbind('mousemove', self.drag);
			return;
		}

		var previous = self.getPreviousBlock(self.placeholder[0]);
		if (previous) {
			var rect = previous.getBoundingClientRect();
			if (e.pageY < rect.top + rect.height / 2) {
				self.placeholder.insertBefore(previous);
			}
		}

		var next = self.getNextBlock(self.placeholder[0]);
		if (next) {
			var rect = next.getBoundingClientRect();
			if (e.pageY > rect.top + rect.height / 2) {
				self.placeholder.insertAfter(next);
			}
		}

		var x = self.drag_offset_x + (e.pageX - self.drag_start_x);
		var y = self.drag_offset_y + (e.pageY - window.scrollY - self.drag_start_y);
		self.figure.css({
			top: y,
			left: x
		});
	};

	this.showMenu = function () {
		var rect = self.img[0].getBoundingClientRect();

		var top = 10 - self.img_menu.height() - 5;
		if (rect.top - self.img_menu.height() - 10 < 38) { // include admin-bar
			top = 10 + rect.height + 5;
		}

		var left = 10 + (rect.width - self.img_menu.width()) / 2;
		if (rect.left + left < 5) {
			left = 5 - rect.left + 10;
		} else if (rect.left + left + self.img_menu.width() > window.innerWidth - 5) {
			left = window.innerWidth - rect.left - self.img_menu.width() - 5 - 10;
		}

		self.img_menu.stop().css({
			top: top,
			left: left
		}).fadeIn('fast');
	}

	this.img.on({
	    mouseenter: function () {
	        $(this).data('title', this.title).prop('title', '');
	    },
	    mouseleave: function () {
	        $(this).prop('title', $(this).data('title'));
	    }
	});

	this.wrapper.on({
		mouseenter: function (e) {
			self.hovering = true;
			if (self.dragging === false) {
				if (self.resizing === false) {
					self.showMenu();
				}
				self.img_resize.stop().fadeIn('fast');
			}
		},
		mouseleave: function (e) {
			self.hovering = false;
			if (self.dragging === false) {
				self.img_menu.stop().fadeOut('fast');
				if (self.resizing === false) {
					self.img_resize.stop().fadeOut('fast');
				}
			}
		}
	});

	this.wrapper.on('mousedown', '>div, img', function (e) {
        e.preventDefault();
        e.stopPropagation();

        if (e.which == 1) {
			var target = $(this);
			var buttonTarget = $(e.target);

			if (target.hasClass('dexedit_img_resize')) {
				if (buttonTarget.hasClass('dexedit_img_resize_tl')) {
					$('html').addClass('dexedit_img_resize_nwse');
					self.img.addClass('dexedit_img_resize_nwse');
					self.drag_sign_x = -1;
					self.drag_sign_y = -1;
				} else if (buttonTarget.hasClass('dexedit_img_resize_tr')) {
					$('html').addClass('dexedit_img_resize_nesw');
					self.img.addClass('dexedit_img_resize_nesw');
					self.drag_sign_x = 1;
					self.drag_sign_y = -1;
				} else if (buttonTarget.hasClass('dexedit_img_resize_bl')) {
					$('html').addClass('dexedit_img_resize_nesw');
					self.img.addClass('dexedit_img_resize_nesw');
					self.drag_sign_x = -1;
					self.drag_sign_y = 1;
				} else if (buttonTarget.hasClass('dexedit_img_resize_br')) {
					$('html').addClass('dexedit_img_resize_nwse');
					self.img.addClass('dexedit_img_resize_nwse');
					self.drag_sign_x = 1;
					self.drag_sign_y = 1;
				}
			} else if (target.hasClass('dexedit_img_menu')) {
				if (e.target.nodeName === 'I') {
					buttonTarget = $(e.target.parentNode);
				}

				if (buttonTarget.hasClass('dexedit_img_menu_left')) {
					self.figure.css('float', 'left');
				} else if (buttonTarget.hasClass('dexedit_img_menu_edit')) {
					//self.figure.css('float', 'right');
				} else if (buttonTarget.hasClass('dexedit_img_menu_center')) {
					self.figure.css('float', '');
				} else if (buttonTarget.hasClass('dexedit_img_menu_trash')) {
					self.figure.remove();
				} else if (buttonTarget.hasClass('dexedit_img_menu_right')) {
					self.figure.css('float', 'right');
				} else {
					return;
				}
				self.root.trigger('input');
				return;
			} else if (target[0].nodeName === 'IMG') {
				if (self.hovering === true) {
					self.img_menu.stop().fadeOut('fast');
					self.img_resize.stop().fadeOut('fast');
				}

				self.dragging = true;
				self.drag_start_x = e.pageX;
				self.drag_start_y = e.pageY - window.scrollY;
				self.drag_offset_x = self.figure[0].offsetLeft;
				self.drag_offset_y = self.wrapper[0].offsetTop + 10;

				self.figure.css({
					position: 'absolute',
					margin: '0',
					top: self.drag_offset_y,
					left: self.drag_offset_x
				});
				self.placeholder.css('float', self.figure[0].style.cssFloat).show();

				$(document).bind('mousemove', self.drag);
				return;
			} else {
				return;
			}

			if (self.hovering === true) {
				self.img_menu.stop().fadeOut('fast');
			}

			self.resizing = true;
			self.drag_start_x = e.pageX;
			self.drag_start_y = e.pageY - window.scrollY;
			self.drag_start_w = self.img[0].width;
			self.drag_start_h = self.img[0].height;

			$(document).bind('mousemove', self.resize);
		}
	});

	$('html').on('mouseup', function (e) {
		if (self.resizing === true) {
            e.preventDefault();
			$(document).unbind('mousemove', self.resize);

			$('html').removeClass('dexedit_img_resize_nwse');
			$('html').removeClass('dexedit_img_resize_nesw');
			self.img.removeClass('dexedit_img_resize_nwse');
			self.img.removeClass('dexedit_img_resize_nesw');

			if (self.hovering === true) {
				self.showMenu();
			} else {
				self.img_resize.stop().fadeOut('fast');
			}

			var src = self.img.attr('src');
			src = addParameter(src, 'w', self.img[0].width);
			self.img.attr('src', src);

			self.root.trigger('input');
			self.resizing = false;
		} else if (self.dragging) {
            e.preventDefault();
			$(document).unbind('mousemove', self.drag);

			if (self.hovering === true) {
				self.showMenu();
				self.img_resize.stop().fadeIn('fast');
			}

			self.figure.insertAfter(self.placeholder);

			self.placeholder.hide();
			self.figure.css({
				position: '',
				margin: '',
				left: '',
				right: ''
			});

			self.root.trigger('input');
			self.dragging = false;
		}
	});
};

$(function () {
	$('[data-dexeditable]').each(function (i, root) {
		new DexEdit(root);
	});
});

function getDexEditContent(selector) {
	var content = $(selector).clone().detach();
	removeDexEdit(content);
	return content.html();
}

function removeDexEdit(root) {
	root.find('.dexedit_menu, .dexedit_img_placeholder, .dexedit_img_resize, .dexedit_img_menu').remove();
	root.find('[data-dexeditable], figure, hr').attr('contenteditable', null);
	root.find('.dexedit_img > img').unwrap();
}

function removeAllDexEdit() {
	$('[data-dexeditable]').each(function (i, root) {
		removeDexEdit($(root));
	});
}

function addParameter(url, param, value) {
    // Using a positive lookahead (?=\=) to find the
    // given parameter, preceded by a ? or &, and followed
    // by a = with a value after than (using a non-greedy selector)
    // and then followed by a & or the end of the string
    var val = new RegExp('(\\?|\\&)' + param + '=.*?(?=(&|$))'),
        parts = url.toString().split('#'),
        url = parts[0],
        hash = parts[1]
        qstring = /\?.+$/,
        newURL = url;

    // Check if the parameter exists
    if (val.test(url)) {
        // if it does, replace it, using the captured group
        // to determine & or ? at the beginning
        newURL = url.replace(val, '$1' + param + '=' + value);
    } else if (qstring.test(url)) {
        // otherwise, if there is a query string at all
        // add the param to the end of it
        newURL = url + '&' + param + '=' + value;
    } else {
        // if there's no query string, add one
        newURL = url + '?' + param + '=' + value;
    }

    if (hash) {
        newURL += '#' + hash;
    }
    return newURL;
}