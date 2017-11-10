<?php
/*
Plugin Name:	Table of Contents Plus
Plugin URI: 	http://dublue.com/plugins/toc/
Description: 	A powerful yet user friendly plugin that automatically creates a table of contents. Can also output a sitemap listing all pages and categories.
Author: 		Michael Tran
Author URI: 	http://dublue.com/
Text Domain:	table-of-contents-plus
Domain Path:	/languages
Version: 		1601
License:		GPL2
*/

/*  Copyright 2016  Michael Tran  (michael@dublue.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
GPL licenced Oxygen icon used for the colour wheel - http://www.iconfinder.com/search/?q=iconset%3Aoxygen
*/

/**
FOR CONSIDERATION:
- back to top links
- sitemap
	- easier exclude pages/categories
	- support other taxonomies
- advanced options
	- highlight target css
*/

define( 'TOC_VERSION', '1509' );
define( 'TOC_POSITION_BEFORE_FIRST_HEADING', 1 );
define( 'TOC_POSITION_TOP', 2 );
define( 'TOC_POSITION_BOTTOM', 3 );
define( 'TOC_POSITION_AFTER_FIRST_HEADING', 4 );
define( 'TOC_MIN_START', 2 );
define( 'TOC_MAX_START', 10 );
define( 'TOC_SMOOTH_SCROLL_OFFSET', 30 );
define( 'TOC_WRAPPING_NONE', 0 );
define( 'TOC_WRAPPING_LEFT', 1 );
define( 'TOC_WRAPPING_RIGHT', 2 );
define( 'TOC_THEME_GREY', 1 );
define( 'TOC_THEME_LIGHT_BLUE', 2 );
define( 'TOC_THEME_WHITE', 3 );
define( 'TOC_THEME_BLACK', 4 );
define( 'TOC_THEME_TRANSPARENT', 99 );
define( 'TOC_THEME_CUSTOM', 100 );
define( 'TOC_DEFAULT_BACKGROUND_COLOUR', '#f9f9f9' );
define( 'TOC_DEFAULT_BORDER_COLOUR', '#aaaaaa' );
define( 'TOC_DEFAULT_TITLE_COLOUR', '#' );
define( 'TOC_DEFAULT_LINKS_COLOUR', '#' );
define( 'TOC_DEFAULT_LINKS_HOVER_COLOUR', '#' );
define( 'TOC_DEFAULT_LINKS_VISITED_COLOUR', '#' );


if ( !class_exists( 'toc' ) ) :
	class toc {
		
		private $path;		// eg /wp-content/plugins/toc
		private $options;
		private $show_toc;	// allows to override the display (eg through [no_toc] shortcode)
		private $exclude_post_types;
		private $collision_collector;	// keeps a track of used anchors for collision detecting
		
		function __construct()
		{
			$this->path = plugins_url( '', __FILE__ );
			$this->show_toc = true;
			$this->exclude_post_types = array( 'attachment', 'revision', 'nav_menu_item', 'safecss' );
			$this->collision_collector = array();

			// get options
			$defaults = array(		// default options
				'fragment_prefix' => 'i',
				'position' => TOC_POSITION_BEFORE_FIRST_HEADING,
				'start' => 4,
				'show_heading_text' => true,
				'heading_text' => 'Contents',
				'auto_insert_post_types' => array('page'),
				'show_heirarchy' => true,
				'ordered_list' => true,
				'smooth_scroll' => false,
				'smooth_scroll_offset' => TOC_SMOOTH_SCROLL_OFFSET,
				'visibility' => true,
				'visibility_show' => 'show',
				'visibility_hide' => 'hide',
				'visibility_hide_by_default' => false,
				'width' => 'Auto',
				'width_custom' => '275',
				'width_custom_units' => 'px',
				'wrapping' => TOC_WRAPPING_NONE,
				'font_size' => '95',
				'font_size_units' => '%',
				'theme' => TOC_THEME_GREY,
				'custom_background_colour' => TOC_DEFAULT_BACKGROUND_COLOUR,
				'custom_border_colour' => TOC_DEFAULT_BORDER_COLOUR,
				'custom_title_colour' => TOC_DEFAULT_TITLE_COLOUR,
				'custom_links_colour' => TOC_DEFAULT_LINKS_COLOUR,
				'custom_links_hover_colour' => TOC_DEFAULT_LINKS_HOVER_COLOUR,
				'custom_links_visited_colour' => TOC_DEFAULT_LINKS_VISITED_COLOUR,
				'lowercase' => false,
				'hyphenate' => false,
				'bullet_spacing' => false,
				'include_homepage' => false,
				'exclude_css' => false,
				'exclude' => '',
				'heading_levels' => array('1', '2', '3', '4', '5', '6'),
				'restrict_path' => '',
				'css_container_class' => '',
				'sitemap_show_page_listing' => true,
				'sitemap_show_category_listing' => true,
				'sitemap_heading_type' => 3,
				'sitemap_pages' => 'Pages',
				'sitemap_categories' => 'Categories',
				'show_toc_in_widget_only' => false,
				'show_toc_in_widget_only_post_types' => array('page')
			);
			$options = get_option( 'toc-options', $defaults );
			$this->options = wp_parse_args( $options, $defaults );

			add_action( 'plugins_loaded', array(&$this, 'plugins_loaded') );
			add_action( 'wp_enqueue_scripts', array(&$this, 'wp_enqueue_scripts') );
			add_action( 'wp_head', array(&$this, 'wp_head') );
			add_action( 'admin_init', array(&$this, 'admin_init') );
			add_action( 'admin_menu', array(&$this, 'admin_menu') );
			add_action( 'widgets_init', array(&$this, 'widgets_init') );
			add_action( 'sidebar_admin_setup', array(&$this, 'sidebar_admin_setup') );
			
			add_filter( 'the_content', array(&$this, 'the_content'), 100 );	// run after shortcodes are interpretted (level 10)
			add_filter( 'plugin_action_links', array(&$this, 'plugin_action_links'), 10, 2 );
			add_filter( 'widget_text', 'do_shortcode' );
			
			add_shortcode( 'toc', array(&$this, 'shortcode_toc') );
			add_shortcode( 'no_toc', array(&$this, 'shortcode_no_toc') );
			add_shortcode( 'sitemap', array(&$this, 'shortcode_sitemap') );
			add_shortcode( 'sitemap_pages', array(&$this, 'shortcode_sitemap_pages') );
			add_shortcode( 'sitemap_categories', array(&$this, 'shortcode_sitemap_categories') );
			add_shortcode( 'sitemap_posts', array(&$this, 'shortcode_sitemap_posts') );
		}
		
		
		function __destruct()
		{
		}
		
		
		public function get_options()
		{
			return $this->options;
		}


		public function set_option($array)
		{
			$this->options = array_merge($this->options, $array);
		}


		/**
		 * Allows the developer to disable TOC execution
		 */
		public function disable()
		{
			$this->show_toc = false;
		}


		/**
		 * Allows the developer to enable TOC execution
		 */
		public function enable()
		{
			$this->show_toc = true;
		}
		
		
		public function set_show_toc_in_widget_only( $value = false )
		{
			if ( $value )
				$this->options['show_toc_in_widget_only'] = true;
			else
				$this->options['show_toc_in_widget_only'] = false;
			
			update_option( 'toc-options', $this->options );
		}


		public function set_show_toc_in_widget_only_post_types( $value = false )
		{
			if ( $value )
				$this->options['show_toc_in_widget_only_post_types'] = $value;
			else
				$this->options['show_toc_in_widget_only_post_types'] = array();
			
			update_option( 'toc-options', $this->options );
		}


		public function get_exclude_post_types()
		{
			return $this->exclude_post_types;
		}
		
		
		function plugin_action_links( $links, $file )
		{
			if ( $file == 'table-of-contents-plus/' . basename(__FILE__) ) {
				$settings_link = '<a href="options-general.php?page=toc">' . __('Settings', 'table-of-contents-plus') . '</a>';
				$links = array_merge( array( $settings_link ), $links );
			}
			return $links;
		}
		
		
		function shortcode_toc( $atts )
		{
			extract( shortcode_atts( array(
				'label' => $this->options['heading_text'],
				'label_show' => $this->options['visibility_show'],
				'label_hide' => $this->options['visibility_hide'],
				'no_label' => false,
				'class' => false,
				'wrapping' => $this->options['wrapping'],
				'heading_levels' => $this->options['heading_levels'],
				'exclude' => $this->options['exclude'],
				'collapse' => false
				), $atts )
			);

			$re_enqueue_scripts = false;

			if ( $no_label ) $this->options['show_heading_text'] = false;
			if ( $label ) $this->options['heading_text'] = html_entity_decode( $label );
			if ( $label_show ) {
				$this->options['visibility_show'] = html_entity_decode( $label_show );
				$re_enqueue_scripts = true;
			}
			if ( $label_hide ) {
				$this->options['visibility_hide'] = html_entity_decode( $label_hide );
				$re_enqueue_scripts = true;
			}
			if ( $class ) $this->options['css_container_class'] = $class;
			if ( $wrapping ) {
				switch ( strtolower(trim($wrapping)) ) {
					case 'left':
						$this->options['wrapping'] = TOC_WRAPPING_LEFT;
						break;
						
					case 'right':
						$this->options['wrapping'] = TOC_WRAPPING_RIGHT;
						break;
						
					default:
						// do nothing
				}
			}

			if ( $exclude ) $this->options['exclude'] = $exclude;
			if ( $collapse ) {
				$this->options['visibility_hide_by_default'] = true;
				$re_enqueue_scripts = true;
			}

			if ( $re_enqueue_scripts ) do_action( 'wp_enqueue_scripts' );

			// if $heading_levels is an array, then it came from the global options
			// and wasn't provided by per instance
			if ( $heading_levels && !is_array($heading_levels) ) {
				// make sure they are numbers between 1 and 6 and put into 
				// the $clean_heading_levels array if not already
				$clean_heading_levels = array();
				foreach (explode(',', $heading_levels) as $heading_level) {
					if ( is_numeric($heading_level) ) {
						if ( 1 <= $heading_level && $heading_level <= 6 ) {
							if ( !in_array($heading_level, $clean_heading_levels) ) {
								$clean_heading_levels[] = $heading_level;
							}
						}
					}
				}
				
				if ( count($clean_heading_levels) > 0 )
					$this->options['heading_levels'] = $clean_heading_levels;
			}
		
			if ( !is_search() && !is_archive() && !is_feed() )
				return '<!--TOC-->';
			else
				return;
		}
		
		
		function shortcode_no_toc( $atts )
		{
			$this->show_toc = false;

			return;
		}
		
		
		function shortcode_sitemap( $atts )
		{
			$html = '';
			
			// only do the following if enabled
			if ( $this->options['sitemap_show_page_listing'] || $this->options['sitemap_show_category_listing'] ) {
				$html = '<div class="toc_sitemap">';
				if ( $this->options['sitemap_show_page_listing'] )
					$html .=
						'<h' . $this->options['sitemap_heading_type'] . ' class="toc_sitemap_pages">' . htmlentities( $this->options['sitemap_pages'], ENT_COMPAT, 'UTF-8' ) . '</h' . $this->options['sitemap_heading_type'] . '>' .
						'<ul class="toc_sitemap_pages_list">' .
							wp_list_pages( array('title_li' => '', 'echo' => false ) ) .
						'</ul>'
					;
				if ( $this->options['sitemap_show_category_listing'] )
					$html .=
						'<h' . $this->options['sitemap_heading_type'] . ' class="toc_sitemap_categories">' . htmlentities( $this->options['sitemap_categories'], ENT_COMPAT, 'UTF-8' ) . '</h' . $this->options['sitemap_heading_type'] . '>' .
						'<ul class="toc_sitemap_categories_list">' .
							wp_list_categories( array( 'title_li' => '', 'echo' => false ) ) .
						'</ul>'
					;
				$html .= '</div>';
			}
			
			return $html;
		}
		
		
		function shortcode_sitemap_pages( $atts )
		{
			extract( shortcode_atts( array(
				'heading' => $this->options['sitemap_heading_type'],
				'label' => htmlentities( $this->options['sitemap_pages'], ENT_COMPAT, 'UTF-8' ),
				'no_label' => false,
				'exclude' => '',
				'exclude_tree' => '',
				'only_subpages' => false,
				'sort_column' => 'menu_order' // By default we sort the entries by the given menu order within WP
				), $atts )
			);

			if ( $heading < 1 || $heading > 6 )		// h1 to h6 are valid
				$heading = $this->options['sitemap_heading_type'];
			
			// 0 is the default-value for the child_of attribute of wp_list_pages()
			$only_subpages_of = 0;
			
			// If only subpages should be listed, we get the ID of the page here
			if ($only_subpages) {
				global $post;
				$only_subpages_of = $post->ID; 
			}

			$html = '<div class="toc_sitemap">';
			if ( !$no_label ) $html .= '<h' . $heading . ' class="toc_sitemap_pages">' . $label . '</h' . $heading . '>';
			$html .=
					'<ul class="toc_sitemap_pages_list">' . 
						$only_subpages ? 
							wp_list_pages( array('title_li' => '', 'echo' => false, 'exclude' => $exclude, 'exclude_tree' => $exclude_tree,
									'child_of' => $only_subpages_of, 'sort_column' => $sort_column ) ) :
							wp_list_pages( array('title_li' => '', 'echo' => false, 'exclude' => $exclude, 'exclude_tree' => $exclude_tree,
									'sort_column' => $sort_column ) ).
					'</ul>'.
				'</div>'
			;
			
			return $html;
		}
		
		
		function shortcode_sitemap_categories( $atts )
		{
			extract( shortcode_atts( array(
				'heading' => $this->options['sitemap_heading_type'],
				'label' => htmlentities( $this->options['sitemap_categories'], ENT_COMPAT, 'UTF-8' ),
				'no_label' => false,
				'exclude' => '',
				'exclude_tree' => ''
				), $atts )
			);
			
			if ( $heading < 1 || $heading > 6 )		// h1 to h6 are valid
				$heading = $this->options['sitemap_heading_type'];
			
			$html = '<div class="toc_sitemap">';
			if ( !$no_label ) $html .= '<h' . $heading . ' class="toc_sitemap_categories">' . $label . '</h' . $heading . '>';
			$html .=
					'<ul class="toc_sitemap_categories_list">' .
						wp_list_categories( array('title_li' => '', 'echo' => false, 'exclude' => $exclude, 'exclude_tree' => $exclude_tree ) ) .
					'</ul>' .
				'</div>'
			;
			
			return $html;
		}
		
		
		function shortcode_sitemap_posts( $atts )
		{
			extract( shortcode_atts( array(
				'order' => 'ASC',
				'orderby' => 'title',
				'separate' => true
				), $atts )
			);
			
			$articles = new WP_Query(array(
				'post_type' => 'post',
				'post_status' => 'publish',
				'order' => $order,
				'orderby' => $orderby,
				'posts_per_page' => -1
			));
			
			$html = $letter = '';

			$separate = strtolower($separate);
			if ( $separate == 'false' || $separate == 'no') $separate = false;
			
			while ( $articles->have_posts() ) {
				$articles->the_post();
				$title = strip_tags(get_the_title());

				if ( $separate ) {
					if ( $letter != strtolower($title[0]) ) {
						if ( $letter ) $html .= '</ul></div>';
						
						$html .= '<div class="toc_sitemap_posts_section"><p class="toc_sitemap_posts_letter">' . strtolower($title[0]) . '</p><ul class="toc_sitemap_posts_list">';
						$letter = strtolower($title[0]);
					}
				}

				$html .= '<li><a href="' . get_permalink($articles->post->ID) . '">' . $title . '</a></li>';
			}
			
			if ( $html ) {
				if ( $separate )
					$html .= '</div>';
				else
					$html = '<div class="toc_sitemap_posts_section"><ul class="toc_sitemap_posts_list">' . $html . '</ul></div>';
			}
			
			wp_reset_postdata();
			
			return $html;
		}
		
		
		/**
		 * Register and load CSS and javascript files for frontend.
		 */
		function wp_enqueue_scripts()
		{
			$js_vars = array();
			
			// register our CSS and scripts
			wp_register_style( 'toc-screen', $this->path . '/screen.min.css', array(), TOC_VERSION );
			wp_register_script( 'toc-front', $this->path . '/front.min.js', array('jquery'), TOC_VERSION, true );
			
			// enqueue them!
			if ( !$this->options['exclude_css'] ) wp_enqueue_style("toc-screen");
			
			if ( $this->options['smooth_scroll'] ) $js_vars['smooth_scroll'] = true;
			wp_enqueue_script( 'toc-front' );
			if ( $this->options['show_heading_text'] && $this->options['visibility'] ) {
				$width = ( $this->options['width'] != 'User defined' ) ? $this->options['width'] : $this->options['width_custom'] . $this->options['width_custom_units'];
				$js_vars['visibility_show'] = esc_js($this->options['visibility_show']);
				$js_vars['visibility_hide'] = esc_js($this->options['visibility_hide']);
				if ( $this->options['visibility_hide_by_default'] ) $js_vars['visibility_hide_by_default'] = true;
				$js_vars['width'] = esc_js($width);
			}
			if ( $this->options['smooth_scroll_offset'] != TOC_SMOOTH_SCROLL_OFFSET )
				$js_vars['smooth_scroll_offset'] = esc_js($this->options['smooth_scroll_offset']);
			
			if ( count($js_vars) > 0 ) {
				wp_localize_script(
					'toc-front',
					'tocplus',
					$js_vars
				);
			}
		}
		
		
		function plugins_loaded()
		{
			load_plugin_textdomain( 'table-of-contents-plus', false, dirname(plugin_basename(__FILE__)) . '/languages/' );
		}
		
		
		function admin_init()
		{
			wp_register_script( 'toc_admin_script', $this->path . '/admin.js' );
			wp_register_style( 'toc_admin_style', $this->path . '/admin.css' );
		}
		
		
		function admin_menu()
		{
			$page = add_submenu_page(
				'options-general.php', 
				__('TOC', 'table-of-contents-plus') . '+', 
				__('TOC', 'table-of-contents-plus') . '+', 
				'manage_options', 
				'toc', 
				array(&$this, 'admin_options')
			);
			
			add_action( 'admin_print_styles-' . $page, array(&$this, 'admin_options_head') );
		}
		
		
		function widgets_init()
		{
			register_widget('toc_widget');
		}


		/**
		 * Remove widget options on widget deletion
		 */
		function sidebar_admin_setup()
		{
			// this action is loaded at the start of the widget screen
			// so only do the following only when a form action has been initiated
			if ( 'post' == strtolower( $_SERVER['REQUEST_METHOD'] ) ) {
				if ( @$_POST['id_base'] == 'toc-widget' ) {
					if ( isset( $_POST['delete_widget'] ) ) {
						if ( 1 === (int) $_POST['delete_widget'] ) {
							$this->set_show_toc_in_widget_only( false );
							$this->set_show_toc_in_widget_only_post_types( array('page') );
						}
					}
				}
			}
		}
		
		
		/**
		 * Load needed scripts and styles only on the toc administration interface.
		 */
		function admin_options_head()
		{
			wp_enqueue_style( 'farbtastic' );
			wp_enqueue_script( 'farbtastic' );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'toc_admin_script' );
			wp_enqueue_style( 'toc_admin_style' );
		}
		
		
		/**
		 * Tries to convert $string into a valid hex colour.
		 * Returns $default if $string is not a hex value, otherwise returns verified hex.
		 */
		private function hex_value( $string = '', $default = '#' )
		{
			$return = $default;
			
			if ( $string ) {
				// strip out non hex chars
				$return = preg_replace( '/[^a-fA-F0-9]*/', '', $string );
				
				switch ( strlen($return) ) {
					case 3:	// do next
					case 6:
						$return = '#' . $return;
						break;
					
					default:
						if ( strlen($return) > 6 )
							$return = '#' . substr($return, 0, 6);	// if > 6 chars, then take the first 6
						elseif ( strlen($return) > 3 && strlen($return) < 6 )
							$return = '#' . substr($return, 0, 3);	// if between 3 and 6, then take first 3
						else
							$return = $default;						// not valid, return $default
				}
			}
			
			return $return;
		}
		
		
		private function save_admin_options()
		{
			global $post_id;

			// security check
			if ( !wp_verify_nonce( @$_POST['toc-admin-options'], plugin_basename(__FILE__) ) )
				return false;

			// require an administrator level to save
			if ( !current_user_can( 'manage_options', $post_id ) )
				return false;
			
			// use stripslashes on free text fields that can have ' " \
			// WordPress automatically slashes these characters as part of 
			// wp-includes/load.php::wp_magic_quotes()
			
			$custom_background_colour = $this->hex_value( trim($_POST['custom_background_colour']), TOC_DEFAULT_BACKGROUND_COLOUR );
			$custom_border_colour = $this->hex_value( trim($_POST['custom_border_colour']), TOC_DEFAULT_BORDER_COLOUR );
			$custom_title_colour = $this->hex_value( trim($_POST['custom_title_colour']), TOC_DEFAULT_TITLE_COLOUR );
			$custom_links_colour = $this->hex_value( trim($_POST['custom_links_colour']), TOC_DEFAULT_LINKS_COLOUR );
			$custom_links_hover_colour = $this->hex_value( trim($_POST['custom_links_hover_colour']), TOC_DEFAULT_LINKS_HOVER_COLOUR );
			$custom_links_visited_colour = $this->hex_value( trim($_POST['custom_links_visited_colour']), TOC_DEFAULT_LINKS_VISITED_COLOUR );
			
			if ( $restrict_path = trim($_POST['restrict_path']) ) {
				if ( strpos($restrict_path, '/') !== 0 ) {
					// restrict path did not start with a / so unset it
					$restrict_path = '';
				}
			}
			
			$this->options = array_merge(
				$this->options,
				array(
					'fragment_prefix' => trim($_POST['fragment_prefix']),
					'position' => intval($_POST['position']),
					'start' => intval($_POST['start']),
					'show_heading_text' => (isset($_POST['show_heading_text']) && $_POST['show_heading_text']) ? true : false,
					'heading_text' => stripslashes( trim($_POST['heading_text']) ),
					'auto_insert_post_types' => @(array)$_POST['auto_insert_post_types'],
					'show_heirarchy' => (isset($_POST['show_heirarchy']) && $_POST['show_heirarchy']) ? true : false,
					'ordered_list' => (isset($_POST['ordered_list']) && $_POST['ordered_list']) ? true : false,
					'smooth_scroll' => (isset($_POST['smooth_scroll']) && $_POST['smooth_scroll']) ? true : false,
					'smooth_scroll_offset' => intval($_POST['smooth_scroll_offset']),
					'visibility' => (isset($_POST['visibility']) && $_POST['visibility']) ? true : false,
					'visibility_show' => stripslashes( trim($_POST['visibility_show']) ),
					'visibility_hide' => stripslashes( trim($_POST['visibility_hide']) ),
					'visibility_hide_by_default' => (isset($_POST['visibility_hide_by_default']) && $_POST['visibility_hide_by_default']) ? true : false,
					'width' => trim($_POST['width']),
					'width_custom' => floatval($_POST['width_custom']),
					'width_custom_units' => trim($_POST['width_custom_units']),
					'wrapping' => intval($_POST['wrapping']),
					'font_size' => floatval($_POST['font_size']),
					'font_size_units' => trim($_POST['font_size_units']),
					'theme' => intval($_POST['theme']),
					'custom_background_colour' => $custom_background_colour,
					'custom_border_colour' => $custom_border_colour,
					'custom_title_colour' => $custom_title_colour,
					'custom_links_colour' => $custom_links_colour,
					'custom_links_hover_colour' => $custom_links_hover_colour,
					'custom_links_visited_colour' => $custom_links_visited_colour,
					'lowercase' => (isset($_POST['lowercase']) && $_POST['lowercase']) ? true : false,
					'hyphenate' => (isset($_POST['hyphenate']) && $_POST['hyphenate']) ? true : false,
					'bullet_spacing' => (isset($_POST['bullet_spacing']) && $_POST['bullet_spacing']) ? true : false,
					'include_homepage' => (isset($_POST['include_homepage']) && $_POST['include_homepage']) ? true : false,
					'exclude_css' => (isset($_POST['exclude_css']) && $_POST['exclude_css']) ? true : false,
					'heading_levels' => @(array)$_POST['heading_levels'],
					'exclude' => stripslashes( trim($_POST['exclude']) ),
					'restrict_path' => $restrict_path,
					'sitemap_show_page_listing' => (isset($_POST['sitemap_show_page_listing']) && $_POST['sitemap_show_page_listing']) ? true : false,
					'sitemap_show_category_listing' => (isset($_POST['sitemap_show_category_listing']) && $_POST['sitemap_show_category_listing']) ? true : false,
					'sitemap_heading_type' => intval($_POST['sitemap_heading_type']),
					'sitemap_pages' => stripslashes( trim($_POST['sitemap_pages']) ),
					'sitemap_categories' => stripslashes( trim($_POST['sitemap_categories']) )
				)
			);
			
			// update_option will return false if no changes were made
			update_option( 'toc-options', $this->options );
			
			return true;
		}
		
		
		function admin_options() 
		{
			$msg = '';
		
			if ( isset( $_GET['update'] ) ) {
				if ( $this->save_admin_options() )
					$msg = '<div id="message" class="updated fade"><p>' . __('Options saved.', 'table-of-contents-plus') . '</p></div>';
				else
					$msg = '<div id="message" class="error fade"><p>' . __('Save failed.', 'table-of-contents-plus') . '</p></div>';
			}

?>
<div id='toc' class='wrap'>
<div id="icon-options-general" class="icon32"><br /></div>
<h2>Table of Contents Plus</h2>
<?php echo $msg; ?>
<form method="post" action="<?php echo htmlentities('?page=' . $_GET['page'] . '&update'); ?>">
<?php wp_nonce_field( plugin_basename(__FILE__), 'toc-admin-options' ); ?>

<ul id="tabbed-nav">
	<li><a href="#tab1"><?php _e('Main Options', 'table-of-contents-plus'); ?></a></li>
	<li><a href="#tab2"><?php _e('Sitemap', 'table-of-contents-plus'); ?></a></li>
	<li class="url"><a href="http://dublue.com/plugins/toc/#Help"><?php _e('Help', 'table-of-contents-plus'); ?></a></li>
</ul>
<div class="tab_container">
	<div id="tab1" class="tab_content">
  
<table class="form-table">
<tbody>
<tr>
	<th><label for="position"><?php _e('Position', 'table-of-contents-plus'); ?></label></th>
	<td>
		<select name="position" id="position">
			<option value="<?php echo TOC_POSITION_BEFORE_FIRST_HEADING; ?>"<?php if ( TOC_POSITION_BEFORE_FIRST_HEADING == $this->options['position'] ) echo ' selected="selected"'; ?>><?php _e('Before first heading (default)', 'table-of-contents-plus'); ?></option>
			<option value="<?php echo TOC_POSITION_AFTER_FIRST_HEADING; ?>"<?php if ( TOC_POSITION_AFTER_FIRST_HEADING == $this->options['position'] ) echo ' selected="selected"'; ?>><?php _e('After first heading', 'table-of-contents-plus'); ?></option>
			<option value="<?php echo TOC_POSITION_TOP; ?>"<?php if ( TOC_POSITION_TOP == $this->options['position'] ) echo ' selected="selected"'; ?>><?php _e('Top', 'table-of-contents-plus'); ?></option>
			<option value="<?php echo TOC_POSITION_BOTTOM; ?>"<?php if ( TOC_POSITION_BOTTOM == $this->options['position'] ) echo ' selected="selected"'; ?>><?php _e('Bottom', 'table-of-contents-plus'); ?></option>
		</select>
	</td>
</tr>
<tr>
	<th><label for="start"><?php _e('Show when', 'table-of-contents-plus'); ?></label></th>
	<td>
		<select name="start" id="start">
<?php
			for ($i = TOC_MIN_START; $i <= TOC_MAX_START; $i++) {
				echo '<option value="' . $i . '"';
				if ( $i == $this->options['start'] ) echo ' selected="selected"';
				echo '>' . $i . '</option>' . "\n";
			}
?>
		</select> <?php 
		/* translators: text follows drop down list of numbers */
		_e('or more headings are present', 'table-of-contents-plus'); ?>
	</td>
</tr>
<tr>
	<th><?php _e('Auto insert for the following content types', 'table-of-contents-plus'); ?></th>
	<td><?php
			foreach (get_post_types() as $post_type) {
				// make sure the post type isn't on the exclusion list
				if ( !in_array($post_type, $this->exclude_post_types) ) {
					echo '<input type="checkbox" value="' . $post_type . '" id="auto_insert_post_types_' . $post_type .'" name="auto_insert_post_types[]"';
					if ( in_array($post_type, $this->options['auto_insert_post_types']) ) echo ' checked="checked"';
					echo ' /><label for="auto_insert_post_types_' . $post_type .'"> ' . $post_type . '</label><br />';
				}
			}
?>
</tr>
<tr>
	<th><label for="show_heading_text"><?php 
	/* translators: this is the title of the table of contents */
	_e('Heading text', 'table-of-contents-plus'); ?></label></th>
	<td>
		<input type="checkbox" value="1" id="show_heading_text" name="show_heading_text"<?php if ( $this->options['show_heading_text'] ) echo ' checked="checked"'; ?> /><label for="show_heading_text"> <?php _e('Show title on top of the table of contents', 'table-of-contents-plus'); ?></label><br />
		<div class="more_toc_options<?php if ( !$this->options['show_heading_text'] ) echo ' disabled'; ?>">
			<input type="text" class="regular-text" value="<?php echo htmlentities( $this->options['heading_text'], ENT_COMPAT, 'UTF-8' ); ?>" id="heading_text" name="heading_text" />
			<span class="description"><label for="heading_text"><?php _e('Eg: Contents, Table of Contents, Page Contents', 'table-of-contents-plus'); ?></label></span><br /><br />
			
			<input type="checkbox" value="1" id="visibility" name="visibility"<?php if ( $this->options['visibility'] ) echo ' checked="checked"'; ?> /><label for="visibility"> <?php _e( 'Allow the user to toggle the visibility of the table of contents', 'table-of-contents-plus'); ?></label><br />
			<div class="more_toc_options<?php if ( !$this->options['visibility'] ) echo ' disabled'; ?>">
				<table class="more_toc_options_table">
				<tbody>
				<tr>
					<th><label for="visibility_show"><?php _e('Show text', 'table-of-contents-plus'); ?></label></th>
					<td><input type="text" class="" value="<?php echo htmlentities( $this->options['visibility_show'], ENT_COMPAT, 'UTF-8' ); ?>" id="visibility_show" name="visibility_show" />
					<span class="description"><label for="visibility_show"><?php 
					/* translators: example text to display when you want to expand the table of contents */
					_e('Eg: show', 'table-of-contents-plus'); ?></label></span></td>
				</tr>
				<tr>
					<th><label for="visibility_hide"><?php _e('Hide text', 'table-of-contents-plus'); ?></label></th>
					<td><input type="text" class="" value="<?php echo htmlentities( $this->options['visibility_hide'], ENT_COMPAT, 'UTF-8' ); ?>" id="visibility_hide" name="visibility_hide" />
					<span class="description"><label for="visibility_hide"><?php 
					/* translators: example text to display when you want to collapse the table of contents */
					_e('Eg: hide', 'table-of-contents-plus'); ?></label></span></td>
				</tr>
				</tbody>
				</table>
				<input type="checkbox" value="1" id="visibility_hide_by_default" name="visibility_hide_by_default"<?php if ( $this->options['visibility_hide_by_default'] ) echo ' checked="checked"'; ?> /><label for="visibility_hide_by_default"> <?php _e( 'Hide the table of contents initially', 'table-of-contents-plus'); ?></label>
			</div>
		</div>
	</td>
</tr>
<tr>
	<th><label for="show_heirarchy"><?php _e('Show hierarchy', 'table-of-contents-plus'); ?></label></th>
	<td><input type="checkbox" value="1" id="show_heirarchy" name="show_heirarchy"<?php if ( $this->options['show_heirarchy'] ) echo ' checked="checked"'; ?> /></td>
</tr>
<tr>
	<th><label for="ordered_list"><?php _e('Number list items', 'table-of-contents-plus'); ?></label></th>
	<td><input type="checkbox" value="1" id="ordered_list" name="ordered_list"<?php if ( $this->options['ordered_list'] ) echo ' checked="checked"'; ?> /></td>
</tr>
<tr>
	<th><label for="smooth_scroll"><?php _e('Enable smooth scroll effect', 'table-of-contents-plus'); ?></label></th>
	<td><input type="checkbox" value="1" id="smooth_scroll" name="smooth_scroll"<?php if ( $this->options['smooth_scroll'] ) echo ' checked="checked"'; ?> /><label for="smooth_scroll"> <?php _e( 'Scroll rather than jump to the anchor link', 'table-of-contents-plus'); ?></label></td>
</tr>
</tbody>
</table>

<h3><?php _e('Appearance', 'table-of-contents-plus'); ?></h3>
<table class="form-table">
<tbody>
<tr>
	<th><label for="width"><?php _e('Width', 'table-of-contents-plus'); ?></label></td>
	<td>
		<select name="width" id="width">
			<optgroup label="<?php _e('Fixed width', 'table-of-contents-plus'); ?>">
				<option value="200px"<?php if ( '200px' == $this->options['width'] ) echo ' selected="selected"'; ?>>200px</option>
				<option value="225px"<?php if ( '225px' == $this->options['width'] ) echo ' selected="selected"'; ?>>225px</option>
				<option value="250px"<?php if ( '250px' == $this->options['width'] ) echo ' selected="selected"'; ?>>250px</option>
				<option value="275px"<?php if ( '275px' == $this->options['width'] ) echo ' selected="selected"'; ?>>275px</option>
				<option value="300px"<?php if ( '300px' == $this->options['width'] ) echo ' selected="selected"'; ?>>300px</option>
				<option value="325px"<?php if ( '325px' == $this->options['width'] ) echo ' selected="selected"'; ?>>325px</option>
				<option value="350px"<?php if ( '350px' == $this->options['width'] ) echo ' selected="selected"'; ?>>350px</option>
				<option value="375px"<?php if ( '375px' == $this->options['width'] ) echo ' selected="selected"'; ?>>375px</option>
				<option value="400px"<?php if ( '400px' == $this->options['width'] ) echo ' selected="selected"'; ?>>400px</option>
			</optgroup>
			<optgroup label="<?php _e('Relative', 'table-of-contents-plus'); ?>">
				<option value="Auto"<?php if ( 'Auto' == $this->options['width'] ) echo ' selected="selected"'; ?>><?php _e('Auto (default)', 'table-of-contents-plus'); ?></option>
				<option value="25%"<?php if ( '25%' == $this->options['width'] ) echo ' selected="selected"'; ?>>25%</option>
				<option value="33%"<?php if ( '33%' == $this->options['width'] ) echo ' selected="selected"'; ?>>33%</option>
				<option value="50%"<?php if ( '50%' == $this->options['width'] ) echo ' selected="selected"'; ?>>50%</option>
				<option value="66%"<?php if ( '66%' == $this->options['width'] ) echo ' selected="selected"'; ?>>66%</option>
				<option value="75%"<?php if ( '75%' == $this->options['width'] ) echo ' selected="selected"'; ?>>75%</option>
				<option value="100%"<?php if ( '100%' == $this->options['width'] ) echo ' selected="selected"'; ?>>100%</option>
			</optgroup>
			<optgroup label="<?php 
			/* translators: other width */
			_e('Other', 'table-of-contents-plus'); ?>">
				<option value="User defined"<?php if ( 'User defined' == $this->options['width'] ) echo ' selected="selected"'; ?>><?php _e('User defined', 'table-of-contents-plus'); ?></option>
			</optgroup>
		</select>
		<div class="more_toc_options<?php if ( 'User defined' != $this->options['width'] ) echo ' disabled'; ?>">
			<label for="width_custom"><?php 
			/* translators: ignore %s as it's some HTML label tags */
			printf( __('Please enter a number and %s select its units, eg: 100px, 10em', 'table-of-contents-plus'), '</label><label for="width_custom_units">' ); ?></label><br />
			<input type="text" class="regular-text" value="<?php echo floatval($this->options['width_custom']); ?>" id="width_custom" name="width_custom" />
			<select name="width_custom_units" id="width_custom_units">
				<option value="px"<?php if ( 'px' == $this->options['width_custom_units'] ) echo ' selected="selected"'; ?>>px</option>
				<option value="%"<?php if ( '%' == $this->options['width_custom_units'] ) echo ' selected="selected"'; ?>>%</option>
				<option value="em"<?php if ( 'em' == $this->options['width_custom_units'] ) echo ' selected="selected"'; ?>>em</option>
			</select>
		</div>
	</td>
</tr>
<tr>
	<th><label for="wrapping"><?php _e('Wrapping', 'table-of-contents-plus'); ?></label></td>
	<td>
		<select name="wrapping" id="wrapping">
			<option value="<?php echo TOC_WRAPPING_NONE; ?>"<?php if ( TOC_WRAPPING_NONE == $this->options['wrapping'] ) echo ' selected="selected"'; ?>><?php _e('None (default)', 'table-of-contents-plus'); ?></option>
			<option value="<?php echo TOC_WRAPPING_LEFT; ?>"<?php if ( TOC_WRAPPING_LEFT == $this->options['wrapping'] ) echo ' selected="selected"'; ?>><?php _e('Left', 'table-of-contents-plus'); ?></option>
			<option value="<?php echo TOC_WRAPPING_RIGHT; ?>"<?php if ( TOC_WRAPPING_RIGHT == $this->options['wrapping'] ) echo ' selected="selected"'; ?>><?php _e('Right', 'table-of-contents-plus'); ?></option>
		</select>
	</td>
</tr>
<tr>
	<th><label for="font_size"><?php _e('Font size', 'table-of-contents-plus'); ?></label></th>
	<td>
		<input type="text" class="regular-text" value="<?php echo floatval($this->options['font_size']); ?>" id="font_size" name="font_size" />
		<select name="font_size_units" id="font_size_units">
			<option value="px"<?php if ( 'pt' == $this->options['font_size_units'] ) echo ' selected="selected"'; ?>>pt</option>
			<option value="%"<?php if ( '%' == $this->options['font_size_units'] ) echo ' selected="selected"'; ?>>%</option>
			<option value="em"<?php if ( 'em' == $this->options['font_size_units'] ) echo ' selected="selected"'; ?>>em</option>
		</select>
	</td>
</tr>
<tr>
	<th><?php 
	/* translators: appearance / colour / look and feel options */
	_e('Presentation', 'table-of-contents-plus'); ?></th>
	<td>
		<div class="toc_theme_option">
			<input type="radio" name="theme" id="theme_<?php echo TOC_THEME_GREY; ?>" value="<?php echo TOC_THEME_GREY; ?>"<?php if ( $this->options['theme'] == TOC_THEME_GREY ) echo ' checked="checked"'; ?> /><label for="theme_<?php echo TOC_THEME_GREY; ?>"> <?php _e('Grey (default)', 'table-of-contents-plus'); ?><br />
			<img src="<?php echo $this->path; ?>/images/grey.png" alt="" />
			</label>
		</div>
		<div class="toc_theme_option">
			<input type="radio" name="theme" id="theme_<?php echo TOC_THEME_LIGHT_BLUE; ?>" value="<?php echo TOC_THEME_LIGHT_BLUE; ?>"<?php if ( $this->options['theme'] == TOC_THEME_LIGHT_BLUE ) echo ' checked="checked"'; ?> /><label for="theme_<?php echo TOC_THEME_LIGHT_BLUE; ?>"> <?php _e('Light blue', 'table-of-contents-plus'); ?><br />
			<img src="<?php echo $this->path; ?>/images/blue.png" alt="" />
			</label>
		</div>
		<div class="toc_theme_option">
			<input type="radio" name="theme" id="theme_<?php echo TOC_THEME_WHITE; ?>" value="<?php echo TOC_THEME_WHITE; ?>"<?php if ( $this->options['theme'] == TOC_THEME_WHITE ) echo ' checked="checked"'; ?> /><label for="theme_<?php echo TOC_THEME_WHITE; ?>"> <?php _e('White', 'table-of-contents-plus'); ?><br />
			<img src="<?php echo $this->path; ?>/images/white.png" alt="" />
			</label>
		</div>
		<div class="toc_theme_option">
			<input type="radio" name="theme" id="theme_<?php echo TOC_THEME_BLACK; ?>" value="<?php echo TOC_THEME_BLACK; ?>"<?php if ( $this->options['theme'] == TOC_THEME_BLACK ) echo ' checked="checked"'; ?> /><label for="theme_<?php echo TOC_THEME_BLACK; ?>"> <?php _e('Black', 'table-of-contents-plus'); ?><br />
			<img src="<?php echo $this->path; ?>/images/black.png" alt="" />
			</label>
		</div>
		<div class="toc_theme_option">
			<input type="radio" name="theme" id="theme_<?php echo TOC_THEME_TRANSPARENT; ?>" value="<?php echo TOC_THEME_TRANSPARENT; ?>"<?php if ( $this->options['theme'] == TOC_THEME_TRANSPARENT ) echo ' checked="checked"'; ?> /><label for="theme_<?php echo TOC_THEME_TRANSPARENT; ?>"> <?php _e('Transparent', 'table-of-contents-plus'); ?><br />
			<img src="<?php echo $this->path; ?>/images/transparent.png" alt="" />
			</label>
		</div>
		<div class="toc_theme_option">
			<input type="radio" name="theme" id="theme_<?php echo TOC_THEME_CUSTOM; ?>" value="<?php echo TOC_THEME_CUSTOM; ?>"<?php if ( $this->options['theme'] == TOC_THEME_CUSTOM ) echo ' checked="checked"'; ?> /><label for="theme_<?php echo TOC_THEME_CUSTOM; ?>"> <?php _e('Custom', 'table-of-contents-plus'); ?><br />
			<img src="<?php echo $this->path; ?>/images/custom.png" alt="" />
			</label>
		</div>
		<div class="clear"></div>
		
		<div class="more_toc_options<?php if ( TOC_THEME_CUSTOM != $this->options['theme'] ) echo ' disabled'; ?>">
			<table id="theme_custom" class="more_toc_options_table">
			<tbody>
			<tr>
				<th><label for="custom_background_colour"><?php _e('Background', 'table-of-contents-plus'); ?></label></th>
				<td><input type="text" class="custom_colour_option" value="<?php echo htmlentities( $this->options['custom_background_colour'] ); ?>" id="custom_background_colour" name="custom_background_colour" /> <img src="<?php echo $this->path; ?>/images/colour-wheel.png" alt="" /></td>
			</tr>
			<tr>
				<th><label for="custom_border_colour"><?php _e('Border', 'table-of-contents-plus'); ?></label></th>
				<td><input type="text" class="custom_colour_option" value="<?php echo htmlentities( $this->options['custom_border_colour'] ); ?>" id="custom_border_colour" name="custom_border_colour" /> <img src="<?php echo $this->path; ?>/images/colour-wheel.png" alt="" /></td>
			</tr>
			<tr>
				<th><label for="custom_title_colour"><?php _e('Title', 'table-of-contents-plus'); ?></label></th>
				<td><input type="text" class="custom_colour_option" value="<?php echo htmlentities( $this->options['custom_title_colour'] ); ?>" id="custom_title_colour" name="custom_title_colour" /> <img src="<?php echo $this->path; ?>/images/colour-wheel.png" alt="" /></td>
			</tr>
			<tr>
				<th><label for="custom_links_colour"><?php _e('Links', 'table-of-contents-plus'); ?></label></th>
				<td><input type="text" class="custom_colour_option" value="<?php echo htmlentities( $this->options['custom_links_colour'] ); ?>" id="custom_links_colour" name="custom_links_colour" /> <img src="<?php echo $this->path; ?>/images/colour-wheel.png" alt="" /></td>
			</tr>
			<tr>
				<th><label for="custom_links_hover_colour"><?php _e('Links (hover)', 'table-of-contents-plus'); ?></label></th>
				<td><input type="text" class="custom_colour_option" value="<?php echo htmlentities( $this->options['custom_links_hover_colour'] ); ?>" id="custom_links_hover_colour" name="custom_links_hover_colour" /> <img src="<?php echo $this->path; ?>/images/colour-wheel.png" alt="" /></td>
			</tr>
			<tr>
				<th><label for="custom_links_visited_colour"><?php _e('Links (visited)', 'table-of-contents-plus'); ?></label></th>
				<td><input type="text" class="custom_colour_option" value="<?php echo htmlentities( $this->options['custom_links_visited_colour'] ); ?>" id="custom_links_visited_colour" name="custom_links_visited_colour" /> <img src="<?php echo $this->path; ?>/images/colour-wheel.png" alt="" /></td>
			</tr>
			</tbody>
			</table>
			<div id="farbtastic_colour_wheel"></div>
			<div class="clear"></div>
			<p><?php printf(__("Leaving the value as %s will inherit your theme's styles", 'table-of-contents-plus'), '<code>#</code>'); ?></p>
		</div>
	</td>
</tr>
</tbody>
</table>

<h3><?php _e('Advanced', 'table-of-contents-plus'); ?> <span class="show_hide">(<a href="#toc_advanced_usage"><?php _e('show', 'table-of-contents-plus'); ?></a>)</span></h3>
<div id="toc_advanced_usage">
	<h4><?php _e('Power options', 'table-of-contents-plus'); ?></h4>
	<table class="form-table">
	<tbody>
	<tr>
		<th><label for="lowercase"><?php _e('Lowercase', 'table-of-contents-plus'); ?></label></th>
		<td><input type="checkbox" value="1" id="lowercase" name="lowercase"<?php if ( $this->options['lowercase'] ) echo ' checked="checked"'; ?> /><label for="lowercase"> <?php _e('Ensure anchors are in lowercase', 'table-of-contents-plus'); ?></label></td>
	</tr>
	<tr>
		<th><label for="hyphenate"><?php _e('Hyphenate', 'table-of-contents-plus'); ?></label></th>
		<td><input type="checkbox" value="1" id="hyphenate" name="hyphenate"<?php if ( $this->options['hyphenate'] ) echo ' checked="checked"'; ?> /><label for="hyphenate"> <?php _e('Use - rather than _ in anchors', 'table-of-contents-plus'); ?></label></td>
	</tr>
	<tr>
		<th><label for="include_homepage"><?php _e('Include homepage', 'table-of-contents-plus'); ?></label></th>
		<td><input type="checkbox" value="1" id="include_homepage" name="include_homepage"<?php if ( $this->options['include_homepage'] ) echo ' checked="checked"'; ?> /><label for="include_homepage"> <?php _e('Show the table of contents for qualifying items on the homepage', 'table-of-contents-plus'); ?></label></td>
	</tr>
	<tr>
		<th><label for="exclude_css"><?php _e('Exclude CSS file', 'table-of-contents-plus'); ?></label></th>
		<td><input type="checkbox" value="1" id="exclude_css" name="exclude_css"<?php if ( $this->options['exclude_css'] ) echo ' checked="checked"'; ?> /><label for="exclude_css"> <?php _e("Prevent the loading of this plugin's CSS styles. When selected, the appearance options from above will also be ignored.", 'table-of-contents-plus'); ?></label></td>
	</tr>
	<tr>
		<th><label for="bullet_spacing"><?php _e('Preserve theme bullets', 'table-of-contents-plus'); ?></label></th>
		<td><input type="checkbox" value="1" id="bullet_spacing" name="bullet_spacing"<?php if ( $this->options['bullet_spacing'] ) echo ' checked="checked"'; ?> /><label for="bullet_spacing"> <?php _e('If your theme includes background images for unordered list elements, enable this to support them', 'table-of-contents-plus'); ?></label></td>
	</tr>
	<tr>
		<th><?php _e('Heading levels', 'table-of-contents-plus'); ?></th>
		<td>
		<p><?php _e('Include the following heading levels. Deselecting a heading will exclude it.', 'table-of-contents-plus'); ?></p>
<?php
			// show heading 1 to 6 options
			for ($i = 1; $i <= 6; $i++) {
				echo '<input type="checkbox" value="' . $i . '" id="heading_levels' . $i .'" name="heading_levels[]"';
				if ( in_array($i, $this->options['heading_levels']) ) echo ' checked="checked"';
				echo ' /><label for="heading_levels' . $i .'"> ' . __('heading ') . $i . ' - h' . $i . '</label><br />';
			}
?>
		</td>
	</tr>
	<tr>
		<th><label for="exclude"><?php _e('Exclude headings', 'table-of-contents-plus'); ?></label></th>
		<td>
			<input type="text" class="regular-text" value="<?php echo htmlentities( $this->options['exclude'], ENT_COMPAT, 'UTF-8' ); ?>" id="exclude" name="exclude" style="width: 100%;" /><br />
			<label for="exclude"><?php _e('Specify headings to be excluded from appearing in the table of contents.  Separate multiple headings with a pipe <code>|</code>.  Use an asterisk <code>*</code> as a wildcard to match other text.  Note that this is not case sensitive. Some examples:', 'table-of-contents-plus'); ?></label><br/>
			<ul>
				<li><?php _e('<code>Fruit*</code> ignore headings starting with "Fruit"', 'table-of-contents-plus'); ?></li>
				<li><?php _e('<code>*Fruit Diet*</code> ignore headings with "Fruit Diet" somewhere in the heading', 'table-of-contents-plus'); ?></li>
				<li><?php _e('<code>Apple Tree|Oranges|Yellow Bananas</code> ignore headings that are exactly "Apple Tree", "Oranges" or "Yellow Bananas"', 'table-of-contents-plus'); ?></li>
			</ul>
		</td>
	</tr>
	<tr id="smooth_scroll_offset_tr" class="<?php if ( !$this->options['smooth_scroll'] ) echo 'disabled'; ?>">
		<th><label for="smooth_scroll_offset"><?php _e('Smooth scroll top offset', 'table-of-contents-plus'); ?></label></th>
		<td>
			<input type="text" class="regular-text" value="<?php echo intval($this->options['smooth_scroll_offset']); ?>" id="smooth_scroll_offset" name="smooth_scroll_offset" /> px<br />
			<label for="smooth_scroll_offset"><?php _e('If you have a consistent menu across the top of your site, you can adjust the top offset to stop the headings from appearing underneath the top menu. A setting of 30 accommodates the WordPress admin bar. This setting appears after you have enabled smooth scrolling from above.', 'table-of-contents-plus'); ?></label>
		</td>
	</tr>
	<tr>
		<th><label for="restrict_path"><?php _e('Restrict path', 'table-of-contents-plus'); ?></label></th>
		<td>
			<input type="text" class="regular-text" value="<?php echo htmlentities( $this->options['restrict_path'], ENT_COMPAT, 'UTF-8' ); ?>" id="restrict_path" name="restrict_path" /><br />
			<label for="restrict_path"><?php _e('Restrict generation of the table of contents to pages that match the required path. This path is from the root of your site and always begins with a forward slash.', 'table-of-contents-plus'); ?><br />
			<span class="description"><?php 
			/* translators: example URL path restriction */
			_e('Eg: /wiki/, /corporate/annual-reports/', 'table-of-contents-plus'); ?></span></label>
		</td>
	</tr>
	<tr>
		<th><label for="fragment_prefix"><?php _e('Default anchor prefix', 'table-of-contents-plus'); ?></label></th>
		<td>
			<input type="text" class="regular-text" value="<?php echo htmlentities( $this->options['fragment_prefix'], ENT_COMPAT, 'UTF-8' ); ?>" id="fragment_prefix" name="fragment_prefix" /><br />
			<label for="fragment_prefix"><?php _e('Anchor targets are restricted to alphanumeric characters as per HTML specification (see readme for more detail). The default anchor prefix will be used when no characters qualify. When left blank, a number will be used instead.', 'table-of-contents-plus'); ?><br />
			<?php _e('This option normally applies to content written in character sets other than ASCII.', 'table-of-contents-plus'); ?><br />
			<span class="description"><?php 
			/* translators: example anchor prefixes when no ascii characters match */
			_e('Eg: i, toc_index, index, _', 'table-of-contents-plus'); ?></span></label>
		</td>
	</tr>
	</tbody>
	</table>

	<h4><?php 
	/* translators: advanced usage */
	_e('Usage', 'table-of-contents-plus'); ?></h4>
	<p><?php printf(__('If you would like to fully customise the position of the table of contents, you can use the %s shortcode by placing it at the desired position of your post, page or custom post type. This method allows you to generate the table of contents despite having auto insertion disabled for its content type. Please visit the help tab for further information about this shortcode.', 'table-of-contents-plus'), '<code>[toc]</code>'); ?></p>
</div>


	</div>
	<div id="tab2" class="tab_content">
	

<p><?php printf(__('At its simplest, placing %s into a page will automatically create a sitemap of all pages and categories. This also works in a text widget.', 'table-of-contents-plus'), '<code>[sitemap]</code>'); ?></p>
<table class="form-table">
<tbody>
<tr>
	<th><label for="sitemap_show_page_listing"><?php _e('Show page listing', 'table-of-contents-plus'); ?></label></th>
	<td><input type="checkbox" value="1" id="sitemap_show_page_listing" name="sitemap_show_page_listing"<?php if ( $this->options['sitemap_show_page_listing'] ) echo ' checked="checked"'; ?> /></td>
</tr>
<tr>
	<th><label for="sitemap_show_category_listing"><?php _e('Show category listing', 'table-of-contents-plus'); ?></label></th>
	<td><input type="checkbox" value="1" id="sitemap_show_category_listing" name="sitemap_show_category_listing"<?php if ( $this->options['sitemap_show_category_listing'] ) echo ' checked="checked"'; ?> /></td>
</tr>
<tr>
	<th><label for="sitemap_heading_type"><?php _e('Heading type', 'table-of-contents-plus'); ?></label></th>
	<td><label for="sitemap_heading_type"><?php 
	/* translators: the full line is supposed to read - Use [1-6 drop down list] to print out the titles */
	_e('Use', 'table-of-contents-plus'); ?> h</label><select name="sitemap_heading_type" id="sitemap_heading_type">
<?php
			// h1 to h6
			for ($i = 1; $i <= 6; $i++) {
				echo '<option value="' . $i . '"';
				if ( $i == $this->options['sitemap_heading_type'] ) echo ' selected="selected"';
				echo '>' . $i . '</option>' . "\n";
			}
?>
		</select> <?php 
		/* translators: the full line is supposed to read - Use [h1-h6 drop down list] to print out the titles */
		_e('to print out the titles', 'table-of-contents-plus'); ?>
	</td>
</tr>
<tr>
	<th><label for="sitemap_pages"><?php _e('Pages label', 'table-of-contents-plus'); ?></label></th>
	<td><input type="text" class="regular-text" value="<?php echo htmlentities( $this->options['sitemap_pages'], ENT_COMPAT, 'UTF-8' ); ?>" id="sitemap_pages" name="sitemap_pages" />
		<span class="description"><?php _e('Eg: Pages, Page List', 'table-of-contents-plus'); ?></span>
	</td>
</tr>
<tr>
	<th><label for="sitemap_categories"><?php _e('Categories label', 'table-of-contents-plus'); ?></label></th>
	<td><input type="text" class="regular-text" value="<?php echo htmlentities( $this->options['sitemap_categories'], ENT_COMPAT, 'UTF-8' ); ?>" id="sitemap_categories" name="sitemap_categories" />
		<span class="description"><?php _e('Eg: Categories, Category List', 'table-of-contents-plus'); ?></span>
	</td>
</tr>
</tbody>
</table>

<h3><?php _e('Advanced usage', 'table-of-contents-plus'); ?> <span class="show_hide">(<a href="#sitemap_advanced_usage"><?php _e('show', 'table-of-contents-plus'); ?></a>)</span></h3>
<div id="sitemap_advanced_usage">
	<p><code>[sitemap_pages]</code> <?php printf(__('lets you print out a listing of only pages. Similarly %s can be used to print out a category listing. They both can accept a number of attributes so visit the help tab for more information.', 'table-of-contents-plus'), '<code>[sitemap_categories]</code>'); ?></p>
	<p><?php _e('Examples', 'table-of-contents-plus'); ?></p>
	<ol>
		<li><code>[sitemap_categories no_label="true"]</code> <?php _e('hides the heading from a category listing', 'table-of-contents-plus'); ?></li>
		<li><code>[sitemap_pages heading="6" label="This is an awesome listing" exclude="1,15"]</code> <?php printf(__('Uses h6 to display %s on a page listing excluding pages with IDs 1 and 15', 'table-of-contents-plus'), '<em>This is an awesome listing</em>'); ?></li>
	</ol>
</div>


	</div>
</div>
	

<p class="submit"><input type="submit" name="submit" class="button-primary" value="<?php _e('Update Options', 'table-of-contents-plus'); ?>" /></p>
</form>
</div>
<?php
		}
		
							
		function wp_head()
		{
			$css = '';
			
			if ( !$this->options['exclude_css'] ) {
				if ( $this->options['theme'] == TOC_THEME_CUSTOM || $this->options['width'] != 'Auto' ) {
					$css .= 'div#toc_container {';
					if ( $this->options['theme'] == TOC_THEME_CUSTOM )
						$css .= 'background: ' . $this->options['custom_background_colour'] . ';border: 1px solid ' . $this->options['custom_border_colour'] . ';';
					if ( $this->options['width'] != 'Auto' ) {
						$css .= 'width: ';
						if ( $this->options['width'] != 'User defined' )
							$css .= $this->options['width'];
						else
							$css .= $this->options['width_custom'] . $this->options['width_custom_units'];
						$css .= ';';
					}
					$css .= '}';
				}
				
				if ( '95%' != $this->options['font_size'] . $this->options['font_size_units'] )
					$css .= 'div#toc_container ul li {font-size: ' . $this->options['font_size'] . $this->options['font_size_units'] . ';}';
	
				if ( $this->options['theme'] == TOC_THEME_CUSTOM ) {
					if ( $this->options['custom_title_colour'] != TOC_DEFAULT_TITLE_COLOUR )
						$css .= 'div#toc_container p.toc_title {color: ' . $this->options['custom_title_colour'] . ';}';
					if ( $this->options['custom_links_colour'] != TOC_DEFAULT_LINKS_COLOUR )
						$css .= 'div#toc_container p.toc_title a,div#toc_container ul.toc_list a {color: ' . $this->options['custom_links_colour'] . ';}';
					if ( $this->options['custom_links_hover_colour'] != TOC_DEFAULT_LINKS_HOVER_COLOUR )
						$css .= 'div#toc_container p.toc_title a:hover,div#toc_container ul.toc_list a:hover {color: ' . $this->options['custom_links_hover_colour'] . ';}';
					if ( $this->options['custom_links_hover_colour'] != TOC_DEFAULT_LINKS_HOVER_COLOUR )
						$css .= 'div#toc_container p.toc_title a:hover,div#toc_container ul.toc_list a:hover {color: ' . $this->options['custom_links_hover_colour'] . ';}';
					if ( $this->options['custom_links_visited_colour'] != TOC_DEFAULT_LINKS_VISITED_COLOUR )
						$css .= 'div#toc_container p.toc_title a:visited,div#toc_container ul.toc_list a:visited {color: ' . $this->options['custom_links_visited_colour'] . ';}';
				}
			}
			
			if ( $css )
				echo '<style type="text/css">' . $css . '</style>';
		}
		
		
		/**
		 * Returns a clean url to be used as the destination anchor target
		 */
		private function url_anchor_target( $title )
		{
			$return = false;
			
			if ( $title ) {
				$return = trim( strip_tags($title) );

				// convert accented characters to ASCII 
				$return = remove_accents( $return );
				
				// replace newlines with spaces (eg when headings are split over multiple lines)
				$return = str_replace( array("\r", "\n", "\n\r", "\r\n"), ' ', $return );
				
				// remove &amp;
				$return = str_replace( '&amp;', '', $return );
				
				// remove non alphanumeric chars
				$return = preg_replace( '/[^a-zA-Z0-9 \-_]*/', '', $return );
				
				// convert spaces to _
				$return = str_replace(
					array('  ', ' '),
					'_',
					$return
				);
				
				// remove trailing - and _
				$return = rtrim( $return, '-_' );
				
				// lowercase everything?
				if ( $this->options['lowercase'] ) $return = strtolower($return);

				// if blank, then prepend with the fragment prefix
				// blank anchors normally appear on sites that don't use the latin charset
				if ( !$return ) {
					$return = ( $this->options['fragment_prefix'] ) ? $this->options['fragment_prefix'] : '_';
				}
				
				// hyphenate?
				if ( $this->options['hyphenate'] ) {
					$return = str_replace('_', '-', $return);
					$return = str_replace('--', '-', $return);
				}
			}
			
			if ( array_key_exists($return, $this->collision_collector) ) {
				$this->collision_collector[$return]++;
				$return .= '-' . $this->collision_collector[$return];
			}
			else
				$this->collision_collector[$return] = 1;
			
			return apply_filters( 'toc_url_anchor_target', $return );
		}
		
		
		private function build_hierarchy( &$matches )
		{
			$current_depth = 100;	// headings can't be larger than h6 but 100 as a default to be sure
			$html = '';
			$numbered_items = array();
			$numbered_items_min = null;
			
			// reset the internal collision collection
			$this->collision_collector = array();
			
			// find the minimum heading to establish our baseline
			for ($i = 0; $i < count($matches); $i++) {
				if ( $current_depth > $matches[$i][2] )
					$current_depth = (int)$matches[$i][2];
			}
			
			$numbered_items[$current_depth] = 0;
			$numbered_items_min = $current_depth;

			for ($i = 0; $i < count($matches); $i++) {

				if ( $current_depth == (int)$matches[$i][2] )
					$html .= '<li>';
			
				// start lists
				if ( $current_depth != (int)$matches[$i][2] ) {
					for ($current_depth; $current_depth < (int)$matches[$i][2]; $current_depth++) {
						$numbered_items[$current_depth + 1] = 0;
						$html .= '<ul><li>';
					}
				}
				
				// list item
				if ( in_array($matches[$i][2], $this->options['heading_levels']) ) {
					$html .= '<a href="#' . $this->url_anchor_target( $matches[$i][0] ) . '">';
					if ( $this->options['ordered_list'] ) {
						// attach leading numbers when lower in hierarchy
						$html .= '<span class="toc_number toc_depth_' . ($current_depth - $numbered_items_min + 1) . '">';
						for ($j = $numbered_items_min; $j < $current_depth; $j++) {
							$number = ($numbered_items[$j]) ? $numbered_items[$j] : 0;
							$html .= $number . '.';
						}
						
						$html .= ($numbered_items[$current_depth] + 1) . '</span> ';
						$numbered_items[$current_depth]++;
					}
					$html .= strip_tags($matches[$i][0]) . '</a>';
				}
				
				
				// end lists
				if ( $i != count($matches) - 1 ) {
					if ( $current_depth > (int)$matches[$i + 1][2] ) {
						for ($current_depth; $current_depth > (int)$matches[$i + 1][2]; $current_depth--) {
							$html .= '</li></ul>';
							$numbered_items[$current_depth] = 0;
						}
					}
					
					if ( $current_depth == (int)@$matches[$i + 1][2] )
						$html .= '</li>';
				}
				else {
					// this is the last item, make sure we close off all tags
					for ($current_depth; $current_depth >= $numbered_items_min; $current_depth--) {
						$html .= '</li>';
						if ( $current_depth != $numbered_items_min ) $html .= '</ul>';
					}
				}
			}

			return $html;
		}
		
		
		/**
		 * Returns a string with all items from the $find array replaced with their matching
		 * items in the $replace array.  This does a one to one replacement (rather than
		 * globally).
		 *
		 * This function is multibyte safe.
		 *
		 * $find and $replace are arrays, $string is the haystack.  All variables are
		 * passed by reference.
		 */
		private function mb_find_replace( &$find = false, &$replace = false, &$string = '' )
		{
			if ( is_array($find) && is_array($replace) && $string ) {
				// check if multibyte strings are supported
				if ( function_exists( 'mb_strpos' ) ) {
					for ($i = 0; $i < count($find); $i++) {
						$string = 
							mb_substr( $string, 0, mb_strpos($string, $find[$i]) ) .	// everything befor $find
							$replace[$i] .												// its replacement
							mb_substr( $string, mb_strpos($string, $find[$i]) + mb_strlen($find[$i]) )	// everything after $find
						;
					}
				}
				else {
					for ($i = 0; $i < count($find); $i++) {
						$string = substr_replace(
							$string,
							$replace[$i],
							strpos($string, $find[$i]),
							strlen($find[$i])
						);
					}
				}
			}
			
			return $string;
		}
		
		
		/**
		 * This function extracts headings from the html formatted $content.  It will pull out
		 * only the required headings as specified in the options.  For all qualifying headings,
		 * this function populates the $find and $replace arrays (both passed by reference)
		 * with what to search and replace with.
		 * 
		 * Returns a html formatted string of list items for each qualifying heading.  This 
		 * is everything between and NOT including <ul> and </ul>
		 */
		public function extract_headings( &$find, &$replace, $content = '' )
		{
			$matches = array();
			$anchor = '';
			$items = false;
			
			// reset the internal collision collection as the_content may have been triggered elsewhere
			// eg by themes or other plugins that need to read in content such as metadata fields in
			// the head html tag, or to provide descriptions to twitter/facebook
			$this->collision_collector = array();
			
			if ( is_array($find) && is_array($replace) && $content ) {
				// get all headings
				// the html spec allows for a maximum of 6 heading depths
				if ( preg_match_all('/(<h([1-6]{1})[^>]*>).*<\/h\2>/msuU', $content, $matches, PREG_SET_ORDER) ) {

					// remove undesired headings (if any) as defined by heading_levels
					if ( count($this->options['heading_levels']) != 6 ) {
						$new_matches = array();
						for ($i = 0; $i < count($matches); $i++) {
							if ( in_array($matches[$i][2], $this->options['heading_levels']) )
								$new_matches[] = $matches[$i];
						}
						$matches = $new_matches;
					}

					// remove specific headings if provided via the 'exclude' property
					if ( $this->options['exclude'] ) {
						$excluded_headings = explode('|', $this->options['exclude']);
						if ( count($excluded_headings) > 0 ) {
							for ($j = 0; $j < count($excluded_headings); $j++) {
								// escape some regular expression characters
								// others: http://www.php.net/manual/en/regexp.reference.meta.php
								$excluded_headings[$j] = str_replace(
									array('*'), 
									array('.*'), 
									trim($excluded_headings[$j])
								);
							}
	
							$new_matches = array();
							for ($i = 0; $i < count($matches); $i++) {
								$found = false;
								for ($j = 0; $j < count($excluded_headings); $j++) {
									if ( @preg_match('/^' . $excluded_headings[$j] . '$/imU', strip_tags($matches[$i][0])) ) {
										$found = true;
										break;
									}
								}
								if (!$found) $new_matches[] = $matches[$i];
							}
							if ( count($matches) != count($new_matches) )
								$matches = $new_matches;
						}
					}

					// remove empty headings
					$new_matches = array();
					for ($i = 0; $i < count($matches); $i++) {
						if ( trim( strip_tags($matches[$i][0]) ) != false )
							$new_matches[] = $matches[$i];
					}
					if ( count($matches) != count($new_matches) )
						$matches = $new_matches;

					// check minimum number of headings
					if ( count($matches) >= $this->options['start'] ) {

						for ($i = 0; $i < count($matches); $i++) {
							// get anchor and add to find and replace arrays
							$anchor = $this->url_anchor_target( $matches[$i][0] );
							$find[] = $matches[$i][0];
							$replace[] = str_replace(
								array(
									$matches[$i][1],				// start of heading
									'</h' . $matches[$i][2] . '>'	// end of heading
								),
								array(
									$matches[$i][1] . '<span id="' . $anchor . '">',
									'</span></h' . $matches[$i][2] . '>'
								),
								$matches[$i][0]
							);

							// assemble flat list
							if ( !$this->options['show_heirarchy'] ) {
								$items .= '<li><a href="#' . $anchor . '">';
								if ( $this->options['ordered_list'] ) $items .= count($replace) . ' ';
								$items .= strip_tags($matches[$i][0]) . '</a></li>';
							}
						}

						// build a hierarchical toc?
						// we could have tested for $items but that var can be quite large in some cases
						if ( $this->options['show_heirarchy'] ) $items = $this->build_hierarchy( $matches );
						
					}
				}
			}
			
			return $items;
		}
		
		
		/**
		 * Returns true if the table of contents is eligible to be printed, false otherwise.
		 */
		public function is_eligible( $shortcode_used = false )
		{
			global $post;

			// do not trigger the TOC when displaying an XML/RSS feed
			if ( is_feed() ) return false;
			
			// if the shortcode was used, this bypasses many of the global options
			if ( $shortcode_used !== false ) {
				// shortcode is used, make sure it adheres to the exclude from 
				// homepage option if we're on the homepage
				if ( !$this->options['include_homepage'] && is_front_page() )
					return false;
				else
					return true;
			}
			else {
				if (
					( in_array(get_post_type($post), $this->options['auto_insert_post_types']) && $this->show_toc && !is_search() && !is_archive() && !is_front_page() ) || 
					( $this->options['include_homepage'] && is_front_page() )
				) {
					if ( $this->options['restrict_path'] ) {
						if ( strpos($_SERVER['REQUEST_URI'], $this->options['restrict_path']) === 0 )
							return true;
						else
							return false;
					}
					else
						return true;
				}
				else
					return false;
			}
		}
		
		
		function the_content( $content )
		{
			global $post;
			$items = $css_classes = $anchor = '';
			$custom_toc_position = strpos($content, '<!--TOC-->');
			$find = $replace = array();

			if ( $this->is_eligible($custom_toc_position) ) {
				
				$items = $this->extract_headings($find, $replace, $content);

				if ( $items ) {
					// do we display the toc within the content or has the user opted
					// to only show it in the widget?  if so, then we still need to 
					// make the find/replace call to insert the anchors
					if ( $this->options['show_toc_in_widget_only'] && (in_array(get_post_type(), $this->options['show_toc_in_widget_only_post_types'])) ) {
						$content = $this->mb_find_replace($find, $replace, $content);
					}
					else {

						// wrapping css classes
						switch( $this->options['wrapping'] ) {
							case TOC_WRAPPING_LEFT:
								$css_classes .= ' toc_wrap_left';
								break;
								
							case TOC_WRAPPING_RIGHT:
								$css_classes .= ' toc_wrap_right';
								break;

							case TOC_WRAPPING_NONE:
							default:
								// do nothing
						}
						
						// colour themes
						switch ( $this->options['theme'] ) {
							case TOC_THEME_LIGHT_BLUE:
								$css_classes .= ' toc_light_blue';
								break;
							
							case TOC_THEME_WHITE:
								$css_classes .= ' toc_white';
								break;
								
							case TOC_THEME_BLACK:
								$css_classes .= ' toc_black';
								break;
							
							case TOC_THEME_TRANSPARENT:
								$css_classes .= ' toc_transparent';
								break;
						
							case TOC_THEME_GREY:
							default:
								// do nothing
						}
						
						// bullets?
						if ( $this->options['bullet_spacing'] )
							$css_classes .= ' have_bullets';
						else
							$css_classes .= ' no_bullets';
						
						if ( $this->options['css_container_class'] ) $css_classes .= ' ' . $this->options['css_container_class'];

						$css_classes = trim($css_classes);
						
						// an empty class="" is invalid markup!
						if ( !$css_classes ) $css_classes = ' ';
						
						// add container, toc title and list items
						$html = '<div id="toc_container" class="' . $css_classes . '">';
						if ( $this->options['show_heading_text'] ) {
							$toc_title = $this->options['heading_text'];
							if ( strpos($toc_title, '%PAGE_TITLE%') !== false ) $toc_title = str_replace( '%PAGE_TITLE%', get_the_title(), $toc_title );
							if ( strpos($toc_title, '%PAGE_NAME%') !== false ) $toc_title = str_replace( '%PAGE_NAME%', get_the_title(), $toc_title );
							$html .= '<p class="toc_title">' . htmlentities( $toc_title, ENT_COMPAT, 'UTF-8' ) . '</p>';
						}
						$html .= '<ul class="toc_list">' . $items . '</ul></div>' . "\n";
						
						if ( $custom_toc_position !== false ) {
							$find[] = '<!--TOC-->';
							$replace[] = $html;
							$content = $this->mb_find_replace($find, $replace, $content);
						}
						else {	
							if ( count($find) > 0 ) {
								switch ( $this->options['position'] ) {
									case TOC_POSITION_TOP:
										$content = $html . $this->mb_find_replace($find, $replace, $content);
										break;
									
									case TOC_POSITION_BOTTOM:
										$content = $this->mb_find_replace($find, $replace, $content) . $html;
										break;
									
									case TOC_POSITION_AFTER_FIRST_HEADING:
										$replace[0] = $replace[0] . $html;
										$content = $this->mb_find_replace($find, $replace, $content);
										break;
								
									case TOC_POSITION_BEFORE_FIRST_HEADING:
									default:
										$replace[0] = $html . $replace[0];
										$content = $this->mb_find_replace($find, $replace, $content);
								}
							}
						}
					}
				}
			}
			else {
				// remove <!--TOC--> (inserted from shortcode) from content
				$content = str_replace('<!--TOC-->', '', $content);
			}
		
			return $content;
		}
		
	} // end class
endif;



if ( !class_exists( 'toc_widget' ) ) :
	class toc_widget extends WP_Widget {

		function __construct()
		{
			$widget_options = array( 
				'classname' => 'toc_widget', 
				'description' => __('Display the table of contents in the sidebar with this widget', 'table-of-contents-plus') 
			);
			$control_options = array( 
				'width' => 250, 
				'height' => 350, 
				'id_base' => 'toc-widget'
			);
			parent::__construct( 'toc-widget', 'TOC+', $widget_options, $control_options );
		}
		

		/**
		 * Widget output to the public
		 */
		function widget( $args, $instance ) 
		{
			global $tic, $wp_query;
			$items = $custom_toc_position = '';
			$find = $replace = array();
			
			$toc_options = $tic->get_options();
			$post = get_post( $wp_query->post->ID );
			$custom_toc_position = strpos( $post->post_content, '[toc]' );	// at this point, shortcodes haven't run yet so we can't search for <!--TOC-->
			
			if ( $tic->is_eligible($custom_toc_position) ) {
				
				extract( $args );
				
				$items = $tic->extract_headings( $find, $replace, wptexturize($post->post_content) );
				$title = ( array_key_exists('title', $instance) ) ? apply_filters('widget_title', $instance['title']) : '';
				if ( strpos($title, '%PAGE_TITLE%') !== false ) $title = str_replace( '%PAGE_TITLE%', get_the_title(), $title );
				if ( strpos($title, '%PAGE_NAME%') !== false ) $title = str_replace( '%PAGE_NAME%', get_the_title(), $title );
				$hide_inline = $toc_options['show_toc_in_widget_only'];

				$css_classes = '';
				// bullets?
				if ( $toc_options['bullet_spacing'] )
					$css_classes .= ' have_bullets';
				else
					$css_classes .= ' no_bullets';
				
				if ( $items ) {
					// before widget (defined by themes)
					echo $before_widget;

					// display the widget title if one was input (before and after titles defined by themes)
					if ( $title ) echo $before_title . $title . $after_title;

					// display the list
					echo '<ul class="toc_widget_list' . $css_classes . '">' . $items . '</ul>';
					
					// after widget (defined by themes)
					echo $after_widget;
				}
			}
		}
		

		/**
		 * Update the widget settings
		 */
		function update( $new_instance, $old_instance ) 
		{
			global $tic;
			
			$instance = $old_instance;

			// strip tags for title to remove HTML (important for text inputs)
			$instance['title'] = strip_tags( trim( $new_instance['title'] ) );
			
			// no need to strip tags for the following
			//$instance['hide_inline'] = $new_instance['hide_inline'];
			$tic->set_show_toc_in_widget_only( $new_instance['hide_inline'] );
			$tic->set_show_toc_in_widget_only_post_types( (array)$new_instance['show_toc_in_widget_only_post_types'] );

			return $instance;
		}
		

		/**
		 * Displays the widget settings on the widget panel.
		 */
		function form( $instance )
		{
			global $tic;
			$toc_options = $tic->get_options();
		
			$defaults = array( 
				'title' => $toc_options['heading_text']
			);
			$instance = wp_parse_args( (array)$instance, $defaults );

?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title', 'table-of-contents-plus'); ?>:</label>
				<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
			</p>

			<p>
				<input class="checkbox" type="checkbox" <?php checked( $toc_options['show_toc_in_widget_only'], 1 ); ?> id="<?php echo $this->get_field_id( 'hide_inline' ); ?>" name="<?php echo $this->get_field_name( 'hide_inline' ); ?>" value="1" /> 
				<label for="<?php echo $this->get_field_id( 'hide_inline' ); ?>"> <?php _e('Show the table of contents only in the sidebar', 'table-of-contents-plus'); ?></label>
			</p>

			<div class="show_toc_in_widget_only_post_types" style="margin: 0 0 25px 25px; display: <?php echo ( $toc_options['show_toc_in_widget_only'] == 1 ) ? 'block' : 'none'; ?>;">
				<p><?php _e('For the following content types:', 'table-of-contents-plus'); ?></p>

			<?php
			foreach (get_post_types() as $post_type) {
				// make sure the post type isn't on the exclusion list
				if ( !in_array($post_type, $tic->get_exclude_post_types()) ) {
					echo '<input type="checkbox" value="' . $post_type . '" id="' . $this->get_field_id( 'show_toc_in_widget_only_post_types_' . $post_type ) . '" name="' . $this->get_field_name( "show_toc_in_widget_only_post_types" ) . '[]"';
					if ( in_array($post_type, $toc_options['show_toc_in_widget_only_post_types']) ) echo ' checked="checked"';
					echo ' /><label for="' . $this->get_field_id( 'show_toc_in_widget_only_post_types_' . $post_type ) . '"> ' . $post_type . '</label><br />';
				}
			}

			?></div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#<?php echo $this->get_field_id( 'hide_inline' ); ?>').click(function() {
		$(this).parent().siblings('div.show_toc_in_widget_only_post_types').toggle('fast');
	});
});
</script>
<?php
		}
		
	} // end class
endif;


/**
 * Returns a HTML formatted string of the table of contents without the surrounding UL or OL
 * tags to enable the theme editor to supply their own ID and/or classes to the outer list.
 *
 * There are three optional parameters you can feed this function with:
 *
 *		- $content is the entire content with headings.  If blank, will default to the current $post
 *
 * 		- $link is the URL to prefix the anchor with.  If provided a string, will use it as the prefix.  
 *		If set to true then will try to obtain the permalink from the $post object.
 *
 *		- $apply_eligibility bool, defaults to false.  When set to true, will apply the check to
 *		see if bit of content has the prerequisites needed for a TOC, eg minimum number of headings
 *		enabled post type, etc.
 */
function toc_get_index( $content = '', $prefix_url = '', $apply_eligibility = false )
{
	global $wp_query, $tic;
	
	$return = '';
	$find = $replace = array();
	$proceed = true;
	
	if ( !$content ) {
		$post = get_post( $wp_query->post->ID );
		$content = wptexturize($post->post_content);
	}
	
	if ( $apply_eligibility ) {
		if ( !$tic->is_eligible() ) {
			$proceed = false;
		}
	}
	else {
		$tic->set_option(array('start' => 0));
	}

	if ( $proceed ) {
		$return = $tic->extract_headings( $find, $replace, $content );
		if ( $prefix_url ) $return = str_replace('href="#', 'href="' . $prefix_url . '#', $return);
	}
	
	return $return;
}


// do the magic
$tic = new toc();
