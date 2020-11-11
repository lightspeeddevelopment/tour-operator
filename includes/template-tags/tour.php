<?php
/**
 * Template Tags
 *
 * @package         tour-operator
 * @subpackage      template-tags
 * @category        tour
 * @license         GPL3
 */

/**
 * Outputs the posts attached tours
 *
 * @package     tour-operator
 * @subpackage  template-tags
 * @category    tour
 */
function lsx_to_tour_posts() {
	global $lsx_to_archive;

	$args = array(
		'from'   => 'post',
		'to'     => 'tour',
		'column' => '3',
		'before' => '<section id="posts" class="lsx-to-section ' . lsx_to_collapsible_class() . '"><h2 class="lsx-to-section-title lsx-to-collapse-title lsx-title" ' . lsx_to_collapsible_attributes( 'collapse-posts' ) . '>' . esc_html__( 'Featured Posts', 'tour-operator' ) . '</h2><div id="collapse-posts" class="collapse in"><div class="collapse-inner">',
		'after'  => '</div></div></section>',
	);

	lsx_to_connected_panel_query( $args );
}

/**
 * Gets the current tours price
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
function lsx_to_price( $before = '', $after = '', $echo = true ) {
	lsx_to_custom_field_query( 'price', $before, $after, $echo );
}

/**
 * Gets the current tours duration
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
function lsx_to_duration( $before = '', $after = '', $echo = true ) {
	lsx_to_custom_field_query( 'duration', $before, $after, $echo );
}

/**
 * Gets the current tours included pricing field
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
function lsx_to_included( $before = '', $after = '', $echo = true ) {
	return lsx_to_custom_field_query( 'included', $before, $after, $echo );
}

/**
 * Gets the current tours not included pricing field
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
function lsx_to_not_included( $before = '', $after = '', $echo = true ) {
	return lsx_to_custom_field_query( 'not_included', $before, $after, $echo );
}

/**
 * Gets the current tours departure points
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
function lsx_to_departure_point( $before = '', $after = '', $echo = true ) {
	$departs_from = get_post_meta( get_the_ID(), 'departs_from', false );

	if ( ! empty( $departs_from ) && is_array( $departs_from ) && count( $departs_from ) > 0 ) {
		$return = $before . lsx_to_connected_list( $departs_from, 'destination', true, ', ' ) . $after;

		if ( $echo ) {
			echo wp_kses_post( $return );
		} else {
			return $return;
		}
	}
}

/**
 * Gets the current tours end points
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
function lsx_to_end_point( $before = '', $after = '', $echo = true ) {
	$end_point = get_post_meta( get_the_ID(), 'ends_in', false );

	if ( ! empty( $end_point ) && is_array( $end_point ) && count( $end_point ) > 0 ) {
		$return = $before . lsx_to_connected_list( $end_point, 'destination', true, ', ' ) . $after;

		if ( $echo ) {
			echo wp_kses_post( $return );
		} else {
			return $return;
		}
	}
}

/**
 * Outputs the tours included / not included block
 *
 * @package     tour-operator
 * @subpackage  template-tags
 * @category    tour
 */
function lsx_to_included_block() {
	$tour_included     = lsx_to_included( '', '', false );
	$tour_not_included = lsx_to_not_included( '', '', false );

	if ( null !== $tour_included || null !== $tour_not_included ) {
		$class = 'col-xs-12 col-sm-6';

		if ( ( null === $tour_included && null !== $tour_not_included ) || ( null !== $tour_included && null === $tour_not_included ) ) {
			$class = 'col-xs-12';
		}
		?>
		<section id="included-excluded" class="lsx-to-section <?php lsx_to_collapsible_class( 'tour', false ); ?>">
			<h2 class="lsx-to-section-title lsx-to-collapse-title lsx-title hidden-lg" <?php lsx_to_collapsible_attributes_not_post( 'collapse-included-excluded' ); ?>><?php esc_html_e( 'Included / Not Included', 'tour-operator' ); ?></h2>

			<div id="collapse-included-excluded" class="collapse in">
				<div class="collapse-inner">
					<div class="row">
						<?php if ( null !== $tour_included ) { ?>
							<div class="<?php echo esc_attr( $class ); ?> included">
								<h2 class="lsx-to-section-title lsx-to-section-title-small"><?php esc_html_e( 'Included', 'tour-operator' ); ?></h2>
								<div class="entry-content">
									<?php echo wp_kses_post( apply_filters( 'the_content', wpautop( $tour_included ) ) ); ?>
								</div>
							</div>
							<?php
						}
						if ( null !== $tour_not_included ) {
							?>
							<div class="<?php echo esc_attr( $class ); ?> not-included">
								<h2 class="lsx-to-section-title lsx-to-section-title-small"><?php esc_html_e( 'Excluded', 'tour-operator' ); ?></h2>
								<div class="entry-content">
									<?php echo wp_kses_post( apply_filters( 'the_content', wpautop( $tour_not_included ) ) ); ?>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</section>
		<?php
	}
}

/**
 * Outputs the Tour Highlights
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
function lsx_to_highlights( $before = '', $after = '', $echo = true ) {
	$highlights = get_post_meta( get_the_ID(), 'hightlights', true );

	if ( false !== $highlights && '' !== $highlights ) {
		$return = $before . '<div class="entry-content">' . apply_filters( 'the_content', wpautop( $highlights ) ) . '</div>' . $after;

		if ( $echo ) {
			echo wp_kses_post( $return );
		} else {
			return $return;
		}
	}
}

/**
 * Outputs the Best Time to Visit HTML
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
function lsx_to_best_time_to_visit( $before = '', $after = '', $echo = true ) {
	$best_time_to_visit = get_post_meta( get_the_ID(), 'best_time_to_visit', true );

	if ( false !== $best_time_to_visit && '' !== $best_time_to_visit && is_array( $best_time_to_visit ) && ! empty( $best_time_to_visit ) ) {
		$this_year = array(
			'january'   => esc_html__( 'January', 'tour-operator' ),
			'february'  => esc_html__( 'February', 'tour-operator' ),
			'march'     => esc_html__( 'March', 'tour-operator' ),
			'april'     => esc_html__( 'April', 'tour-operator' ),
			'may'       => esc_html__( 'May', 'tour-operator' ),
			'june'      => esc_html__( 'June', 'tour-operator' ),
			'july'      => esc_html__( 'July', 'tour-operator' ),
			'august'    => esc_html__( 'August', 'tour-operator' ),
			'september' => esc_html__( 'September', 'tour-operator' ),
			'october'   => esc_html__( 'October', 'tour-operator' ),
			'november'  => esc_html__( 'November', 'tour-operator' ),
			'december'  => esc_html__( 'December', 'tour-operator ' ),
		);

		foreach ( $this_year as $month => $label ) {
			$checked       = '';
			$checked_class = '';

			if ( in_array( $month, $best_time_to_visit ) ) {
				$checked       = '<i class="fa fa-check-circle" aria-hidden="true"></i>';
				$checked_class = 'lsx-to-month-check';
			}

			$shortname    = str_split( $label, 3 );
			$best_times[] = '<div class="col-xs-2 col-sm-1 lsx-to-month ' . $checked_class . '"><small>' . $shortname[0] . '</small>' . $checked . '</div>';
		};

		$return = $before . implode( $best_times ) . $after;

		if ( $echo ) {
			echo wp_kses_post( $return );
		} else {
			return $return;
		}
	}
}

/**
 * Gets the current specials connected tours
 *
 * @param       $before | string
 * @param       $after  | string
 * @param       $echo   | boolean
 * @return      string
 *
 * @package     tour-operator
 * @subpackage  template-tags
 * @category    connections
 */
function lsx_to_connected_tours( $before = '', $after = '', $echo = true ) {
	lsx_to_connected_items_query( 'tour', get_post_type(), $before, $after, $echo );
}

/**
 * Return the Room basis Label.
 *
 * @param string $index
 * @return string
 */
function lsx_to_room_basis_label( $index = '' ) {
	$label = $index;
	if ( isset( tour_operator()->legacy->tour->room_basis[ $index ] ) ) {
		$label = tour_operator()->legacy->tour->room_basis[ $index ];
	}
	return $label;
}

/**
 * Outputs The current Itinerary connected destinations, can only be used in
 * the itinerary loop.
 *
 * @package       tour-operator
 * @subpackage    template-tags
 * @category      itinerary
 */
function lsx_to_itinerary_room_basis( $before = '', $after = '' ) {
	global $tour_itinerary;
	if ( $tour_itinerary && $tour_itinerary->has_itinerary && ! empty( $tour_itinerary->itinerary ) ) {
		if ( ! empty( $tour_itinerary->itinerary['room_basis'] ) && 'None' !== $tour_itinerary->itinerary['room_basis'] ) {
			$label = lsx_to_room_basis_label( $tour_itinerary->itinerary['room_basis'] );
			echo wp_kses_post( $before . $label . $after );
		}
	}
}

/**
 * Return the Drinks basis Label.
 *
 * @param string $index
 * @return string
 */
function lsx_to_drinks_basis_label( $index = '' ) {
	$label = $index;
	if ( isset( tour_operator()->legacy->tour->drinks_basis[ $index ] ) ) {
		$label = tour_operator()->legacy->tour->drinks_basis[ $index ];
	}
	return $label;
}

/**
 * Outputs The current Itinerary connected destinations, can only be used in
 * the itinerary loop.
 *
 * @package       tour-operator
 * @subpackage    template-tags
 * @category      itinerary
 */
function lsx_to_itinerary_drinks_basis( $before = '', $after = '' ) {
	global $tour_itinerary;
	if ( $tour_itinerary && $tour_itinerary->has_itinerary && ! empty( $tour_itinerary->itinerary ) ) {
		if ( ! empty( $tour_itinerary->itinerary['drinks_basis'] ) && 'None' !== $tour_itinerary->itinerary['drinks_basis'] ) {
			$label = lsx_to_drinks_basis_label( $tour_itinerary->itinerary['drinks_basis'] );
			echo wp_kses_post( $before . $label . $after );
		}
	}
}

/**
 * Retrieves the accommodation ids from the itinerary, mostly for use in the map.
 * the itinerary loop.
 *
 * @package       tour-operator
 * @subpackage    template-tags
 * @category      itinerary
 *
 * @return array
 */
function lsx_to_get_tour_itinerary_ids() {
	$tour_itinerary = new \lsx\legacy\Itinerary_Query();
	$itinerary_ids  = array();
	if ( $tour_itinerary->has_itinerary() ) {
		$itinerary_count = 1;
		while ( $tour_itinerary->while_itinerary() ) {
			$tour_itinerary->current_itinerary_item();
			if ( ! empty( $tour_itinerary->itinerary['accommodation_to_tour'] ) && is_array( $tour_itinerary->itinerary['accommodation_to_tour'] ) ) {
				$itinerary_ids = array_merge( $itinerary_ids, array_values( $tour_itinerary->itinerary['accommodation_to_tour'] ) );
			}
		}
	}
	return $itinerary_ids;
}
