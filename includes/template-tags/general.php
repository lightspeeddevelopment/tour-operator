<?php
/**
 * Template Tags
 *
 * @package         tour-operator
 * @subpackage      template-tags
 * @category        general
 * @license         GPL3
 */

/* ==================   LAYOUT  ================== */

/**
 * Returns the CSS class for the archive panels
 *
 * @package     tour-operator
 * @subpackage  template-tags
 * @category    class
 */
function lsx_to_archive_class( $classes = array() ) {
	$layout = tour_operator()->archive_layout;

	if ( ! is_array( $classes ) ) {
		$classes = explode( ' ', $classes );
	}

	$new_classes = $classes;

	if ( 'grid' === $layout ) {
		$new_classes[] = 'col-xs-12 col-sm-6 col-md-4';
	} else {
		$new_classes[] = 'col-xs-12';
	}

	$new_classes = apply_filters( 'lsx_to_archive_class', $new_classes, $classes, $layout );

	return implode( ' ', $new_classes );
}

/**
 * Outputs the CSS class for the widget panels
 *
 * @package     tour-operator
 * @subpackage  template-tags
 * @category    class
 */
function lsx_to_widget_class( $type = '', $return = false ) {
	global $columns;

	$cols  = 'col-xs-12 col-sm-';
	$cols .= '5' == $columns ? '15' : 12 / $columns;

	$class = 'lsx-to-widget-item-wrap lsx-' . $type . ' ' . $cols;

	if ( false === $return ) {
		echo 'class="' . esc_attr( $class ) . '"';
	} else {
		return 'class="' . $class . '"';
	}
}

/**
 * Outputs the 'content' class.
 *
 * @param   $classes string or array
 */
function lsx_to_entry_class( $classes = false ) {
	global $post;

	if ( false !== $classes ) {
		if ( ! is_array( $classes ) ) {
			$classes = explode( ' ', $classes );
		}

		$classes = apply_filters( 'lsx_to_entry_class', $classes, $post->ID );
	}

	echo wp_kses_post( 'class="' . implode( ' ', $classes ) . '"' );
}

/**
 * Outputs the 'content' class.
 *
 * @param   $classes string or array
 */
function lsx_to_column_class( $classes = false ) {
	global $post;

	if ( false !== $classes ) {
		if ( ! is_array( $classes ) ) {
			$classes = explode( ' ', $classes );
		}
		$classes = apply_filters( 'lsx_to_column_class', $classes, $post->ID );
	}

	echo wp_kses_post( 'class="' . implode( ' ', $classes ) . '"' );
}

/**
 * Returns the collapsible class if it is active.
 *
 * @param bool $post_type
 * @param bool $return
 * @return string
 */
function lsx_to_collapsible_class( $post_type = false, $return = true ) {
	if ( false === $post_type ) {
		$post_type = get_post_type();
	}
	if ( ! lsx_to_is_collapsible( $post_type ) ) {

		$output = 'lsx-to-collapse-section';
		if ( false === $return ) {
			echo esc_attr( $output );
		} else {
			return $output;
		}
	}
}

/**
 * Returns the collapsible class if it is active.
 *
 * @param bool $target
 * @param bool $post_type
 * @param bool $return
 * @return  string
 */
function lsx_to_collapsible_attributes( $target = false, $post_type = false, $return = true ) {
	if ( false === $post_type ) {
		$post_type = get_post_type();
	}
	if ( ! lsx_to_is_collapsible( $post_type ) ) {
		$output = 'data-toggle="collapse" data-target="#' . $target . '"';
		if ( false === $return ) {
			echo esc_attr( $output );
		} else {
			return $output;
		}
	}
}

function lsx_to_collapsible_attributes_not_post( $target = false, $return = true ) {
	// $output = 'data-toggle="collapse" data-target="#' . $target . '"';
	$output  = 'data-toggle=';
	$output .= 'collapse';
	$output .= ' ';
	$output .= 'data-target=';
	$output .= '#' . $target;
	echo esc_attr( $output );
}

/* ==================   HEADER   ================== */

/**
 * Global header.
 *
 * @package     tour-operator
 * @subpackage  template-tag
 * @category    header
 */
function lsx_to_global_header() {
	$default_size = 'sm';
	$size         = apply_filters( 'lsx_bootstrap_column_size', $default_size );
	?>
	<div class="archive-header-wrapper col-<?php echo esc_attr( $size ); ?>-12">
		<header class="archive-header">
			<h1 class="archive-title">
				<?php
				if ( is_archive() ) {
					the_archive_title();
				} else {
					the_title();
				}
				?>
			</h1>

			<?php lsx_to_tagline( '<p class="tagline">', '</p>' ); ?>
		</header>

		<?php lsx_global_header_inner_bottom(); ?>
	</div>
	<?php
}

/**
 * Taglines
 *
 * @package     tour-operator
 * @subpackage  template-tag
 * @category    header
 */
function lsx_to_tagline( $before = '', $after = '', $echo = false ) {
	echo wp_kses_post( apply_filters( 'lsx_to_tagline', '', $before, $after ) );
}

/* ==================   ARCHIVE   ================== */

/**
 * Archive Descriptions
 *
 * @package     tour-operator
 * @subpackage  template-tag
 * @category    description
 */
function lsx_to_archive_description() {
	echo wp_kses_post( apply_filters( 'lsx_to_archive_description', '', '<div class="content-area lsx-to-archive-header row"><div class="col-xs-12 lsx-to-archive-description">', '</div></div>' ) );
}


/* ==================   SINGLE   ================== */

/**
 * Outputs the Single pages navigation
 *
 * @param $echo
 * @return string
 *
 * @package     tour-operator
 * @subpackage  template-tag
 * @category    navigation
 */
function lsx_to_page_navigation( $echo = true ) {
	$page_links = array();

	if ( is_single() ) {
		$page_links['summary'] = esc_html__( 'Summary', 'tour-operator' );
	}

	// Allow 3rd party plugins and themes to disable the page links.
	if ( apply_filters( 'lsx_to_page_navigation_disable', false, get_post_type() ) ) {
		return false;
	}

	$page_links = apply_filters( 'lsx_to_page_navigation', $page_links );

	if ( ! empty( $page_links ) && count( $page_links ) > 1 ) {
		$return  = '<div class="lsx-to-navigation col-xs-12 ' . get_post_type() . '-navigation visible-lg-block">';
		$return .= '<ul class="scroll-easing nav lsx-to-content-spy">';

		if ( ! empty( $page_links ) ) {
			foreach ( $page_links as $link_slug => $link_value ) {
				$return .= '<li><a href="#' . esc_html( $link_slug ) . '">' . $link_value . '</a></li>';
			}
		}

		$return .= '</ul>';
		$return .= '</div>';

		if ( $echo ) {
			echo wp_kses_post( $return );
		} else {
			return $return;
		}
	}
}

/**
 * outputs the sharing
 *
 * @package     tour-operator
 * @subpackage  setup
 * @category    helper
 */
function lsx_to_sharing() {
	echo '<section id="sharing">';

	if ( class_exists( 'LSX_Sharing' ) ) {
		global $lsx_sharing;
		echo wp_kses_post( $lsx_sharing->sharing_buttons() );
	} else {
		if ( function_exists( 'sharing_display' ) ) {
			sharing_display( '', true );
		}

		if ( class_exists( 'Jetpack_Likes' ) ) {
			$custom_likes = new Jetpack_Likes();
			echo wp_kses_post( $custom_likes->post_likes( '' ) );
		}
	}

	echo '</section>';
}

/**
 * Outputs the Envira Video Gallery
 *
 * @param       $before | string
 * @param       $after  | string
 * @param       $echo   | boolean
 * @return      string
 *
 * @package     tour-operator
 * @subpackage  template-tags
 * @category    tour
 */
function lsx_to_envira_videos( $before = '', $after = '', $echo = true ) {
	$envira_video = get_post_meta( get_the_ID(), 'envira_video', true );
	$return       = false;

	if ( function_exists( 'envira_gallery' ) && ! empty( $envira_video ) ) {
		// Envira Gallery
		ob_start();
		envira_gallery( $envira_video );
		$return = ob_get_clean();

		$return = $before . $return . $after;

		if ( $echo ) {
			echo wp_kses_post( $return );
		} else {
			return $return;
		}
	}
}

/* ==================  TAXONOMIES  ================== */

/**
 * Outputs the widget with some styling
 *
 * @package     tour-operator
 * @subpackage  template-tags
 * @category    helper
 */
function lsx_to_safari_brands( $before = '', $after = '', $echo = true ) {
	$args          = array(
		'name'          => 'Home',
		'id'            => 'sidebar-home',
		'description'   => '',
		'class'         => '',
		'before_widget' => '<aside id="lsx_to_taxonomy_widget-6" class="widget lsx-widget">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
		'widget_id'     => 'lsx_to_taxonomy_widget-6',
		'widget_name'   => 'LSX Taxonomies',
	);
	$instance      = array(
		'title'       => '',
		'title_link'  => '',
		'columns'     => '4',
		'orderby'     => 'menu_order',
		'order'       => 'ASC',
		'limit'       => '100',
		'include'     => '',
		'size'        => '100',
		'buttons'     => '',
		'button_text' => '',
		'responsive'  => '1',
		'carousel'    => '1',
		'taxonomy'    => 'accommodation-brand',
		'class'       => '',
		'interval'    => '7000',
		'indicators'  => '1',
	);
	$safari_brands = new \lsx\legacy\Taxonomy_Widget();
	ob_start();
	$safari_brands->widget( $args, $instance );
	$return = ob_get_clean();
	$return = $before . $return . $after;
	if ( $echo ) {
		echo wp_kses_post( $return );
	} else {
		return $return;
	}
}

/**
 * Outputs the travel styles widget with some styling
 *
 * @package     tour-operator
 * @subpackage  template-tags
 * @category    helper
 */
function lsx_to_travel_styles( $before = '', $after = '', $echo = true ) {
	$args          = array(
		'name'          => 'Home',
		'id'            => 'sidebar-home',
		'description'   => '',
		'class'         => '',
		'before_widget' => '<aside id="lsx_to_taxonomy_widget-6" class="widget lsx-widget">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
		'widget_id'     => 'lsx_to_taxonomy_widget-6',
		'widget_name'   => 'LSX Taxonomies',
	);
	$instance      = array(
		'title'       => '',
		'title_link'  => '',
		'columns'     => '3',
		'orderby'     => 'rand',
		'order'       => 'DESC',
		'limit'       => '100',
		'include'     => '',
		'size'        => '100',
		'buttons'     => '',
		'button_text' => '',
		'responsive'  => '1',
		'carousel'    => '1',
		'taxonomy'    => 'travel-style',
		'class'       => '',
		'interval'    => '7000',
		'indicators'  => '1',
	);
	$travel_styles = new \lsx\legacy\Taxonomy_Widget();
	ob_start();
	$travel_styles->widget( $args, $instance );
	$return = ob_get_clean();
	$return = $before . $return . $after;
	if ( $echo ) {
		echo wp_kses_post( $return );
	} else {
		return $return;
	}
}

/* ==================  ENQUIRE  ================== */
/**
 * Test if Enquire Contact exists
 *
 * @return      boolean
 * @package     tour-operator
 */
function lsx_to_has_enquiry_contact() {
	$tour_operator = tour_operator();

	$has_enquiry_contact = false;

	if ( function_exists( 'lsx_to_has_team_member' ) ) {
		$has_enquiry_contact = lsx_to_has_team_member();
	}

	if ( false === $has_enquiry_contact ) {
		if ( isset( $tour_operator->options['general'] ) && isset( $tour_operator->options['general']['enquiry_contact_name'] ) && '' !== $tour_operator->options['general']['enquiry_contact_name'] ) {
			$has_enquiry_contact = true;
		}
	}

	return $has_enquiry_contact;
}
/**
 * Display Enquire Contact
 *
 * @return      void
 * @package     tour-operator
 */
function lsx_to_enquiry_contact( $before = '', $after = '' ) {
	$tour_operator = tour_operator();

	$fields = array(
		'enquiry_contact_name'     => '',
		'enquiry_contact_email'    => '',
		'enquiry_contact_phone'    => '',
		'enquiry_contact_image_id' => '',
		'enquiry_contact_image'    => '',
	);

	foreach ( $fields as $key => $field ) {
		if ( isset( $tour_operator->options['general'] ) && isset( $tour_operator->options['general'][ $key ] ) ) {
			$fields[ $key ] = $tour_operator->options['general'][ $key ];
		}
	}

	if ( ! empty( $fields['enquiry_contact_image_id'] ) ) {
		$temp_src_array = wp_get_attachment_image_src( $fields['enquiry_contact_image_id'], 'medium' );

		if ( is_array( $temp_src_array ) && count( $temp_src_array ) > 0 ) {
			$fields['enquiry_contact_image'] = $temp_src_array[0];
		}
	}

	echo wp_kses_post( $before );
	?>
	<article <?php post_class(); ?>>
		<?php if ( ! empty( $fields['enquiry_contact_image'] ) ) : ?>
			<div class="lsx-to-contact-thumb">
				<?php echo wp_kses_post( '<img alt="' . esc_attr( $fields['enquiry_contact_name'] ) . '" class="attachment-responsive wp-post-image lsx-responsive" src="' . esc_url( $fields['enquiry_contact_image'] ) . '" />' ); ?>
			</div>
		<?php endif; ?>

		<div class="lsx-to-contact-info">
			<small class="lsx-to-contact-prefix text-left">Your travel expert:</small>
			<h4 class="lsx-to-contact-name text-left"><?php echo esc_html( $fields['enquiry_contact_name'] ); ?></h4>
		</div>

		<div class="lsx-to-contact-meta-data text-left hidden">
			<?php if ( ! empty( $fields['enquiry_contact_phone'] ) ) : ?>
				<div class="lsx-to-meta-data contact-number"><i class="fa fa-phone"></i> <a href="tel:+<?php echo esc_attr( $fields['enquiry_contact_phone'] ); ?>"><?php echo esc_html( $fields['enquiry_contact_phone'] ); ?></a></div>
			<?php endif; ?>
			<?php if ( ! empty( $fields['enquiry_contact_email'] ) ) : ?>
				<div class="lsx-to-meta-data email"><i class="fa fa-envelope"></i> <a href="mailto:<?php echo esc_attr( antispambot( $fields['enquiry_contact_email'] ) ); ?>"><?php echo esc_html( $fields['enquiry_contact_email'] ); ?></a></div>
			<?php endif; ?>
		</div>
	</article>
	<?php
	echo wp_kses_post( $after );
}

/* ==================  MODALS  ================== */
/**
 * Outputs the Enquire Modal
 *
 * @param       $before  | string
 * @param       $after   | string
 * @param       $echo    | boolean
 * @param       $form_id | string
 * @param       $disable_modal | boolean
 * @return      string
 *
 * @package     tour-operator
 * @subpackage  template-tags
 * @category    tour
 */
function lsx_to_enquire_modal( $cta_text = '', $before = '', $after = '', $echo = true, $form_id = false, $disable_modal = false ) {
	$tour_operator = tour_operator();

	if ( empty( $cta_text ) ) {
		$cta_text = esc_html__( 'Enquire', 'tour-operator' );
	}

	if ( false === $form_id ) {
		// First set the general form
		if ( isset( $tour_operator->options['general'] ) && isset( $tour_operator->options['general']['enquiry'] ) && '' !== $tour_operator->options['general']['enquiry'] ) {
			$form_id = $tour_operator->options['general']['enquiry'];
		}

		if ( is_singular( $tour_operator->active_post_types ) ) {
			if ( isset( $tour_operator->options[ get_post_type() ] ) && isset( $tour_operator->options[ get_post_type() ]['enquiry'] ) && '' !== $tour_operator->options[ get_post_type() ]['enquiry'] ) {
				$form_id = $tour_operator->options[ get_post_type() ]['enquiry'];
			}
		}
		if ( is_archive( $tour_operator->active_post_types ) ) {
			if ( isset( $tour_operator->options[ get_post_type() ] ) && isset( $tour_operator->options[ get_post_type() ]['enquiry'] ) && '' !== $tour_operator->options[ get_post_type() ]['enquiry'] ) {
				$form_id = $tour_operator->options[ get_post_type() ]['enquiry'];
			}
		}
	}

	$link = '#';

	if ( isset( $tour_operator->options['general'] ) && isset( $tour_operator->options['general']['disable_enquire_modal'] ) && 'on' === $tour_operator->options['general']['disable_enquire_modal'] ) {
		if ( isset( $tour_operator->options['general']['enquire_link'] ) && '' !== $tour_operator->options['general']['enquire_link'] ) {
			$link = $tour_operator->options['general']['enquire_link'];
		}
	}

	if ( is_singular( $tour_operator->active_post_types ) ) {
		if ( isset( $tour_operator->options[ get_post_type() ] ) && isset( $tour_operator->options[ get_post_type() ]['disable_enquire_modal'] ) && 'on' === $tour_operator->options[ get_post_type() ]['disable_enquire_modal'] ) {
			$disable_modal = true;
			if ( isset( $tour_operator->options[ get_post_type() ]['enquire_link'] ) && '' !== $tour_operator->options[ get_post_type() ]['enquire_link'] ) {
				$link = $tour_operator->options[ get_post_type() ]['enquire_link'];
			}
		}
	}

	if ( false !== $form_id ) {
		?>
	<div class="lsx-to-enquire-form">
		<a href="<?php echo esc_url( $link ); ?>" class="btn cta-btn" 
							<?php
							if ( false === $disable_modal ) {
								?>
data-toggle="modal" data-target="#lsx-enquire-modal"<?php } ?> ><?php echo esc_html( $cta_text ); ?></a>

		<?php
		if ( false === $disable_modal ) {
			add_action(
				'wp_footer',
				function( $arg ) use ( $form_id, $cta_text ) {
					?>
					<div class="lsx-modal modal fade" id="lsx-enquire-modal" tabindex="-1" role="dialog">
						<div class="modal-dialog">
							<div class="modal-content">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<div class="modal-header">
									<h4 class="modal-title"><?php echo esc_html( $cta_text ); ?></h4>
								</div>
								<div class="modal-body">
								<?php
								if ( class_exists( 'WPForms' ) ) {
									echo do_shortcode( '[wpforms id="' . $form_id . '"]' );
								} elseif ( class_exists( 'Ninja_Forms' ) ) {
									echo do_shortcode( '[ninja_form id="' . $form_id . '"]' );
								} elseif ( class_exists( 'GFForms' ) ) {
									echo do_shortcode( '[gravityform id="' . $form_id . '" title="false" description="false" ajax="true"]' );
								} elseif ( class_exists( 'Caldera_Forms_Forms' ) ) {
									echo do_shortcode( '[caldera_form id="' . $form_id . '"]' );
								} else {
									echo wp_kses_post( apply_filters( 'the_content', $form_id ) );
								}
								?>
								</div>
							</div>
						</div>
					</div>
						<?php
				}
			);
		}
		?>
	</div>
		<?php
	} }

if ( ! function_exists( 'lsx_to_gallery' ) ) {
	/**
	 * Outputs the TO Gallery
	 *
	 * @param       $before | string
	 * @param       $after  | string
	 * @param       $echo   | boolean
	 * @param       $args   | array
	 * @return      string
	 *
	 * @package     tour-operator
	 * @subpackage  template-tags
	 * @category    galleries
	 */
	function lsx_to_gallery( $before = '', $after = '', $echo = true, $args = array() ) {
		$defaults = array(
			'gallery_ids' => array(),
		);
		$args     = wp_parse_args( $args, $defaults );
		if ( ! empty( $args['gallery_ids'] ) ) {
			if ( ! is_array( $args['gallery_ids'] ) ) {
				$args['gallery_ids'] = explode( ',', '', $args['gallery_ids'] );
			}
			$gallery_ids = $args['gallery_ids'];
		} else {
			$gallery_ids = get_post_meta( get_the_ID(), 'gallery', false );
		}
		$envira_gallery = get_post_meta( get_the_ID(), 'envira_gallery', true );

		if ( ( ! empty( $gallery_ids ) && is_array( $gallery_ids ) ) || ( function_exists( 'envira_gallery' ) && ! empty( $envira_gallery ) && false === lsx_to_enable_envira_banner() ) ) {
			if ( function_exists( 'envira_gallery' ) && ! empty( $envira_gallery ) && false === lsx_to_enable_envira_banner() ) {
				// Envira Gallery
				ob_start();
				envira_gallery( $envira_gallery );
				$return = ob_get_clean();
			} else {
				if ( function_exists( 'envira_dynamic' ) ) {
					// Envira Gallery - Dynamic
					ob_start();

					envira_dynamic(
						array(
							'id'     => 'custom' . sanitize_title( get_the_title( get_the_ID() ) ) . '-' . date( 'H-i' ),
							'images' => implode( ',', $gallery_ids ),
						)
					);

					$return = ob_get_clean();
				} else {
					// WordPress Gallery
					$columns = 3;
					$return  = do_shortcode( '[gallery ids="' . implode( ',', $gallery_ids ) . '" size="large" columns="' . $columns . '" link="file"]' );
				}
			}

			$return = $before . $return . $after;

			if ( $echo ) {
				echo wp_kses_post( $return );
			} else {
				return $return;
			}
		}
	}
}

/* ==================  SITEMAP  ================== */

if ( ! function_exists( 'to_sitemap_loops' ) ) {
	/**
	 * Add Hierarchy for Destinations Sitemap.
	 *
	 * @param [type] $sitemap_loops
	 * @return void
	 */
	function to_sitemap_loops( $sitemap_loops ) {
		$sitemap_loops['destination'] = array(
			'type'      => 'post_type',
			'label'     => __( 'Destinations', 'lsx' ),
			'heirarchy' => true,
		);

		return $sitemap_loops;
	}
	add_filter( 'lsx_sitemap_loops_list', 'to_sitemap_loops', 1 );
}
