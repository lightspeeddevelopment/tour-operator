<?php
/**
 * @package   Taxonomy_Widget
 * @author    LightSpeed
 * @license   GPL3
 * @link
 * @copyright 2016 LightSpeed
 **/
namespace lsx\legacy;

class Taxonomy_Widget extends \WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'lsx-widget',
			'description' => 'TO Taxonomy',
		);

		parent::__construct( 'LSX_TO_Taxonomy_Widget', 'TO Taxonomies', $widget_ops );
	}

	/** @see WP_Widget::widget -- do not rename this */
	public function widget( $args, $instance ) {
		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			$title = false;
		}

		if ( isset( $instance['title_link'] ) ) {
			$title_link = $instance['title_link'];
		} else {
			$title_link = false;
		}

		if ( isset( $instance['columns'] ) ) {
			$columns = $instance['columns'];
		} else {
			$columns = false;
		}

		if ( isset( $instance['orderby'] ) ) {
			$orderby = $instance['orderby'];
		} else {
			$orderby = false;
		}

		if ( isset( $instance['order'] ) ) {
			$order = $instance['order'];
		} else {
			$order = false;
		}

		if ( isset( $instance['limit'] ) ) {
			$limit = $instance['limit'];
		} else {
			$limit = 0;
		}

		if ( isset( $instance['include'] ) ) {
			$include = $instance['include'];
		} else {
			$include = false;
		}

		if ( isset( $instance['disable_placeholder'] ) ) {
			$disable_placeholder = $instance['disable_placeholder'];
		} else {
			$disable_placeholder = false;
		}

		if ( isset( $instance['disable_text'] ) ) {
			$disable_text = $instance['disable_text'];
		} else {
			$disable_text = false;
		}

		if ( isset( $instance['disable_single_link'] ) ) {
			$disable_single_link = $instance['disable_single_link'];
		} else {
			$disable_single_link = false;
		}

		if ( isset( $instance['buttons'] ) ) {
			$buttons = $instance['buttons'];
		} else {
			$buttons = false;
		}

		if ( isset( $instance['button_text'] ) ) {
			$button_text = $instance['button_text'];
		} else {
			$button_text = false;
		}

		if ( isset( $instance['carousel'] ) ) {
			$carousel = $instance['carousel'];
		} else {
			$carousel = false;
		}

		if ( isset( $instance['taxonomy'] ) ) {
			$taxonomy = $instance['taxonomy'];
		} else {
			$taxonomy = false;
		}

		if ( isset( $instance['class'] ) ) {
			$class = $instance['class'];
		} else {
			$class = false;
		}

		if ( isset( $instance['interval'] ) ) {
			$interval = $instance['interval'];
		} else {
			$interval = false;
		}

		// arguments

		if ( isset( $args['before_widget'] ) ) {
			$before_widget = $args['before_widget'];
		} else {
			$before_widget = '';
		}

		if ( isset( $args['after_widget'] ) ) {
			$after_widget = $args['after_widget'];
		} else {
			$after_widget = '';
		}

		if ( isset( $args['before_title'] ) ) {
			$before_title = $args['before_title'];
		} else {
			$before_title = '';
		}

		if ( isset( $args['after_title'] ) ) {
			$after_title = $args['after_title'];
		} else {
			$after_title = '';
		}

		if ( '' != $include ) {
			$limit = 0;
		}

		if ( '1' == $buttons ) {
			$buttons = true;
		} else {
			$buttons = false;
		}

		if ( $title_link ) {
			// $link_open = "<a href='$title_link'>";
			$link_open = '';
			// $link_close = "</a>";
			$link_close = '';
		} else {
			$link_open  = '';
			$link_close = '';
		}

		$class = 'class="' . $class . ' ';

		echo wp_kses_post( str_replace( 'class="', $class, $before_widget ) );

		if ( false != $title ) {
			$title = $before_title . $link_open . $title . $link_close . $after_title;
			echo wp_kses_post( apply_filters( 'lsx_to_taxonomy_widget_title', $title ) );
		}

		$args = array(
			'title'               => $title,
			'link'                => $title_link,
			'columns'             => $columns,
			'orderby'             => $orderby,
			'order'               => $order,
			'limit'               => $limit,
			'include'             => $include,
			'disable_placeholder' => $disable_placeholder,
			'disable_text'        => $disable_text,
			'disable_single_link' => $disable_single_link,
			'buttons'             => $buttons,
			'button_text'         => $button_text,
			'taxonomy'            => $taxonomy,
			'class'               => $class,
			'interval'            => $interval,
		);

		$args['carousel'] = $carousel;

		echo wp_kses_post( $this->output( $args ) );

		echo wp_kses_post( $after_widget );
	}

	/** @see WP_Widget::update -- do not rename this */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title']               = wp_kses_post( force_balance_tags( $new_instance['title'] ) );
		$instance['title_link']          = strip_tags( $new_instance['title_link'] );
		$instance['columns']             = strip_tags( $new_instance['columns'] );
		$instance['orderby']             = strip_tags( $new_instance['orderby'] );
		$instance['order']               = strip_tags( $new_instance['order'] );
		$instance['limit']               = strip_tags( $new_instance['limit'] );
		$instance['include']             = strip_tags( $new_instance['include'] );
		$instance['disable_placeholder'] = strip_tags( $new_instance['disable_placeholder'] );
		$instance['disable_text']        = strip_tags( $new_instance['disable_text'] );
		$instance['disable_single_link'] = strip_tags( $new_instance['disable_single_link'] );
		$instance['buttons']             = strip_tags( $new_instance['buttons'] );
		$instance['button_text']         = strip_tags( $new_instance['button_text'] );
		$instance['carousel']            = strip_tags( $new_instance['carousel'] );
		$instance['taxonomy']            = strip_tags( $new_instance['taxonomy'] );
		$instance['class']               = strip_tags( $new_instance['class'] );
		$instance['interval']            = strip_tags( $new_instance['interval'] );

		return $instance;
	}

	/** @see WP_Widget::form -- do not rename this */
	function form( $instance ) {
		$defaults = array(
			'title'               => '',
			'title_link'          => '',
			'columns'             => '1',
			'orderby'             => 'date',
			'order'               => 'DESC',
			'limit'               => '',
			'include'             => '',
			'disable_placeholder' => false,
			'disable_text'        => 0,
			'disable_single_link' => 0,
			'buttons'             => false,
			'button_text'         => false,
			'taxonomy'            => '',
			'class'               => '',
			'interval'            => '7000',
		);

		$defaults['carousel'] = 0;

		$instance = wp_parse_args( (array) $instance, $defaults );

		$title               = esc_attr( $instance['title'] );
		$title_link          = esc_attr( $instance['title_link'] );
		$columns             = esc_attr( $instance['columns'] );
		$orderby             = esc_attr( $instance['orderby'] );
		$order               = esc_attr( $instance['order'] );
		$limit               = esc_attr( $instance['limit'] );
		$include             = esc_attr( $instance['include'] );
		$disable_placeholder = esc_attr( $instance['disable_placeholder'] );
		$disable_text        = esc_attr( $instance['disable_text'] );
		$disable_single_link = esc_attr( $instance['disable_single_link'] );
		$buttons             = esc_attr( $instance['buttons'] );
		$button_text         = esc_attr( $instance['button_text'] );
		$taxonomy            = esc_attr( $instance['taxonomy'] );
		$class               = esc_attr( $instance['class'] );
		$interval            = esc_attr( $instance['interval'] );
		$carousel            = esc_attr( $instance['carousel'] );
		$interval            = esc_attr( $instance['interval'] );

		?>
		<p>
			<label for="<?php echo wp_kses_post( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'tour-operator' ); ?></label>
			<input class="widefat" id="<?php echo wp_kses_post( $this->get_field_id( 'title' ) ); ?>"
				name="<?php echo wp_kses_post( $this->get_field_name( 'title' ) ); ?>" type="text"
				value="<?php echo wp_kses_post( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo wp_kses_post( $this->get_field_id( 'title_link' ) ); ?>"><?php esc_html_e( 'Title Link:', 'tour-operator' ); ?></label>
			<input class="widefat"
				id="<?php echo wp_kses_post( $this->get_field_id( 'title_link' ) ); ?>"
				name="<?php echo wp_kses_post( $this->get_field_name( 'title_link' ) ); ?>" type="text"
				value="<?php echo wp_kses_post( $title_link ); ?>" /> <small>
								  <?php
									esc_html_e(
										'Link the widget title to
				a URL',
										'tour-operator'
									);
									?>
</small>
		</p>
		<h4 class="widget-title" style="border-top: 1px solid #e5e5e5;padding-top:10px;"><?php esc_html_e( 'Query', 'tour-operator' ); ?></h4>
		<p>
			<label for="<?php echo wp_kses_post( $this->get_field_id( 'taxonomy' ) ); ?>"><?php esc_html_e( 'Taxonomy:', 'tour-operator' ); ?></label>
			<select name="<?php echo wp_kses_post( $this->get_field_name( 'taxonomy' ) ); ?>" id="<?php echo wp_kses_post( $this->get_field_id( 'taxonomy' ) ); ?>"	class="widefat layout">
				<?php
				$options = array();
				$options = lsx_to_get_taxonomies();
				if ( empty( $options ) ) {
					$options['none'] = esc_attr__( 'None', 'tour-operator' );
				}

				foreach ( $options as $key => $name ) {
					$selected = ( $taxonomy == $key ) ? ' selected="selected"' : '';
					?>
					<option value="<?php echo wp_kses_post( $key ); ?>" id="<?php echo wp_kses_post( $key ); ?>" <?php echo wp_kses_post( $selected ); ?>><?php echo wp_kses_post( $name ); ?></option>
					<?php
				}
				?>
			</select>
		</p>
		<p>
			<label for="<?php echo wp_kses_post( $this->get_field_id( 'orderby' ) ); ?>"><?php esc_html_e( 'Order By:', 'tour-operator' ); ?></label>
			<select name="<?php echo wp_kses_post( $this->get_field_name( 'orderby' ) ); ?>"
				id="<?php echo wp_kses_post( $this->get_field_id( 'orderby' ) ); ?>" class="widefat">
					<?php
					$options = array(
						'Name'                 => 'name',
						'Slug'                 => 'slug',
						'ID'                   => 'term_id',
						'Count'                => 'count',
						'Admin (custom order)' => 'none',
					);
					foreach ( $options as $name => $value ) {
						$selected = ( $orderby == $value ) ? ' selected="selected"' : '';
						?>
						<option value="<?php echo wp_kses_post( $value ); ?>" id="<?php echo wp_kses_post( $value ); ?>" <?php echo wp_kses_post( $selected ); ?>><?php echo wp_kses_post( $name ); ?></option>
						<?php
					}
					?>
					</select>
		</p>
		<p>
			<label for="<?php echo wp_kses_post( $this->get_field_id( 'order' ) ); ?>"><?php esc_html_e( 'Order:', 'tour-operator' ); ?></label>
			<select name="<?php echo wp_kses_post( $this->get_field_name( 'order' ) ); ?>"
				id="<?php echo wp_kses_post( $this->get_field_id( 'order' ) ); ?>" class="widefat">
					<?php
					$options = array(
						'Ascending'  => 'ASC',
						'Descending' => 'DESC',
					);
					foreach ( $options as $name => $value ) {
						$selected = ( $orderby == $value ) ? ' selected="selected"' : '';
						?>
						<option value="<?php echo wp_kses_post( $value ); ?>" id="<?php echo wp_kses_post( $value ); ?>" <?php echo wp_kses_post( $selected ); ?>><?php echo wp_kses_post( $name ); ?></option>
						<?php
					}
					?>
			</select>
		</p>
		<p>
			<label for="<?php echo wp_kses_post( $this->get_field_id( 'limit' ) ); ?>"><?php esc_html_e( 'Maximum amount:', 'tour-operator' ); ?></label>
			<input class="widefat" id="<?php echo wp_kses_post( $this->get_field_id( 'limit' ) ); ?>"
				name="<?php echo wp_kses_post( $this->get_field_name( 'limit' ) ); ?>" type="text"
				value="<?php echo wp_kses_post( $limit ); ?>" /> <small><?php esc_html_e( 'Leave empty to display all', 'tour-operator' ); ?></small>
		</p>
		<p>
			<label for="<?php echo wp_kses_post( $this->get_field_id( 'include' ) ); ?>"><?php esc_html_e( 'Specify by ID:', 'tour-operator' ); ?></label>
			<input class="widefat"
				id="<?php echo wp_kses_post( $this->get_field_id( 'include' ) ); ?>"
				name="<?php echo wp_kses_post( $this->get_field_name( 'include' ) ); ?>" type="text"
				value="<?php echo wp_kses_post( $include ); ?>" /> <small><?php esc_html_e( 'Comma separated list, overrides limit setting', 'tour-operator' ); ?></small>
		</p>
		<h4 class="widget-title" style="border-top: 1px solid #e5e5e5;padding-top:10px;"><?php esc_html_e( 'Layout', 'tour-operator' ); ?></h4>
		<p>
			<label for="<?php echo wp_kses_post( $this->get_field_id( 'columns' ) ); ?>"><?php esc_html_e( 'Columns:', 'tour-operator' ); ?></label>
			<select name="<?php echo wp_kses_post( $this->get_field_name( 'columns' ) ); ?>"
				id="<?php echo wp_kses_post( $this->get_field_id( 'columns' ) ); ?>"
				class="widefat layout">
					<?php
					$options = array( '1', '2', '3', '4', '5', '6' );
					foreach ( $options as $option ) {
						$key      = lcfirst( $option );
						$selected = ( $columns == $key ) ? ' selected="selected"' : '';
						?>
						<option value="<?php echo wp_kses_post( $key ); ?>" id="<?php echo wp_kses_post( $key ); ?>" <?php echo wp_kses_post( $selected ); ?>><?php echo wp_kses_post( $option ); ?></option>
						<?php
					}
					?>
			 </select>
		</p>
		<p>
			<label for="<?php echo wp_kses_post( $this->get_field_id( 'class' ) ); ?>"><?php esc_html_e( 'Class:', 'tour-operator' ); ?></label>
			<input class="widefat" id="<?php echo wp_kses_post( $this->get_field_id( 'class' ) ); ?>"
				name="<?php echo wp_kses_post( $this->get_field_name( 'class' ) ); ?>" type="text"
				value="<?php echo wp_kses_post( $class ); ?>" />
			<small>Add your own class to the opening element of the widget</small>
		</p>
		<p>
			<input id="<?php echo wp_kses_post( $this->get_field_id( 'disable_placeholder' ) ); ?>"
				name="<?php echo wp_kses_post( $this->get_field_name( 'disable_placeholder' ) ); ?>" type="checkbox"
				value="1" <?php checked( '1', $disable_placeholder ); ?> /> <label
				for="<?php echo wp_kses_post( $this->get_field_id( 'disable_placeholder' ) ); ?>"><?php esc_html_e( 'Disable Featured Image', 'tour-operator' ); ?></label>
		</p>
		<p>
			<input id="<?php echo wp_kses_post( $this->get_field_id( 'disable_text' ) ); ?>"
				name="<?php echo wp_kses_post( $this->get_field_name( 'disable_text' ) ); ?>" type="checkbox"
				value="1" <?php checked( '1', $disable_text ); ?> /> <label
				for="<?php echo wp_kses_post( $this->get_field_id( 'disable_text' ) ); ?>"><?php esc_html_e( 'Disable Excerpt and Tagline', 'tour-operator' ); ?></label>
		</p>
		<p>
			<input id="<?php echo wp_kses_post( $this->get_field_id( 'disable_single_link' ) ); ?>"
				name="<?php echo wp_kses_post( $this->get_field_name( 'disable_single_link' ) ); ?>" type="checkbox"
				value="1" <?php checked( '1', $disable_single_link ); ?> /> <label
				for="<?php echo wp_kses_post( $this->get_field_id( 'disable_single_link' ) ); ?>"><?php esc_html_e( 'Disable Single Link', 'tour-operator' ); ?></label>
		</p>
		<p>
			<input id="<?php echo wp_kses_post( $this->get_field_id( 'buttons' ) ); ?>"
				name="<?php echo wp_kses_post( $this->get_field_name( 'buttons' ) ); ?>" type="checkbox"
				value="1" <?php checked( '1', $buttons ); ?> /> <label
				for="<?php echo wp_kses_post( $this->get_field_id( 'buttons' ) ); ?>"><?php esc_html_e( 'Display Button', 'tour-operator' ); ?></label>
		</p>
		<p>
			<label for="<?php echo wp_kses_post( $this->get_field_id( 'button_text' ) ); ?>"><?php esc_html_e( 'Button Text:', 'tour-operator' ); ?></label>
			<input class="widefat" id="<?php echo wp_kses_post( $this->get_field_id( 'button_text' ) ); ?>"
				name="<?php echo wp_kses_post( $this->get_field_name( 'button_text' ) ); ?>" type="text"
				value="<?php echo wp_kses_post( $button_text ); ?>" />
		</p>
		<h4 class="widget-title" style="border-top: 1px solid #e5e5e5;padding-top:10px;"><?php esc_html_e( 'Slider', 'tour-operator' ); ?></h4>
		<p>
			<input id="<?php echo wp_kses_post( $this->get_field_id( 'carousel' ) ); ?>"
				name="<?php echo wp_kses_post( $this->get_field_name( 'carousel' ) ); ?>"
				type="checkbox" value="1" <?php checked( '1', $carousel ); ?> /> <label
				for="<?php echo wp_kses_post( $this->get_field_id( 'carousel' ) ); ?>"><?php esc_html_e( 'Enable Carousel', 'tour-operator' ); ?></label>
		</p>
		<p>
			<label for="<?php echo wp_kses_post( $this->get_field_id( 'interval' ) ); ?>"><?php esc_html_e( 'Slide Interval:', 'tour-operator' ); ?></label>
			<input class="widefat" id="<?php echo wp_kses_post( $this->get_field_id( 'interval' ) ); ?>"
				name="<?php echo wp_kses_post( $this->get_field_name( 'interval' ) ); ?>" type="text"
				value="<?php echo wp_kses_post( $interval ); ?>" />
			<small>Type "false" to disable.</small>
		</p>
		<?php

	}

	public function output( $atts ) {
		global $columns, $term, $taxonomy, $disable_placeholder, $disable_text, $disable_single_link;

		// @codingStandardsIgnoreStart
		extract( shortcode_atts( array(
			'tag' => 'h3',
			'columns' => 1,
			'orderby' => 'date',
			'order' => 'DESC',
			'limit' => 0,
			'include' => '',
			'disable_placeholder' => false,
			'disable_text' => false,
			'disable_single_link' => false,
			'link' => false,
			'buttons' => false,
			'button_text' => false,
			'carousel' => false,
			'layout' => 'standard',
			'taxonomy' => '',
			'interval' => '7000',
		), $atts ) );
		// @codingStandardsIgnoreEnd

		$output = '';

		if ( '' != $include ) {
			$include = explode( ',', $include );
			$args    = array(
				'taxonomy'   => $taxonomy,
				'number'     => (int) $limit,
				'include'    => $include,
				'orderby'    => 'include',
				'order'      => $order,
				'hide_empty' => 0,
			);
		} else {
			$args = array(
				'taxonomy'   => $taxonomy,
				'number'     => (int) $limit,
				'orderby'    => $orderby,
				'order'      => $order,
				'hide_empty' => 0,
			);
		}

		if ( 'none' !== $orderby ) {
			$args['suppress_filters']           = true;
			$args['disabled_custom_post_order'] = true;
		}

		$widget_query = get_terms( $args );

		if ( ! empty( $widget_query ) && ! is_wp_error( $widget_query ) ) {
			$count = 1;

			$this->before_while( $columns, $carousel, $taxonomy, count( $widget_query ), $interval );

			foreach ( $widget_query as $term ) {
				$this->loop_start( $columns, $carousel, $taxonomy, count( $widget_query ), $count );

				if ( ! $carousel ) {
					echo wp_kses_post( '<div ' . lsx_to_widget_class( $taxonomy, true ) . '>' );
				}

				$this->content_part( 'content', 'widget-' . $taxonomy );

				if ( ! $carousel ) {
					echo wp_kses_post( '</div>' );
				}

				$this->loop_end( $columns, $carousel, $taxonomy, count( $widget_query ), $count );

				$count++;
			}

			$this->after_while( $columns, $carousel, $taxonomy, count( $widget_query ) );

			if ( false !== $buttons && false != $link ) {
				$link = '/' . $taxonomy . 's';
				echo wp_kses_post( '<p class="lsx-to-widget-view-all"><span><a href="' . esc_url( $link ) . '" class="btn border-btn">' . $button_text . ' <i class="fa fa-angle-right"></i></a></span></p>' );
			}
		}

		return $output;
	}

	/**
	 * Runs just after the if and before the while statement in $this->output()
	 */
	public function before_while( $columns = 1, $carousel = 0, $taxonomy = '', $post_count = 0, $interval = 'false' ) {
		$output = '';

		// Carousel Output Opening
		if ( $carousel ) {
			$landing_image     = '';
			$this->carousel_id = rand( 20, 20000 );

			$output .= "<div class='slider-container lsx-to-widget-items'>";
			$output .= "<div id='slider-{$this->carousel_id}' class='lsx-to-slider'>";
			$output .= '<div class="lsx-to-slider-wrap">';
			$output .= "<div class='lsx-to-slider-inner' data-interval='{$interval}' data-slick='{ \"slidesToShow\": {$columns}, \"slidesToScroll\": {$columns} }'>";
		} else {
			$output .= "<div class='lsx-to-widget-items'>";
		}

		echo wp_kses_post( $output );
	}

	/**
	 * Runs at the very end of the loop before it runs again.
	 */
	public function loop_start( $columns = 1, $carousel = 0, $taxonomy = '', $post_count = 0, $count = 0 ) {
		$output = '';

		if ( $carousel ) {
			$output .= "<div class='lsx-to-widget-item-wrap lsx-{$taxonomy}'>";
		} elseif ( 1 === $count ) {
			$output .= "<div class='row'>";
		}
		echo wp_kses_post( $output );
	}

	/**
	 * Runs at the very end of the loop before it runs again.
	 */
	public function loop_end( $columns = 1, $carousel = 0, $taxonomy = '', $post_count = 0, $count = 0 ) {
		$output = '';

		if ( $carousel ) {
			$output .= '</div>';
		} elseif ( 0 === $count % $columns || $count === $post_count ) {
			$output .= '</div>';
			if ( $count < $post_count ) {
				$output .= "<div class='row'>";
			}
		}
		echo wp_kses_post( $output );
	}

	/**
	 * Runs just after the if and before the while statement in $this->output()
	 */
	public function after_while( $columns = 1, $carousel = 0, $taxonomy = '', $post_count = 0 ) {
		$output = '';

		if ( $carousel ) {
			$output .= '</div>';
			$output .= '</div>';
			$output .= '</div>';
			$output .= '</div>';
		} else {
			$output .= '</div>';
		}

		echo wp_kses_post( $output );
	}

	/**
	 * Redirect WordPress to the single template located in the plugin
	 *
	 * @param    $template
	 *
	 * @return    $template
	 */
	public function content_part( $slug, $name = null ) {
		global $taxonomy;

		$template = array();
		$name     = (string) $name;

		if ( '' !== $name ) {
			$template = "{$slug}-{$name}.php";
		} else {
			$template = "{$slug}.php";
		}

		$original_name = $template;
		$path          = apply_filters( 'lsx_to_widget_path', '', $taxonomy );

		if ( '' == locate_template( array( $template ) ) && file_exists( $path . 'templates/' . $template ) ) {
			$template = $path . 'templates/' . $template;
		} elseif ( file_exists( get_stylesheet_directory() . '/' . $template ) ) {
			$template = get_stylesheet_directory() . '/' . $template;
		} else {
			$template = false;
		}

		if ( false !== $template ) {
			load_template( $template, false );
		} else {
			echo wp_kses_post( '<p>No ' . $original_name . ' can be found.</p>' );
		}
	}

}

?>
