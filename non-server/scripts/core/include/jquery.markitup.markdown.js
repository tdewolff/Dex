// -------------------------------------------------------------------
// markItUp!
// -------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// -------------------------------------------------------------------
// MarkDown tags example
// http://en.wikipedia.org/wiki/Markdown
// http://daringfireball.net/projects/markdown/
// -------------------------------------------------------------------
// Feel free to add more tags
// -------------------------------------------------------------------
mySettings = {
	previewParserPath:	'',
	onShiftEnter:		{keepDefault:false, openWith:'\n\n'},
	markupSet: [
		{name:'First Level Heading', key:'3', placeHolder:'Your title here...', closeWith:function(markItUp) { return miu.markdownTitle(markItUp, '=') } },
		{name:'Second Level Heading', key:'4', placeHolder:'Your title here...', closeWith:function(markItUp) { return miu.markdownTitle(markItUp, '-') } },
		{name:'Heading 3', key:'5', openWith:'### ', placeHolder:'Your title here...' },
		{name:'Heading 4', key:'6', openWith:'#### ', placeHolder:'Your title here...' },
		{separator:'---------------' },
		{name:'Bold', key:'B', openWith:'**', closeWith:'**'},
		{name:'Italic', key:'I', openWith:'_', closeWith:'_'},
		{separator:'---------------' },
		{name:'Bulleted List', openWith:'- ' },
		{name:'Numeric List', openWith:function(markItUp) {
			return markItUp.line+'. ';
		}}
	]
};

// mIu nameSpace to avoid conflict.
miu = {
	markdownTitle: function(markItUp, c) {
		heading = '';
		n = $.trim(markItUp.selection||markItUp.placeHolder).length;
		for(i = 0; i < n; i++) {
			heading += c;
		}
		return '\n'+heading;
	}
};