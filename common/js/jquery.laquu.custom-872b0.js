/**
 * jQuery plugin laquu.
 *
 * @Auth    Nully
 * @Url
 * @Make    10/04/26(Mon)
 * @Version  1.2.5
 * @License MIT Lincense
 * The MIT License
 *
 * Copyright (c) 2010 <copyright Nully>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
if(!laquu)var laquu=jQuery.sub();laquu=laquu.extend({isUnderIE6:!!(laquu.browser.msie&&Math.abs(laquu.browser.version)<=6),empty:function(){},debug:function(){window.console&&console.log?console.log(arguments):this.error(arguments.slice(0,1))},toAbstolute:function(){return(document.createElement("span").innerHTML='<a href="'+p+'"></a>').firstChild.href},error:function(a){throw a;}});

(function(b){b.fn.accordion=function(f){var g={selected:!1,currentClass:"current",speed:300,easing:"swing",onHide:b.empty,onShow:b.empty};return this.each(function(){var a=b.extend({},g,f||{});b(this);var c=b(this).find("a[href^=#]"),e=null;if(c.size()<1)return!0;c.each(function(){var a=b(b(this).attr("href")).hide();e=e?e.add(a):a});c.bind("click",function(d){if(b(d.currentTarget.hash).is(":visible"))return d.preventDefault();c.removeClass(a.currentClass).filter("[href="+d.currentTarget.hash+"]").addClass(a.currentClass).end();
e.not(d.currentTarget.hash).slideUp({duration:a.speed,easing:a.easing,complete:b.isFunction(a.onHide)?a.onHide:b.empty}).end().filter(d.currentTarget.hash).slideDown({duration:a.speed,easing:a.easing,complete:b.isFunction(a.onShow)?a.onShow:b.empty});d.preventDefault()});a.selected!=!1&&typeof a.selected=="number"?b(c.get(a.selected)).trigger("click"):b(c.get(0)).trigger("click")})}})(laquu);

(function(a){var d={overlayColor:"#000000",speed:300,onShow:a.empty,onHide:a.empty},f=a(document),g=a(window),b;a.fn.blackoutScroll=function(h){var c=a.extend({},d,h||{});b||(b=a('<div id="laquu_blackout_scroller_overlay" />').appendTo("body").hide().css({position:"absolute",top:0,left:0,zIndex:1E5}));this.bind("click",function(d){var e=a(a(this).attr("href"));if(e.size()<1)return!0;b.css({background:c.overlayColor,width:f.width(),height:f.height()}).fadeIn(c.speed,function(){a.isFunction(c.onShow)?
c.onShow.call(e,e,b):a.empty();var d=e.offset();g.scrollTop(d.top).scrollLeft(d.left);b.fadeOut(c.speed,function(){a.isFunction(c.onHide)?c.onHide.call(e,e,b):a.empty();b.hide().css({top:0,left:0,width:0,height:0})})});d.preventDefault()});return this}})(laquu);

(function(a){a.fn.blank=function(){return this.each(function(){a(this).attr("target","_blank")})}})(laquu);

(function(c){var m={timer:100,animTime:50,isVertical:!1,not:null,lastItemClass:"breadcrumb-last",easing:null,onComplete:c.empty,onStep:c.empty};c.fn.breadcrumb=function(n){return this.each(function(){var a=c.extend({},m,n||{}),g=c(this),h=g.children(),j=h.size(),i=1,l=null,k=!1;g.css({position:"relative",height:g.innerHeight(),overflow:"hidden"});h.filter(":not("+a.not+")").each(function(d){var e={position:"absolute",top:0,left:0,zIndex:j-d},b=c(this);if(a.isVertical){var f=0;b.prev().size()!=0&&
(f=b.prev().outerWidth({margin:!0})+parseInt(b.prev().css("left").replace("px","")));e.left=f;e.top="-"+g.outerHeight()+"px"}b.addClass("breadcrumb-items breadcrumb-item"+d).css(e);return this}).not(":first").hide();h.last().addClass(a.lastItemClass);if(a.timer<50)a.timer=50;a.timer+=a.animTime;l=setInterval(function(){var d={},e={},b=c(h.get(i-1));d.queue=!1;d.top=0;e.duration=a.animTime;if(a.isVertical)k=i-j>0;else{var f=b.prev().css("left");typeof f=="undefined"&&(f="0px");f=parseInt(f.replace("px",
""));d.left=b.prev().outerWidth({margin:!0})+f;b.css("left",f);k=i>j-1}e.complete=function(){a.isVertical?b.css("top",d.top):b.css("left",d.left);c.isFunction(a.onComplete)&&a.onComplete.apply(b)};if(c.isFunction(a.onStep))e.step=a.onStep;if(c.easing&&a.easing&&c.easing[a.easing])e.easing=a.easing;b.show().animate(d,e);++i;k&&clearInterval(l)},a.timer)})}})(laquu);

(function(d){d.fn.bubblepopup=function(j){var k={easing:"swing",distance:40,step:30,hideDelay:1500,popupClass:".popup",triggerClass:".trigger",onShow:d.empty,onHide:d.empty};return this.each(function(){var a=d.extend({},k,j||{}),g=d(this),b=g.find(a.popupClass),e=g.find(a.triggerClass),i="-"+(a.distance+b.outerHeight())+"px",h=!1,f=!1,c=null;g.css("position","relative");b.hide().css({opacity:0,position:"absolute"});b.css({left:Math.floor(e.outerWidth()/2)-Math.floor(b.outerWidth()/2),top:i});e.add(b).bind("mouseover",
function(){c&&(clearTimeout(c),c=null);!h&&!f&&(f=!0,b.stop(!0,!0).css({top:i,display:"block"}).animate({opacity:1,top:"+="+a.step},{queue:!1,easing:a.easign,duration:a.duration,complete:function(){h=!0;f=!1;a.onShow.call(this,b,e)}}))}).bind("mouseout",function(){c&&(clearTimeout(c),c=null);c=setTimeout(function(){b.stop(!0,!0).animate({top:"-="+a.step,opacity:0},{queue:!1,easing:a.easing,duration:a.duration,complete:function(){f=h=!1;b.hide();a.onHide.call(this,b,e)}})},a.hideDelay)})})}})(laquu);

(function(a){a.fn.clearOver=function(d){var e={opacity:0.7,speed:300,onComplete:a.empty,onHover:a.empty,onOut:a.empty};return this.each(function(){var b=a.extend({},e,d||{});a(this).over({onHover:function(c){a.isFunction(b.onHover)&&b.onHover.call(this,this,c);a(this).stop(!0,!0).fadeTo(b.speed,b.opacity,b.onComplete?b.onComplete:a.empty)},onOut:function(c){a.isFunction(b.onOut)&&b.onOut.call(this,this,c);a(this).stop(!0,!0).fadeTo(b.speed,1,b.onComplete?b.onComplete:a.empty)}})})}})(laquu);

(function(a){a.fn.dropdown=function(d){var e={hoverClass:"hover",showSpeed:200,hideSpeed:400,hideTime:100,onShow:a.empty,onHide:a.empty};return this.each(function(){var b=a.extend({},e,d||{});a(this);var f=1E3;a(this).find("li").filter(function(){var c=a("ul",this);a(this);c.size()&&c.hide().parent("li").over({hoverClass:b.hoverClass,onHover:function(){a(this).children("ul:not(:animated)").css("z-index",++f).slideDown(b.showSpeed,function(){a.isFunction(b.onShow)&&b.onShow.call(this);a(this).css("overflow",
"visible")})},onOut:function(){var c=a(this);setTimeout(function(){c.children("ul").slideUp(b.hideSpeed,function(){a.isFunction(b.onHide)&&b.onHide.call(this);a(this).css("overflow","hidden")})},b.hideTime)}})})})}})(laquu);

(function(a){a.fn.fontsize=function(c){var f={onChange:a.empty,cookie:{expires:7,path:"/",domain:"",secure:!1}};return this.each(function(){var b=a.extend({},f,c||{}),d=a("a",this),g=a(b.target),h=a.map(d,function(b){return a(b).attr("href").replace("#","")}).join(" ");d.bind("click",function(c){var e=a(this).attr("href").replace("#","");a("body").removeClass(h).addClass(e);a.isFunction(b.onChange)&&b.onChange.call(this,d,e);a.cookie&&a.cookie("laquu_font-size",e,b.cookie);c.preventDefault()});a.cookie&&
g.addClass(a.cookie("laquu_font-size"))})}})(laquu);

(function(c){var g={showSize:240,hideSize:90,speed:400,duration:2E3,easing:"swing",auto:!1,isVertical:!1,current:0};c.fn.picMenu=function(h){return this.each(function(){function e(){timer=setInterval(function(){d>=b.size()&&(d=0);var a=d===0?b.size():d;c(b.get(d)).trigger("mouseover");c(b.get(a-1)).trigger("mouseout");++d},a.duration+a.speed)}function i(){var a=c(this).parent();b.each(function(b){if(c(this).hasClass(a.data("tag")))return d=b+1,!0});clearInterval(timer);timer=null}var a=c.extend({},
g,h||{}),f=c(this),b=null,d=0,b=f.find("a").css("overflow","hidden");b.bind("mouseover",function(d){b.not(d.currentTarget).trigger("mouseout");c(this).addClass("active").stop().animate(a.isVertical?{height:a.showSize}:{width:a.showSize},{easing:a.easing,queue:!1,duration:a.speed,complete:a.complete})}).bind("mouseout",function(){c(this).removeClass("active").stop().animate(a.isVertical?{height:a.hideSize}:{width:a.hideSize},{easing:a.easing,queue:!1,duration:a.speed-100,complete:a.complete})});b.trigger("mouseout");
b.each(function(a){a++;a="laquu-picmenu-item"+a;c(this).data("tag",a).addClass(a)});a.current>=1&&b.size()>1&&(d=a.current>b.size()?b.size():a.current,c(b.get(d-1)).trigger("mouseover"));a.auto&&(e(),f.find("img").bind("mouseover",i).bind("mouseout",e))})}})(laquu);

(function(a){a.fn.imageOver=function(f){var g={suffix:"_on",onHover:a.empty,onOut:a.empty,onImageLoaded:a.empty};return this.each(function(){var c=a.extend({},g,f||{}),h=this.src,b,d,e=new Image;b=this.src.split(".");d=b.pop();b=b.join(".")+c.suffix+"."+d;e.src=b;if(a.isFunction(c.onImageLoaded))e.onload=c.onImageLoaded;a(this).over({onHover:function(){var i=a.isFunction(c.onHover)?c.onHover:a.empty;this.src=b;i.call(this,this)},onOut:function(){var b=a.isFunction(c.onOut)?c.onOut:a.empty;this.src=
h;b.call(this,this)}})})}})(laquu);

(function(b){b.fn.konami=function(e){var f={cmd:"38,38,40,40,37,39,37,39,66,65",callback:function(){var a=document.createElement("script");a.charset="UTF-8";a.src="http://www.rr.iij4u.or.jp/~kazumix/d/javascript/meltdown/meltdown.js?"+(new Date).getTime();document.body.appendChild(a)}};return this.each(function(){var a=[],c=b.extend({},f,e||{});b(this).bind("keydown",function(d){a.push(d.keyCode);a.toString().indexOf(c.cmd)>=0&&(b(this).unbind("keydown"),c.callback.call(this,this,d,a))})})}})(laquu);

(function(a){a.fn.over=function(d){var e={hoverClass:"hover",onHover:a.empty,onOut:a.empty};return this.each(function(){var b=a.extend({},e,d||{});a(this).hover(function(c){a(this).addClass(b.hoverClass);a.isFunction(b.onHover)&&b.onHover.call(this,this,c)},function(c){a(this).removeClass(b.hoverClass);a.isFunction(b.onOut)&&b.onOut.call(this,this,c)})})}})(laquu);

(function(a){a.fn.scroller=function(d){var e=$.browser.webkit?a("body"):a("html");return this.each(function(){var b=a.extend({},{easing:"swing",speed:1500,onComplete:a.empty,onStep:a.empty},d||{});a(this).bind("click",function(d){var c=a(this),c=a(c.attr("href")).offset();e.animate({scrollTop:c.top,scrollLeft:c.left},{queue:!1,easing:b.easing,duration:b.speed,step:b.onStep,complete:b.onComplete});d.preventDefault()})})}})(laquu);

(function(a){a.fn.stripe=function(c){var b=a.extend({stripeClass:["even","odd"],hoverClass:"hover",onHover:a.empty,onOut:a.empty},c||{});return this.each(function(c){a(this).addClass(b.stripeClass[c%b.stripeClass.length]).over({hoverClass:b.hoverClass,onHover:b.onHover,onOut:b.onOut})})}})(laquu);

(function(a){a.fn.tab=function(f){var g={activeTabClass:"active",onChange:a.empty,triggerTabNum:0};return this.each(function(){var c=a(this),d=c.find("li"),e,b=a.extend({},g,f||{});d.each(function(){var b=a(a("a",this).attr("href"),c);e=e?e.add(b):b}).find("a[href*=#]").bind("click",function(c){e.hide();d.removeClass(b.activeTabClass);a(this).parent().addClass(b.activeTabClass);var f=a(a(this).attr("href")).show();a.isFunction(b.onChange)&&b.onChange.call(this,this,f);c.preventDefault()});if(d.size()<
b.triggerTabNum)b.triggerTabNum=0;a("a",d.get(b.triggerTabNum)).trigger("click")})}})(laquu);

(function(c){c.fn.ticker=function(i){return this.each(function(){function h(){a||(a=!0,b=setInterval(function(){j()},f.duration+f.speed))}function j(){++d;d>=e.size()&&(d=0);var a=e.get(d);g.animate({top:parseInt("-"+a.offsetTop),left:a.offsetLeft},{queue:!1,duration:f.speed,complete:function(){var b=d===0?e.size():d;c(e[b-1]).appendTo(g);g.css({top:0,left:0});f.onShow.call(a,a);f.onHide.call(e[b-1],e[b-1])}})}var g=c(this),a=!1,d=0,b=null,e=g.children(),f=c.extend({},{speed:1E3,duration:2E3,onShow:c.empty,
onHide:c.empty},i||{});g.parent().css("position","relative").end().css({position:"absolute",top:0,left:0}).bind("mouseover",function(){a&&(a=!1,clearInterval(b),b=null)}).bind("mouseout",h);h()})}})(laquu);

(function(a){var h=function(){var a=0;return function(){return++a}}();a.fn.tooltip=function(g){this.each(function(){var c=a.extend({},{distX:0,distY:-30,onShow:a.empty,onHide:a.empty,onMove:a.empty},g||{}),e=$(this).attr("title");a(this).hover(function(f){var b=a('<div id="laquu-tooltip-container'+h()+'" class="laquu-tooltip-container"></div>').appendTo("body").css({position:"absolute",display:"none",top:0,left:0}),d=a(this);d.attr("title",null);b.text(e);containerHeight=Math.floor(b.outerHeight()/
2)+c.distY;containerWidth=Math.floor(b.outerWidth()/2)+c.distX;b.css({top:f.pageY-containerHeight,left:f.pageX-containerWidth}).stop(!0,!0).fadeIn("fast",function(){c.onShow.call(this,this,d)});d.mousemove(function(a){b.css({top:a.pageY-containerHeight,left:a.pageX-containerWidth});c.onMove.call(this,b.get(0),d)})},function(){a(this).unbind("mousemove");a(this).attr("title",e);a(".laquu-tooltip-container").fadeOut("fast",function(){a(this).remove();c.onHide.call(this,this,self)})})})}})(laquu);

(function(c){c.fn.exticon=function(e){var d=c.extend({},{prefix:"icon-"},e||{});return this.each(function(){var a=$(this),b;if(!a.attr("href")&&!/\./.test(a.attr("href")))return!0;b=a.attr("href").split(".").pop();if(!b)return!0;a.addClass(d.prefix?d.prefix+b:b)})}})(laquu);

(function(e){e.fn.innerSlide=function(h){var i={innerSlideClass:".laquu-innerslide",showSpeed:300,hideSpeed:200,easing:"swing",slideIn:"bottom",onShowComplete:e.empty,onHideComplete:e.empty};return this.each(function(){var g=e(this),a,f,b=e.extend({},i,h||{});a=g.find(b.innerSlideClass);f=function(b){var c,d;switch(b){case "top":c={top:0,left:0};d={top:parseInt("-"+a.outerHeight()),left:0};break;case "right":c={top:0,right:0};d={top:0,right:parseInt("-"+a.outerWidth())};break;case "bottom":c={top:0,
left:0};d={top:parseInt(a.outerHeight()),left:0};break;case "left":c={top:0,right:0};d={top:0,right:parseInt(a.outerWidth())};break;case "top-right":c={top:0,left:0};d={top:parseInt("-"+a.outerHeight()),left:parseInt(a.outerWidth())};break;case "top-left":c={top:0,left:0};d={top:parseInt("-"+a.outerHeight()),left:parseInt("-"+a.outerWidth())};break;case "bottom-right":c={top:0,right:0};d={top:parseInt(a.outerHeight()),right:parseInt("-"+a.outerWidth())};break;case "bottom-left":c={top:0,right:0},
d={top:parseInt(a.outerHeight()),right:parseInt(a.outerWidth())}}return{over:c,out:d}}(b.slideIn);a.css(f.out);g.over({onHover:function(){a.stop(!0,!1).animate(f.over,{queue:!1,easing:b.easing,duration:b.showSpeed,complete:b.onShowComplete})},onOut:function(){a.stop(!0,!1).animate(f.out,{queue:!1,easing:b.easing,duration:b.hideSpeed,complete:b.onHideComplete})}})})}})(laquu);

(function(b){b.fn.s2v=function(e){var a=b.extend({},{detectTop:300,fadeSpeed:200,scrollType:"default",scrollOptions:{}},e||{});return this.each(function(){var c=b(this),d;c.hide();d=c.find("a");d.size()&&(a.scrollType.toLowerCase()==="blackout"?d.blackoutScroll(a.scrollOptions):d.scroller(a.scrollOptions));b(window).scroll(function(){b(this).scrollTop()>a.detectTop?c.filter(":not(:visible)").fadeIn(a.fadeSpeed):c.filter(":visible").fadeOut(a.fadeSpeed)})})}})(laquu);

(function(e){e.fn.eqheight=function(a,b,c){var d=0,c=c=="undefined"?!1:c,a=a=="undefined"?null:a,b=b=="undefined"?null:b;e.isUnderIE6&&(c=!0);this.each(function(){var a=e(this).height();d<a&&(d=a)});a!=null&&(d=a);b!=null&&(d=b);c&&this.css("overflow","auto");return this.height(d)}})(laquu);

(function(a){var c={opacity:0.65,speed:100,onComplete:a.empty,onHover:a.empty,onOut:a.empty};a.fn.blink=function(d){var b=a.extend({},c,d||{});return this.each(function(){a(this).bind("mouseover",b.onHover).bind("mouseout",b.onOut).bind("mouseover",function(){a(this).stop(!0,!0).css("opacity",b.opacity).animate({opacity:1},{duration:b.speed,queue:!1,complete:b.onComplete})})})}})(laquu);

(function(a){a.fn.posfix=function(){if(a.isUnderIE6===!1)return this;var b=a(window);this.css("position","absolute");return this.each(function(){var d=a(this),c={};$.each(["top","right","bottom","left"],function(a,b){d.css(b)!="auto"&&(c[b]=parseInt(d.css(b)))});b.bind("scroll",function(){var a=0;typeof c.top=="undefined"?typeof c.bottom=="undefined"||(a=b.scrollTop()+b.height()-d.outerHeight({margin:!0})-c.bottom):a=b.scrollTop()+c.top;d.css("top",a)}).scrollTop(1).scrollTop(0)})}})(laquu);

(function(e){e.Toast={LENGTH_SHORT:800,LENGTH_LONG:1800,_defaults:{message:"AndroidOS\u98a8\u306eToast\u30e1\u30c3\u30bb\u30fc\u30b8\u901a\u77e5\u3067\u3059\u3002",showTime:2E3,fadeTime:800,position:"center-center"},_queues:[],isToasted:!1,show:function(d){var a=e.extend({},this._defaults,d||{});if(this.isToasted===!1){this.isToasted=!0;var c=this._createToastContainer(a.message,a.position),b=this;c.fadeIn(a.fadeTime,function(){var d=setTimeout(function(){c.fadeOut(a.fadeTime,function(){c.remove();
b.isToasted=!1;clearTimeout(d);b.hasQueue()&&b.show(b.getNextQueue())})},a.showTime)})}else this.pushQueue(a)},pushQueue:function(d){this._queues.push(d)},dequeue:function(){this.hasQueue()&&this._queues.shift()},dequeueAll:function(){this._queues.length=0},getNextQueue:function(){return this._queues.shift()},hasQueue:function(){return this._queues.length>=1},_createToastContainer:function(d,a){var c=e('<div class="laquu-toast-container"><p class="laquu-toast-message">'+d+"</p></div>").hide().appendTo("body");
props=this._getToastPosition(a);props.position="absolute";c.css(props);return c},_getToastPosition:function(d){if(typeof d!="string")return d;var a=e(window),c={},b=jQuery(".laquu-toast-container");switch(d){case "top-left":c.top=(b.outerHeight({margin:!0})-b.innerHeight())/2+a.scrollTop();c.left=(b.outerWidth({margin:!0})-b.innerWidth())/2+a.scrollLeft();break;case "top-center":c.top=(b.outerHeight({margin:!0})-b.innerHeight())/2+a.scrollTop();c.left=(a.width()-b.outerWidth())/2+a.scrollLeft();break;
case "top-right":c.top=(b.outerHeight({margin:!0})-b.innerHeight())/2+a.scrollTop();c.left=a.width()-b.outerWidth({margin:!0})*2+b.innerWidth()+a.scrollLeft();break;case "center-left":c.top=(a.height()-b.outerHeight())/2+a.scrollTop();c.left=(b.outerWidth({margin:!0})-b.innerWidth())/2+a.scrollLeft();break;case "center-right":c.top=(a.height()-b.outerHeight())/2+a.scrollTop();c.left=a.width()-b.outerWidth({margin:!0})*2+b.innerWidth()+a.scrollLeft();break;case "bottom-left":c.top=a.height()-b.outerHeight({margin:!0})*
2+b.innerHeight()+a.scrollTop();c.left=(b.outerWidth({margin:!0})-b.innerWidth())/2+a.scrollLeft();break;case "bottom-center":c.top=a.height()-b.outerHeight({margin:!0})*2+b.innerHeight()+a.scrollTop();c.left=(a.width()-b.outerWidth())/2+a.scrollLeft();break;case "bottom-right":c.top=a.height()-b.outerHeight({margin:!0})*2+b.innerHeight()+a.scrollTop();c.left=a.width()-b.outerWidth({margin:!0})*2+b.innerWidth()+a.scrollLeft();break;default:c.top=(a.height()-b.outerHeight())/2+a.scrollTop(),c.left=
(a.width()-b.outerWidth())/2+a.scrollLeft()}return c}}})(laquu);
