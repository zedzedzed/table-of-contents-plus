<?php

/**
 * Returns a HTML formatted string of the table of contents without the surrounding UL or OL
 * tags to enable the theme editor to supply their own ID and/or classes to the outer list.
 *
 * There are three optional parameters you can feed this function with:
 *
 *  - $content is the entire content with headings.  If blank, will default to the current $post
 *
 *  - $link is the URL to prefix the anchor with.  If provided a string, will use it as the prefix.
 *    If set to true then will try to obtain the permalink from the $post object.
 *
 *  - $apply_eligibility bool, defaults to false.  When set to true, will apply the check to
 *    see if bit of content has the prerequisites needed for a TOC, eg minimum number of headings
 *    enabled post type, etc.
 */
function toc_get_index( $content = '', $prefix_url = '', $apply_eligibility = false ) {
	global $wp_query, $toc_plus;

	$return  = '';
	$find    = [];
	$replace = [];
	$proceed = true;

	if ( ! $content ) {
		$post    = get_post( $wp_query->post->ID );
		$content = wptexturize( $post->post_content );
	}

	if ( $apply_eligibility ) {
		if ( ! $toc_plus->is_eligible() ) {
			$proceed = false;
		}
	} else {
		$toc_plus->set_option( [ 'start' => 0 ] );
	}

	if ( $proceed ) {
		$return = $toc_plus->extract_headings( $find, $replace, $content );
		if ( $prefix_url ) {
			$return = str_replace( 'href="#', 'href="' . $prefix_url . '#', $return );
		}
	}

	return $return;
}
