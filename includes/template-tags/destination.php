<?php
/**
 * Template Tags
 *
 * @package         tour-operator
 * @subpackage      template-tags
 * @category        destination
 * @license         GPL3
 */

/**
 * Outputs the posts attached destinations
 *
 * @package     tour-operator
 * @subpackage  template-tags
 * @category    destination
 */
function lsx_to_destination_posts() {
	global $lsx_to_archive;

	$args = array(
		'from'   => 'post',
		'to'     => 'destination',
		'column' => '3',
		'before' => '<section id="posts" class="lsx-to-section ' . lsx_to_collapsible_class() . '"><h2 class="lsx-to-section-title lsx-to-collapse-title lsx-title" ' . lsx_to_collapsible_attributes( 'collapse-posts' ) . '>' . esc_html__( 'Featured Posts', 'tour-operator' ) . '</h2><div id="collapse-posts" class="collapse in"><div class="collapse-inner">',
		'after'  => '</div></div></section>',
	);

	lsx_to_connected_panel_query( $args );
}

/**
 * Outputs the connected accommodation only on a "region"
 *
 * @package     tour-operator
 * @subpackage  template-tags
 * @category    destination
 */
function lsx_to_region_accommodation() {
	global $lsx_to_archive;

	if ( post_type_exists( 'accommodation' ) && is_singular( 'destination' ) && ! lsx_to_item_has_children( get_the_ID(), 'destination' ) ) {
		$args = array(
			'from'   => 'accommodation',
			'to'     => 'destination',
			'column' => '3',
			'before' => '<section id="accommodation" class="lsx-to-section ' . lsx_to_collapsible_class() . '"><h2 class="lsx-to-section-title lsx-to-collapse-title lsx-title" ' . lsx_to_collapsible_attributes( 'collapse-accommodation' ) . '>' . lsx_to_get_post_type_section_title( 'accommodation', '', esc_html__( 'Featured Accommodation', 'tour-operator' ) ) . '</h2><div id="collapse-accommodation" class="collapse in"><div class="collapse-inner">',
			'after'  => '</div></div></section>',
		);

		lsx_to_connected_panel_query( $args );
	}
}

/**
 * Outputs the child destinations
 *
 * @param $args array
 *
 * @package     tour-operator
 * @subpackage  template-tags
 * @category    destination
 */
function lsx_to_country_regions( $args = array() ) {
	global $lsx_to_archive, $wp_query;
	$tour_operator = tour_operator();
	$defaults      = array(
		'slider'  => true,
		'parent'  => get_the_ID(),
		'title'   => lsx_to_get_post_type_section_title( 'destination', 'regions', 'Regions' ),
		'tagline' => false,
		'exclude' => false,
		'orderby' => 'name',
	);

	$settings = wp_parse_args( $args, $defaults );

	if ( is_singular( 'destination' ) && ( lsx_to_item_has_children( get_the_ID(), 'destination' ) || isset( $args['parent'] ) ) ) {

		$region_args = array(
			'post_type'      => 'destination',
			'post_status'    => 'publish',
			'nopagin'        => true,
			'posts_per_page' => '-1',
			'post_parent'    => $settings['parent'],
			'orderby'        => $settings['orderby'],
			'order'          => 'ASC',
		);

		if ( false === $settings['exclude'] && is_array( $tour_operator->options ) && isset( $tour_operator->options['destination'] ) && isset( $tour_operator->options['destination']['sticky_countries'] ) ) {
			$sticky_posts = $tour_operator->options['destination']['sticky_countries'];
			switch ( $sticky_posts ) {
				case 'sticky-only':
					$meta_query                = array(
						array(
							'key'     => 'sticky_order',
							'value'   => '0',
							'compare' => '!=',
						),
					);
					$region_args['meta_query'] = $meta_query;
					$region_args['meta_key']   = 'sticky_order';
					$region_args['orderby']    = 'meta_value_num';
					$region_args['order']      = 'DESC';
					break;

				case 'sticky-first':
					$region_args['meta_key'] = 'sticky_order';
					$region_args['orderby']  = array(
						'meta_value_num' => 'DESC',
						'menu_order'     => 'ASC',
					);
					break;

				case 'sticky-last':
					$region_args['meta_key'] = 'sticky_order';
					$region_args['orderby']  = array(
						'menu_order'     => 'ASC',
						'meta_value_num' => 'DESC',
					);
					break;

				default:
					break;
			}
		}
		if ( false !== $settings['exclude'] ) {
			$region_args['post__not_in'] = array( $settings['exclude'] );
		}
		$regions        = new WP_Query( $region_args );
		$region_counter = 0;
		$total_counter  = 0;

		if ( $regions->have_posts() ) : ?>
			<section id="regions" class="lsx-to-section <?php lsx_to_collapsible_class( 'destination', false ); ?>">
				<h2 class="lsx-to-section-title lsx-to-collapse-title lsx-title" <?php lsx_to_collapsible_attributes_not_post( 'collapse-regions' ); ?>><?php echo esc_html( $settings['title'] ); ?>
																															   <?php
																																if ( false !== $settings['tagline'] ) {
																																	echo '<small>' . wp_kses_post( $settings['tagline'] ) . '</small>'; }
																																?>
</h2>

				<div id="collapse-regions" class="collapse in">
					<div class="collapse-inner">
						<div class="slider-container lsx-to-widget-items lsx-to-archive-template-grid">
							<div id="slider-<?php echo esc_attr( rand( 20, 20000 ) ); ?>" class="lsx-to-slider">
								<div class="lsx-to-slider-wrap">
									<div class="lsx-to-slider-inner 
									<?php
									if ( false === $settings['slider'] ) {
										echo esc_attr( 'slider-disabled' ); }
									?>
" data-interval="6000" data-slick='{ "slidesToShow": 3, "slidesToScroll": 3 }'>

									<?php
										$lsx_to_archive                 = 1;
										$wp_query->is_single            = 0;
										$wp_query->is_singular          = 0;
										$wp_query->is_post_type_archive = 1;

									while ( $regions->have_posts() ) {
										$regions->the_post();

										global $disable_placeholder, $disable_text;

										$disable_placeholder = apply_filters( 'lsx_to_widget_disable_placeholder', false, 'destination', 'destination' );
										$disable_text        = apply_filters( 'lsx_to_widget_disable_text', false, 'destination', 'destination' );

										echo '<div class="lsx-to-widget-item-wrap lsx-regions">';
										lsx_to_content( 'content-widget', 'destination' );
										echo '</div>';
									}

										$lsx_to_archive                 = 0;
										$wp_query->is_single            = 1;
										$wp_query->is_singular          = 1;
										$wp_query->is_post_type_archive = 0;
									?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
			<?php
			wp_reset_postdata();
		endif;
	}
}

/**
 * Outputs the destinations attached tours
 *
 * @package     tour-operator
 * @subpackage  template-tags
 * @category    destination
 */
function lsx_to_destination_tours() {
	global $lsx_to_archive, $wp_query;

	if ( post_type_exists( 'tour' ) && is_singular( 'destination' ) ) {
		$args = array(
			'from'   => 'tour',
			'to'     => 'destination',
			'column' => '3',
			'before' => '<section id="tours" class="lsx-to-section ' . lsx_to_collapsible_class() . '"><h2 class="lsx-to-section-title lsx-to-collapse-title lsx-title" ' . lsx_to_collapsible_attributes( 'collapse-tours' ) . '>' . lsx_to_get_post_type_section_title( 'tour', '', esc_html__( 'Featured Tours', 'tour-operator' ) ) . '</h2><div id="collapse-tours" class="collapse in"><div class="collapse-inner">',
			'after'  => '</div></div></section>',
		);

		lsx_to_connected_panel_query( $args );
	}
}

/**
 * Outputs the destinations attached activites
 *
 * @package     tour-operator
 * @subpackage  template-tags
 * @category    destination
 */
function lsx_to_destination_activities() {
	global $lsx_to_archive;

	if ( post_type_exists( 'activity' ) && is_singular( 'destination' ) && ! lsx_to_item_has_children( get_the_ID(), 'destination' ) ) {
		$args = array(
			'from'   => 'activity',
			'to'     => 'destination',
			// 'content_part'	=>	'widget-activity',
			'column' => '3',
			'before' => '<section id="activities" class="lsx-to-section ' . lsx_to_collapsible_class() . '"><h2 class="lsx-to-section-title lsx-to-collapse-title lsx-title" ' . lsx_to_collapsible_attributes( 'collapse-activities' ) . '>' . lsx_to_get_post_type_section_title( 'activity', '', esc_html__( 'Featured Activities', 'tour-operator' ) ) . '</h2><div id="collapse-activities" class="collapse in"><div class="collapse-inner">',
			'after'  => '</div></div></section>',
		);

		lsx_to_connected_panel_query( $args );
	}
}

/**
 * Outputs the destination travel info
 *
 * @package     tour-operator
 * @subpackage  template-tags
 * @category    destination
 */
function lsx_to_destination_travel_info() {
	$electricity = get_post_meta( get_the_ID(), 'electricity', true );
	$banking     = get_post_meta( get_the_ID(), 'banking', true );
	$cuisine     = get_post_meta( get_the_ID(), 'cuisine', true );
	$climate     = get_post_meta( get_the_ID(), 'climate', true );
	$transport   = get_post_meta( get_the_ID(), 'transport', true );
	$dress       = get_post_meta( get_the_ID(), 'dress', true );
	$health      = get_post_meta( get_the_ID(), 'health', true );
	$safety      = get_post_meta( get_the_ID(), 'safety', true );
	$visa        = get_post_meta( get_the_ID(), 'visa', true );
	$general     = get_post_meta( get_the_ID(), 'additional_info', true );

	if ( ! empty( $electricity ) || ! empty( $banking ) || ! empty( $cuisine ) || ! empty( $climate ) || ! empty( $transport ) || ! empty( $dress ) || ! empty( $health ) || ! empty( $safety ) || ! empty( $visa ) || ! empty( $general ) ) :
		$limit_words = 20;
		$limit_chars = 150;
		$more_button = "\n\n" . '<a class="moretag moretag-travel-info" href="#">Read More</a>' . "\n\n";

		$items = array(
			esc_html__( 'Electricity', 'tour-operator' ) => $electricity,
			esc_html__( 'Banking', 'tour-operator' )     => $banking,
			esc_html__( 'Cuisine', 'tour-operator' )     => $cuisine,
			esc_html__( 'Climate', 'tour-operator' )     => $climate,
			esc_html__( 'Transport', 'tour-operator' )   => $transport,
			esc_html__( 'Dress', 'tour-operator' )       => $dress,
			esc_html__( 'Health', 'tour-operator' )      => $health,
			esc_html__( 'Safety', 'tour-operator' )      => $safety,
			esc_html__( 'Visa', 'tour-operator' )        => $visa,
			esc_html__( 'General', 'tour-operator' )     => $general,
		);
		?>
		<section id="travel-info" class="lsx-to-section <?php lsx_to_collapsible_class( 'destination', false ); ?>">
			<h2 class="lsx-to-section-title lsx-to-collapse-title lsx-title" <?php lsx_to_collapsible_attributes_not_post( 'collapse-travel-info' ); ?>><?php esc_html_e( 'Travel Information', 'tour-operator' ); ?></h2>

			<div id="collapse-travel-info" class="collapse in">
				<div class="collapse-inner">
					<div class="slider-container lsx-to-widget-items lsx-to-archive-template-grid">
						<div id="slider-<?php echo esc_attr( rand( 20, 20000 ) ); ?>" class="lsx-to-slider">
							<div class="lsx-to-slider-wrap">
								<div class="lsx-to-slider-inner" data-slick='{ "slidesToShow": 4, "slidesToScroll": 4 }'>
									<?php foreach ( $items as $key => $value ) : ?>
										<?php if ( ! empty( $value ) ) : ?>
											<div class="lsx-to-widget-item-wrap lsx-travel-info">
												<article <?php post_class(); ?>>
													<div class="travel-info-entry-content hidden">
														<?php echo wp_kses_post( apply_filters( 'the_content', $value ) ); ?>
													</div>

													<div class="lsx-to-widget-content">
														<h4 class="lsx-to-widget-title text-center"><?php echo esc_html( $key ); ?></h4>

														<?php
															$value = str_replace( '</p><p>', ' - ', $value );
															$value = str_replace( array( '</p>', '<p>' ), '', $value );
															$value = str_replace( '<br>', ' - ', $value );

															// if ( str_word_count( $value, 0 ) > $limit_chars ) {
															// $tokens       = array();
															// $value_output = '';
															// $has_more     = false;
															// $count        = 0;

															// preg_match_all( '/(<[^>]+>|[^<>\s]+)\s*/u', $value, $tokens );

															// foreach ( $tokens[0] as $token ) {
															// if ( $count >= $limit_words ) {
															// $value_output .= trim( $token );
															// $has_more = true;
															// break;
															// }

															// $count++;
															// $value_output .= $token;
															// }

															// $value = trim( force_balance_tags( $value_output . '...' ) );
															// }

														if ( strlen( $value ) > $limit_chars ) {
															$position     = strpos( $value, ' ', $limit_chars );
															$value_output = substr( $value, 0, $position );

															$value = trim( force_balance_tags( $value_output . '...' ) );
														}

															$value = trim( force_balance_tags( $value . $more_button ) );

															echo wp_kses_post( apply_filters( 'the_content', $value ) );
														?>
													</div>
												</article>
											</div>
										<?php endif; ?>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<?php
	endif;
}

/**
 * Gets the current specials connected destinations
 *
 * @param       $before | string
 * @param       $after  | string
 * @param       $echo   | boolean
 * @param       $parents| boolean
 * @return      string
 *
 * @package     tour-operator
 * @subpackage  template-tags
 * @category    connections
 */
function lsx_to_connected_destinations( $before = '', $after = '', $echo = true ) {
	$return = lsx_to_connected_items_query( 'destination', get_post_type(), $before, $after, $echo );
	if ( true !== $echo ) {
		return $return;
	}
}

/**
 * Gets the current connected countries
 *
 * @param       $before | string
 * @param       $after  | string
 * @param       $echo   | boolean
 * @param       $parents| boolean
 * @return      string
 *
 * @package     tour-operator
 * @subpackage  template-tags
 * @category    connections
 */
function lsx_to_connected_countries( $before = '', $after = '', $echo = true ) {
	lsx_to_connected_items_query( 'destination', get_post_type(), $before, $after, $echo, true );
}

/**
 * Gets the current connected destination children or parent list for fast facts section
 *
 * @package     tour-operator
 * @subpackage  template-tags
 * @category    connections
 */

function destination_children( $parent_id ) {
	$theid      = get_the_ID();
	$child      = new WP_Query(
		array(
			'post_parent' => $theid,
			'post_type'   => 'destination',
		)
	);
	$meta_class = 'lsx-to-meta-data lsx-to-meta-data-';
	if ( $child->have_posts() ) {
		$list_destinations       = array();
		$final_list_destinations = '';
		echo '<span class="' . esc_attr( $meta_class ) . 'regions"><span class="lsx-to-meta-data-key">' . esc_html__( 'Regions', 'tour-operator' ) . ':</span>';
		while ( $child->have_posts() ) {
			$child->the_post();
			$childtitle          = get_the_title();
			$childlink           = get_the_permalink();
			$list_destinations[] = '<a href="' . esc_attr( $childlink ) . '"> ' . esc_attr( $childtitle ) . '</a>';
		}
		$final_list_destinations = implode( ', ', $list_destinations );
		echo wp_kses_post( $final_list_destinations );
	} else {
		echo '<span class="' . esc_attr( $meta_class ) . 'regions"><span class="lsx-to-meta-data-key">' . esc_html__( 'Country', 'tour-operator' ) . ':</span>';
		$parent_title = get_the_title( wp_get_post_parent_id( $theid ) );
		$parent_link  = get_the_permalink( wp_get_post_parent_id( $theid ) );
		echo '<a href="' . esc_attr( $parent_link ) . '"> ' . esc_attr( $parent_title ) . '</a>';
	}
	echo '</span>';
	wp_reset_postdata();
}

/**
 * Gets the current best months to visit
 *
 * @package     tour-operator
 * @subpackage  template-tags
 * @category    connections
 */

function months_to_visit() {
	$meta_class = 'lsx-to-meta-data lsx-to-meta-data-';
	$months     = get_post_meta( get_the_ID(), 'best_time_to_visit', true );
	$month_list = array();

	foreach ( (array) $months as $single_month ) {
		$single_month = str_split( $single_month, 3 );
		$month_list[] = '<span>' . $single_month[0] .
		'</span>';
	}

	if ( ! empty( $months ) ) {
		echo '<span class="' . esc_attr( $meta_class ) . 'best-time" style="text-transform:capitalize;"><span class="lsx-to-meta-data-key">' . esc_html__( 'Best Time', 'tour-operator' ) . ': </span>';
		echo implode( ', ', wp_kses_post( $month_list ) );
		echo '</span>';
	}

}
