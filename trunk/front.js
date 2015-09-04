/*!
 * jQuery Smooth Scroll - v1.5.5 - 2015-02-19
 * https://github.com/kswedberg/jquery-smooth-scroll
 * Copyright (c) 2015 Karl Swedberg
 * Licensed MIT (https://github.com/kswedberg/jquery-smooth-scroll/blob/master/LICENSE-MIT)
 */
(function(t){"function"==typeof define&&define.amd?define(["jquery"],t):"object"==typeof module&&module.exports?t(require("jquery")):t(jQuery)})(function(t){function e(t){return t.replace(/(:|\.|\/)/g,"\\$1")}var l="1.5.5",o={},n={exclude:[],excludeWithin:[],offset:0,direction:"top",scrollElement:null,scrollTarget:null,beforeScroll:function(){},afterScroll:function(){},easing:"swing",speed:400,autoCoefficient:2,preventDefault:!0},s=function(e){var l=[],o=!1,n=e.dir&&"left"===e.dir?"scrollLeft":"scrollTop";return this.each(function(){if(this!==document&&this!==window){var e=t(this);e[n]()>0?l.push(this):(e[n](1),o=e[n]()>0,o&&l.push(this),e[n](0))}}),l.length||this.each(function(){"BODY"===this.nodeName&&(l=[this])}),"first"===e.el&&l.length>1&&(l=[l[0]]),l};t.fn.extend({scrollable:function(t){var e=s.call(this,{dir:t});return this.pushStack(e)},firstScrollable:function(t){var e=s.call(this,{el:"first",dir:t});return this.pushStack(e)},smoothScroll:function(l,o){if(l=l||{},"options"===l)return o?this.each(function(){var e=t(this),l=t.extend(e.data("ssOpts")||{},o);t(this).data("ssOpts",l)}):this.first().data("ssOpts");var n=t.extend({},t.fn.smoothScroll.defaults,l),s=t.smoothScroll.filterPath(location.pathname);return this.unbind("click.smoothscroll").bind("click.smoothscroll",function(l){var o=this,r=t(this),i=t.extend({},n,r.data("ssOpts")||{}),c=n.exclude,a=i.excludeWithin,f=0,h=0,u=!0,d={},p=location.hostname===o.hostname||!o.hostname,m=i.scrollTarget||t.smoothScroll.filterPath(o.pathname)===s,S=e(o.hash);if(i.scrollTarget||p&&m&&S){for(;u&&c.length>f;)r.is(e(c[f++]))&&(u=!1);for(;u&&a.length>h;)r.closest(a[h++]).length&&(u=!1)}else u=!1;u&&(i.preventDefault&&l.preventDefault(),t.extend(d,i,{scrollTarget:i.scrollTarget||S,link:o}),t.smoothScroll(d))}),this}}),t.smoothScroll=function(e,l){if("options"===e&&"object"==typeof l)return t.extend(o,l);var n,s,r,i,c,a=0,f="offset",h="scrollTop",u={},d={};"number"==typeof e?(n=t.extend({link:null},t.fn.smoothScroll.defaults,o),r=e):(n=t.extend({link:null},t.fn.smoothScroll.defaults,e||{},o),n.scrollElement&&(f="position","static"===n.scrollElement.css("position")&&n.scrollElement.css("position","relative"))),h="left"===n.direction?"scrollLeft":h,n.scrollElement?(s=n.scrollElement,/^(?:HTML|BODY)$/.test(s[0].nodeName)||(a=s[h]())):s=t("html, body").firstScrollable(n.direction),n.beforeScroll.call(s,n),r="number"==typeof e?e:l||t(n.scrollTarget)[f]()&&t(n.scrollTarget)[f]()[n.direction]||0,u[h]=r+a+n.offset,i=n.speed,"auto"===i&&(c=u[h]-s.scrollTop(),0>c&&(c*=-1),i=c/n.autoCoefficient),d={duration:i,easing:n.easing,complete:function(){n.afterScroll.call(n.link,n)}},n.step&&(d.step=n.step),s.length?s.stop().animate(u,d):n.afterScroll.call(n.link,n)},t.smoothScroll.version=l,t.smoothScroll.filterPath=function(t){return t=t||"",t.replace(/^\//,"").replace(/(?:index|default).[a-zA-Z]{3,4}$/,"").replace(/\/$/,"")},t.fn.smoothScroll.defaults=n});

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