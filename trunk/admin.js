jQuery(document).ready(function($) {
	$('.tab_content, #toc_advanced_usage, #sitemap_advanced_usage, #toc_for_developers, div.more_toc_options.disabled, tr.disabled').hide();
	$('ul#tabbed-nav li:first').addClass('active').show(); // show first tab
	$('.tab_content:first').show(); // show first tab content

	$('ul#tabbed-nav li').click(function(event) {
		if ( !$(this).hasClass('url') ) {
			event.preventDefault();
			$('ul#tabbed-nav li').removeClass('active');
			$(this).addClass('active');
			$('.tab_content').hide();

			var activeTab = $(this).find('a').attr('href');
			$(activeTab).fadeIn();
		}
	});
	
	$('h3 span.show_hide a').click(function(event) {
		event.preventDefault();
		$( $(this).attr('href') ).toggle('fast');
		if ( $(this).text() == 'show' )
			$(this).text('hide');
		else
			$(this).text('show');
	});
	
	$('input#show_heading_text, input#visibility').click(function() {
		$(this).siblings('div.more_toc_options').toggle('fast');
	});
	
	$('input#smooth_scroll').click(function() {
		$('#smooth_scroll_offset_tr').toggle('fast');
	});
	
	$('input[name="theme"]').click(function() {
		// check custom theme selection
		if ( $(this).val() == 100 ) {
			$(this).parent().siblings('div.more_toc_options').show('fast');
		}
		else
			$(this).parent().siblings('div.more_toc_options').hide('fast');
	});
	
	/* width drop down */
	$('select#width').change(function() {
		if ( $(this).find('option:selected').val() == 'User defined' ) {
			$(this).siblings('div.more_toc_options').show('fast');
			$('input#width_custom').focus();
		}
		else
			$(this).siblings('div.more_toc_options').hide('fast');
	});
	$('input#width_custom, input#font_size, input#smooth_scroll_offset').keyup(function() {
		var value = $(this).val();
		$(this).val( value.replace(/[^0-9\.]/, '') );
	});
	$('input#fragment_prefix').keyup(function() {
		var fragment = $(this).val();
		$(this).val( fragment.replace(/[^a-zA-Z0-9_\-]/g, '') );
	});
	
	if ( $.farbtastic ) {
		var f = $.farbtastic('#farbtastic_colour_wheel');
		var selected;
		$('#farbtastic_colour_wheel').css('opacity', 0.5).hide();
		$('input.custom_colour_option')
			.each(function() { f.linkTo(this); $(this).css('opacity', 0.5); })
			.keyup(function() {
				var hex = $(this).val();
				hex = hex.replace(/[^a-fA-F0-9]/g, '');
				if ( hex.length > 6 ) hex = hex.substr(0, 6);
				$(this).val( '#' + hex );
			})
			.focus(function() {
				if (selected) {
					$(selected).css('opacity', 0.5);
					$(selected).siblings('img').css('opacity', 0.4);
				}
				f.linkTo(this);
				$('#farbtastic_colour_wheel').css('opacity', 1).show('fast');
				$(this).siblings('img').css('opacity', 1);
				$(selected = this).css('opacity', 1);
			});
		$('table#theme_custom img').click(function() {
			$(this).siblings('input.custom_colour_option').focus();
		});
	}
});