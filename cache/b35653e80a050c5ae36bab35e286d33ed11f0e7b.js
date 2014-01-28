/*! fancyBox v2.1.5 fancyapps.com | fancyapps.com/fancybox/#license */
(function(r,G,f,v){var J=f("html"),n=f(r),p=f(G),b=f.fancybox=function(){b.open.apply(this,arguments)},I=navigator.userAgent.match(/msie/i),B=null,s=G.createTouch!==v,t=function(a){return a&&a.hasOwnProperty&&a instanceof f},q=function(a){return a&&"string"===f.type(a)},E=function(a){return q(a)&&0<a.indexOf("%")},l=function(a,d){var e=parseInt(a,10)||0;d&&E(a)&&(e*=b.getViewport()[d]/100);return Math.ceil(e)},w=function(a,b){return l(a,b)+"px"};f.extend(b,{version:"2.1.5",defaults:{padding:15,margin:20,
width:800,height:600,minWidth:20,minHeight:20,maxWidth:9999,maxHeight:9999,pixelRatio:1,autoSize:!0,autoHeight:!1,autoWidth:!1,autoResize:!0,autoCenter:!s,fitToView:!0,aspectRatio:!1,topRatio:0.5,leftRatio:0.5,scrolling:"auto",wrapCSS:"",arrows:!0,closeBtn:!0,closeClick:!1,nextClick:!1,mouseWheel:!0,autoPlay:!1,playSpeed:3E3,preload:3,modal:!1,loop:!0,ajax:{dataType:"html",headers:{"X-fancyBox":!0}},iframe:{scrolling:"auto",preload:!0},swf:{wmode:"transparent",allowfullscreen:"true",allowscriptaccess:"always"},
keys:{next:{13:"left",34:"up",39:"left",40:"up"},prev:{8:"right",33:"down",37:"right",38:"down"},close:[27],play:[32],toggle:[70]},direction:{next:"left",prev:"right"},scrollOutside:!0,index:0,type:null,href:null,content:null,title:null,tpl:{wrap:'<div class="fancybox-wrap" tabIndex="-1"><div class="fancybox-skin"><div class="fancybox-outer"><div class="fancybox-inner"></div></div></div></div>',image:'<img class="fancybox-image" src="{href}" alt="" />',iframe:'<iframe id="fancybox-frame{rnd}" name="fancybox-frame{rnd}" class="fancybox-iframe" frameborder="0" vspace="0" hspace="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen'+
(I?' allowtransparency="true"':"")+"></iframe>",error:'<p class="fancybox-error">The requested content cannot be loaded.<br/>Please try again later.</p>',closeBtn:'<a title="Close" class="fancybox-item fancybox-close" href="javascript:;"></a>',next:'<a title="Next" class="fancybox-nav fancybox-next" href="javascript:;"><span></span></a>',prev:'<a title="Previous" class="fancybox-nav fancybox-prev" href="javascript:;"><span></span></a>'},openEffect:"fade",openSpeed:250,openEasing:"swing",openOpacity:!0,
openMethod:"zoomIn",closeEffect:"fade",closeSpeed:250,closeEasing:"swing",closeOpacity:!0,closeMethod:"zoomOut",nextEffect:"elastic",nextSpeed:250,nextEasing:"swing",nextMethod:"changeIn",prevEffect:"elastic",prevSpeed:250,prevEasing:"swing",prevMethod:"changeOut",helpers:{overlay:!0,title:!0},onCancel:f.noop,beforeLoad:f.noop,afterLoad:f.noop,beforeShow:f.noop,afterShow:f.noop,beforeChange:f.noop,beforeClose:f.noop,afterClose:f.noop},group:{},opts:{},previous:null,coming:null,current:null,isActive:!1,
isOpen:!1,isOpened:!1,wrap:null,skin:null,outer:null,inner:null,player:{timer:null,isActive:!1},ajaxLoad:null,imgPreload:null,transitions:{},helpers:{},open:function(a,d){if(a&&(f.isPlainObject(d)||(d={}),!1!==b.close(!0)))return f.isArray(a)||(a=t(a)?f(a).get():[a]),f.each(a,function(e,c){var k={},g,h,j,m,l;"object"===f.type(c)&&(c.nodeType&&(c=f(c)),t(c)?(k={href:c.data("fancybox-href")||c.attr("href"),title:c.data("fancybox-title")||c.attr("title"),isDom:!0,element:c},f.metadata&&f.extend(!0,k,
c.metadata())):k=c);g=d.href||k.href||(q(c)?c:null);h=d.title!==v?d.title:k.title||"";m=(j=d.content||k.content)?"html":d.type||k.type;!m&&k.isDom&&(m=c.data("fancybox-type"),m||(m=(m=c.prop("class").match(/fancybox\.(\w+)/))?m[1]:null));q(g)&&(m||(b.isImage(g)?m="image":b.isSWF(g)?m="swf":"#"===g.charAt(0)?m="inline":q(c)&&(m="html",j=c)),"ajax"===m&&(l=g.split(/\s+/,2),g=l.shift(),l=l.shift()));j||("inline"===m?g?j=f(q(g)?g.replace(/.*(?=#[^\s]+$)/,""):g):k.isDom&&(j=c):"html"===m?j=g:!m&&(!g&&
k.isDom)&&(m="inline",j=c));f.extend(k,{href:g,type:m,content:j,title:h,selector:l});a[e]=k}),b.opts=f.extend(!0,{},b.defaults,d),d.keys!==v&&(b.opts.keys=d.keys?f.extend({},b.defaults.keys,d.keys):!1),b.group=a,b._start(b.opts.index)},cancel:function(){var a=b.coming;a&&!1!==b.trigger("onCancel")&&(b.hideLoading(),b.ajaxLoad&&b.ajaxLoad.abort(),b.ajaxLoad=null,b.imgPreload&&(b.imgPreload.onload=b.imgPreload.onerror=null),a.wrap&&a.wrap.stop(!0,!0).trigger("onReset").remove(),b.coming=null,b.current||
b._afterZoomOut(a))},close:function(a){b.cancel();!1!==b.trigger("beforeClose")&&(b.unbindEvents(),b.isActive&&(!b.isOpen||!0===a?(f(".fancybox-wrap").stop(!0).trigger("onReset").remove(),b._afterZoomOut()):(b.isOpen=b.isOpened=!1,b.isClosing=!0,f(".fancybox-item, .fancybox-nav").remove(),b.wrap.stop(!0,!0).removeClass("fancybox-opened"),b.transitions[b.current.closeMethod]())))},play:function(a){var d=function(){clearTimeout(b.player.timer)},e=function(){d();b.current&&b.player.isActive&&(b.player.timer=
setTimeout(b.next,b.current.playSpeed))},c=function(){d();p.unbind(".player");b.player.isActive=!1;b.trigger("onPlayEnd")};if(!0===a||!b.player.isActive&&!1!==a){if(b.current&&(b.current.loop||b.current.index<b.group.length-1))b.player.isActive=!0,p.bind({"onCancel.player beforeClose.player":c,"onUpdate.player":e,"beforeLoad.player":d}),e(),b.trigger("onPlayStart")}else c()},next:function(a){var d=b.current;d&&(q(a)||(a=d.direction.next),b.jumpto(d.index+1,a,"next"))},prev:function(a){var d=b.current;
d&&(q(a)||(a=d.direction.prev),b.jumpto(d.index-1,a,"prev"))},jumpto:function(a,d,e){var c=b.current;c&&(a=l(a),b.direction=d||c.direction[a>=c.index?"next":"prev"],b.router=e||"jumpto",c.loop&&(0>a&&(a=c.group.length+a%c.group.length),a%=c.group.length),c.group[a]!==v&&(b.cancel(),b._start(a)))},reposition:function(a,d){var e=b.current,c=e?e.wrap:null,k;c&&(k=b._getPosition(d),a&&"scroll"===a.type?(delete k.position,c.stop(!0,!0).animate(k,200)):(c.css(k),e.pos=f.extend({},e.dim,k)))},update:function(a){var d=
a&&a.type,e=!d||"orientationchange"===d;e&&(clearTimeout(B),B=null);b.isOpen&&!B&&(B=setTimeout(function(){var c=b.current;c&&!b.isClosing&&(b.wrap.removeClass("fancybox-tmp"),(e||"load"===d||"resize"===d&&c.autoResize)&&b._setDimension(),"scroll"===d&&c.canShrink||b.reposition(a),b.trigger("onUpdate"),B=null)},e&&!s?0:300))},toggle:function(a){b.isOpen&&(b.current.fitToView="boolean"===f.type(a)?a:!b.current.fitToView,s&&(b.wrap.removeAttr("style").addClass("fancybox-tmp"),b.trigger("onUpdate")),
b.update())},hideLoading:function(){p.unbind(".loading");f("#fancybox-loading").remove()},showLoading:function(){var a,d;b.hideLoading();a=f('<div id="fancybox-loading"><div></div></div>').click(b.cancel).appendTo("body");p.bind("keydown.loading",function(a){if(27===(a.which||a.keyCode))a.preventDefault(),b.cancel()});b.defaults.fixed||(d=b.getViewport(),a.css({position:"absolute",top:0.5*d.h+d.y,left:0.5*d.w+d.x}))},getViewport:function(){var a=b.current&&b.current.locked||!1,d={x:n.scrollLeft(),
y:n.scrollTop()};a?(d.w=a[0].clientWidth,d.h=a[0].clientHeight):(d.w=s&&r.innerWidth?r.innerWidth:n.width(),d.h=s&&r.innerHeight?r.innerHeight:n.height());return d},unbindEvents:function(){b.wrap&&t(b.wrap)&&b.wrap.unbind(".fb");p.unbind(".fb");n.unbind(".fb")},bindEvents:function(){var a=b.current,d;a&&(n.bind("orientationchange.fb"+(s?"":" resize.fb")+(a.autoCenter&&!a.locked?" scroll.fb":""),b.update),(d=a.keys)&&p.bind("keydown.fb",function(e){var c=e.which||e.keyCode,k=e.target||e.srcElement;
if(27===c&&b.coming)return!1;!e.ctrlKey&&(!e.altKey&&!e.shiftKey&&!e.metaKey&&(!k||!k.type&&!f(k).is("[contenteditable]")))&&f.each(d,function(d,k){if(1<a.group.length&&k[c]!==v)return b[d](k[c]),e.preventDefault(),!1;if(-1<f.inArray(c,k))return b[d](),e.preventDefault(),!1})}),f.fn.mousewheel&&a.mouseWheel&&b.wrap.bind("mousewheel.fb",function(d,c,k,g){for(var h=f(d.target||null),j=!1;h.length&&!j&&!h.is(".fancybox-skin")&&!h.is(".fancybox-wrap");)j=h[0]&&!(h[0].style.overflow&&"hidden"===h[0].style.overflow)&&
(h[0].clientWidth&&h[0].scrollWidth>h[0].clientWidth||h[0].clientHeight&&h[0].scrollHeight>h[0].clientHeight),h=f(h).parent();if(0!==c&&!j&&1<b.group.length&&!a.canShrink){if(0<g||0<k)b.prev(0<g?"down":"left");else if(0>g||0>k)b.next(0>g?"up":"right");d.preventDefault()}}))},trigger:function(a,d){var e,c=d||b.coming||b.current;if(c){f.isFunction(c[a])&&(e=c[a].apply(c,Array.prototype.slice.call(arguments,1)));if(!1===e)return!1;c.helpers&&f.each(c.helpers,function(d,e){if(e&&b.helpers[d]&&f.isFunction(b.helpers[d][a]))b.helpers[d][a](f.extend(!0,
{},b.helpers[d].defaults,e),c)});p.trigger(a)}},isImage:function(a){return q(a)&&a.match(/(^data:image\/.*,)|(\.(jp(e|g|eg)|gif|png|bmp|webp|svg)((\?|#).*)?$)/i)},isSWF:function(a){return q(a)&&a.match(/\.(swf)((\?|#).*)?$/i)},_start:function(a){var d={},e,c;a=l(a);e=b.group[a]||null;if(!e)return!1;d=f.extend(!0,{},b.opts,e);e=d.margin;c=d.padding;"number"===f.type(e)&&(d.margin=[e,e,e,e]);"number"===f.type(c)&&(d.padding=[c,c,c,c]);d.modal&&f.extend(!0,d,{closeBtn:!1,closeClick:!1,nextClick:!1,arrows:!1,
mouseWheel:!1,keys:null,helpers:{overlay:{closeClick:!1}}});d.autoSize&&(d.autoWidth=d.autoHeight=!0);"auto"===d.width&&(d.autoWidth=!0);"auto"===d.height&&(d.autoHeight=!0);d.group=b.group;d.index=a;b.coming=d;if(!1===b.trigger("beforeLoad"))b.coming=null;else{c=d.type;e=d.href;if(!c)return b.coming=null,b.current&&b.router&&"jumpto"!==b.router?(b.current.index=a,b[b.router](b.direction)):!1;b.isActive=!0;if("image"===c||"swf"===c)d.autoHeight=d.autoWidth=!1,d.scrolling="visible";"image"===c&&(d.aspectRatio=
!0);"iframe"===c&&s&&(d.scrolling="scroll");d.wrap=f(d.tpl.wrap).addClass("fancybox-"+(s?"mobile":"desktop")+" fancybox-type-"+c+" fancybox-tmp "+d.wrapCSS).appendTo(d.parent||"body");f.extend(d,{skin:f(".fancybox-skin",d.wrap),outer:f(".fancybox-outer",d.wrap),inner:f(".fancybox-inner",d.wrap)});f.each(["Top","Right","Bottom","Left"],function(a,b){d.skin.css("padding"+b,w(d.padding[a]))});b.trigger("onReady");if("inline"===c||"html"===c){if(!d.content||!d.content.length)return b._error("content")}else if(!e)return b._error("href");
"image"===c?b._loadImage():"ajax"===c?b._loadAjax():"iframe"===c?b._loadIframe():b._afterLoad()}},_error:function(a){f.extend(b.coming,{type:"html",autoWidth:!0,autoHeight:!0,minWidth:0,minHeight:0,scrolling:"no",hasError:a,content:b.coming.tpl.error});b._afterLoad()},_loadImage:function(){var a=b.imgPreload=new Image;a.onload=function(){this.onload=this.onerror=null;b.coming.width=this.width/b.opts.pixelRatio;b.coming.height=this.height/b.opts.pixelRatio;b._afterLoad()};a.onerror=function(){this.onload=
this.onerror=null;b._error("image")};a.src=b.coming.href;!0!==a.complete&&b.showLoading()},_loadAjax:function(){var a=b.coming;b.showLoading();b.ajaxLoad=f.ajax(f.extend({},a.ajax,{url:a.href,error:function(a,e){b.coming&&"abort"!==e?b._error("ajax",a):b.hideLoading()},success:function(d,e){"success"===e&&(a.content=d,b._afterLoad())}}))},_loadIframe:function(){var a=b.coming,d=f(a.tpl.iframe.replace(/\{rnd\}/g,(new Date).getTime())).attr("scrolling",s?"auto":a.iframe.scrolling).attr("src",a.href);
f(a.wrap).bind("onReset",function(){try{f(this).find("iframe").hide().attr("src","//about:blank").end().empty()}catch(a){}});a.iframe.preload&&(b.showLoading(),d.one("load",function(){f(this).data("ready",1);s||f(this).bind("load.fb",b.update);f(this).parents(".fancybox-wrap").width("100%").removeClass("fancybox-tmp").show();b._afterLoad()}));a.content=d.appendTo(a.inner);a.iframe.preload||b._afterLoad()},_preloadImages:function(){var a=b.group,d=b.current,e=a.length,c=d.preload?Math.min(d.preload,
e-1):0,f,g;for(g=1;g<=c;g+=1)f=a[(d.index+g)%e],"image"===f.type&&f.href&&((new Image).src=f.href)},_afterLoad:function(){var a=b.coming,d=b.current,e,c,k,g,h;b.hideLoading();if(a&&!1!==b.isActive)if(!1===b.trigger("afterLoad",a,d))a.wrap.stop(!0).trigger("onReset").remove(),b.coming=null;else{d&&(b.trigger("beforeChange",d),d.wrap.stop(!0).removeClass("fancybox-opened").find(".fancybox-item, .fancybox-nav").remove());b.unbindEvents();e=a.content;c=a.type;k=a.scrolling;f.extend(b,{wrap:a.wrap,skin:a.skin,
outer:a.outer,inner:a.inner,current:a,previous:d});g=a.href;switch(c){case "inline":case "ajax":case "html":a.selector?e=f("<div>").html(e).find(a.selector):t(e)&&(e.data("fancybox-placeholder")||e.data("fancybox-placeholder",f('<div class="fancybox-placeholder"></div>').insertAfter(e).hide()),e=e.show().detach(),a.wrap.bind("onReset",function(){f(this).find(e).length&&e.hide().replaceAll(e.data("fancybox-placeholder")).data("fancybox-placeholder",!1)}));break;case "image":e=a.tpl.image.replace("{href}",
g);break;case "swf":e='<object id="fancybox-swf" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="100%" height="100%"><param name="movie" value="'+g+'"></param>',h="",f.each(a.swf,function(a,b){e+='<param name="'+a+'" value="'+b+'"></param>';h+=" "+a+'="'+b+'"'}),e+='<embed src="'+g+'" type="application/x-shockwave-flash" width="100%" height="100%"'+h+"></embed></object>"}(!t(e)||!e.parent().is(a.inner))&&a.inner.append(e);b.trigger("beforeShow");a.inner.css("overflow","yes"===k?"scroll":
"no"===k?"hidden":k);b._setDimension();b.reposition();b.isOpen=!1;b.coming=null;b.bindEvents();if(b.isOpened){if(d.prevMethod)b.transitions[d.prevMethod]()}else f(".fancybox-wrap").not(a.wrap).stop(!0).trigger("onReset").remove();b.transitions[b.isOpened?a.nextMethod:a.openMethod]();b._preloadImages()}},_setDimension:function(){var a=b.getViewport(),d=0,e=!1,c=!1,e=b.wrap,k=b.skin,g=b.inner,h=b.current,c=h.width,j=h.height,m=h.minWidth,u=h.minHeight,n=h.maxWidth,p=h.maxHeight,s=h.scrolling,q=h.scrollOutside?
h.scrollbarWidth:0,x=h.margin,y=l(x[1]+x[3]),r=l(x[0]+x[2]),v,z,t,C,A,F,B,D,H;e.add(k).add(g).width("auto").height("auto").removeClass("fancybox-tmp");x=l(k.outerWidth(!0)-k.width());v=l(k.outerHeight(!0)-k.height());z=y+x;t=r+v;C=E(c)?(a.w-z)*l(c)/100:c;A=E(j)?(a.h-t)*l(j)/100:j;if("iframe"===h.type){if(H=h.content,h.autoHeight&&1===H.data("ready"))try{H[0].contentWindow.document.location&&(g.width(C).height(9999),F=H.contents().find("body"),q&&F.css("overflow-x","hidden"),A=F.outerHeight(!0))}catch(G){}}else if(h.autoWidth||
h.autoHeight)g.addClass("fancybox-tmp"),h.autoWidth||g.width(C),h.autoHeight||g.height(A),h.autoWidth&&(C=g.width()),h.autoHeight&&(A=g.height()),g.removeClass("fancybox-tmp");c=l(C);j=l(A);D=C/A;m=l(E(m)?l(m,"w")-z:m);n=l(E(n)?l(n,"w")-z:n);u=l(E(u)?l(u,"h")-t:u);p=l(E(p)?l(p,"h")-t:p);F=n;B=p;h.fitToView&&(n=Math.min(a.w-z,n),p=Math.min(a.h-t,p));z=a.w-y;r=a.h-r;h.aspectRatio?(c>n&&(c=n,j=l(c/D)),j>p&&(j=p,c=l(j*D)),c<m&&(c=m,j=l(c/D)),j<u&&(j=u,c=l(j*D))):(c=Math.max(m,Math.min(c,n)),h.autoHeight&&
"iframe"!==h.type&&(g.width(c),j=g.height()),j=Math.max(u,Math.min(j,p)));if(h.fitToView)if(g.width(c).height(j),e.width(c+x),a=e.width(),y=e.height(),h.aspectRatio)for(;(a>z||y>r)&&(c>m&&j>u)&&!(19<d++);)j=Math.max(u,Math.min(p,j-10)),c=l(j*D),c<m&&(c=m,j=l(c/D)),c>n&&(c=n,j=l(c/D)),g.width(c).height(j),e.width(c+x),a=e.width(),y=e.height();else c=Math.max(m,Math.min(c,c-(a-z))),j=Math.max(u,Math.min(j,j-(y-r)));q&&("auto"===s&&j<A&&c+x+q<z)&&(c+=q);g.width(c).height(j);e.width(c+x);a=e.width();
y=e.height();e=(a>z||y>r)&&c>m&&j>u;c=h.aspectRatio?c<F&&j<B&&c<C&&j<A:(c<F||j<B)&&(c<C||j<A);f.extend(h,{dim:{width:w(a),height:w(y)},origWidth:C,origHeight:A,canShrink:e,canExpand:c,wPadding:x,hPadding:v,wrapSpace:y-k.outerHeight(!0),skinSpace:k.height()-j});!H&&(h.autoHeight&&j>u&&j<p&&!c)&&g.height("auto")},_getPosition:function(a){var d=b.current,e=b.getViewport(),c=d.margin,f=b.wrap.width()+c[1]+c[3],g=b.wrap.height()+c[0]+c[2],c={position:"absolute",top:c[0],left:c[3]};d.autoCenter&&d.fixed&&
!a&&g<=e.h&&f<=e.w?c.position="fixed":d.locked||(c.top+=e.y,c.left+=e.x);c.top=w(Math.max(c.top,c.top+(e.h-g)*d.topRatio));c.left=w(Math.max(c.left,c.left+(e.w-f)*d.leftRatio));return c},_afterZoomIn:function(){var a=b.current;a&&(b.isOpen=b.isOpened=!0,b.wrap.css("overflow","visible").addClass("fancybox-opened"),b.update(),(a.closeClick||a.nextClick&&1<b.group.length)&&b.inner.css("cursor","pointer").bind("click.fb",function(d){!f(d.target).is("a")&&!f(d.target).parent().is("a")&&(d.preventDefault(),
b[a.closeClick?"close":"next"]())}),a.closeBtn&&f(a.tpl.closeBtn).appendTo(b.skin).bind("click.fb",function(a){a.preventDefault();b.close()}),a.arrows&&1<b.group.length&&((a.loop||0<a.index)&&f(a.tpl.prev).appendTo(b.outer).bind("click.fb",b.prev),(a.loop||a.index<b.group.length-1)&&f(a.tpl.next).appendTo(b.outer).bind("click.fb",b.next)),b.trigger("afterShow"),!a.loop&&a.index===a.group.length-1?b.play(!1):b.opts.autoPlay&&!b.player.isActive&&(b.opts.autoPlay=!1,b.play()))},_afterZoomOut:function(a){a=
a||b.current;f(".fancybox-wrap").trigger("onReset").remove();f.extend(b,{group:{},opts:{},router:!1,current:null,isActive:!1,isOpened:!1,isOpen:!1,isClosing:!1,wrap:null,skin:null,outer:null,inner:null});b.trigger("afterClose",a)}});b.transitions={getOrigPosition:function(){var a=b.current,d=a.element,e=a.orig,c={},f=50,g=50,h=a.hPadding,j=a.wPadding,m=b.getViewport();!e&&(a.isDom&&d.is(":visible"))&&(e=d.find("img:first"),e.length||(e=d));t(e)?(c=e.offset(),e.is("img")&&(f=e.outerWidth(),g=e.outerHeight())):
(c.top=m.y+(m.h-g)*a.topRatio,c.left=m.x+(m.w-f)*a.leftRatio);if("fixed"===b.wrap.css("position")||a.locked)c.top-=m.y,c.left-=m.x;return c={top:w(c.top-h*a.topRatio),left:w(c.left-j*a.leftRatio),width:w(f+j),height:w(g+h)}},step:function(a,d){var e,c,f=d.prop;c=b.current;var g=c.wrapSpace,h=c.skinSpace;if("width"===f||"height"===f)e=d.end===d.start?1:(a-d.start)/(d.end-d.start),b.isClosing&&(e=1-e),c="width"===f?c.wPadding:c.hPadding,c=a-c,b.skin[f](l("width"===f?c:c-g*e)),b.inner[f](l("width"===
f?c:c-g*e-h*e))},zoomIn:function(){var a=b.current,d=a.pos,e=a.openEffect,c="elastic"===e,k=f.extend({opacity:1},d);delete k.position;c?(d=this.getOrigPosition(),a.openOpacity&&(d.opacity=0.1)):"fade"===e&&(d.opacity=0.1);b.wrap.css(d).animate(k,{duration:"none"===e?0:a.openSpeed,easing:a.openEasing,step:c?this.step:null,complete:b._afterZoomIn})},zoomOut:function(){var a=b.current,d=a.closeEffect,e="elastic"===d,c={opacity:0.1};e&&(c=this.getOrigPosition(),a.closeOpacity&&(c.opacity=0.1));b.wrap.animate(c,
{duration:"none"===d?0:a.closeSpeed,easing:a.closeEasing,step:e?this.step:null,complete:b._afterZoomOut})},changeIn:function(){var a=b.current,d=a.nextEffect,e=a.pos,c={opacity:1},f=b.direction,g;e.opacity=0.1;"elastic"===d&&(g="down"===f||"up"===f?"top":"left","down"===f||"right"===f?(e[g]=w(l(e[g])-200),c[g]="+=200px"):(e[g]=w(l(e[g])+200),c[g]="-=200px"));"none"===d?b._afterZoomIn():b.wrap.css(e).animate(c,{duration:a.nextSpeed,easing:a.nextEasing,complete:b._afterZoomIn})},changeOut:function(){var a=
b.previous,d=a.prevEffect,e={opacity:0.1},c=b.direction;"elastic"===d&&(e["down"===c||"up"===c?"top":"left"]=("up"===c||"left"===c?"-":"+")+"=200px");a.wrap.animate(e,{duration:"none"===d?0:a.prevSpeed,easing:a.prevEasing,complete:function(){f(this).trigger("onReset").remove()}})}};b.helpers.overlay={defaults:{closeClick:!0,speedOut:200,showEarly:!0,css:{},locked:!s,fixed:!0},overlay:null,fixed:!1,el:f("html"),create:function(a){a=f.extend({},this.defaults,a);this.overlay&&this.close();this.overlay=
f('<div class="fancybox-overlay"></div>').appendTo(b.coming?b.coming.parent:a.parent);this.fixed=!1;a.fixed&&b.defaults.fixed&&(this.overlay.addClass("fancybox-overlay-fixed"),this.fixed=!0)},open:function(a){var d=this;a=f.extend({},this.defaults,a);this.overlay?this.overlay.unbind(".overlay").width("auto").height("auto"):this.create(a);this.fixed||(n.bind("resize.overlay",f.proxy(this.update,this)),this.update());a.closeClick&&this.overlay.bind("click.overlay",function(a){if(f(a.target).hasClass("fancybox-overlay"))return b.isActive?
b.close():d.close(),!1});this.overlay.css(a.css).show()},close:function(){var a,b;n.unbind("resize.overlay");this.el.hasClass("fancybox-lock")&&(f(".fancybox-margin").removeClass("fancybox-margin"),a=n.scrollTop(),b=n.scrollLeft(),this.el.removeClass("fancybox-lock"),n.scrollTop(a).scrollLeft(b));f(".fancybox-overlay").remove().hide();f.extend(this,{overlay:null,fixed:!1})},update:function(){var a="100%",b;this.overlay.width(a).height("100%");I?(b=Math.max(G.documentElement.offsetWidth,G.body.offsetWidth),
p.width()>b&&(a=p.width())):p.width()>n.width()&&(a=p.width());this.overlay.width(a).height(p.height())},onReady:function(a,b){var e=this.overlay;f(".fancybox-overlay").stop(!0,!0);e||this.create(a);a.locked&&(this.fixed&&b.fixed)&&(e||(this.margin=p.height()>n.height()?f("html").css("margin-right").replace("px",""):!1),b.locked=this.overlay.append(b.wrap),b.fixed=!1);!0===a.showEarly&&this.beforeShow.apply(this,arguments)},beforeShow:function(a,b){var e,c;b.locked&&(!1!==this.margin&&(f("*").filter(function(){return"fixed"===
f(this).css("position")&&!f(this).hasClass("fancybox-overlay")&&!f(this).hasClass("fancybox-wrap")}).addClass("fancybox-margin"),this.el.addClass("fancybox-margin")),e=n.scrollTop(),c=n.scrollLeft(),this.el.addClass("fancybox-lock"),n.scrollTop(e).scrollLeft(c));this.open(a)},onUpdate:function(){this.fixed||this.update()},afterClose:function(a){this.overlay&&!b.coming&&this.overlay.fadeOut(a.speedOut,f.proxy(this.close,this))}};b.helpers.title={defaults:{type:"float",position:"bottom"},beforeShow:function(a){var d=
b.current,e=d.title,c=a.type;f.isFunction(e)&&(e=e.call(d.element,d));if(q(e)&&""!==f.trim(e)){d=f('<div class="fancybox-title fancybox-title-'+c+'-wrap">'+e+"</div>");switch(c){case "inside":c=b.skin;break;case "outside":c=b.wrap;break;case "over":c=b.inner;break;default:c=b.skin,d.appendTo("body"),I&&d.width(d.width()),d.wrapInner('<span class="child"></span>'),b.current.margin[2]+=Math.abs(l(d.css("margin-bottom")))}d["top"===a.position?"prependTo":"appendTo"](c)}}};f.fn.fancybox=function(a){var d,
e=f(this),c=this.selector||"",k=function(g){var h=f(this).blur(),j=d,k,l;!g.ctrlKey&&(!g.altKey&&!g.shiftKey&&!g.metaKey)&&!h.is(".fancybox-wrap")&&(k=a.groupAttr||"data-fancybox-group",l=h.attr(k),l||(k="rel",l=h.get(0)[k]),l&&(""!==l&&"nofollow"!==l)&&(h=c.length?f(c):e,h=h.filter("["+k+'="'+l+'"]'),j=h.index(this)),a.index=j,!1!==b.open(h,a)&&g.preventDefault())};a=a||{};d=a.index||0;!c||!1===a.live?e.unbind("click.fb-start").bind("click.fb-start",k):p.undelegate(c,"click.fb-start").delegate(c+
":not('.fancybox-item, .fancybox-nav')","click.fb-start",k);this.filter("[data-fancybox-start=1]").trigger("click");return this};p.ready(function(){var a,d;f.scrollbarWidth===v&&(f.scrollbarWidth=function(){var a=f('<div style="width:50px;height:50px;overflow:auto"><div/></div>').appendTo("body"),b=a.children(),b=b.innerWidth()-b.height(99).innerWidth();a.remove();return b});if(f.support.fixedPosition===v){a=f.support;d=f('<div style="position:fixed;top:20px;"></div>').appendTo("body");var e=20===
d[0].offsetTop||15===d[0].offsetTop;d.remove();a.fixedPosition=e}f.extend(b.defaults,{scrollbarWidth:f.scrollbarWidth(),fixed:f.support.fixedPosition,parent:f("body")});a=f(r).width();J.addClass("fancybox-lock-test");d=f(r).width();J.removeClass("fancybox-lock-test");f("<style type='text/css'>.fancybox-margin{margin-right:"+(d-a)+"px;}</style>").appendTo("head")})})(window,document,jQuery);(function(a){if(typeof define==="function"&&define.amd){define(["jquery"],a)}else{a(window.jQuery)}}(function(b){var a=0;b.ajaxTransport("iframe",function(d){if(d.async){var c=d.initialIframeSrc||"javascript:false;",f,e,g;return{send:function(h,i){f=b('<form style="display:none;"></form>');f.attr("accept-charset",d.formAcceptCharset);g=/\?/.test(d.url)?"&":"?";if(d.type==="DELETE"){d.url=d.url+g+"_method=DELETE";d.type="POST"}else{if(d.type==="PUT"){d.url=d.url+g+"_method=PUT";d.type="POST"}else{if(d.type==="PATCH"){d.url=d.url+g+"_method=PATCH";d.type="POST"}}}a+=1;e=b('<iframe src="'+c+'" name="iframe-transport-'+a+'"></iframe>').bind("load",function(){var j,k=b.isArray(d.paramName)?d.paramName:[d.paramName];e.unbind("load").bind("load",function(){var l;try{l=e.contents();if(!l.length||!l[0].firstChild){throw new Error()}}catch(m){l=undefined}i(200,"success",{iframe:l});b('<iframe src="'+c+'"></iframe>').appendTo(f);window.setTimeout(function(){f.remove()},0)});f.prop("target",e.prop("name")).prop("action",d.url).prop("method",d.type);if(d.formData){b.each(d.formData,function(l,m){b('<input type="hidden"/>').prop("name",m.name).val(m.value).appendTo(f)})}if(d.fileInput&&d.fileInput.length&&d.type==="POST"){j=d.fileInput.clone();d.fileInput.after(function(l){return j[l]});if(d.paramName){d.fileInput.each(function(l){b(this).prop("name",k[l]||d.paramName)})}f.append(d.fileInput).prop("enctype","multipart/form-data").prop("encoding","multipart/form-data")}f.submit();if(j&&j.length){d.fileInput.each(function(m,l){var n=b(j[m]);b(l).prop("name",n.prop("name"));n.replaceWith(l)})}});f.append(e).appendTo(document.body)},abort:function(){if(e){e.unbind("load").prop("src",c)}if(f){f.remove()}}}}});b.ajaxSetup({converters:{"iframe text":function(c){return c&&b(c[0].body).text()},"iframe json":function(c){return c&&b.parseJSON(b(c[0].body).text())},"iframe html":function(c){return c&&b(c[0].body).html()},"iframe xml":function(c){var d=c&&c[0];return d&&b.isXMLDoc(d)?d:b.parseXML((d.XMLDocument&&d.XMLDocument.xml)||b(d.body).html())},"iframe script":function(c){return c&&b.globalEval(b(c[0].body).text())}}})}));(function(a){if(typeof define==="function"&&define.amd){define(["jquery","jquery.ui.widget"],a)}else{a(window.jQuery)}}(function(a){a.support.fileInput=!(new RegExp("(Android (1\\.[0156]|2\\.[01]))|(Windows Phone (OS 7|8\\.0))|(XBLWP)|(ZuneWP)|(WPDesktop)|(w(eb)?OSBrowser)|(webOS)|(Kindle/(1\\.0|2\\.[05]|3\\.0))").test(window.navigator.userAgent)||a('<input type="file">').prop("disabled"));a.support.xhrFileUpload=!!(window.ProgressEvent&&window.FileReader);a.support.xhrFormDataFileUpload=!!window.FormData;a.support.blobSlice=window.Blob&&(Blob.prototype.slice||Blob.prototype.webkitSlice||Blob.prototype.mozSlice);a.widget("blueimp.fileupload",{options:{dropZone:a(document),pasteZone:a(document),fileInput:undefined,replaceFileInput:true,paramName:undefined,singleFileUploads:true,limitMultiFileUploads:undefined,sequentialUploads:false,limitConcurrentUploads:undefined,forceIframeTransport:false,redirect:undefined,redirectParamName:undefined,postMessage:undefined,multipart:true,maxChunkSize:undefined,uploadedBytes:undefined,recalculateProgress:true,progressInterval:100,bitrateInterval:500,autoUpload:true,messages:{uploadedBytes:"Uploaded bytes exceed file size"},i18n:function(c,b){c=this.messages[c]||c.toString();if(b){a.each(b,function(d,e){c=c.replace("{"+d+"}",e)})}return c},formData:function(b){return b.serializeArray()},add:function(c,b){if(c.isDefaultPrevented()){return false}if(b.autoUpload||(b.autoUpload!==false&&a(this).fileupload("option","autoUpload"))){b.process().done(function(){b.submit()})}},processData:false,contentType:false,cache:false},_specialOptions:["fileInput","dropZone","pasteZone","multipart","forceIframeTransport"],_blobSlice:a.support.blobSlice&&function(){var b=this.slice||this.webkitSlice||this.mozSlice;return b.apply(this,arguments)},_BitrateTimer:function(){this.timestamp=((Date.now)?Date.now():(new Date()).getTime());this.loaded=0;this.bitrate=0;this.getBitrate=function(d,c,b){var e=d-this.timestamp;if(!this.bitrate||!b||e>b){this.bitrate=(c-this.loaded)*(1000/e)*8;this.loaded=c;this.timestamp=d}return this.bitrate}},_isXHRUpload:function(b){return !b.forceIframeTransport&&((!b.multipart&&a.support.xhrFileUpload)||a.support.xhrFormDataFileUpload)},_getFormData:function(b){var c;if(typeof b.formData==="function"){return b.formData(b.form)}if(a.isArray(b.formData)){return b.formData}if(a.type(b.formData)==="object"){c=[];a.each(b.formData,function(d,e){c.push({name:d,value:e})});return c}return[]},_getTotal:function(c){var b=0;a.each(c,function(d,e){b+=e.size||1});return b},_initProgressObject:function(c){var b={loaded:0,total:0,bitrate:0};if(c._progress){a.extend(c._progress,b)}else{c._progress=b}},_initResponseObject:function(b){var c;if(b._response){for(c in b._response){if(b._response.hasOwnProperty(c)){delete b._response[c]}}}else{b._response={}}},_onProgress:function(f,d){if(f.lengthComputable){var c=((Date.now)?Date.now():(new Date()).getTime()),b;if(d._time&&d.progressInterval&&(c-d._time<d.progressInterval)&&f.loaded!==f.total){return}d._time=c;b=Math.floor(f.loaded/f.total*(d.chunkSize||d._progress.total))+(d.uploadedBytes||0);this._progress.loaded+=(b-d._progress.loaded);this._progress.bitrate=this._bitrateTimer.getBitrate(c,this._progress.loaded,d.bitrateInterval);d._progress.loaded=d.loaded=b;d._progress.bitrate=d.bitrate=d._bitrateTimer.getBitrate(c,b,d.bitrateInterval);this._trigger("progress",a.Event("progress",{delegatedEvent:f}),d);this._trigger("progressall",a.Event("progressall",{delegatedEvent:f}),this._progress)}},_initProgressListener:function(b){var c=this,d=b.xhr?b.xhr():a.ajaxSettings.xhr();if(d.upload){a(d.upload).bind("progress",function(f){var g=f.originalEvent;f.lengthComputable=g.lengthComputable;f.loaded=g.loaded;f.total=g.total;c._onProgress(f,b)});b.xhr=function(){return d}}},_isInstanceOf:function(b,c){return Object.prototype.toString.call(c)==="[object "+b+"]"},_initXHRData:function(c){var e=this,g,d=c.files[0],b=c.multipart||!a.support.xhrFileUpload,f=c.paramName[0];c.headers=a.extend({},c.headers);if(c.contentRange){c.headers["Content-Range"]=c.contentRange}if(!b||c.blob||!this._isInstanceOf("File",d)){c.headers["Content-Disposition"]='attachment; filename="'+encodeURI(d.name)+'"'}if(!b){c.contentType=d.type;c.data=c.blob||d}else{if(a.support.xhrFormDataFileUpload){if(c.postMessage){g=this._getFormData(c);if(c.blob){g.push({name:f,value:c.blob})}else{a.each(c.files,function(h,i){g.push({name:c.paramName[h]||f,value:i})})}}else{if(e._isInstanceOf("FormData",c.formData)){g=c.formData}else{g=new FormData();a.each(this._getFormData(c),function(h,i){g.append(i.name,i.value)})}if(c.blob){g.append(f,c.blob,d.name)}else{a.each(c.files,function(h,i){if(e._isInstanceOf("File",i)||e._isInstanceOf("Blob",i)){g.append(c.paramName[h]||f,i,i.uploadName||i.name)}})}}c.data=g}}c.blob=null},_initIframeSettings:function(b){var c=a("<a></a>").prop("href",b.url).prop("host");b.dataType="iframe "+(b.dataType||"");b.formData=this._getFormData(b);if(b.redirect&&c&&c!==location.host){b.formData.push({name:b.redirectParamName||"redirect",value:b.redirect})}},_initDataSettings:function(b){if(this._isXHRUpload(b)){if(!this._chunkedUpload(b,true)){if(!b.data){this._initXHRData(b)}this._initProgressListener(b)}if(b.postMessage){b.dataType="postmessage "+(b.dataType||"")}}else{this._initIframeSettings(b)}},_getParamName:function(b){var c=a(b.fileInput),d=b.paramName;if(!d){d=[];c.each(function(){var e=a(this),f=e.prop("name")||"files[]",g=(e.prop("files")||[1]).length;while(g){d.push(f);g-=1}});if(!d.length){d=[c.prop("name")||"files[]"]}}else{if(!a.isArray(d)){d=[d]}}return d},_initFormSettings:function(b){if(!b.form||!b.form.length){b.form=a(b.fileInput.prop("form"));if(!b.form.length){b.form=a(this.options.fileInput.prop("form"))}}b.paramName=this._getParamName(b);if(!b.url){b.url=b.form.prop("action")||location.href}b.type=(b.type||(a.type(b.form.prop("method"))==="string"&&b.form.prop("method"))||"").toUpperCase();if(b.type!=="POST"&&b.type!=="PUT"&&b.type!=="PATCH"){b.type="POST"}if(!b.formAcceptCharset){b.formAcceptCharset=b.form.attr("accept-charset")}},_getAJAXSettings:function(c){var b=a.extend({},this.options,c);this._initFormSettings(b);this._initDataSettings(b);return b},_getDeferredState:function(b){if(b.state){return b.state()}if(b.isResolved()){return"resolved"}if(b.isRejected()){return"rejected"}return"pending"},_enhancePromise:function(b){b.success=b.done;b.error=b.fail;b.complete=b.always;return b},_getXHRPromise:function(e,d,c){var b=a.Deferred(),f=b.promise();d=d||this.options.context||f;if(e===true){b.resolveWith(d,c)}else{if(e===false){b.rejectWith(d,c)}}f.abort=b.promise;return this._enhancePromise(f)},_addConvenienceMethods:function(f,d){var c=this,b=function(e){return a.Deferred().resolveWith(c,e).promise()};d.process=function(g,e){if(g||e){d._processQueue=this._processQueue=(this._processQueue||b([this])).pipe(function(){if(d.errorThrown){return a.Deferred().rejectWith(c,[d]).promise()}return b(arguments)}).pipe(g,e)}return this._processQueue||b([this])};d.submit=function(){if(this.state()!=="pending"){d.jqXHR=this.jqXHR=(c._trigger("submit",a.Event("submit",{delegatedEvent:f}),this)!==false)&&c._onSend(f,this)}return this.jqXHR||c._getXHRPromise()};d.abort=function(){if(this.jqXHR){return this.jqXHR.abort()}this.errorThrown="abort";return c._getXHRPromise()};d.state=function(){if(this.jqXHR){return c._getDeferredState(this.jqXHR)}if(this._processQueue){return c._getDeferredState(this._processQueue)}};d.processing=function(){return !this.jqXHR&&this._processQueue&&c._getDeferredState(this._processQueue)==="pending"};d.progress=function(){return this._progress};d.response=function(){return this._response}},_getUploadedBytes:function(d){var b=d.getResponseHeader("Range"),e=b&&b.split("-"),c=e&&e.length>1&&parseInt(e[1],10);return c&&c+1},_chunkedUpload:function(m,g){m.uploadedBytes=m.uploadedBytes||0;var f=this,d=m.files[0],e=d.size,b=m.uploadedBytes,c=m.maxChunkSize||e,i=this._blobSlice,j=a.Deferred(),l=j.promise(),h,k;if(!(this._isXHRUpload(m)&&i&&(b||c<e))||m.data){return false}if(g){return true}if(b>=e){d.error=m.i18n("uploadedBytes");return this._getXHRPromise(false,m.context,[null,"error",d.error])}k=function(){var p=a.extend({},m),n=p._progress.loaded;p.blob=i.call(d,b,b+c,d.type);p.chunkSize=p.blob.size;p.contentRange="bytes "+b+"-"+(b+p.chunkSize-1)+"/"+e;f._initXHRData(p);f._initProgressListener(p);h=((f._trigger("chunksend",null,p)!==false&&a.ajax(p))||f._getXHRPromise(false,p.context)).done(function(o,r,q){b=f._getUploadedBytes(q)||(b+p.chunkSize);if(n+p.chunkSize-p._progress.loaded){f._onProgress(a.Event("progress",{lengthComputable:true,loaded:b-p.uploadedBytes,total:b-p.uploadedBytes}),p)}m.uploadedBytes=p.uploadedBytes=b;p.result=o;p.textStatus=r;p.jqXHR=q;f._trigger("chunkdone",null,p);f._trigger("chunkalways",null,p);if(b<e){k()}else{j.resolveWith(p.context,[o,r,q])}}).fail(function(o,r,q){p.jqXHR=o;p.textStatus=r;p.errorThrown=q;f._trigger("chunkfail",null,p);f._trigger("chunkalways",null,p);j.rejectWith(p.context,[o,r,q])})};this._enhancePromise(l);l.abort=function(){return h.abort()};k();return l},_beforeSend:function(c,b){if(this._active===0){this._trigger("start");this._bitrateTimer=new this._BitrateTimer();this._progress.loaded=this._progress.total=0;this._progress.bitrate=0}this._initResponseObject(b);this._initProgressObject(b);b._progress.loaded=b.loaded=b.uploadedBytes||0;b._progress.total=b.total=this._getTotal(b.files)||1;b._progress.bitrate=b.bitrate=0;this._active+=1;this._progress.loaded+=b.loaded;this._progress.total+=b.total},_onDone:function(b,g,f,d){var e=d._progress.total,c=d._response;if(d._progress.loaded<e){this._onProgress(a.Event("progress",{lengthComputable:true,loaded:e,total:e}),d)}c.result=d.result=b;c.textStatus=d.textStatus=g;c.jqXHR=d.jqXHR=f;this._trigger("done",null,d)},_onFail:function(d,f,e,c){var b=c._response;if(c.recalculateProgress){this._progress.loaded-=c._progress.loaded;this._progress.total-=c._progress.total}b.jqXHR=c.jqXHR=d;b.textStatus=c.textStatus=f;b.errorThrown=c.errorThrown=e;this._trigger("fail",null,c)},_onAlways:function(d,e,c,b){this._trigger("always",null,b)},_onSend:function(h,f){if(!f.submit){this._addConvenienceMethods(h,f)}var g=this,j,b,i,c,k=g._getAJAXSettings(f),d=function(){g._sending+=1;k._bitrateTimer=new g._BitrateTimer();j=j||(((b||g._trigger("send",a.Event("send",{delegatedEvent:h}),k)===false)&&g._getXHRPromise(false,k.context,b))||g._chunkedUpload(k)||a.ajax(k)).done(function(e,m,l){g._onDone(e,m,l,k)}).fail(function(e,m,l){g._onFail(e,m,l,k)}).always(function(m,n,l){g._onAlways(m,n,l,k);g._sending-=1;g._active-=1;if(k.limitConcurrentUploads&&k.limitConcurrentUploads>g._sending){var e=g._slots.shift();while(e){if(g._getDeferredState(e)==="pending"){e.resolve();break}e=g._slots.shift()}}if(g._active===0){g._trigger("stop")}});return j};this._beforeSend(h,k);if(this.options.sequentialUploads||(this.options.limitConcurrentUploads&&this.options.limitConcurrentUploads<=this._sending)){if(this.options.limitConcurrentUploads>1){i=a.Deferred();this._slots.push(i);c=i.pipe(d)}else{this._sequence=this._sequence.pipe(d,d);c=this._sequence}c.abort=function(){b=[undefined,"abort","abort"];if(!j){if(i){i.rejectWith(k.context,b)}return d()}return j.abort()};return this._enhancePromise(c)}return d()},_onAdd:function(k,g){var j=this,n=true,m=a.extend({},this.options,g),d=m.limitMultiFileUploads,h=this._getParamName(m),c,b,l,f;if(!(m.singleFileUploads||d)||!this._isXHRUpload(m)){l=[g.files];c=[h]}else{if(!m.singleFileUploads&&d){l=[];c=[];for(f=0;f<g.files.length;f+=d){l.push(g.files.slice(f,f+d));b=h.slice(f,f+d);if(!b.length){b=h}c.push(b)}}else{c=h}}g.originalFiles=g.files;a.each(l||g.files,function(e,i){var o=a.extend({},g);o.files=l?i:[i];o.paramName=c[e];j._initResponseObject(o);j._initProgressObject(o);j._addConvenienceMethods(k,o);n=j._trigger("add",a.Event("add",{delegatedEvent:k}),o);return n});return n},_replaceFileInput:function(b){var c=b.clone(true);a("<form></form>").append(c)[0].reset();b.after(c).detach();a.cleanData(b.unbind("remove"));this.options.fileInput=this.options.fileInput.map(function(d,e){if(e===b[0]){return c[0]}return e});if(b[0]===this.element[0]){this.element=c}},_handleFileTreeEntry:function(f,g){var e=this,b=a.Deferred(),c=function(h){if(h&&!h.entry){h.entry=f}b.resolve([h])},d;g=g||"";if(f.isFile){if(f._file){f._file.relativePath=g;b.resolve(f._file)}else{f.file(function(h){h.relativePath=g;b.resolve(h)},c)}}else{if(f.isDirectory){d=f.createReader();d.readEntries(function(h){e._handleFileTreeEntries(h,g+f.name+"/").done(function(i){b.resolve(i)}).fail(c)},c)}else{b.resolve([])}}return b.promise()},_handleFileTreeEntries:function(b,d){var c=this;return a.when.apply(a,a.map(b,function(e){return c._handleFileTreeEntry(e,d)})).pipe(function(){return Array.prototype.concat.apply([],arguments)})},_getDroppedFiles:function(c){c=c||{};var b=c.items;if(b&&b.length&&(b[0].webkitGetAsEntry||b[0].getAsEntry)){return this._handleFileTreeEntries(a.map(b,function(e){var d;if(e.webkitGetAsEntry){d=e.webkitGetAsEntry();if(d){d._file=e.getAsFile()}return d}return e.getAsEntry()}))}return a.Deferred().resolve(a.makeArray(c.files)).promise()},_getSingleFileInputFiles:function(d){d=a(d);var b=d.prop("webkitEntries")||d.prop("entries"),c,e;if(b&&b.length){return this._handleFileTreeEntries(b)}c=a.makeArray(d.prop("files"));if(!c.length){e=d.prop("value");if(!e){return a.Deferred().resolve([]).promise()}c=[{name:e.replace(/^.*\\/,"")}]}else{if(c[0].name===undefined&&c[0].fileName){a.each(c,function(f,g){g.name=g.fileName;g.size=g.fileSize})}}return a.Deferred().resolve(c).promise()},_getFileInputFiles:function(b){if(!(b instanceof a)||b.length===1){return this._getSingleFileInputFiles(b)}return a.when.apply(a,a.map(b,this._getSingleFileInputFiles)).pipe(function(){return Array.prototype.concat.apply([],arguments)})},_onChange:function(d){var b=this,c={fileInput:a(d.target),form:a(d.target.form)};this._getFileInputFiles(c.fileInput).always(function(e){c.files=e;if(b.options.replaceFileInput){b._replaceFileInput(c.fileInput)}if(b._trigger("change",a.Event("change",{delegatedEvent:d}),c)!==false){b._onAdd(d,c)}})},_onPaste:function(d){var b=d.originalEvent&&d.originalEvent.clipboardData&&d.originalEvent.clipboardData.items,c={files:[]};if(b&&b.length){a.each(b,function(e,g){var f=g.getAsFile&&g.getAsFile();if(f){c.files.push(f)}});if(this._trigger("paste",a.Event("paste",{delegatedEvent:d}),c)!==false){this._onAdd(d,c)}}},_onDrop:function(f){f.dataTransfer=f.originalEvent&&f.originalEvent.dataTransfer;var b=this,d=f.dataTransfer,c={};if(d&&d.files&&d.files.length){f.preventDefault();this._getDroppedFiles(d).always(function(e){c.files=e;if(b._trigger("drop",a.Event("drop",{delegatedEvent:f}),c)!==false){b._onAdd(f,c)}})}},_onDragOver:function(c){c.dataTransfer=c.originalEvent&&c.originalEvent.dataTransfer;var b=c.dataTransfer;if(b&&a.inArray("Files",b.types)!==-1&&this._trigger("dragover",a.Event("dragover",{delegatedEvent:c}))!==false){c.preventDefault();b.dropEffect="copy"}},_initEventHandlers:function(){if(this._isXHRUpload(this.options)){this._on(this.options.dropZone,{dragover:this._onDragOver,drop:this._onDrop});this._on(this.options.pasteZone,{paste:this._onPaste})}if(a.support.fileInput){this._on(this.options.fileInput,{change:this._onChange})}},_destroyEventHandlers:function(){this._off(this.options.dropZone,"dragover drop");this._off(this.options.pasteZone,"paste");this._off(this.options.fileInput,"change")},_setOption:function(b,c){var d=a.inArray(b,this._specialOptions)!==-1;if(d){this._destroyEventHandlers()}this._super(b,c);if(d){this._initSpecialOptions();this._initEventHandlers()}},_initSpecialOptions:function(){var b=this.options;if(b.fileInput===undefined){b.fileInput=this.element.is('input[type="file"]')?this.element:this.element.find('input[type="file"]')}else{if(!(b.fileInput instanceof a)){b.fileInput=a(b.fileInput)}}if(!(b.dropZone instanceof a)){b.dropZone=a(b.dropZone)}if(!(b.pasteZone instanceof a)){b.pasteZone=a(b.pasteZone)}},_getRegExp:function(d){var c=d.split("/"),b=c.pop();c.shift();return new RegExp(c.join("/"),b)},_isRegExpOption:function(b,c){return b!=="url"&&a.type(c)==="string"&&/^\/.*\/[igm]{0,3}$/.test(c)},_initDataAttributes:function(){var c=this,b=this.options;a.each(a(this.element[0].cloneNode(false)).data(),function(d,e){if(c._isRegExpOption(d,e)){e=c._getRegExp(e)}b[d]=e})},_create:function(){this._initDataAttributes();this._initSpecialOptions();this._slots=[];this._sequence=this._getXHRPromise(true);this._sending=this._active=0;this._initProgressObject(this);this._initEventHandlers()},active:function(){return this._active},progress:function(){return this._progress},add:function(c){var b=this;if(!c||this.options.disabled){return}if(c.fileInput&&!c.files){this._getFileInputFiles(c.fileInput).always(function(d){c.files=d;b._onAdd(null,c)})}else{c.files=a.makeArray(c.files);this._onAdd(null,c)}},send:function(f){if(f&&!this.options.disabled){if(f.fileInput&&!f.files){var d=this,b=a.Deferred(),g=b.promise(),c,e;g.abort=function(){e=true;if(c){return c.abort()}b.reject(null,"abort","abort");return g};this._getFileInputFiles(f.fileInput).always(function(h){if(e){return}if(!h.length){b.reject();return}f.files=h;c=d._onSend(null,f).then(function(i,k,j){b.resolve(i,k,j)},function(i,k,j){b.reject(i,k,j)})});return this._enhancePromise(g)}f.files=a.makeArray(f.files);if(f.files.length){return this._onSend(null,f)}}return this._getXHRPromise(false,f&&f.context)}})}));/*!jQuery Knob*/
(function(d){var b={},a=Math.max,c=Math.min;b.c={};b.c.d=d(document);b.c.t=function(f){return f.originalEvent.touches.length-1};b.o=function(){var e=this;this.o=null;this.$=null;this.i=null;this.g=null;this.v=null;this.cv=null;this.x=0;this.y=0;this.w=0;this.h=0;this.$c=null;this.c=null;this.t=0;this.isInit=false;this.fgColor=null;this.pColor=null;this.dH=null;this.cH=null;this.eH=null;this.rH=null;this.scale=1;this.relative=false;this.relativeWidth=false;this.relativeHeight=false;this.$div=null;this.run=function(){var f=function(i,h){var g;for(g in h){e.o[g]=h[g]}e.init();e._configure()._draw()};if(this.$.data("kontroled")){return}this.$.data("kontroled",true);this.extend();this.o=d.extend({min:this.$.data("min")||0,max:this.$.data("max")||100,stopper:true,readOnly:this.$.data("readonly")||(this.$.attr("readonly")=="readonly"),cursor:(this.$.data("cursor")===true&&30)||this.$.data("cursor")||0,thickness:(this.$.data("thickness")&&Math.max(Math.min(this.$.data("thickness"),1),0.01))||0.35,lineCap:this.$.data("linecap")||"butt",width:this.$.data("width")||200,height:this.$.data("height")||200,displayInput:this.$.data("displayinput")==null||this.$.data("displayinput"),displayPrevious:this.$.data("displayprevious"),fgColor:this.$.data("fgcolor")||"#87CEEB",inputColor:this.$.data("inputcolor"),font:this.$.data("font")||"Arial",fontWeight:this.$.data("font-weight")||"bold",inline:false,step:this.$.data("step")||1,draw:null,change:null,cancel:null,release:null,error:null},this.o);if(!this.o.inputColor){this.o.inputColor=this.o.fgColor}if(this.$.is("fieldset")){this.v={};this.i=this.$.find("input");this.i.each(function(g){var h=d(this);e.i[g]=h;e.v[g]=h.val();h.bind("change",function(){var i={};i[g]=h.val();e.val(i)})});this.$.find("legend").remove()}else{this.i=this.$;this.v=this.$.val();(this.v=="")&&(this.v=this.o.min);this.$.bind("change",function(){e.val(e._validate(e.$.val()))})}(!this.o.displayInput)&&this.$.hide();this.$c=d(document.createElement("canvas"));if(typeof G_vmlCanvasManager!=="undefined"){G_vmlCanvasManager.initElement(this.$c[0])}this.c=this.$c[0].getContext?this.$c[0].getContext("2d"):null;if(!this.c){this.o.error&&this.o.error();return}this.scale=(window.devicePixelRatio||1)/(this.c.webkitBackingStorePixelRatio||this.c.mozBackingStorePixelRatio||this.c.msBackingStorePixelRatio||this.c.oBackingStorePixelRatio||this.c.backingStorePixelRatio||1);this.relativeWidth=((this.o.width%1!==0)&&this.o.width.indexOf("%"));this.relativeHeight=((this.o.height%1!==0)&&this.o.height.indexOf("%"));this.relative=(this.relativeWidth||this.relativeHeight);this.$div=d('<div style="'+(this.o.inline?"display:inline;":"")+'"></div>');this.$.wrap(this.$div).before(this.$c);this.$div=this.$.parent();this._carve();if(this.v instanceof Object){this.cv={};this.copy(this.v,this.cv)}else{this.cv=this.v}this.$.bind("configure",f).parent().bind("configure",f);this._listen()._configure()._xy().init();this.isInit=true;this._draw();return this};this._carve=function(){if(this.relative){var f=this.relativeWidth?this.$div.parent().width()*parseInt(this.o.width)/100:this.$div.parent().width(),g=this.relativeHeight?this.$div.parent().height()*parseInt(this.o.height)/100:this.$div.parent().height();this.w=this.h=Math.min(f,g)}else{this.w=this.o.width;this.h=this.o.height}this.$div.css({width:this.w+"px",height:this.h+"px"});this.$c.attr({width:this.w,height:this.h});if(this.scale!==1){this.$c[0].width=this.$c[0].width*this.scale;this.$c[0].height=this.$c[0].height*this.scale;this.$c.width(this.w);this.$c.height(this.h)}return this};this._draw=function(){var f=true;e.g=e.c;e.clear();e.dH&&(f=e.dH());(f!==false)&&e.draw()};this._touch=function(f){var g=function(i){var h=e.xy2val(i.originalEvent.touches[e.t].pageX,i.originalEvent.touches[e.t].pageY);if(h==e.cv){return}if(e.cH&&(e.cH(h)===false)){return}e.change(e._validate(h));e._draw()};this.t=b.c.t(f);g(f);b.c.d.bind("touchmove.k",g).bind("touchend.k",function(){b.c.d.unbind("touchmove.k touchend.k");if(e.rH&&(e.rH(e.cv)===false)){return}e.val(e.cv)});return this};this._mouse=function(g){var f=function(i){var h=e.xy2val(i.pageX,i.pageY);if(h==e.cv){return}if(e.cH&&(e.cH(h)===false)){return}e.change(e._validate(h));e._draw()};f(g);b.c.d.bind("mousemove.k",f).bind("keyup.k",function(h){if(h.keyCode===27){b.c.d.unbind("mouseup.k mousemove.k keyup.k");if(e.eH&&(e.eH()===false)){return}e.cancel()}}).bind("mouseup.k",function(h){b.c.d.unbind("mousemove.k mouseup.k keyup.k");if(e.rH&&(e.rH(e.cv)===false)){return}e.val(e.cv)});return this};this._xy=function(){var f=this.$c.offset();this.x=f.left;this.y=f.top;return this};this._listen=function(){if(!this.o.readOnly){this.$c.bind("mousedown",function(f){f.preventDefault();e._xy()._mouse(f)}).bind("touchstart",function(f){f.preventDefault();e._xy()._touch(f)});this.listen()}else{this.$.attr("readonly","readonly")}if(this.relative){d(window).resize(function(){e._carve().init();e._draw()})}return this};this._configure=function(){if(this.o.draw){this.dH=this.o.draw}if(this.o.change){this.cH=this.o.change}if(this.o.cancel){this.eH=this.o.cancel}if(this.o.release){this.rH=this.o.release}if(this.o.displayPrevious){this.pColor=this.h2rgba(this.o.fgColor,"0.4");this.fgColor=this.h2rgba(this.o.fgColor,"0.6")}else{this.fgColor=this.o.fgColor}return this};this._clear=function(){this.$c[0].width=this.$c[0].width};this._validate=function(f){return(~~(((f<0)?-0.5:0.5)+(f/this.o.step)))*this.o.step};this.listen=function(){};this.extend=function(){};this.init=function(){};this.change=function(f){};this.val=function(f){};this.xy2val=function(f,g){};this.draw=function(){};this.clear=function(){this._clear()};this.h2rgba=function(i,f){var g;i=i.substring(1,7);g=[parseInt(i.substring(0,2),16),parseInt(i.substring(2,4),16),parseInt(i.substring(4,6),16)];return"rgba("+g[0]+","+g[1]+","+g[2]+","+f+")"};this.copy=function(j,h){for(var g in j){h[g]=j[g]}}};b.Dial=function(){b.o.call(this);this.startAngle=null;this.xy=null;this.radius=null;this.lineWidth=null;this.cursorExt=null;this.w2=null;this.PI2=2*Math.PI;this.extend=function(){this.o=d.extend({bgColor:this.$.data("bgcolor")||"#EEEEEE",angleOffset:this.$.data("angleoffset")||0,angleArc:this.$.data("anglearc")||360,inline:true},this.o)};this.val=function(e){if(null!=e){this.cv=this.o.stopper?a(c(e,this.o.max),this.o.min):e;this.v=this.cv;this.$.val(this.v);this._draw()}else{return this.v}};this.xy2val=function(e,h){var f,g;f=Math.atan2(e-(this.x+this.w2),-(h-this.y-this.w2))-this.angleOffset;if(this.angleArc!=this.PI2&&(f<0)&&(f>-0.5)){f=0}else{if(f<0){f+=this.PI2}}g=~~(0.5+(f*(this.o.max-this.o.min)/this.angleArc))+this.o.min;this.o.stopper&&(g=a(c(g,this.o.max),this.o.min));return g};this.listen=function(){var f=this,j=function(o){o.preventDefault();var n=o.originalEvent,l=n.detail||n.wheelDeltaX,k=n.detail||n.wheelDeltaY,m=parseInt(f.$.val())+(l>0||k>0?f.o.step:l<0||k<0?-f.o.step:0);if(f.cH&&(f.cH(m)===false)){return}f.val(m)},h,i,e=1,g={37:-f.o.step,38:f.o.step,39:f.o.step,40:-f.o.step};this.$.bind("keydown",function(m){var l=m.keyCode;if(l>=96&&l<=105){l=m.keyCode=l-48}h=parseInt(String.fromCharCode(l));if(isNaN(h)){(l!==13)&&(l!==8)&&(l!==9)&&(l!==189)&&m.preventDefault();if(d.inArray(l,[37,38,39,40])>-1){m.preventDefault();var k=parseInt(f.$.val())+g[l]*e;f.o.stopper&&(k=a(c(k,f.o.max),f.o.min));f.change(k);f._draw();i=window.setTimeout(function(){e*=2},30)}}}).bind("keyup",function(k){if(isNaN(h)){if(i){window.clearTimeout(i);i=null;e=1;f.val(f.$.val())}}else{(f.$.val()>f.o.max&&f.$.val(f.o.max))||(f.$.val()<f.o.min&&f.$.val(f.o.min))}});this.$c.bind("mousewheel DOMMouseScroll",j);this.$.bind("mousewheel DOMMouseScroll",j)};this.init=function(){if(this.v<this.o.min||this.v>this.o.max){this.v=this.o.min}this.$.val(this.v);this.w2=this.w/2;this.cursorExt=this.o.cursor/100;this.xy=this.w2*this.scale;this.lineWidth=this.xy*this.o.thickness;this.lineCap=this.o.lineCap;this.radius=this.xy-this.lineWidth/2;this.o.angleOffset&&(this.o.angleOffset=isNaN(this.o.angleOffset)?0:this.o.angleOffset);this.o.angleArc&&(this.o.angleArc=isNaN(this.o.angleArc)?this.PI2:this.o.angleArc);this.angleOffset=this.o.angleOffset*Math.PI/180;this.angleArc=this.o.angleArc*Math.PI/180;this.startAngle=1.5*Math.PI+this.angleOffset;this.endAngle=1.5*Math.PI+this.angleOffset+this.angleArc;var e=a(String(Math.abs(this.o.max)).length,String(Math.abs(this.o.min)).length,2)+2;this.o.displayInput&&this.i.css({width:((this.w/2+4)>>0)+"px",height:((this.w/3)>>0)+"px",position:"absolute","vertical-align":"middle","margin-top":((this.w/3)>>0)+"px","margin-left":"-"+((this.w*3/4+2)>>0)+"px",border:0,background:"none",font:this.o.fontWeight+" "+((this.w/e)>>0)+"px "+this.o.font,"text-align":"center",color:this.o.inputColor||this.o.fgColor,padding:"0px","-webkit-appearance":"none"})||this.i.css({width:"0px",visibility:"hidden"})};this.change=function(e){this.cv=e;this.$.val(e)};this.angle=function(e){return(e-this.o.min)*this.angleArc/(this.o.max-this.o.min)};this.draw=function(){var k=this.g,f=this.angle(this.cv),g=this.startAngle,h=g+f,e,i,j=1;k.lineWidth=this.lineWidth;k.lineCap=this.lineCap;this.o.cursor&&(g=h-this.cursorExt)&&(h=h+this.cursorExt);k.beginPath();k.strokeStyle=this.o.bgColor;k.arc(this.xy,this.xy,this.radius,this.endAngle,this.startAngle,true);k.stroke();if(this.o.displayPrevious){i=this.startAngle+this.angle(this.v);e=this.startAngle;this.o.cursor&&(e=i-this.cursorExt)&&(i=i+this.cursorExt);k.beginPath();k.strokeStyle=this.pColor;k.arc(this.xy,this.xy,this.radius,e,i,false);k.stroke();j=(this.cv==this.v)}k.beginPath();k.strokeStyle=j?this.o.fgColor:this.fgColor;k.arc(this.xy,this.xy,this.radius,g,h,false);k.stroke()};this.cancel=function(){this.val(this.v)}};d.fn.dial=d.fn.knob=function(e){return this.each(function(){var f=new b.Dial();f.o=e;f.$=d(this);f.run()}).parent()}})(jQuery);/* Laura Doktorova https://github.com/olado/doT */
(function(){function o(){var a={"&":"&#38;","<":"&#60;",">":"&#62;",'"':"&#34;","'":"&#39;","/":"&#47;"},b=/&(?!#?\w+;)|<|>|"|'|\//g;return function(){return this?this.replace(b,function(c){return a[c]||c}):this}}function p(a,b,c){return(typeof b==="string"?b:b.toString()).replace(a.define||i,function(l,e,f,g){if(e.indexOf("def.")===0)e=e.substring(4);if(!(e in c))if(f===":"){a.defineParams&&g.replace(a.defineParams,function(n,h,d){c[e]={arg:h,text:d}});e in c||(c[e]=g)}else(new Function("def","def['"+
e+"']="+g))(c);return""}).replace(a.use||i,function(l,e){if(a.useParams)e=e.replace(a.useParams,function(g,n,h,d){if(c[h]&&c[h].arg&&d){g=(h+":"+d).replace(/'|\\/g,"_");c.__exp=c.__exp||{};c.__exp[g]=c[h].text.replace(RegExp("(^|[^\\w$])"+c[h].arg+"([^\\w$])","g"),"$1"+d+"$2");return n+"def.__exp['"+g+"']"}});var f=(new Function("def","return "+e))(c);return f?p(a,f,c):f})}function m(a){return a.replace(/\\('|\\)/g,"$1").replace(/[\r\t\n]/g," ")}var j={version:"1.0.1",templateSettings:{evaluate:/\{\{([\s\S]+?(\}?)+)\}\}/g,
interpolate:/\{\{=([\s\S]+?)\}\}/g,encode:/\{\{!([\s\S]+?)\}\}/g,use:/\{\{#([\s\S]+?)\}\}/g,useParams:/(^|[^\w$])def(?:\.|\[[\'\"])([\w$\.]+)(?:[\'\"]\])?\s*\:\s*([\w$\.]+|\"[^\"]+\"|\'[^\']+\'|\{[^\}]+\})/g,define:/\{\{##\s*([\w\.$]+)\s*(\:|=)([\s\S]+?)#\}\}/g,defineParams:/^\s*([\w$]+):([\s\S]+)/,conditional:/\{\{\?(\?)?\s*([\s\S]*?)\s*\}\}/g,iterate:/\{\{~\s*(?:\}\}|([\s\S]+?)\s*\:\s*([\w$]+)\s*(?:\:\s*([\w$]+))?\s*\}\})/g,varname:"it",strip:true,append:true,selfcontained:false},template:undefined,
compile:undefined},q;if(typeof module!=="undefined"&&module.exports)module.exports=j;else if(typeof define==="function"&&define.amd)define(function(){return j});else{q=function(){return this||(0,eval)("this")}();q.doT=j}String.prototype.encodeHTML=o();var r={append:{start:"'+(",end:")+'",endencode:"||'').toString().encodeHTML()+'"},split:{start:"';out+=(",end:");out+='",endencode:"||'').toString().encodeHTML();out+='"}},i=/$^/;j.template=function(a,b,c){b=b||j.templateSettings;var l=b.append?r.append:
r.split,e,f=0,g;a=b.use||b.define?p(b,a,c||{}):a;a=("var out='"+(b.strip?a.replace(/(^|\r|\n)\t* +| +\t*(\r|\n|$)/g," ").replace(/\r|\n|\t|\/\*[\s\S]*?\*\//g,""):a).replace(/'|\\/g,"\\$&").replace(b.interpolate||i,function(h,d){return l.start+m(d)+l.end}).replace(b.encode||i,function(h,d){e=true;return l.start+m(d)+l.endencode}).replace(b.conditional||i,function(h,d,k){return d?k?"';}else if("+m(k)+"){out+='":"';}else{out+='":k?"';if("+m(k)+"){out+='":"';}out+='"}).replace(b.iterate||i,function(h,
d,k,s){if(!d)return"';} } out+='";f+=1;g=s||"i"+f;d=m(d);return"';var arr"+f+"="+d+";if(arr"+f+"){var "+k+","+g+"=-1,l"+f+"=arr"+f+".length-1;while("+g+"<l"+f+"){"+k+"=arr"+f+"["+g+"+=1];out+='"}).replace(b.evaluate||i,function(h,d){return"';"+m(d)+"out+='"})+"';return out;").replace(/\n/g,"\\n").replace(/\t/g,"\\t").replace(/\r/g,"\\r").replace(/(\s|;|\}|^|\{)out\+='';/g,"$1").replace(/\+''/g,"").replace(/(\s|;|\}|^|\{)out\+=''\+/g,"$1out+=");if(e&&b.selfcontained)a="String.prototype.encodeHTML=("+
o.toString()+"());"+a;try{return new Function(b.varname,a)}catch(n){typeof console!=="undefined"&&console.log("Could not create a template function: "+a);throw n;}};j.compile=function(a,b){return j.template(a,null,b)}})();
function api(url, data, success, error) {
    if (!url)
        apiFatal('no API URL set');
    else
        $.ajax({
            type: (data == null ? 'GET' : 'POST'),
            url: url,
            data: data,
            dataType: 'json',
            success: function(data) {
                if (typeof data['error'] !== 'undefined')
                {
                    if (typeof error !== 'undefined' && error)
                        if (error(data) === false)
                            return;

                    apiFatal(data['error'].join('<br>'));
                }
                else if (typeof success !== 'undefined' && success)
                {
                    success(data);
                    if (typeof applyTooltips !== 'undefined')
                        applyTooltips();
                }
            },
            error: function(data) {
                if (typeof error !== 'undefined' && error)
                    if (error(data) === false)
                        return;

                if (typeof data['responseJSON'] !== 'undefined' && typeof data['responseJSON']['error'] !== 'undefined') // PHP error but still handled by API
                    apiFatal(data['responseJSON']['error'].join('<br>'));
                else if (typeof data['responseText'] !== 'undefined') // Non-JSON response
                    apiFatal(data['responseText']);
                else if (typeof data['statusText'] !== 'undefined') // Some XHR thing went wrong
                    apiFatal(data['statusText']);
                else // ...shrugs
                    apiFatal(data);
            }
        });
}

function apiFatal(message) {
    $.fancybox.open({
        content: message,
        closeBtn: false,
        beforeShow: function() {
            this.skin.css({
                'background': '#F2DEDE',
                'color': '#B94A48',
                'border': 'solid 1px #EED3D7'
            });
        },
        overlay: {
            closeClick: true,
            locked: false
        }
    });
}

function apiStatusClear() {
    $('#api_status div').stop(true).hide();
}

function apiStatusWorking(message) {
    apiStatusClear();
    $('#api_status div.working').delay(800).fadeIn('fast');
    if (typeof message !== 'undefined') {
        $('#api_status div.working span').delay(800).html(message).find('span[data-time]').attr('data-time', new Date().getTime());
        apiStatusTime();
    }
}

function apiStatusSuccess(message) {
    apiStatusClear();
    $('#api_status div.success').fadeIn('fast');
    if (typeof message !== 'undefined') {
        $('#api_status div.success span').html(message).find('span[data-time]').attr('data-time', new Date().getTime());
        apiStatusTime();
    }
}

function apiStatusError(message) {
    apiStatusClear();
    $('#api_status div.error').fadeIn('fast');
    if (typeof message !== 'undefined') {
        $('#api_status div.error span').html(message).find('span[data-time]').attr('data-time', new Date().getTime());
        apiStatusTime();
    }
}

function apiStatusTime() {
    $('span[data-time]').each(function () {
        var self = $(this),
            time = parseInt(self.attr('data-time'));
        if (!isNaN(time)) {
            var diff = Math.round((new Date().getTime() - time) / 1000),
                then = new Date(time),
                value;

            if (diff < 15)
                value = ' just now';
            else if (diff < 45)
                value = ' half a minute ago';
            else if (diff < 90)
                value = ' 1 minute ago';
            else if (diff < 600)
                value = ' ' + Math.round(diff / 60) + ' minutes ago';
            else
                value = ' ' + then.getHours() + ':' + then.getMinutes();

            if (self.text() == '')
                self.text(value);
            else if (self.text() !== value)
                self.fadeOut(function () {
                    self.text(value);
                }).fadeIn();
        }
    });
}

function apiLoadStatusClear(load) {
    load.find('div').stop(true).hide();
}

function apiLoadStatusWorking(load) {
    apiLoadStatusClear(load);
    load.find('div.working').fadeIn('fast');
}

function apiLoadStatusSuccess(load) {
    load.remove();
}

function apiLoadStatusEmpty(load) {
    apiLoadStatusClear(load);
    load.find('div.empty').fadeIn('fast');
}

function apiLoadStatusError(load) {
    apiLoadStatusClear(load);
    load.find('div.error').fadeIn('fast');
}

$(function() {
    setInterval(apiStatusTime, 5000);
    apiStatusTime();
});

var apiUpdateConsoleTimeout;
function apiUpdateConsole(dest) {
    apiUpdateConsoleTimeout = setTimeout(function() {
        api('/' + base_url + 'api/core/index/', {
            action: 'console'
        }, function(data) {
            if (typeof data['status'] !== 'undefined')
            {
                dest.html(data['status']);
                dest[0].scrollTop = dest[0].scrollHeight;
            }
            apiUpdateConsole(dest);
        });
    }, 200);
}

function apiStopConsole() {
    setTimeout(function() {
        clearTimeout(apiUpdateConsoleTimeout);
    }, 1000);
}$(function() {
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
});$('#publish-site').click(function() {
    $.fancybox.open({
        content: '<textarea id="console" readonly></textarea>'
    });

    apiStatusWorking('Publishing site...');
    apiUpdateConsole($('#console'));
    api('/' + base_url + 'api/core/publish-site/', {
    }, function(data) {
        apiStopConsole();
        apiStatusSuccess('Published site');
    }, function() {
        apiStopConsole();
        apiStatusError('Publishing site failed');
        return false;
    });
});

$('#edit').on('click', 'a', function() {
    $('#edit').fadeOut('fast', function() {
        $('#save').fadeIn('fast');
    });
});

$('#log-out').click(function() {
    apiStatusWorking('Logging out...');
    api('/' + base_url + 'api/core/users/', {
        'action': 'logout'
    }, function(data) {
        $('#api_fatal').fadeOut().remove();
        $('#api_status').fadeOut().remove();
        $('#admin-bar').slideUp(function() {
            this.remove();
        });
        $('body').animate({
            'padding-top': '0'
        });
    }, function() {
        apiStatusError('Logging out failed');
        return false;
    });
});function initializeUpload(upload, done) {
    upload = $(upload);

    var total_loaded = 0, total_size = 0;
    upload.find('#big-knob input, #small-knob input').knob();

    var upload_i = 0;
    var done_n = 0;
    upload.find('#drop a').click(function() {
        $(this).parent().find('input').click();
    });

    upload.fileupload({
        dropZone: upload,
        dataType: 'json',
        sequentialUploads: true,

        add: function (e, data) {
            if (upload_i == done_n) {
                upload.find('ul li').remove();
                total_loaded = 0;
                total_size = 0;
            }
            total_size += data.files[0].size;

            data.i = upload_i;
            upload_i++;

            $('<li id="upload_' + data.i + '"><i class="fa fa-fw fa-cog fa-spin"></i>&ensp;' + data.files[0].name + '</li>').hide().appendTo('#upload ul').slideDown();
            var jqXHR = data.submit();
        },
        progress: function(e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            var bigProgress = (total_size > 0 ? parseInt((total_loaded + data.loaded) / total_size * 100, 10) : 100);
            upload.find('#small-knob input').val(progress).change();
            upload.find('#big-knob input').val(bigProgress).change();
        },
        always: function(e, data) {
            total_loaded += data.total;
            done_n++;
        },
        done: function(e, data) {
            if (typeof data.response().result['upload_error'] !== 'undefined')
                upload.find('#upload_' + data.i).addClass('fail').append(' (' + data.response().result['upload_error'] + ')').find('i').attr('class', 'fa fa-fw fa-times');
            else {
                upload.find('#upload_' + data.i).addClass('done').find('i').attr('class', 'fa fa-fw fa-check');
                done(data.response().result);
            }
        },
        fail: function(e, data) {
            if (typeof data.response().jqXHR['responseText'] !== 'undefined')
                apiFatal(data.response().jqXHR['responseText']);
            upload.find('#upload_' + data.i).addClass('fail').append(' (Unknown error)').find('i').attr('class', 'fa fa-fw fa-times');
        }
    });

    $(document).on('drop dragover', function (e) {
        e.preventDefault();
    });

    function formatFileSize(bytes) {
        if (typeof bytes !== 'number')
            return '';
        if (bytes >= 1000000000)
            return (bytes / 1000000000).toFixed(2) + ' GB';
        if (bytes >= 1000000)
            return (bytes / 1000000).toFixed(2) + ' MB';
        return (bytes / 1000).toFixed(2) + ' KB';
    }
}(function($){$.fn.markItUp=function(settings,extraSettings){var method,params,options,ctrlKey,shiftKey,altKey;ctrlKey=shiftKey=altKey=false;if(typeof settings=="string"){method=settings;params=extraSettings}options={id:"",nameSpace:"",root:"",previewHandler:false,previewInWindow:"",previewInElement:"",previewAutoRefresh:true,previewPosition:"after",previewTemplatePath:"~/templates/preview.html",previewParser:false,previewParserPath:"",previewParserVar:"data",resizeHandle:true,beforeInsert:"",afterInsert:"",onEnter:{},onShiftEnter:{},onCtrlEnter:{},onTab:{},markupSet:[{}]};$.extend(options,settings,extraSettings);if(!options.root){$("script").each(function(a,tag){miuScript=$(tag).get(0).src.match(/(.*)jquery\.markitup(\.pack)?\.js$/);if(miuScript!==null){options.root=miuScript[1]}})}var uaMatch=function(ua){ua=ua.toLowerCase();var match=/(chrome)[ \/]([\w.]+)/.exec(ua)||/(webkit)[ \/]([\w.]+)/.exec(ua)||/(opera)(?:.*version|)[ \/]([\w.]+)/.exec(ua)||/(msie) ([\w.]+)/.exec(ua)||ua.indexOf("compatible")<0&&/(mozilla)(?:.*? rv:([\w.]+)|)/.exec(ua)||[];return{browser:match[1]||"",version:match[2]||"0"}};var matched=uaMatch(navigator.userAgent);var browser={};if(matched.browser){browser[matched.browser]=true;browser.version=matched.version}if(browser.chrome){browser.webkit=true}else{if(browser.webkit){browser.safari=true}}return this.each(function(){var $$,textarea,levels,scrollPosition,caretPosition,caretOffset,clicked,hash,header,footer,previewWindow,template,iFrame,abort;$$=$(this);textarea=this;levels=[];abort=false;scrollPosition=caretPosition=0;caretOffset=-1;options.previewParserPath=localize(options.previewParserPath);options.previewTemplatePath=localize(options.previewTemplatePath);if(method){switch(method){case"remove":remove();break;case"insert":markup(params);break;default:$.error("Method "+method+" does not exist on jQuery.markItUp")}return}function localize(data,inText){if(inText){return data.replace(/("|')~\//g,"$1"+options.root)}return data.replace(/^~\//,options.root)}function init(){id="";nameSpace="";if(options.id){id='id="'+options.id+'"'}else{if($$.attr("id")){id='id="markItUp'+($$.attr("id").substr(0,1).toUpperCase())+($$.attr("id").substr(1))+'"'}}if(options.nameSpace){nameSpace='class="'+options.nameSpace+'"'}$$.wrap("<div "+nameSpace+"></div>");$$.wrap("<div "+id+' class="markItUp"></div>');$$.wrap('<div class="markItUpContainer"></div>');$$.addClass("markItUpEditor");header=$('<div class="markItUpHeader"></div>').insertBefore($$);$(dropMenus(options.markupSet)).appendTo(header);footer=$('<div class="markItUpFooter"></div>').insertAfter($$);if(options.resizeHandle===true&&browser.safari!==true){resizeHandle=$('<div class="markItUpResizeHandle"></div>').insertAfter($$).bind("mousedown.markItUp",function(e){var h=$$.height(),y=e.clientY,mouseMove,mouseUp;mouseMove=function(e){$$.css("height",Math.max(20,e.clientY+h-y)+"px");return false};mouseUp=function(e){$("html").unbind("mousemove.markItUp",mouseMove).unbind("mouseup.markItUp",mouseUp);return false};$("html").bind("mousemove.markItUp",mouseMove).bind("mouseup.markItUp",mouseUp)});footer.append(resizeHandle)}$$.bind("keydown.markItUp",keyPressed).bind("keyup",keyPressed);$$.bind("insertion.markItUp",function(e,settings){if(settings.target!==false){get()}if(textarea===$.markItUp.focused){markup(settings)}});$$.bind("focus.markItUp",function(){$.markItUp.focused=this});if(options.previewInElement){refreshPreview()}}function dropMenus(markupSet){var ul=$("<ul></ul>"),i=0;$("li:hover > ul",ul).css("display","block");$.each(markupSet,function(){var button=this,t="",title,li,j;title=(button.key)?(button.name||"")+" [Ctrl+"+button.key+"]":(button.name||"");key=(button.key)?'accesskey="'+button.key+'"':"";if(button.separator){li=$('<li class="markItUpSeparator">'+(button.separator||"")+"</li>").appendTo(ul)}else{i++;for(j=levels.length-1;j>=0;j--){t+=levels[j]+"-"}li=$('<li class="markItUpButton markItUpButton'+t+(i)+" "+(button.className||"")+'"><a href="" '+key+' title="'+title+'">'+(button.name||"")+"</a></li>").bind("contextmenu.markItUp",function(){return false}).bind("click.markItUp",function(e){e.preventDefault()}).bind("focusin.markItUp",function(){$$.focus()}).bind("mouseup",function(){if(button.call){eval(button.call)()}setTimeout(function(){markup(button)},1);return false}).bind("mouseenter.markItUp",function(){$("> ul",this).show();$(document).one("click",function(){$("ul ul",header).hide()})}).bind("mouseleave.markItUp",function(){$("> ul",this).hide()}).appendTo(ul);if(button.dropMenu){levels.push(i);$(li).addClass("markItUpDropMenu").append(dropMenus(button.dropMenu))}}});levels.pop();return ul}function magicMarkups(string){if(string){string=string.toString();string=string.replace(/\(\!\(([\s\S]*?)\)\!\)/g,function(x,a){var b=a.split("|!|");if(altKey===true){return(b[1]!==undefined)?b[1]:b[0]}else{return(b[1]===undefined)?"":b[0]}});string=string.replace(/\[\!\[([\s\S]*?)\]\!\]/g,function(x,a){var b=a.split(":!:");if(abort===true){return false}value=prompt(b[0],(b[1])?b[1]:"");if(value===null){abort=true}return value});return string}return""}function prepare(action){if($.isFunction(action)){action=action(hash)}return magicMarkups(action)}function build(string){var openWith=prepare(clicked.openWith);var placeHolder=prepare(clicked.placeHolder);var replaceWith=prepare(clicked.replaceWith);var closeWith=prepare(clicked.closeWith);var openBlockWith=prepare(clicked.openBlockWith);var closeBlockWith=prepare(clicked.closeBlockWith);var multiline=clicked.multiline;if(replaceWith!==""){block=openWith+replaceWith+closeWith}else{if(selection===""&&placeHolder!==""){block=openWith+placeHolder+closeWith}else{string=string||selection;var lines=[string],blocks=[];if(multiline===true){lines=string.split(/\r?\n/)}for(var l=0;l<lines.length;l++){line=lines[l];var trailingSpaces;if(trailingSpaces=line.match(/ *$/)){blocks.push(openWith+line.replace(/ *$/g,"")+closeWith+trailingSpaces)}else{blocks.push(openWith+line+closeWith)}}block=blocks.join("\n")}}block=openBlockWith+block+closeBlockWith;return{block:block,openBlockWith:openBlockWith,openWith:openWith,replaceWith:replaceWith,placeHolder:placeHolder,closeWith:closeWith,closeBlockWith:closeBlockWith}}function markup(button){var len,j,n,i;hash=clicked=button;get();$.extend(hash,{line:"",root:options.root,textarea:textarea,selection:(selection||""),caretPosition:caretPosition,ctrlKey:ctrlKey,shiftKey:shiftKey,altKey:altKey});prepare(options.beforeInsert);prepare(clicked.beforeInsert);if((ctrlKey===true&&shiftKey===true)||button.multiline===true){prepare(clicked.beforeMultiInsert)}$.extend(hash,{line:1});if((ctrlKey===true&&shiftKey===true)){lines=selection.split(/\r?\n/);for(j=0,n=lines.length,i=0;i<n;i++){if($.trim(lines[i])!==""){$.extend(hash,{line:++j,selection:lines[i]});lines[i]=build(lines[i]).block}else{lines[i]=""}}string={block:lines.join("\n")};start=caretPosition;len=string.block.length+((browser.opera)?n-1:0)}else{if(ctrlKey===true){string=build(selection);start=caretPosition+string.openWith.length;len=string.block.length-string.openWith.length-string.closeWith.length;len=len-(string.block.match(/ $/)?1:0);len-=fixIeBug(string.block)}else{if(shiftKey===true){string=build(selection);start=caretPosition;len=string.block.length;len-=fixIeBug(string.block)}else{string=build(selection);start=caretPosition+string.block.length;len=0;start-=fixIeBug(string.block)}}}if((selection===""&&string.replaceWith==="")){caretOffset+=fixOperaBug(string.block);start=caretPosition+string.openBlockWith.length+string.openWith.length;len=string.block.length-string.openBlockWith.length-string.openWith.length-string.closeWith.length-string.closeBlockWith.length;caretOffset=$$.val().substring(caretPosition,$$.val().length).length;caretOffset-=fixOperaBug($$.val().substring(0,caretPosition))}$.extend(hash,{caretPosition:caretPosition,scrollPosition:scrollPosition});if(string.block!==selection&&abort===false){insert(string.block);set(start,len)}else{caretOffset=-1}get();$.extend(hash,{line:"",selection:selection});if((ctrlKey===true&&shiftKey===true)||button.multiline===true){prepare(clicked.afterMultiInsert)}prepare(clicked.afterInsert);prepare(options.afterInsert);if(previewWindow&&options.previewAutoRefresh){refreshPreview()}shiftKey=altKey=ctrlKey=abort=false}function fixOperaBug(string){if(browser.opera){return string.length-string.replace(/\n*/g,"").length}return 0}function fixIeBug(string){if(browser.msie){return string.length-string.replace(/\r*/g,"").length}return 0}function insert(block){if(document.selection){var newSelection=document.selection.createRange();newSelection.text=block}else{textarea.value=textarea.value.substring(0,caretPosition)+block+textarea.value.substring(caretPosition+selection.length,textarea.value.length)}}function set(start,len){if(textarea.createTextRange){if(browser.opera&&browser.version>=9.5&&len==0){return false}range=textarea.createTextRange();range.collapse(true);range.moveStart("character",start);range.moveEnd("character",len);range.select()}else{if(textarea.setSelectionRange){textarea.setSelectionRange(start,start+len)}}textarea.scrollTop=scrollPosition;textarea.focus()}function get(){textarea.focus();scrollPosition=textarea.scrollTop;if(document.selection){selection=document.selection.createRange().text;if(browser.msie){var range=document.selection.createRange(),rangeCopy=range.duplicate();rangeCopy.moveToElementText(textarea);caretPosition=-1;while(rangeCopy.inRange(range)){rangeCopy.moveStart("character");caretPosition++}}else{caretPosition=textarea.selectionStart}}else{caretPosition=textarea.selectionStart;selection=textarea.value.substring(caretPosition,textarea.selectionEnd)}return selection}function preview(){if(typeof options.previewHandler==="function"){previewWindow=true}else{if(options.previewInElement){previewWindow=$(options.previewInElement)}else{if(!previewWindow||previewWindow.closed){if(options.previewInWindow){previewWindow=window.open("","preview",options.previewInWindow);$(window).unload(function(){previewWindow.close()})}else{iFrame=$('<iframe class="markItUpPreviewFrame"></iframe>');if(options.previewPosition=="after"){iFrame.insertAfter(footer)}else{iFrame.insertBefore(header)}previewWindow=iFrame[iFrame.length-1].contentWindow||frame[iFrame.length-1]}}else{if(altKey===true){if(iFrame){iFrame.remove()}else{previewWindow.close()}previewWindow=iFrame=false}}}}if(!options.previewAutoRefresh){refreshPreview()}if(options.previewInWindow){previewWindow.focus()}}function refreshPreview(){renderPreview()}function renderPreview(){var phtml;if(options.previewHandler&&typeof options.previewHandler==="function"){options.previewHandler($$.val())}else{if(options.previewParser&&typeof options.previewParser==="function"){var data=options.previewParser($$.val());writeInPreview(localize(data,1))}else{if(options.previewParserPath!==""){$.ajax({type:"POST",dataType:"text",global:false,url:options.previewParserPath,data:options.previewParserVar+"="+encodeURIComponent($$.val()),success:function(data){writeInPreview(localize(data,1))}})}else{if(!template){$.ajax({url:options.previewTemplatePath,dataType:"text",global:false,success:function(data){writeInPreview(localize(data,1).replace(/<!-- content -->/g,$$.val()))}})}}}}return false}function writeInPreview(data){if(options.previewInElement){$(options.previewInElement).html(data)}else{if(previewWindow&&previewWindow.document){try{sp=previewWindow.document.documentElement.scrollTop}catch(e){sp=0}previewWindow.document.open();previewWindow.document.write(data);previewWindow.document.close();previewWindow.document.documentElement.scrollTop=sp}}}function keyPressed(e){shiftKey=e.shiftKey;altKey=e.altKey;ctrlKey=(!(e.altKey&&e.ctrlKey))?(e.ctrlKey||e.metaKey):false;if(e.type==="keydown"){if(ctrlKey===true){li=$('a[accesskey="'+((e.keyCode==13)?"\\n":String.fromCharCode(e.keyCode))+'"]',header).parent("li");if(li.length!==0){ctrlKey=false;setTimeout(function(){li.triggerHandler("mouseup")},1);return false}}if(e.keyCode===13||e.keyCode===10){if(ctrlKey===true){ctrlKey=false;markup(options.onCtrlEnter);return options.onCtrlEnter.keepDefault}else{if(shiftKey===true){shiftKey=false;markup(options.onShiftEnter);return options.onShiftEnter.keepDefault}else{markup(options.onEnter);return options.onEnter.keepDefault}}}if(e.keyCode===9){if(shiftKey==true||ctrlKey==true||altKey==true){return false}if(caretOffset!==-1){get();caretOffset=$$.val().length-caretOffset;set(caretOffset,0);caretOffset=-1;return false}else{markup(options.onTab);return options.onTab.keepDefault}}}}function remove(){$$.unbind(".markItUp").removeClass("markItUpEditor");$$.parent("div").parent("div.markItUp").parent("div").replaceWith($$);$$.data("markItUp",null)}init()})};$.fn.markItUpRemove=function(){return this.each(function(){$(this).markItUp("remove")})};$.markItUp=function(settings){var options={target:false};$.extend(options,settings);if(options.target){return $(options.target).each(function(){$(this).focus();$(this).trigger("insertion",[options])})}else{$("textarea").trigger("insertion",[options])}}})(jQuery);// -------------------------------------------------------------------
// markItUp!
// -------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// -------------------------------------------------------------------

mySettings = {
	previewParserPath:	'/' + base_url + 'admin/auxiliary/markdown_preview/',
	onShiftEnter:		{keepDefault:false, openWith:'\n\n'},
	markupSet: [
		{name:'Heading 3', key:'3', openWith:'### ', placeHolder:'Your title here...' },
		{name:'Heading 4', key:'4', openWith:'#### ', placeHolder:'Your title here...' },
		{name:'Heading 5', key:'5', openWith:'##### ', placeHolder:'Your title here...' },
		{name:'Heading 6', key:'6', openWith:'###### ', placeHolder:'Your title here...' },
		{separator:'---------------' },
		{name:'Bold', key:'B', openWith:'**', closeWith:'**'},
		{name:'Italic', key:'I', openWith:'_', closeWith:'_'},
		{separator:'---------------' },
		{name:'Bulleted List', openWith:'- ' },
		{name:'Numeric List', openWith:function(markItUp) {
			return markItUp.line+'. ';
		}},
		{name:'Preview', call:'preview', className:"hidden"}
	]
};

// mIu nameSpace to avoid conflict.
miu = {
	markdownTitle: function(markItUp, c) {
		heading = '';
		n = $.trim(markItUp.selection || markItUp.placeHolder).length;
		for(i = 0; i < n; i++) {
			heading += c;
		}
		return '\n' + heading;
	}
};var Sha1={};Sha1.hash=function(h,z){z=(typeof z=="undefined")?true:z;if(z){h=Utf8.encode(h)}var n=[1518500249,1859775393,2400959708,3395469782];h+=String.fromCharCode(128);var x=h.length/4+2;var k=Math.ceil(x/16);var m=new Array(k);for(var A=0;A<k;A++){m[A]=new Array(16);for(var y=0;y<16;y++){m[A][y]=(h.charCodeAt(A*64+y*4)<<24)|(h.charCodeAt(A*64+y*4+1)<<16)|(h.charCodeAt(A*64+y*4+2)<<8)|(h.charCodeAt(A*64+y*4+3))}}m[k-1][14]=((h.length-1)*8)/Math.pow(2,32);m[k-1][14]=Math.floor(m[k-1][14]);m[k-1][15]=((h.length-1)*8)&4294967295;var u=1732584193;var r=4023233417;var q=2562383102;var p=271733878;var o=3285377520;var f=new Array(80);var F,E,D,C,B;for(var A=0;A<k;A++){for(var v=0;v<16;v++){f[v]=m[A][v]}for(var v=16;v<80;v++){f[v]=Sha1.ROTL(f[v-3]^f[v-8]^f[v-14]^f[v-16],1)}F=u;E=r;D=q;C=p;B=o;for(var v=0;v<80;v++){var w=Math.floor(v/20);var g=(Sha1.ROTL(F,5)+Sha1.f(w,E,D,C)+B+n[w]+f[v])&4294967295;B=C;C=D;D=Sha1.ROTL(E,30);E=F;F=g}u=(u+F)&4294967295;r=(r+E)&4294967295;q=(q+D)&4294967295;p=(p+C)&4294967295;o=(o+B)&4294967295}return Sha1.toHexStr(u)+Sha1.toHexStr(r)+Sha1.toHexStr(q)+Sha1.toHexStr(p)+Sha1.toHexStr(o)};Sha1.f=function(b,a,d,c){switch(b){case 0:return(a&d)^(~a&c);case 1:return a^d^c;case 2:return(a&d)^(a&c)^(d&c);case 3:return a^d^c}};Sha1.ROTL=function(a,b){return(a<<b)|(a>>>(32-b))};Sha1.toHexStr=function(d){var c="",a;for(var b=7;b>=0;b--){a=(d>>>(b*4))&15;c+=a.toString(16)}return c};var Utf8={};Utf8.encode=function(a){var b=a.replace(/[\u0080-\u07ff]/g,function(e){var d=e.charCodeAt(0);return String.fromCharCode(192|d>>6,128|d&63)});b=b.replace(/[\u0800-\uffff]/g,function(e){var d=e.charCodeAt(0);return String.fromCharCode(224|d>>12,128|d>>6&63,128|d&63)});return b};Utf8.decode=function(b){var a=b.replace(/[\u00e0-\u00ef][\u0080-\u00bf][\u0080-\u00bf]/g,function(e){var d=((e.charCodeAt(0)&15)<<12)|((e.charCodeAt(1)&63)<<6)|(e.charCodeAt(2)&63);return String.fromCharCode(d)});a=a.replace(/[\u00c0-\u00df][\u0080-\u00bf]/g,function(e){var d=(e.charCodeAt(0)&31)<<6|e.charCodeAt(1)&63;return String.fromCharCode(d)});return a};var Form = function(form) {
    var self = this;

    this.form = $(form);
    this.optionals = JSON.parse(this.form.attr('data-optionals'));
    this.hasChange = false;
    this.saveTimeout = null;

    this.updateUnused = function(name) {
        $.each(self.optionals, function(i, optional) {
            if (typeof name === 'undefined' || $.inArray(name, optional) !== -1) {
                inputs = $();
                $.each(optional, function(i, name) {
                    inputs = inputs.add('[name="' + name + '"], [data-name="' + name + '"]');
                });

                var all_empty = true;
                inputs.each(function(i, input) {
                    if ($(input).val().length)
                    {
                        all_empty = false;
                        return false; // break
                    }
                });

                if (all_empty)
                    inputs.addClass('unused');
                else
                    inputs.removeClass('unused');
                return (typeof name === 'undefined'); // break when it's the initial updateUnused call
            }
        });
    };
    this.updateUnused();

    this.form.on('input', 'input', function(e) {
        apiStatusClear();

        var input = $(e.currentTarget),
            name = input.attr('name');

        if (typeof name === 'undefined')
            name = input.attr('data-name');
        else
            self.updateUnused(name);

        if (!self.form.find('button[type="submit"]').length)
            self.needsSave();
    });

    this.form.on('change', 'input', function(e) {
        if (self.hasChange)
        {
            clearTimeout(self.saveTimeout);
            self.save();
            self.hasChange = false;
        }
    });

    this.needsSave = function() {
        self.hasChange = true;
        clearTimeout(self.saveTimeout);
        self.saveTimeout = setTimeout(self.save, 3000);
    };

    this.save = function() {
        apiStatusWorking();
        self.hasChange = false;

        // put data of multi-input fields into single hidden input
        self.form.find('input[type="hidden"]').each(function(i, hidden) {
            hidden = $(hidden);
            var name = hidden.attr('name');
            if (typeof name !== 'undefined')
            {
                var data = [];
                self.form.find('[data-name="' + name + '"]').each(function(i, input) {
                    var value = $(input).val();
                    if (value.length)
                        data.push(value);
                });

                if (data.length)
                {
                    if (hidden.attr('data-type') == 'password' && data.length == 1)
                    {
                        if (data[0] != '********')
                            hidden.val(Sha1.hash(data[0]));
                    }
                    else
                        hidden.val(JSON.stringify(data));
                }
                else
                    hidden.val('');
            }
        });

        api(window.location.href, self.form.serialize(), self.success, self.error); // AJAX
    };

    this.success = function(data) {
        if (data['errors'].length) {
            var errors = data['errors'].join('<br>');
            var form_errors = self.form.find('.form_errors');
            if (form_errors.html() != errors)
                form_errors.html(errors).hide();
            form_errors.fadeIn();
        }
        else
            self.form.find('.form_errors').hide();

        if (data['item_errors'].length)
            $.each(data['item_errors'], function(i, item_error) {
                var input = self.form.find('[name="' + item_error['name'] + '"], [data-name="' + item_error['name'] + '"]');
                input.addClass('invalid');

                var error_box = self.form.find('.form_item_error[data-for-name="' + item_error['name'] + '"]');
                if (error_box.find('span').text() != item_error['error'])
                {
                    error_box.hide();
                    error_box.find('span').text(item_error['error']);
                }
                error_box.fadeIn();
            });
        else
            self.form.find('.form_item_error').hide();

        if (data['errors'].length || data['item_errors'].length)
            apiStatusError(data['response']['error']);
        else if (data['redirect'].length > 0)
            window.location.href = data['redirect'];
        else
            apiStatusSuccess(data['response']['success']);
    };

    this.error = function(data) {
        apiStatusError();
    };

    this.form.on('submit', function(e) {
        e.preventDefault();
        if (self.form.find('button[type="submit"]').length) // make sure you can't double click the submit button
        {
            self.form.find('button[type="submit"]').blur().attr('disabled', 'disabled');
            setTimeout(function() {
                self.form.find('button[type="submit"]').removeAttr('disabled');
            }, 1000);
        }
        self.save();
    });

    // other stuff
    this.form.on('click', 'a.insert-link', function() {
        var textarea = $('[name="' + $(this).attr('data-for-name') + '"]');
        $.fancybox.open({
            'type': 'ajax',
            'href': '/' + base_url + 'admin/auxiliary/insert_link/',
            beforeShow: function() {
                $('.fancybox-skin').css('background', 'white')
            },
            beforeClose: function() {
                if ($('#insert_submit').val() == 1 && $('#insert_url').val())
                {
                    var title = $('#insert_title').val();
                    var url = $('#insert_url').val();
                    var text = $('#insert_text').val();

                    if (!text)
                        text = $('#insert_title').val();

                    textarea.insertAtCaret('[' + text + '](' + encodeURI(url) + ' "' + title + '")');
                    $('a[title="Preview"]').trigger('mouseup');
                }
            },
            helpers:  {
                overlay: {
                    locked: false
                }
            }
        });
    });

    this.form.on('click', 'a.insert-image', function() {
        var textarea = $('[name="' + $(this).attr('data-for-name') + '"]');
        $.fancybox.open({
            'type': 'ajax',
            'href': '/' + base_url + 'admin/auxiliary/insert_image/',
            beforeShow: function() {
                $('.fancybox-skin').css('background', 'white')
            },
            beforeClose: function() {
                if ($('#insert_submit').val() == 1 && $('#insert_url').val())
                {
                    var title = $('#insert_title').val();
                    var url = $('#insert_url').val();
                    var text = $('#insert_text').val();
                    var width = $('#insert_width').val();
                    var position = $('#insert_position').val();

                    if (!text)
                        text = $('#insert_title').val();
                    if (width)
                        url += '?w=' + width;

                    var insert = '![' + text + '](' + encodeURI(url) + ' "' + title + '")';
                    if (position)
                        insert = '<span style="float:' + position + ';">' + insert + '</span>';

                    textarea.insertAtCaret(insert);
                    $('a[title="Preview"]').trigger('mouseup');
                }
            },
            helpers:  {
                overlay: {
                    locked: false
                }
            }
        });
    });

    this.form.on('click', 'a.insert-asset', function() {
        var textarea = $('[name="' + $(this).attr('data-for-name') + '"]');
        $.fancybox.open({
            'type': 'ajax',
            'href': '/' + base_url + 'admin/auxiliary/insert_asset/',
            beforeShow: function() {
                $('.fancybox-skin').css('background', 'white')
            },
            beforeClose: function() {
                if ($('#insert_submit').val() == 1 && $('#insert_url').val())
                {
                    var title = $('#insert_title').val();
                    var url = $('#insert_url').val();
                    var text = $('#insert_text').val();

                    if (!text)
                        text = $('#insert_title').val();

                    textarea.insertAtCaret('[' + text + '](' + encodeURI(url) + ' "' + title + '")');
                    $('a[title="Preview"]').trigger('mouseup');
                }
            },
            helpers:  {
                overlay: {
                    locked: false
                }
            }
        });
    });
};

$('form').each(function(i, form) {
    new Form(form);
});

// form password
$('form input[data-type="password"]').each(function(i, password) {
    password = $(password);
    if (password.val().length)
        $('form input[data-name="' + password.attr('name') + '"]').val('********');
});

// form array
$('form input[data-type="array"]').each(function(i, array) {
    array = $(array);
    var template = doT.template($('#' + array.attr('data-template')).text()),
        ul = $('#' + array.attr('data-ul')),
        placeholders = []
        values = [];

    try {
        placeholders = JSON.parse(array.attr('placeholder'));
        values = JSON.parse(array.val());
    } catch (e) {}

    if (!values.length && placeholders.length)
        $.each(placeholders, function(i, placeholder) {
            ul.append(template({placeholder: placeholder, value: ''}));
        });
    else
    {
        values.push('');
        $.each(values, function(i, value) {
            ul.append(template({placeholder: '', value: value}));
        });
    }

    ul.on('input', 'input', function(e) {
        var input = $(this),
            li = input.closest('li');

        if (input.val().length > 0 && li.next().length == 0)
            $(template({value: ''})).appendTo(ul).hide().fadeIn();
    });
});

// form parameters
$('form input[data-type="parameters"]').each(function(i, array) {
    array = $(array);
    var template = doT.template($('#' + array.attr('data-template')).text()),
        ul = $('#' + array.attr('data-ul')),
        values = [];

    try {
        values = JSON.parse(array.val());
    } catch (e) {}
    values.push('');

    $.each(values, function(key, value) {
        ul.append(template({key: key, value: value}));
    });

    ul.on('input', 'input', function(e) {
        var input = $(this),
            li = input.closest('li');

        if (input.val().length > 0 && li.next().length == 0)
            $(template({key: '', value: ''})).appendTo(ul).hide().fadeIn();
    });
});