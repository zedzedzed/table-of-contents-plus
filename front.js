/*!
 * jQuery Smooth Scroll - v1.6.0 - 2015-12-26
 * https://github.com/kswedberg/jquery-smooth-scroll
 * Copyright (c) 2015 Karl Swedberg
 * Licensed MIT
 */
!function(a){"function"==typeof define&&define.amd?
// AMD. Register as an anonymous module.
define(["jquery"],a):a("object"==typeof module&&module.exports?require("jquery"):jQuery)}(function(a){function b(a){return a.replace(/(:|\.|\/)/g,"\\$1")}var c="1.6.0",d={},e={exclude:[],excludeWithin:[],offset:0,
// one of 'top' or 'left'
direction:"top",
// if set, bind click events through delegation
//  supported since jQuery 1.4.2
delegateSelector:null,
// jQuery set of elements you wish to scroll (for $.smoothScroll).
//  if null (default), $('html, body').firstScrollable() is used.
scrollElement:null,
// only use if you want to override default behavior
scrollTarget:null,
// fn(opts) function to be called before scrolling occurs.
// `this` is the element(s) being scrolled
beforeScroll:function(){},
// fn(opts) function to be called after scrolling occurs.
// `this` is the triggering element
afterScroll:function(){},easing:"swing",speed:400,
// coefficient for "auto" speed
autoCoefficient:2,
// $.fn.smoothScroll only: whether to prevent the default click action
preventDefault:!0},f=function(b){var c=[],d=!1,e=b.dir&&"left"===b.dir?"scrollLeft":"scrollTop";
// If no scrollable elements, fall back to <body>,
// if it's in the jQuery collection
// (doing this because Safari sets scrollTop async,
// so can't set it to 1 and immediately get the value.)
// Use the first scrollable element if we're calling firstScrollable()
return this.each(function(){var b=a(this);if(this!==document&&this!==window)
// if scroll(Top|Left) === 0, nudge the element 1px and see if it moves
// then put it back, of course
return!document.scrollingElement||this!==document.documentElement&&this!==document.body?void(b[e]()>0?c.push(this):(b[e](1),d=b[e]()>0,d&&c.push(this),b[e](0))):(c.push(document.scrollingElement),!1)}),c.length||this.each(function(){"BODY"===this.nodeName&&(c=[this])}),"first"===b.el&&c.length>1&&(c=[c[0]]),c};a.fn.extend({scrollable:function(a){var b=f.call(this,{dir:a});return this.pushStack(b)},firstScrollable:function(a){var b=f.call(this,{el:"first",dir:a});return this.pushStack(b)},smoothScroll:function(c,d){if(c=c||{},"options"===c)return d?this.each(function(){var b=a(this),c=a.extend(b.data("ssOpts")||{},d);a(this).data("ssOpts",c)}):this.first().data("ssOpts");var e=a.extend({},a.fn.smoothScroll.defaults,c),f=function(c){var d=this,f=a(this),g=a.extend({},e,f.data("ssOpts")||{}),h=e.exclude,i=g.excludeWithin,j=0,k=0,l=!0,m={},n=a.smoothScroll.filterPath(location.pathname),o=a.smoothScroll.filterPath(d.pathname),p=location.hostname===d.hostname||!d.hostname,q=g.scrollTarget||o===n,r=b(d.hash);if(g.scrollTarget||p&&q&&r){for(;l&&j<h.length;)f.is(b(h[j++]))&&(l=!1);for(;l&&k<i.length;)f.closest(i[k++]).length&&(l=!1)}else l=!1;l&&(g.preventDefault&&c.preventDefault(),a.extend(m,g,{scrollTarget:g.scrollTarget||r,link:d}),a.smoothScroll(m))};return null!==c.delegateSelector?this.undelegate(c.delegateSelector,"click.smoothscroll").delegate(c.delegateSelector,"click.smoothscroll",f):this.unbind("click.smoothscroll").bind("click.smoothscroll",f),this}}),a.smoothScroll=function(b,c){if("options"===b&&"object"==typeof c)return a.extend(d,c);var e,f,g,h,i,j=0,k="offset",l="scrollTop",m={},n={};"number"==typeof b?(e=a.extend({link:null},a.fn.smoothScroll.defaults,d),g=b):(e=a.extend({link:null},a.fn.smoothScroll.defaults,b||{},d),e.scrollElement&&(k="position","static"===e.scrollElement.css("position")&&e.scrollElement.css("position","relative"))),l="left"===e.direction?"scrollLeft":l,e.scrollElement?(f=e.scrollElement,/^(?:HTML|BODY)$/.test(f[0].nodeName)||(j=f[l]())):f=a("html, body").firstScrollable(e.direction),e.beforeScroll.call(f,e),g="number"==typeof b?b:c||a(e.scrollTarget)[k]()&&a(e.scrollTarget)[k]()[e.direction]||0,m[l]=g+j+e.offset,h=e.speed,"auto"===h&&(i=Math.abs(m[l]-f[l]()),h=i/e.autoCoefficient),n={duration:h,easing:e.easing,complete:function(){e.afterScroll.call(e.link,e)}},e.step&&(n.step=e.step),f.length?f.stop().animate(m,n):e.afterScroll.call(e.link,e)},a.smoothScroll.version=c,a.smoothScroll.filterPath=function(a){return a=a||"",a.replace(/^\//,"").replace(/(?:index|default).[a-zA-Z]{3,4}$/,"").replace(/\/$/,"")},
// default options
a.fn.smoothScroll.defaults=e});

/**
 * jQuery Cookie plugin
 *
 * Copyright (c) 2010 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */
jQuery.cookie=function(a,b,c){if(arguments.length>1&&String(b)!=="[object Object]"){c=jQuery.extend({},c);if(b===null||b===undefined){c.expires=-1}if(typeof c.expires==="number"){var d=c.expires,e=c.expires=new Date;e.setDate(e.getDate()+d)}b=String(b);return document.cookie=[encodeURIComponent(a),"=",c.raw?b:encodeURIComponent(b),c.expires?"; expires="+c.expires.toUTCString():"",c.path?"; path="+c.path:"",c.domain?"; domain="+c.domain:"",c.secure?"; secure":""].join("")}c=b||{};var f,g=c.raw?function(a){return a}:decodeURIComponent;return(f=(new RegExp("(?:^|; )"+encodeURIComponent(a)+"=([^;]*)")).exec(document.cookie))?g(f[1]):null}

jQuery(document).ready(function($) {
	if ( typeof tocplus != 'undefined' ) {
		$.fn.shrinkTOCWidth = function() {
			$(this).css({
				width: 'auto',
				display: 'table'
			});
			if ( /MSIE 7\./.test(navigator.userAgent) )
				$(this).css('width', '');
		}
	
		if ( tocplus.smooth_scroll == 1 ) {
			var target = hostname = pathname = qs = hash = null;
	
			$('body a').click(function(event) {
				hostname = $(this).prop('hostname');
				pathname = $(this).prop('pathname');
				qs = $(this).prop('search');
				hash = $(this).prop('hash');
	
				// ie strips out the preceeding / from pathname
				if ( pathname.length > 0 ) {
					if ( pathname.charAt(0) != '/' ) {
						pathname = '/' + pathname;
					}
				}
				
				if ( (window.location.hostname == hostname) && (window.location.pathname == pathname) && (window.location.search == qs) && (hash !== '') ) {
					// escape jquery selector chars, but keep the #
					var hash_selector = hash.replace(/([ !"$%&'()*+,.\/:;<=>?@[\]^`{|}~])/g, '\\$1');
					// check if element exists with id=__
					if ( $( hash_selector ).length > 0 )
						target = hash;
					else {
						// must be an anchor (a name=__)
						anchor = hash;
						anchor = anchor.replace('#', '');
						target = 'a[name="' + anchor  + '"]';
						// verify it exists
						if ( $(target).length == 0 )
							target = '';
					}
					
					// check offset setting
					if (typeof tocplus.smooth_scroll_offset != 'undefined') {
						offset = -1 * tocplus.smooth_scroll_offset;
					}
					else {
						if ($('#wpadminbar').length > 0) {
							if ($('#wpadminbar').is(':visible'))
								offset = -30;	// admin bar exists, give it the default
							else
								offset = 0;		// there is an admin bar but it's hidden, so no offset!
						}
						else
							offset = 0;			// no admin bar, so no offset!						
					}
					
					if ( target ) {
						$.smoothScroll({
							scrollTarget: target,
							offset: offset
						});
					}
				}
			});
		}

		if ( typeof tocplus.visibility_show != 'undefined' ) {
			var invert = ( typeof tocplus.visibility_hide_by_default != 'undefined' ) ? true : false ;
			
		
			if ( $.cookie )
				var visibility_text = ($.cookie('tocplus_hidetoc')) ? tocplus.visibility_show : tocplus.visibility_hide ;
			else
				var visibility_text = tocplus.visibility_hide;
			
			if ( invert )
				visibility_text = (visibility_text == tocplus.visibility_hide) ? tocplus.visibility_show : tocplus.visibility_hide;
				
			$('#toc_container p.toc_title').append(' <span class="toc_toggle">[<a href="#">' + visibility_text + '</a>]</span>');
			if ( visibility_text == tocplus.visibility_show ) {
				$('ul.toc_list').hide();
				$('#toc_container').addClass('contracted').shrinkTOCWidth();
			}
	
			$('span.toc_toggle a').click(function(event) {
				event.preventDefault();
				switch( $(this).html() ) {
					case $('<div/>').html(tocplus.visibility_hide).text():
						$(this).html(tocplus.visibility_show);
						if ( $.cookie ) {
							if ( invert )
								$.cookie('tocplus_hidetoc', null, { path: '/' });
							else
								$.cookie('tocplus_hidetoc', '1', { expires: 30, path: '/' });
						}
						$('ul.toc_list').hide('fast');
						$('#toc_container').addClass('contracted').shrinkTOCWidth();
						break;
					
					case $('<div/>').html(tocplus.visibility_show).text():	// do next
					default:
						$(this).html(tocplus.visibility_hide);
						if ( $.cookie ) {
							if ( invert )
								$.cookie('tocplus_hidetoc', '1', { expires: 30, path: '/' });
							else 
								$.cookie('tocplus_hidetoc', null, { path: '/' });
						}
						$('#toc_container').css('width', tocplus.width).removeClass('contracted');
						$('ul.toc_list').show('fast');
				}
			});
		}
	}
});