var DexEdit = {};

DexEdit.isFirefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;

DexEdit.DOM = {
	getTag: function (node) {
		if (!node) {
			return null;
		}
		return node.nodeName.toLowerCase();
	},

	isBlock: function (node) {
		node = DexEdit.DOM.getTag(node);
		return (node === 'div'	|| node === 'hr'	|| node === 'ul'	|| node === 'ol'
			 || node === 'p'	|| node === 'h3'	|| node === 'h4'	|| node === 'blockquote'
			 || node === 'figure');
	},

	getClosestBlock: function (node, limit) {
		while (node.parentNode) {
			if (limit && node === limit) {
				break;
			} else if (DexEdit.DOM.isBlock(node)) {
				return node;
			}
			node = node.parentNode;
		}
		return limit;
	},

	getPreviousBlock: function (node) {
		node = node.previousSibling;
		while (node) {
			if (DexEdit.DOM.isBlock(node)) {
				return node;
			}
			node = node.previousSibling;
		}
		return node;
	},

	getNextBlock: function (node) {
		node = node.nextSibling;
		while (node) {
			if (DexEdit.DOM.isBlock(node)) {
				return node;
			}
			node = node.nextSibling;
		}
		return node;
	},

	getClosestTag: function (node, tag, limit) {
		if (node) {
			while (node.parentNode) {
				if (limit && node === limit) {
					break;
				} else if (DexEdit.DOM.getTag(node) === tag) {
					return node;
				}
				node = node.parentNode;
			}
		}
		return limit;
	},

	hasParent: function (node, limit) {
		while (node.parentNode) {
			if (node === limit) {
				return true;
			}
			node = node.parentNode;
		}
		return false;
	},

	hasParentTag: function (node, tag, limit) {
		return !!DexEdit.DOM.getClosestTag(node, tag, limit);
	},

	hasParentClass: function (node, classname, limit) {
		while (node.parentNode) {
			if (limit && node === limit) {
				break;
			} else if ($(node).hasClass(classname)) {
				return true;
			}
			node = node.parentNode;
		}
		return false;
	},

	isEditable: function (node) {
		while (node.parentNode) {
			if (node.hasAttribute('contenteditable')) {
				if (node.getAttribute['contenteditable'] === 'false') {
					return false;
				}
				return true;
			}
			node = node.parentNode;
		}
		return false;
	},

	getTextProperty: function (node) {
		if (node.nodeType === Node.TEXT_NODE) {
			return 'data';
		} else if (DexEdit.isFirefox) {
			return 'textContent';
		} else {
			return 'innerText';
		}
	},

	setText: function (node, text) {
		node[DexEdit.DOM.getTextProperty(node)] = text;
	},

	getText: function (node) {
		return node[DexEdit.DOM.getTextProperty(node)];
	}
};

DexEdit.Selection = (function () {
	if (window.getSelection) {
		return window.getSelection();
	} else if (document.selection) { // Opera
		return document.selection.createRange();
	}
})();

DexEdit.Range = {
	set: function (range) {
		DexEdit.Selection.removeAllRanges();
		if (range) {
			DexEdit.Selection.addRange(range);
		}
	},

	get: function () {
		if (!DexEdit.Selection.rangeCount) {
			return null;
		}
		return DexEdit.Selection.getRangeAt(0);
	},

	getTrimmed: function () {
		var text = DexEdit.Selection.toString();
		var beginTrim = text.match(/^(\s*)/)[1].length;
		var endTrim = text.match(/(\s*)$/)[1].length;

		// remove whitespace begin and end of selection
		var range = document.createRange();
		if (!DexEdit.Range.isBackwards()) {
			range.setStart(DexEdit.Selection.anchorNode, DexEdit.Selection.anchorOffset + beginTrim <= DexEdit.DOM.getText(DexEdit.Selection.anchorNode).length ? DexEdit.Selection.anchorOffset + beginTrim : DexEdit.DOM.getText(DexEdit.Selection.anchorNode).length);
			range.setEnd(DexEdit.Selection.focusNode, DexEdit.Selection.focusOffset - endTrim > 0 ? DexEdit.Selection.focusOffset - endTrim : 0);
		} else {
			range.setStart(DexEdit.Selection.focusNode, DexEdit.Selection.focusOffset + beginTrim <= DexEdit.DOM.getText(DexEdit.Selection.focusNode).length ? DexEdit.Selection.focusOffset + beginTrim : DexEdit.DOM.getText(DexEdit.Selection.focusNode).length);
			range.setEnd(DexEdit.Selection.anchorNode, DexEdit.Selection.anchorOffset - endTrim > 0 ? DexEdit.Selection.anchorOffset - endTrim : 0);
		}
		return range;
	},

	getForward: function () {
		var range = document.createRange();
		if (!DexEdit.Range.isBackwards()) {
			range.setStart(DexEdit.Selection.anchorNode, DexEdit.Selection.anchorOffset);
			range.setEnd(DexEdit.Selection.focusNode, DexEdit.Selection.focusOffset);
		} else {
			range.setStart(DexEdit.Selection.focusNode, DexEdit.Selection.focusOffset);
			range.setEnd(DexEdit.Selection.anchorNode, DexEdit.Selection.anchorOffset);
		}
		return range;
	},

	getRect: function (range) {
		if (range) {
			return range.getClientRects()[0];
		}
		return null;
	},

	isBackwards: function () {
		var backwards = false;
		if (!DexEdit.Selection.isCollapsed) {
			var range = document.createRange();
			range.setStart(DexEdit.Selection.anchorNode, DexEdit.Selection.anchorOffset);
			range.setEnd(DexEdit.Selection.focusNode, DexEdit.Selection.focusOffset);
			backwards = range.collapsed;
			range.detach();
		}
		return backwards;
	},

	getSurroundedBy: function (range, tag, limit) {
		var parent = DexEdit.DOM.getClosestTag(range.commonAncestorContainer, tag, limit);
		if (!!parent) {
			return parent;
		}

		var start = DexEdit.DOM.getClosestTag(range.startContainer, tag, limit);
		var end = DexEdit.DOM.getClosestTag(range.endContainer, tag, limit);
		if (!!start && !!end) {
			return start;
		} else if (!!end   && range.startContainer.nextSibling		&& DexEdit.DOM.getTag(range.startContainer.nextSibling) === tag) {
			return end;
		} else if (!!start && range.endContainer.previousSibling	&& DexEdit.DOM.getTag(range.endContainer.previousSibling) === tag) {
			return start;
		}
		return false;
	},

	isSurroundedBy: function (range, tag, limit) {
		return !!DexEdit.Range.getSurroundedBy(range, tag, limit);
	}
};

DexEdit.Typography = function (s) {
	s = s.replace(/(^|[-\u2013\u2014\s(\["])'|()`/g, "$1\u2018");					// opening singles
	s = s.replace(/'/g, "\u2019");													// closing singles & apostrophes
	s = s.replace(/(^|[-\u2013\u2014\/\[(\u2018\s])"|()\u2018\u2018/g, "$1\u201C");	// opening doubles
	s = s.replace(/"|\u2019\u2019/g, "\u201D");										// closing doubles
	s = s.replace(/--/g, "\u2013");													// en-dashes
	s = s.replace(/[\u2013-]{2}/g, "\u2014");										// em-dashes
	s = s.replace(/\.\.\./g, "\u2026");												// ellipsis
	s = s.replace(/,,/g, "\u201E");													// comma quotes
	s = s.replace(/[\u2039<]{2}/g, "\u00AB");										// opening double guillemets
	s = s.replace(/[\u203A>]{2}/g, "\u00BB");										// opening double guillemets
	s = s.replace(/</g, "\u2039");													// opening single guillemets
	s = s.replace(/>/g, "\u203A");													// opening single guillemets
	return s;
};

DexEdit.Text = function (root) {
	var self = this;

	this.root = $(root).attr('contenteditable', 'true');
	this.root.find('figure, hr').attr('contenteditable', 'false');

	this.menu = $('<div class="dexedit-menu">\
		<div class="dexedit-menu-arrow"></div>\
<span class="dexedit-menu-b"><i class="fa fa-fw fa-bold"></i></span>\
<span class="dexedit-menu-i"><i class="fa fa-fw fa-italic"></i></span>\
<span class="dexedit-menu-h3">H3</span><span class="dexedit-menu-h4">H4</span>\
<span class="dexedit-menu-blockquote"><i class="fa fa-fw fa-quote-right"></i></span>\
<span class="dexedit-menu-edit-link"><i class="fa fa-fw fa-edit"></i></span>\
<span class="dexedit-menu-link"><i class="fa fa-fw fa-link"></i></span>\
	</div>').prependTo('body');

	this.range = null;
	this.select = function (range) {
		DexEdit.Range.set(range);
		self.range = range;
		self.redrawMenu();
	};

	this.reselect = function () {
		DexEdit.Range.set(self.range);
		self.redrawMenu();
	};

	this.redrawMenu = function () {
		if (/^[\s]*$/.test(DexEdit.Selection.toString())) {
			self.hideMenu();
			return;
		}

		// showing/hiding of buttons influences self.menu.width() and height()
		self.menu.find('.dexedit-menu-b').toggleClass('enabled', document.queryCommandState('bold'));
		self.menu.find('.dexedit-menu-i').toggleClass('enabled', document.queryCommandState('italic'));
		self.menu.find('.dexedit-menu-h3').toggleClass('enabled', DexEdit.Range.isSurroundedBy(self.range, 'h3', self.root[0]));
		self.menu.find('.dexedit-menu-h4').toggleClass('enabled', DexEdit.Range.isSurroundedBy(self.range, 'h4', self.root[0]));
		self.menu.find('.dexedit-menu-blockquote').toggleClass('enabled', DexEdit.Range.isSurroundedBy(self.range, 'blockquote', self.root[0]));

		if (self.isLink()) {
			self.menu.find('.dexedit-menu-edit-link').show();
			self.menu.find('.dexedit-menu-link').addClass('enabled');
			self.menu.find('.dexedit-menu-link > i').attr('class', 'fa fa-unlink');
		} else {
			self.menu.find('.dexedit-menu-edit-link').hide();
			self.menu.find('.dexedit-menu-link').removeClass('enabled');
			self.menu.find('.dexedit-menu-link > i').attr('class', 'fa fa-link');
		}

		// positioning
		var rect = DexEdit.Range.getRect(self.range);
		var scrollY = window.scrollY || document.documentElement.scrollTop;

		var top = scrollY + rect.top - self.menu.height() - 7;
		if (top - scrollY < 38) { // include admin-bar
			top = scrollY + rect.bottom + 7;
			self.menu.find('.dexedit-menu-arrow').addClass('dexedit-menu-arrow-upsidedown');
		} else {
			self.menu.find('.dexedit-menu-arrow').removeClass('dexedit-menu-arrow-upsidedown');
		}

		var left = rect.left + rect.width / 2 - self.menu.width() / 2;
		if (left < 5) {
			left = 5;
			self.menu.find('.dexedit-menu-arrow').css({
				left: rect.left + rect.width / 2 - left
			});
		} else if (left + self.menu.width() > window.innerWidth - 5) {
			left = window.innerWidth - self.menu.width() - 5;
			self.menu.find('.dexedit-menu-arrow').css({
				left: rect.left + rect.width / 2 - left
			});
		} else {
			self.menu.find('.dexedit-menu-arrow').css({
				left: '50%'
			});
		}

		self.menu.css({
			top: top,
			left: left
		}).stop().fadeIn(100);
	};

	this.hideMenu = function () {
		self.menu.hide();
	};

	this.toggleFormat = function (tag) {
		var action = '';
		switch (tag) {
		case 'b':
			action = 'bold';
			break;
		case 'i':
			action = 'italic';
			break;
		}

		document.execCommand(action, false);
		self.select(DexEdit.Range.get());
	};

	this.toggleFormatBlock = function (tag) {
		var block = DexEdit.DOM.getClosestBlock(self.range.commonAncestorContainer, self.root[0]);
		if (block) {
			if (!DexEdit.Range.isSurroundedBy(self.range, tag)) {
				document.execCommand('formatBlock', false, '<' + tag.toUpperCase() + '>');
			} else {
				document.execCommand('formatBlock', false, '<P>');
				document.execCommand('outdent', false);
			}
			self.select(DexEdit.Range.get());
		}
	};

	this.removeLink = function (link) {
		document.execCommand('unlink', false);
		self.select(DexEdit.Range.get());
	};

	this.insertLink = function (url, title, text) {
		var a = document.createElement('a');
		a.appendChild(document.createTextNode(text));
		a.title = title;
		a.href = url;

		self.range.deleteContents();
		self.range.insertNode(a);
		self.select(self.range);
		self.select(DexEdit.Range.get()); // somehow this is needed
	};

	this.insertList = function (tag) {
		document.execCommand(tag === 'ol' ? 'insertOrderedList' : 'insertUnorderedList');
		DexEdit.DOM.setText(DexEdit.Selection.anchorNode, '');
		self.select(DexEdit.Range.get());
	};

	this.isLink = function () {
		if (DexEdit.isFirefox) {
			if (self.range.startContainer === self.range.endContainer && DexEdit.DOM.getTag(self.range.startContainer) === 'p') {
				var range = document.createRange();
				range.setStart(self.range.startContainer.childNodes[self.range.startOffset], 0);
				range.setEnd(self.range.endContainer.childNodes[self.range.endOffset], 0);
				return DexEdit.Range.isSurroundedBy(range, 'a', self.root[0]);
			}
		}
		return DexEdit.Range.isSurroundedBy(self.range, 'a', self.root[0]);
	};

	this.fancyboxLink = function (selection) {
		var link = DexEdit.Range.getSurroundedBy(self.range, 'a', self.root[0]);
		if (!!link) {
			var range = document.createRange();
			range.selectNodeContents(link);
			self.select(range);

			selection = DexEdit.Selection.toString();
			self.removeLink();
		}

		$.fancybox.open({
			type: 'ajax',
			href: '/' + base_url + 'admin/auxiliary/insert-link/',
			autoScale: false,
			beforeShow: function () {
				$('.fancybox-skin').css('background', 'white');
				$('#insert_text').val(selection);

				if (!!link) {
					$('#insert_title').val(link.getAttribute('title'));
					$('#insert_url').val(link.getAttribute('href'));
					preSwitchPopupFrame($('.popup'));
				}
				applyTooltips();
			},
			beforeClose: function () {
				if ($('#insert_submit').val() == 1 && $('#insert_url').val()) {
					var title = $('#insert_title').val();
					var url = $('#insert_url').val();
					var text = $('#insert_text').val();

					self.insertLink(url, title, text);
					self.root.trigger('input');
				}
			},
			helpers:  {
				overlay: {
					locked: false
				}
			}
		});
	};

	this.fancyboxImage = function (selection) {
		$.fancybox.open({
			type: 'ajax',
			href: '/' + base_url + 'admin/auxiliary/insert-image/',
			autoScale: false,
			beforeShow: function () {
				$('.fancybox-skin').css('background', 'white');
				$('#insert_caption').val(selection);
			},
			beforeClose: function () {
				if ($('#insert_submit').val() == 1 && $('#insert_url').val()) {
					var title = $('#insert_title').val();
					var url = $('#insert_url').val();
					var alt = $('#insert_alt').val();
					var caption = $('#insert_caption').val();
					if (caption.length) {
						caption = '<figcaption>' + caption + '</figcaption>';
					}

					$('<img src="' + url + '" title="' + title + '" alt="' + alt + '">').one('load', function () {
						var img = $(this);
						img.attr('width', this.width).attr('height', this.height);

						var figure = $('<figure contenteditable="false"></figure>').append(img);
						var block = DexEdit.DOM.getClosestBlock(self.range.commonAncestorContainer);
						if (block === null || block == self.root[0]) {
							figure.appendTo(self.root);
						} else {
							figure.insertAfter(block);
						}
						new DexEdit.Image(self.root, img);

						DexEdit.Selection.removeAllRanges();
						self.menu.stop().fadeOut(100);
						self.root.trigger('input');
					});
				}
			},
			helpers:  {
				overlay: {
					locked: false
				}
			}
		});
	};

	this.fancyboxAsset = function (selection) {
		$.fancybox.open({
			type: 'ajax',
			href: '/' + base_url + 'admin/auxiliary/insert-asset/',
			autoScale: false,
			beforeShow: function () {
				$('.fancybox-skin').css('background', 'white');
				$('#insert_text').val(selection);
				applyTooltips();
			},
			beforeClose: function () {
				if ($('#insert_submit').val() == 1 && $('#insert_url').val()) {
					var title = $('#insert_title').val();
					var url = $('#insert_url').val();
					var text = $('#insert_text').val();

					self.insertLink(url, title, text);
					self.root.trigger('input');
				}
			},
			helpers:  {
				overlay: {
					locked: false
				}
			}
		});
	};

	this.selection = function () {
		if (!DexEdit.Selection.isCollapsed && DexEdit.Selection.anchorNode !== self.root[0]) { // not root for selecting figure in FF
			var range = DexEdit.Range.getTrimmed();
			self.select(range);
		}
	};

	this.root.on('selectstart', function (e) {
		if (!DexEdit.DOM.hasParentClass(e.target, 'dexedit-menu', self.root[0])) {
			self.hideMenu();
			$(document).one('mouseup', function () {
				self.selection();
			});
		}
	});

	// mouse
	$('html').on('mousedown', function (e) {
		if (!DexEdit.DOM.hasParentClass(e.target, 'fancybox-wrap', self.root[0]) && !DexEdit.DOM.hasParentClass(e.target, 'dexedit-menu', self.root[0])) {
			self.menu.stop().fadeOut(100);
		}
	});

	this.root.on('mousedown', function (e) {
		var tag = DexEdit.DOM.getTag(e.target);
		if (!((tag === 'i' || tag === 'span') && DexEdit.DOM.hasParentClass(e.target, 'dexedit-menu', self.root[0])) && self.range) {
			e.stopPropagation();
			self.menu.stop().fadeOut(100);
		}
	});

	this.root.on('mouseup', function (e) {
		if (DexEdit.isFirefox && !DexEdit.DOM.hasParentClass(e.target, 'dexedit-menu', self.root[0])) {
			self.root.trigger('selectstart'); // FF
		}
	});

	this.menu.on('click', 'span', function (e) {
		e.stopPropagation();
		if (e.which === 1 && self.range) {
			// reselect text after blur due to button click
			self.reselect();

			var target = $(this);
			if (target.hasClass('dexedit-menu-b')) {
				self.toggleFormat('b');
			} else if (target.hasClass('dexedit-menu-i')) {
				self.toggleFormat('i');
			} else if (target.hasClass('dexedit-menu-h3')) {
				self.toggleFormatBlock('h3');
			} else if (target.hasClass('dexedit-menu-h4')) {
				self.toggleFormatBlock('h4');
			} else if (target.hasClass('dexedit-menu-blockquote')) {
				self.toggleFormatBlock('blockquote');
			} else if (target.hasClass('dexedit-menu-edit-link')) {
				self.fancyboxLink(DexEdit.Selection.toString());
			} else if (target.hasClass('dexedit-menu-link')) {
				if (self.isLink()) {
					self.removeLink();
				} else {
					self.fancyboxLink(DexEdit.Selection.toString());
				}
			}
		}
	});

	$('.dex-admin-bar .dexedit-insert').on('click', 'a', function (e) {
		e.preventDefault();
		e.stopPropagation();

		if (e.which === 1) {
			self.select(DexEdit.Range.get());
			var block = DexEdit.DOM.getClosestBlock(self.range.commonAncestorContainer, self.root[0]);
			console.log(block);
			if (!self.range || !DexEdit.DOM.hasParent(block, self.root[0]) || block == self.root[0] || (DexEdit.isFirefox && DexEdit.DOM.getTag(block) === 'figure')) {
				// nothing selected, append to root
				var last = DexEdit.DOM.getClosestBlock(self.root.find('*:last'), self.root[0]);
				if (!last || DexEdit.DOM.getTag(last[0]) !== 'p') {
					last = $('<p></p>').appendTo(self.root);
					while (last[0].firstChild) {
						last[0].removeChild(last[0].childNodes[0]);
					}
				}

				var range = document.createRange();
				range.selectNodeContents(last[0]);
				range.collapse(false);
				self.select(range);
			} else {
				if (self.isLink()) {
					self.removeLink();
				}

				var tag = DexEdit.DOM.getTag(block);
				if (tag !== 'p' && tag !== 'h3' && tag !== 'h4' && tag !== 'blockquote') {
					var figure = DexEdit.DOM.getClosestTag(block, 'figure');
					if (!!figure) {
						block = figure;
					}

					var p = $('<p></p>').insertAfter($(block));
					while (p[0].firstChild) {
					   p[0].removeChild(p[0].childNodes[0]);
					}

					var range = document.createRange();
					range.selectNodeContents(p[0]);
					range.collapse(false);
					self.select(range);
				}
			}

			var target = $(this);
			if (target.hasClass('dexedit-menu-link')) {
				self.fancyboxLink(DexEdit.Selection.toString());
			} else if (target.hasClass('dexedit-menu-image')) {
				self.fancyboxImage(DexEdit.Selection.toString());
			} else if (target.hasClass('dexedit-menu-asset')) {
				self.fancyboxAsset(DexEdit.Selection.toString());
			}
		}
	});

	// keyboard
	this.root.on('keydown', function (e) {
		if (e.keyCode === 8 || e.keyCode === 46) { // backspace or delete
			self.hideMenu();

			var block = DexEdit.DOM.getClosestBlock(DexEdit.Range.get().commonAncestorContainer);
			if (block) {
				var sibling = (e.keyCode === 8 ? block.previousSibling : block.nextSibling);
				if (sibling) {
					if (DexEdit.DOM.getTag(sibling) === 'figure') {
						e.preventDefault();

						var secondSibling = (e.keyCode === 8 ? sibling.previousSibling : sibling.nextSibling);
						sibling.remove();
						if (secondSibling && DexEdit.DOM.getTag(sibling) === 'figure') {
							secondSibling.remove();
						}
						self.root.trigger('input');
					} else if (DexEdit.DOM.getTag(sibling) === 'hr') {
						e.preventDefault();

						sibling.remove();
						self.root.trigger('input');
					}
				}
			}
		}
	});

	this.root.on('keyup', function (e) {
		if (e.keyCode === 13) {
			var p = DexEdit.DOM.getClosestTag(DexEdit.Selection.anchorNode, 'p', self.root[0]);
			if (p && p.previousSibling && DexEdit.DOM.getTag(p.previousSibling) === 'p' && !p.previousSibling.textContent.length) {
				var prev = p.previousSibling;
				if (!prev.previousSibling || DexEdit.DOM.getTag(prev.previousSibling) !== 'hr') {
					var hr = document.createElement('hr');
					hr.contentEditable = false;
					prev.parentNode.replaceChild(hr, prev);
				}
			}

			var range = DexEdit.Range.getForward();
			self.select(range);

			var block = DexEdit.DOM.getClosestBlock(self.range.commonAncestorContainer);
			if (DexEdit.DOM.getTag(block) === 'div' || DexEdit.DOM.getTag(block) === 'blockquote') {
				self.toggleFormatBlock('p');
			}
		}

		if (e.keyCode >= 37 && e.keyCode <= 40) {
			if (e.shiftKey) { // keyboard selection of text
				self.selection();
			} else {
				self.hideMenu();
			}
		}

		if (DexEdit.Selection.isCollapsed && DexEdit.Selection.anchorNode) {
			if (DexEdit.DOM.getTag(DexEdit.Selection.anchorNode.parentNode) === 'p') {
				var text = DexEdit.DOM.getText(DexEdit.Selection.anchorNode);
				if (text.match(/^[-*]\s/)) {
					self.insertList('ul');
				} else if (text.match(/^1\.\s/)) {
					self.insertList('ol');
				}
			}

			var text = DexEdit.DOM.getText(DexEdit.Selection.anchorNode);
			var oldText = text;

			text = DexEdit.Typography(text);
			if (text !== oldText) {
				var offset = DexEdit.Selection.anchorOffset + (text.length - oldText.length);

				DexEdit.DOM.setText(DexEdit.Selection.anchorNode, text);

				var range = document.createRange();
				range.setStart(DexEdit.Selection.anchorNode, offset);
				range.setEnd(DexEdit.Selection.anchorNode, offset);
				self.select(range);
			}
		}
	});

	this.root[0].addEventListener('cut', function (e) {
		self.hideMenu();
	});

	this.root[0].addEventListener('paste', function (e) {
		e.preventDefault();
		self.hideMenu();

		var content = '';
		if (/text\/html/.test(e.clipboardData.types)) {
			content = e.clipboardData.getData('text/html');
		} else if (/text\/plain/.test(e.clipboardData.types)) {
			content = e.clipboardData.getData('text/plain');
		}

		content = $('<div>').html(content).html();

		// remove Word or other formatting
		content = content.replace(/(<!--[^]*?-->|\s{2,}|\r|\n)/gi, '');
		content = content.replace(/<(style|script|applet|embed|noframes|noscript|w:[a-z]+)[^]*?\/$1>/gi, '');
		content = content.replace(/<(\/)*(meta|link|span|\\?xml:|st1:|o:|font)([^]*?)>/gi, '');

		// only certain tags are allowed
		content = content.replace(/<(\/?)([^>\s]+)[^>]*>/gi, function (all, close, tag) {
			tag = tag.toLowerCase();
			if (tag === 'p' || tag === 'ul' || tag === 'ol' || tag === 'li' || tag === 'blockquote' || tag === 'strong' || tag === 'i' || tag === 'h1' || tag === 'h2' || tag === 'h3' || tag === 'h4') {
				if (tag === 'h1') {
					tag = 'h3';
				} else if (tag === 'h2') {
					tag = 'h4';
				}

				return '<' + close + tag + '>';
			}
			return '';
		});

		// no redundant whitespaces
		content = content.replace(/<p>(&nbsp;)?<\/p>/gi, '');
		content = content.trim();

		// TODO: Word copy; a garbage line remains at the end

		document.execCommand('insertHTML', false, content);
	});
};

DexEdit.Image = function (root, img) {
	var self = this;

	this.root = $(root);
	this.img = $(img).wrap('<div class="dexedit-img" contenteditable="false"></div>');
	this.wrapper = this.img.parent();
	this.figure	= (this.wrapper.parent()[0] && DexEdit.DOM.getTag(this.wrapper.parent()[0]) === 'figure' ? self.wrapper.parent() : null);
	if (this.figure === null) {
		return;
	}

	this.placeholder = $('<figure class="dexedit-img-placeholder"></figure>').css({
		height: this.figure.css('height')
	}).insertBefore(this.figure);

	this.img_resize = $('<div class="dexedit-img-resize">\
		<div class="dexedit-img-resize-tl"></div><div class="dexedit-img-resize-tr"></div><div class="dexedit-img-resize-bl"></div><div class="dexedit-img-resize-br"></div>\
	</div>').prependTo(this.wrapper);
	this.img_menu = $('<div class="dexedit-img-menu">\
<span class="dexedit-img-menu-left"><i class="fa fa-fw fa-chevron-left"></i></span>\
<span class="dexedit-img-menu-center"><i class="fa fa-fw fa-square"></i></span>\
<span class="dexedit-img-menu-right"><i class="fa fa-fw fa-chevron-right"></i></span>\
<span class="dexedit-img-menu-edit"><i class="fa fa-fw fa-edit"></i></span>\
<span class="dexedit-img-menu-trash"><i class="fa fa-fw fa-trash-o"></i></span>\
	</div>').prependTo(this.wrapper);

	this.img_ratio = this.img[0].height / this.img[0].width;

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

		self.img.attr('width', width);
		self.img.attr('height', height);

		self.placeholder.css({
			height: this.figure.css('height')
		});
	};

	this.resize = function (e) {
		if (!self.resizing) {
			$(document).unbind('mousemove', self.resize);
			return;
		}

		var scrollY = window.scrollY || document.documentElement.scrollTop;

		var diff_x = (e.pageX - self.drag_start_x) * self.drag_sign_x;
		var diff_y = (e.pageY - scrollY - self.drag_start_y) * self.drag_sign_y;

		var width = self.drag_start_w + diff_x;
		if (width > self.figure.width()) { // not wider than the page
			width -= width - self.figure.width();
		}
		var height = width * self.img_ratio;
		self.setDimensions(width, height);
	};

	this.drag = function (e) {
		if (!self.dragging) {
			$(document).unbind('mousemove', self.drag);
			return;
		}

		var scrollY = window.scrollY || document.documentElement.scrollTop;

		var x = self.drag_offset_x + (e.pageX - self.drag_start_x);
		var y = self.drag_offset_y + (e.pageY - self.drag_start_y);

		var previous = DexEdit.DOM.getPreviousBlock(self.placeholder[0]);
		while (previous && (previous === self.figure[0] || $(previous).hasClass('dexedit-img-placeholder'))) {
			previous = DexEdit.DOM.getPreviousBlock(previous);
		}
		if (previous) {
			var rect = previous.getBoundingClientRect();
			if (y + self.placeholder.height() / 2 < rect.top + rect.height + scrollY) {
				self.placeholder.insertBefore(previous);
			}
		}

		var next = DexEdit.DOM.getNextBlock(self.placeholder[0]);
		while (next && (next === self.figure[0] || $(next).hasClass('dexedit-img-placeholder'))) {
			next = DexEdit.DOM.getNextBlock(next);
		}
		if (next) {
			var rect = next.getBoundingClientRect();
			if (y + self.placeholder.height() / 2 > rect.top + rect.height + scrollY) {
				self.placeholder.insertAfter(next);
			}
		}

		self.figure.css({
			top: y,
			left: x
		});
	};

	this.showMenu = function () {
		var rect = self.img[0].getBoundingClientRect();

		var top = 10 + 5; // within image
		if (rect.height < self.img_menu.height() + 10 || rect.width < self.img_menu.width() + 10) {
			top = 10 - self.img_menu.height() - 5; // above image
			if (rect.top - self.img_menu.height() - 10 < 38) { // include admin-bar
				top = 10 + rect.height + 5; // under image
			}
		}

		var left = 10 + (rect.width - self.img_menu.width()) / 2;
		if (rect.left + left < 5 + 10) {
			left = 5 - rect.left + 10;
		} else if (rect.left + left + self.img_menu.width() > window.innerWidth - 5 - 10) {
			left = window.innerWidth - rect.left - self.img_menu.width() - 5 - 10;
		}

		self.img_menu.stop().css({
			top: top,
			left: left
		}).fadeIn(100);
	};

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
			if (!self.dragging) {
				if (!self.resizing) {
					self.showMenu();
				}
				self.img_resize.stop().fadeIn(100);
			}
		},
		mouseleave: function (e) {
			self.hovering = false;
			if (!self.dragging) {
				self.img_menu.stop().fadeOut(100);
				if (!self.resizing) {
					self.img_resize.stop().fadeOut(100);
				}
			}
		}
	});

	this.wrapper.on('mousedown', '>div, img', function (e) {
		e.preventDefault();
		e.stopPropagation();

		$('.dexedit-menu').hide();
		DexEdit.Selection.removeAllRanges();

		if (e.which === 1) {
			var target = $(this);
			var buttonTarget = $(e.target);
			var scrollY = window.scrollY || document.documentElement.scrollTop;

			if (target.hasClass('dexedit-img-resize')) {
				if (buttonTarget.hasClass('dexedit-img-resize-tl')) {
					$('html').addClass('dexedit-img-resize-nwse');
					self.img.addClass('dexedit-img-resize-nwse');
					self.drag_sign_x = -1;
					self.drag_sign_y = -1;
				} else if (buttonTarget.hasClass('dexedit-img-resize-tr')) {
					$('html').addClass('dexedit-img-resize-nesw');
					self.img.addClass('dexedit-img-resize-nesw');
					self.drag_sign_x = 1;
					self.drag_sign_y = -1;
				} else if (buttonTarget.hasClass('dexedit-img-resize-bl')) {
					$('html').addClass('dexedit-img-resize-nesw');
					self.img.addClass('dexedit-img-resize-nesw');
					self.drag_sign_x = -1;
					self.drag_sign_y = 1;
				} else if (buttonTarget.hasClass('dexedit-img-resize-br')) {
					$('html').addClass('dexedit-img-resize-nwse');
					self.img.addClass('dexedit-img-resize-nwse');
					self.drag_sign_x = 1;
					self.drag_sign_y = 1;
				}
			} else if (target.hasClass('dexedit-img-menu')) {
				if (DexEdit.DOM.getTag(e.target) === 'i') {
					buttonTarget = $(e.target.parentNode);
				}

				if (buttonTarget.hasClass('dexedit-img-menu-left')) {
					self.figure.css('float', 'left');
				} else if (buttonTarget.hasClass('dexedit-img-menu-center')) {
					self.figure.css('float', '');
				} else if (buttonTarget.hasClass('dexedit-img-menu-right')) {
					self.figure.css('float', 'right');
				} else if (buttonTarget.hasClass('dexedit-img-menu-edit')) {
					$.fancybox.open({
						'type': 'ajax',
						'href': '/' + base_url + 'admin/auxiliary/insert-image/',
						beforeShow: function () {
							$('.fancybox-skin').css('background', 'white');
							$('#insert_title').val(self.img.attr('title'));
							$('#insert_alt').val(self.img.attr('alt'));
							if (self.figure.find('figcaption').length) {
								$('#insert_caption').val(self.figure.find('figcaption').html());
							}
							preSwitchPopupFrame($('.popup'));
						},
						beforeClose: function () {
							if ($('#insert_submit').val() == 1) {
								self.img.attr('title', $('#insert_title').val());
								self.img.attr('alt', $('#insert_alt').val());

								var caption = $('#insert_caption').val();
								if (caption.length) {
									if (!self.figure.find('figcaption').length) {
										self.figure.append('<figcaption></figcaption>');
									}
									self.figure.find('figcaption').html(caption);
								} else {
									self.figure.find('figcaption').remove();
								}
								self.root.trigger('input');
							}
						},
						helpers:  {
							overlay: {
								locked: false
							}
						}
					});
				} else if (buttonTarget.hasClass('dexedit-img-menu-trash')) {
					self.figure.remove();
					self.placeholder.remove();
				} else {
					return;
				}
				self.root.trigger('input');
				return;
			} else if (DexEdit.DOM.getTag(target[0]) === 'img') {
				if (self.hovering) {
					self.img_menu.stop().fadeOut(100);
					self.img_resize.stop().fadeOut(100);
				}

				self.dragging = true;
				self.drag_start_x = e.pageX;
				self.drag_start_y = e.pageY - scrollY;
				self.drag_offset_x = self.figure.offset().left;
				self.drag_offset_y = self.wrapper.offset().top + 10 - scrollY;

				self.figure.prependTo('body').css({
					position: 'absolute',
					zIndex: '1000',
					margin: '0',
					top: self.drag_offset_y + scrollY,
					left: self.drag_offset_x
				});
				self.placeholder.show();

				$(document).bind('mousemove', self.drag);
				return;
			} else {
				return;
			}

			if (self.hovering) {
				self.img_menu.stop().fadeOut(100);
			}

			self.resizing = true;
			self.drag_start_x = e.pageX;
			self.drag_start_y = e.pageY - scrollY;
			self.drag_start_w = self.img[0].width;
			self.drag_start_h = self.img[0].height;

			$(document).bind('mousemove', self.resize);
		}
	});

	$('html').on('mouseup', function (e) {
		if (self.resizing) {
			e.preventDefault();
			$(document).unbind('mousemove', self.resize);

			$('html').removeClass('dexedit-img-resize-nwse');
			$('html').removeClass('dexedit-img-resize-nesw');
			self.img.removeClass('dexedit-img-resize-nwse');
			self.img.removeClass('dexedit-img-resize-nesw');

			if (self.hovering) {
				self.showMenu();
			} else {
				self.img_resize.stop().fadeOut(100);
			}

			var src = self.img.attr('src');
			var len = src.indexOf('/', src.lastIndexOf('.'));
			if (len === -1) {
				len = src.length;
			}
			self.img.attr('src', src.substr(0, len) + '/w=' + self.img[0].width + '/');

			self.root.trigger('input');
			self.resizing = false;
		} else if (self.dragging) {
			e.preventDefault();
			$(document).unbind('mousemove', self.drag);

			if (self.hovering) {
				self.showMenu();
				self.img_resize.stop().fadeIn(100);
			}

			self.figure.insertAfter(self.placeholder);

			self.placeholder.hide();
			self.figure.css({
				position: '',
				zIndex: '',
				margin: '',
				left: '',
				right: ''
			});

			self.root.trigger('input');
			self.dragging = false;
		}
	});
};

DexEdit.init = function () {
	$('[data-dexeditable]').each(function (i, root) {
		new DexEdit.Text(root);
		$(root).find('img').each(function (i, img) {
			new DexEdit.Image(root, img);
		});
	});
};

DexEdit.destroySingle = function (root) {
	$('body').find('.dexedit-menu').remove();
	root.find('.dexedit-img-placeholder, .dexedit-img-resize, .dexedit-img-menu').remove();
	root.find('figure, hr').attr('contenteditable', null);
	root.find('.dexedit-img > img').unwrap();
	root.find('figure').css('top', '');
	root.attr('contenteditable', null);
};

DexEdit.destroy = function () {
	$('[data-dexeditable]').each(function (i, root) {
		DexEdit.destroySingle($(root));
	});
};

DexEdit.getContent = function (selector) {
	var content = $(selector).clone().detach();
	DexEdit.destroySingle(content);
	if (typeof content.html() !== 'undefined' && content.text() !== '')
		return content.html();
	return '';
};