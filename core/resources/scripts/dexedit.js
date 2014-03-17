function getCharacterOffsetWithin(range, node) {
    var nodeRange = rangy.createRange();
    nodeRange.selectNodeContents(node);
    nodeRange.setEnd(range.startContainer, range.startOffset);

    var charCount = 0, textNodes = nodeRange.getNodes([3]);
    for (var i = 0, len = textNodes.length; i < len; ++i) {
        if (textNodes[i] == range.startContainer) {
            charCount += range.startOffset;
            console.log('a: ' + range.startOffset);
        } else {
            charCount += textNodes[i].length;
            console.log('b: ' + textNodes[i].length);
        }
    }
    return charCount;
}

var DexEdit = function (root) {
	var self = this;

	this.isFirefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;

	this.root = $(root).attr('contenteditable', 'true');
	this.menu = $('<div class="dexedit_menu">\
		<div class="arrow"></div>\
		<span id="dexedit_menu_b"><i class="fa fa-bold"></i></span><span id="dexedit_menu_i"><i class="fa fa-italic"></i></span><span id="dexedit_menu_h3">H3</span><span id="dexedit_menu_h4">H4</span><span id="dexedit_menu_blockquote"><i class="fa fa-quote-right"></i></span><span id="dexedit_menu_link"><i class="fa fa-link"></i></span><span id="dexedit_menu_image"><i class="fa fa-picture-o"></i></span>\
	</div>').prependTo('body');

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

		if (!range.collapsed && !/^[\s]*$/.test(self.selection.toString())) {
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
			} else if (node.nodeName === 'DIV' || node.nodeName === 'P' || node.nodeName === 'H3' || node.nodeName === 'H4' || node.nodeName === 'BLOCKQUOTE') {
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
		var rect = range.getBoundingClientRect();

		var top = rect.top - self.menu.height() - 6;
		if (top < 37) { // include admin-bar
			top = rect.bottom + 6;
			self.menu.find('.arrow').addClass('upsidedown');
		} else {
			self.menu.find('.arrow').removeClass('upsidedown');
		}

		var left = rect.left + rect.width / 2 - self.menu.width() / 2;
		if (left < 5) {
			left = 5;
			self.menu.find('.arrow').css({
				left: rect.left + rect.width / 2 - 5
			});
		} else if (left + self.menu.width() > window.innerWidth - 5) {
			left = window.innerWidth - self.menu.width() - 5;
			self.menu.find('.arrow').css({
				left: rect.left + rect.width / 2 - 5
			});
		} else {
			self.menu.find('.arrow').css({
				left: '50%'
			});
		}

    	if (document.queryCommandState('bold')) {
			$('#dexedit_menu_b').addClass('enabled');
		} else {
			$('#dexedit_menu_b').removeClass('enabled');
		}

    	if (document.queryCommandState('italic')) {
			$('#dexedit_menu_i').addClass('enabled');
		} else {
			$('#dexedit_menu_i').removeClass('enabled');
		}

		if (self.hasParentTag(self.range.commonAncestorContainer, 'H3')) {
			$('#dexedit_menu_h3').addClass('enabled');
		} else {
			$('#dexedit_menu_h3').removeClass('enabled');
		}

		if (self.hasParentTag(self.range.commonAncestorContainer, 'H4')) {
			$('#dexedit_menu_h4').addClass('enabled');
		} else {
			$('#dexedit_menu_h4').removeClass('enabled');
		}

		if (self.hasParentTag(self.range.commonAncestorContainer, 'BLOCKQUOTE')) {
			$('#dexedit_menu_blockquote').addClass('enabled');
		} else {
			$('#dexedit_menu_blockquote').removeClass('enabled');
		}

    	if (self.hasParentTag(self.range.commonAncestorContainer, 'A')) {
			$('#dexedit_menu_link').addClass('enabled');
			$('#dexedit_menu_link > i').attr('class', 'fa fa-unlink');
		} else {
			$('#dexedit_menu_link').removeClass('enabled');
			$('#dexedit_menu_link > i').attr('class', 'fa fa-link');
		}

		self.menu.css({
			top: top,
			left: left
		}).fadeIn('fast');
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

		var startContainer = self.range.startContainer;
		var startOffset = self.range.startOffset;
		var endContainer = self.range.endContainer;
		var endOffset = self.range.endOffset;

		document.execCommand(action, false);

		var range = document.createRange();
		range.setStart(startContainer, startOffset);
		range.setEnd(endContainer, endOffset);
		self.setRange(range);
	}

	this.toggleFormatBlock = function (tag) {
		var block = self.getBlock();
		if (block) {
			var parent = block.parentNode;
			block = block.previousSibling;

			if (!self.hasParentTag(self.range.commonAncestorContainer, tag)) {
				document.execCommand('formatBlock', false, tag);
			} else {
				document.execCommand('formatBlock', false, 'P');
				document.execCommand('outdent', false);
			}

			if (block === null) {
				block = parent.childNodes[0];
			} else {
				block = block.nextSibling;
			}

			var range = document.createRange();
			range.selectNodeContents(block);
			self.setRange(range);
		}
	}

	this.toggleLink = function (link) {
		var startContainer = self.range.startContainer;
		var startOffset = self.range.startOffset;
		var endContainer = self.range.endContainer;
		var endOffset = self.range.endOffset;

		if (self.hasParentTag(self.range.commonAncestorContainer, 'A')) {
			document.execCommand('unlink', false);
		} else {
			document.execCommand('createLink', false, link);
		}

		var range = document.createRange();
		range.setStart(startContainer, startOffset);
		range.setEnd(endContainer, endOffset);
		self.setRange(range);
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

		// make caret visible
		var range = document.createRange();
		range.setStart(self.selection.anchorNode, 0);
		range.setEnd(self.selection.anchorNode, 0);
		self.setRange(range);
	}

	this.root.on('selectstart', function () {
		self.hideMenu();
		$(document).one('mouseup', function() {
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
			self.menu.fadeOut('fast');
		}
	});

	$(document).on('click', '.fancybox-wrap', function(e) {
		//self.setRange(self.range);
	});

	this.root.on('mousedown', function(e) {
		e.stopPropagation();
		if (self.range) {
			self.menu.fadeOut('fast');
		}
	});

	this.menu.on('mousedown', function(e) {
		e.stopPropagation();
		self.setRange(self.range);
	});

	this.menu.on('click', '.arrow', function(e) {
		e.stopPropagation();
		self.setRange(self.range);
	});

	this.menu.on('click', 'span', function(e) {
		e.stopPropagation();

		if (e.which == 1 && self.range) {
			// reselect text after blur due to button click
			self.setRange(self.range);

			switch ($(this).attr('id')) {
			case 'dexedit_menu_b':
				self.toggleFormat('B');
				break;
			case 'dexedit_menu_i':
				self.toggleFormat('I');
				break;
			case 'dexedit_menu_h3':
				self.toggleFormatBlock('H3');
				break;
			case 'dexedit_menu_h4':
				self.toggleFormatBlock('H4');
				break;
			case 'dexedit_menu_blockquote':
				self.toggleFormatBlock('BLOCKQUOTE');
				break;
			case 'dexedit_menu_link':
				self.setRange(self.range);
				if (self.hasParentTag(self.range.commonAncestorContainer, 'A')) {
					self.toggleLink();
				} else {
					$.fancybox.open({
						'type': 'ajax',
						'href': '/' + base_url + 'admin/auxiliary/insert-link/',
						beforeShow: function() {
							$('.fancybox-skin').css('background', 'white');
							$('#insert_text').val(self.selection.toString());
							applyTooltips();
						},
						beforeClose: function() {
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
				break;
			case 'dexedit_menu_image':
				self.setRange(self.range);
				//if (self.hasParentTag(self.range.commonAncestorContainer, 'A')) {
				//	self.toggleLink();
				//} else {
					$.fancybox.open({
						'type': 'ajax',
						'href': '/' + base_url + 'admin/auxiliary/insert-image/',
						beforeShow: function() {
							$('.fancybox-skin').css('background', 'white');
							$('#insert_text').val(self.selection.toString());
						},
						beforeClose: function() {
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
				//}
				break;
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

$(function () {
	$('[data-dexeditable]').each(function (i, root) {
		new DexEdit(root);
	});
});