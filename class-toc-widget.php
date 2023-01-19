<?php

if ( ! class_exists( 'TOC_Widget' ) ) :
	class TOC_Widget extends WP_Widget {

		public function __construct() {
			$widget_options  = [
				'classname'   => 'toc_widget',
				'description' => __( 'Display the table of contents in the sidebar with this widget', 'table-of-contents-plus' ),
			];
			$control_options = [
				'width'   => 250,
				'height'  => 350,
				'id_base' => 'toc-widget',
			];
			parent::__construct( 'toc-widget', 'TOC+', $widget_options, $control_options );
		}


		/**
		 * Widget output to the public
		 */
		public function widget( $args, $instance ) {
			global $toc_plus, $wp_query;

			if ( is_null( $wp_query->post ) ) {
				return;
			}

			$html                = '';
			$items               = '';
			$custom_toc_position = '';
			$find                = [];
			$replace             = [];

			$toc_options         = $toc_plus->get_options();
			$post                = get_post( $wp_query->post->ID );
			$custom_toc_position = strpos( $post->post_content, '[toc]' );  // at this point, shortcodes haven't run yet so we can't search for <!--TOC-->

			if ( $toc_plus->is_eligible( $custom_toc_position ) ) {
				$items = $toc_plus->extract_headings( $find, $replace, wptexturize( do_shortcode( $post->post_content ) ) );
				$title = ( array_key_exists( 'title', $instance ) ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
				if ( false !== strpos( $title, '%PAGE_TITLE%' ) ) {
					$title = str_replace( '%PAGE_TITLE%', get_the_title(), $title );
				}
				if ( false !== strpos( $title, '%PAGE_NAME%' ) ) {
					$title = str_replace( '%PAGE_NAME%', get_the_title(), $title );
				}
				$hide_inline = $toc_options['show_toc_in_widget_only'];

				$css_classes = '';
				// bullets?
				if ( $toc_options['bullet_spacing'] ) {
					$css_classes .= ' have_bullets';
				} else {
					$css_classes .= ' no_bullets';
				}

				if ( $items ) {
					// before widget (defined by themes)
					$html .= $args['before_widget'];

					// display the widget title if one was input (before and after titles defined by themes)
					if ( $title ) {
						$html .= $args['before_title'] . $title . $args['after_title'];
					}

					// display the list
					$html .= '<ul class="toc_widget_list' . $css_classes . '">' . $items . '</ul>';

					// after widget (defined by themes)
					$html .= $args['after_widget'];

					echo wp_kses_post( $html );
				}
			}
		}


		/**
		 * Update the widget settings
		 */
		public function update( $new_instance, $old_instance ) {
			global $toc_plus;

			$instance = $old_instance;

			// strip tags for title to remove HTML (important for text inputs)
			$instance['title'] = wp_strip_all_tags( trim( $new_instance['title'] ) );

			// no need to strip tags for the following
			//$instance['hide_inline'] = $new_instance['hide_inline'];
			$toc_plus->set_show_toc_in_widget_only( $new_instance['hide_inline'] );
			$toc_plus->set_show_toc_in_widget_only_post_types( (array) $new_instance['show_toc_in_widget_only_post_types'] );

			return $instance;
		}


		/**
		 * Displays the widget settings on the widget panel.
		 */
		public function form( $instance ) {
			global $toc_plus;
			$toc_options = $toc_plus->get_options();

			$defaults = [ 'title' => $toc_options['heading_text'] ];
			$instance = wp_parse_args( (array) $instance, $defaults );

			?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'table-of-contents-plus' ); ?>:</label>
				<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" style="width: 100%;">
			</p>

			<p>
				<input class="checkbox" type="checkbox" <?php checked( $toc_options['show_toc_in_widget_only'], 1 ); ?> id="<?php echo esc_attr( $this->get_field_id( 'hide_inline' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'hide_inline' ) ); ?>" value="1">
				<label for="<?php echo esc_attr( $this->get_field_id( 'hide_inline' ) ); ?>"> <?php esc_html_e( 'Show the table of contents only in the sidebar', 'table-of-contents-plus' ); ?></label>
			</p>

			<div class="show_toc_in_widget_only_post_types" style="margin: 0 0 25px 25px; display: <?php echo ( 1 === $toc_options['show_toc_in_widget_only'] ) ? 'block' : 'none'; ?>;">
				<p><?php esc_html_e( 'For the following content types:', 'table-of-contents-plus' ); ?></p>

			<?php
			foreach ( get_post_types() as $post_type ) {
				// make sure the post type isn't on the exclusion list
				if ( ! in_array( $post_type, $toc_plus->get_exclude_post_types(), true ) ) {
					echo '<input type="checkbox" value="' . esc_attr( $post_type ) . '" id="' . esc_attr( $this->get_field_id( 'show_toc_in_widget_only_post_types_' . $post_type ) ) . '" name="' . esc_attr( $this->get_field_name( 'show_toc_in_widget_only_post_types' ) ) . '[]"';
					if ( in_array( $post_type, $toc_options['show_toc_in_widget_only_post_types'], true ) ) {
						echo ' checked="checked"';
					}
					echo ' /><label for="' . esc_attr( $this->get_field_id( 'show_toc_in_widget_only_post_types_' . $post_type ) ) . '"> ' . esc_html( $post_type ) . '</label><br />';
				}
			}

			?>
			</div>
<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#<?php echo esc_js( $this->get_field_id( 'hide_inline' ) ); ?>').click(function() {
		$(this).parent().siblings('div.show_toc_in_widget_only_post_types').toggle('fast');
	});
});
</script>
			<?php
		}

	} // end class
endif;
