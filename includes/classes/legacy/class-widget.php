<?php
/**
 * @package   Widget
 * @author    LightSpeed
 * @license   GPL3
 * @link
 * @copyright 2016 LightSpeed
 **/
namespace lsx\legacy;

class Widget extends \WP_Widget {

	/**
	 * Holds the widget args
	 *
	 * @var array
	 */
	public $args = array();

	/**
	 * Holds the widgets parsed shortcode atts
	 *
	 * @var array
	 */
	public $parsed_atts = array();

	/**
	 * Holds the current widget instance
	 *
	 * @var array
	 */
	public $instance_args = array();

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'lsx-widget',
			'description' => esc_html__( 'TO', 'tour-operator' ),
		);

		parent::__construct( 'LSX_TO_Widget', 'TO Post Types', $widget_ops );
	}

	/** @see WP_Widget::widget -- do not rename this */
	public function widget( $args, $instance ) {
		$this->args          = $args;
		$this->instance_args = $instance;

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
			$limit = '-1';
		}

		if ( isset( $instance['include'] ) ) {
			$include = $instance['include'];
		} else {
			$include = false;
		}

		if ( isset( $instance['parents_only'] ) ) {
			$parents_only = $instance['parents_only'];
		} else {
			$parents_only = false;
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

		if ( isset( $instance['disable_view_more'] ) ) {
			$disable_view_more = $instance['disable_view_more'];
		} else {
			$disable_view_more = false;
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
		if ( isset( $instance['featured'] ) ) {
			$featured = $instance['featured'];
		} else {
			$featured = false;
		}
		if ( isset( $instance['post_type'] ) ) {
			$post_type = $instance['post_type'];
		} else {
			$post_type = false;
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
			$limit = '-1';
		}

		if ( '1' == $buttons ) {
			$buttons = true;
		} else {
			$buttons = false;
		}

		if ( $title_link ) {
			$link_open  = '';
			$link_close = '';
		} else {
			$link_open  = '';
			$link_close = '';
		}

		$class = 'class="' . $class . ' ';
		echo wp_kses_post( str_replace( 'class="', $class, $before_widget ) );

		if ( post_type_exists( $post_type ) ) {
			if ( false != $title ) {

				if ( 'video' != $post_type ) {
					$title = $before_title . $link_open . $title . $link_close . $after_title;
					echo wp_kses_post( apply_filters( 'lsx_to_post_type_widget_title', $title ) );
				}
			}

			$args = array(
				'title'               => $title,
				'link'                => $title_link,
				'columns'             => $columns,
				'orderby'             => $orderby,
				'order'               => $order,
				'limit'               => $limit,
				'include'             => $include,
				'parents_only'        => $parents_only,
				'disable_placeholder' => $disable_placeholder,
				'disable_text'        => $disable_text,
				'disable_view_more'   => $disable_view_more,
				'buttons'             => $buttons,
				'button_text'         => $button_text,
				'featured'            => $featured,
				'post_type'           => $post_type,
				'class'               => $class,
				'interval'            => $interval,
			);

			$args['carousel'] = $carousel;
			echo wp_kses_post( $this->output( $args ) );
		} else {
			echo wp_kses_post( '<p>' . esc_html__( 'That post type does not exist.', 'tour-operator' ) . '</p>' );
		}

		echo wp_kses_post( $after_widget );
	}

	/** @see WP_Widget::update -- do not rename this */
	function update( $new_instance, $old_instance ) {
		$instance                        = $old_instance;
		$instance['title']               = esc_html( force_balance_tags( $new_instance['title'] ) );
		$instance['title_link']          = esc_url_raw( $new_instance['title_link'] );
		$instance['columns']             = in_array( $new_instance['columns'], range( 1, 6 ) ) ? $new_instance['columns'] : 1;
		$instance['orderby']             = in_array( $new_instance['columns'], range( 1, 7 ) ) ? $new_instance['orderby'] : 1;
		$instance['order']               = in_array( $new_instance['columns'], range( 1, 2 ) ) ? $new_instance['order'] : 1;
		$instance['limit']               = wp_kses_post( $new_instance['limit'] );
		$instance['include']             = wp_kses_post( $new_instance['include'] );
		$instance['parents_only']        = sanitize_text_field( $new_instance['parents_only'] );
		$instance['disable_placeholder'] = sanitize_text_field( $new_instance['disable_placeholder'] );
		$instance['disable_text']        = sanitize_text_field( $new_instance['disable_text'] );
		$instance['disable_view_more']   = sanitize_text_field( $new_instance['disable_view_more'] );
		$instance['buttons']             = sanitize_text_field( $new_instance['buttons'] );
		$instance['button_text']         = esc_html( force_balance_tags( $new_instance['button_text'] ) );
		$instance['carousel']            = sanitize_text_field( $new_instance['carousel'] );
		$instance['featured']            = sanitize_text_field( $new_instance['featured'] );
		$instance['post_type']           = in_array( $new_instance['columns'], range( 1, 8 ) ) ? $new_instance['post_type'] : 1;
		$instance['class']               = esc_html( $new_instance['class'] );
		$instance['interval']            = esc_html( $new_instance['interval'] );
		return $instance;
	}

	/** @see WP_Widget::form -- do not rename this */
	public function form( $instance ) {

		$defaults = array(
			'title'               => 'Featured',
			'title_link'          => '',
			'columns'             => '1',
			'orderby'             => 'date',
			'order'               => 'DESC',
			'limit'               => '',
			'include'             => '',
			'parents_only'        => 0,
			'disable_placeholder' => 0,
			'disable_text'        => 0,
			'disable_view_more'   => 0,
			'buttons'             => 1,
			'button_text'         => 'See All',
			'featured'            => 0,
			'post_type'           => 'accommodation',
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
		$parents_only        = esc_attr( $instance['parents_only'] );
		$disable_placeholder = esc_attr( $instance['disable_placeholder'] );
		$disable_text        = esc_attr( $instance['disable_text'] );
		$disable_view_more   = esc_attr( $instance['disable_view_more'] );
		$buttons             = esc_attr( $instance['buttons'] );
		$button_text         = esc_attr( $instance['button_text'] );
		$featured            = esc_attr( $instance['featured'] );
		$post_type           = esc_attr( $instance['post_type'] );
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
						<option value="<?php echo wp_kses_post( $key ); ?>" id="<?php echo wp_kses_post( $option ); ?>" <?php echo wp_kses_post( $selected ); ?>><?php echo wp_kses_post( $option ); ?></option>
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
						'None'                 => 'none',
						'ID'                   => 'ID',
						'Name'                 => 'name',
						'Date'                 => 'date',
						'Modified Date'        => 'modified',
						'Random'               => 'rand',
						'Admin (custom order)' => 'menu_order',
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
						$selected = ( $order == $value ) ? ' selected="selected"' : '';
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

		<p>
			<input id="<?php echo wp_kses_post( $this->get_field_id( 'parents_only' ) ); ?>"
				name="<?php echo wp_kses_post( $this->get_field_name( 'parents_only' ) ); ?>" type="checkbox"
				value="1" <?php checked( '1', $parents_only ); ?> /> <label
				for="<?php echo wp_kses_post( $this->get_field_id( 'parents_only' ) ); ?>"><?php esc_html_e( 'Parents Only (post_parent =  0)', 'tour-operator' ); ?></label>
		</p>

		<p>
			<input id="<?php echo wp_kses_post( $this->get_field_id( 'featured' ) ); ?>"
				name="<?php echo wp_kses_post( $this->get_field_name( 'featured' ) ); ?>"
				type="checkbox" value="1" <?php checked( '1', $featured ); ?> /> <label
				for="<?php echo wp_kses_post( $this->get_field_id( 'featured' ) ); ?>"><?php esc_html_e( 'Featured Items', 'tour-operator' ); ?></label>
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
			<input id="<?php echo wp_kses_post( $this->get_field_id( 'disable_view_more' ) ); ?>"
				name="<?php echo wp_kses_post( $this->get_field_name( 'disable_view_more' ) ); ?>" type="checkbox"
				value="1" <?php checked( '1', $disable_view_more ); ?> /> <label
				for="<?php echo wp_kses_post( $this->get_field_id( 'disable_view_more' ) ); ?>"><?php esc_html_e( 'Disable View More Button', 'tour-operator' ); ?></label>
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
		<p>
			<label for="<?php echo wp_kses_post( $this->get_field_id( 'post_type' ) ); ?>"><?php esc_html_e( 'Post Type:', 'tour-operator' ); ?></label>
			<select name="<?php echo wp_kses_post( $this->get_field_name( 'post_type' ) ); ?>" id="<?php echo wp_kses_post( $this->get_field_id( 'post_type' ) ); ?>"	class="widefat layout">
				<?php
				$options = lsx_to_get_post_types();
				foreach ( $options as $value => $name ) {
					$selected = ( $post_type == $value ) ? ' selected="selected"' : '';
					?>
					<option value="<?php echo wp_kses_post( $value ); ?>" id="<?php echo wp_kses_post( $value ); ?>" <?php echo wp_kses_post( $selected ); ?>><?php echo wp_kses_post( $name ); ?></option>
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
			<small><?php esc_html_e( 'Add your own class to the opening element of the widget', 'tour-operator' ); ?></small>
		</p>
		<?php

	}

	public function output( $atts ) {
		global $columns, $disable_placeholder, $disable_text, $disable_view_more, $this_widget;

		$args = shortcode_atts(
			array(
				'tag'                 => 'h3',
				'columns'             => 1,
				'orderby'             => 'date',
				'link'                => false,
				'order'               => 'DESC',
				'limit'               => '-1',
				'include'             => '',
				'parents_only'        => false,
				'disable_placeholder' => false,
				'disable_text'        => false,
				'disable_view_more'   => false,
				'buttons'             => false,
				'button_text'         => false,
				'carousel'            => false,
				'layout'              => 'standard',
				'featured'            => false,
				'post_type'           => 'accommodation',
				'interval'            => '7000',
			),
			$atts
		);
		// @codingStandardsIgnoreStart
		extract( $args );
		// @codingStandardsIgnoreEnd

		$output = '';
		$paper  = 'paper';

		$this->parsed_atts = $args;
		$this_widget       = $this;

		if ( 'video' == $post_type ) {
			$post_type = 'destination';
			$paper     = '';
		}

		if ( '' != $include ) {
			$include = explode( ',', $include );
			$args    = array(
				'post_type'      => $post_type,
				'posts_per_page' => $limit,
				'post__in'       => $include,
				'orderby'        => 'post__in',
				'order'          => $order,
			);
		} else {
			$args = array(
				'post_type'      => $post_type,
				'posts_per_page' => $limit,
				'orderby'        => $orderby,
				'order'          => $order,
			);
		}

		if ( $featured ) {
			$args ['meta_key']   = 'featured';
			$args ['meta_value'] = 1;
		}

		if ( 'none' !== $orderby ) {
			$args['disabled_custom_post_order'] = true;
		}

		if ( true === $parents_only || '1' === $parents_only ) {
			$args ['post_parent'] = 0;
		}

		$args         = apply_filters( 'lsx_to_post_type_widget_query_args', $args, $this );
		$widget_query = new \WP_Query( $args );

		if ( $widget_query->have_posts() ) {
			if ( 'team' === $post_type ) {
				add_filter( 'lsx_to_placeholder_url', array( $this, 'placeholder' ), 10, 1 );
			}

			$count = 1;

			// output the opening boostrap row divs
			$this->before_while( $columns, $carousel, $post_type, $widget_query->post_count, $interval );

			while ( $widget_query->have_posts() ) {
				$widget_query->the_post();

				$this->loop_start( $columns, $carousel, $post_type, $widget_query->post_count, $count );

				if ( ! $carousel ) {
					echo wp_kses_post( '<div ' . lsx_to_widget_class( $post_type, true ) . '>' );
				}

				$this->content_part( 'content', 'widget-' . $post_type );

				if ( ! $carousel ) {
					echo wp_kses_post( '</div>' );
				}

				$this->loop_end( $columns, $carousel, $post_type, $widget_query->post_count, $count );

				$count++;
			}

			// output the closing boostrap row divs
			$this->after_while( $columns, $carousel, $post_type, $widget_query->post_count );

			if ( false !== $buttons && false !== $link ) {
				echo wp_kses_post( '<p class="lsx-to-widget-view-all"><span><a href="' . esc_url( $link ) . '" class="btn border-btn">' . $button_text . ' <i class="fa fa-angle-right"></i></a></span></p>' );
			}

			wp_reset_postdata();

			if ( 'team' === $post_type ) {
				remove_filter( 'lsx_to_placeholder_url', array( $this, 'placeholder' ), 10, 1 );
			}
		}
	}

	/**
	 * Replaces the widget with Mystery Man
	 */
	public function placeholder( $image ) {
		$image = array(
			LSX_TO_URL . 'assets/img/mystery-man-square.png',
			512,
			512,
			true,
		);

		return $image;
	}

	/**
	 * Runs just after the if and before the while statement in $this->output()
	 */
	public function before_while( $columns = 1, $carousel = 0, $post_type = '', $post_count = 0, $interval = 'false' ) {
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
	public function loop_start( $columns = 1, $carousel = 0, $post_type = '', $post_count = 0, $count = 0 ) {
		$output = '';
		// Get the call for the active slide.
		if ( $carousel ) {
			$output .= "<div class='lsx-to-widget-item-wrap lsx-{$post_type}'>";
		} elseif ( 1 === $count ) {
			$output .= "<div class='row'>";
		}
		echo wp_kses_post( $output );
	}

	/**
	 * Runs at the very end of the loop before it runs again.
	 */
	public function loop_end( $columns = 1, $carousel = 0, $post_type = '', $post_count = 0, $count = 0 ) {
		$output = '';

		// Close the current slide panel
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
	public function after_while( $columns = 1, $carousel = 0, $post_type = '', $post_count = 0 ) {
		$output = '';

		// Carousel output Closing
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
		$template = array();
		$name     = (string) $name;

		if ( '' !== $name ) {
			$template = "{$slug}-{$name}.php";
		} else {
			$template = "{$slug}.php";
		}

		$original_name = $template;
		$path          = apply_filters( 'lsx_to_widget_path', '', get_post_type(), $this );
		$template      = apply_filters( 'lsx_to_widget_template', $template, get_post_type(), $this );

		if ( '' == locate_template( array( $template ) ) && file_exists( $path . 'templates/' . $template ) ) {
			$template = $path . 'templates/' . $template;
		} elseif ( file_exists( get_stylesheet_directory() . '/' . $template ) ) {
			$template = get_stylesheet_directory() . '/' . $template;
		} else {
			$template = false;
		}

		if ( false !== $template ) {
			$this_widget = $this;
			load_template( $template, false );
		} else {
			echo wp_kses_post( '<p>No ' . $original_name . ' can be found.</p>' );
		}
	}

}
?>
