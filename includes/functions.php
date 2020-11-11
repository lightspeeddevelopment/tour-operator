<?php
/**
 * LSX Tour Operator Helper Functions
 *
 * @package   tour_operator
 * @author    LightSpeed
 * @license   GPL-2.0+
 * @copyright 2017 LightSpeed
 */

/**
 * Returns an array of the tour taxonomies.
 *
 * @since unknown
 * @return array List of tour operator taxonomies.
 */
function lsx_to_get_taxonomies() {
	return tour_operator()->get_taxonomies();
}

/**
 * Returns an array of the tour post types.
 *
 * @since unknown
 * @return array List of tour operator post types.
 */
function lsx_to_get_post_types() {
	return tour_operator()->get_post_types();
}

/**
 * Checks if the current tour has an itinerary
 *
 * @package       tour-operator
 * @subpackage    template-tags
 * @category      itinerary
 */
function lsx_to_has_itinerary() {
	global $tour_itinerary;

	$has_itinerary = false;

	if ( null === $tour_itinerary ) {
		$tour_itinerary = new \lsx\legacy\Itinerary_Query();
	}

	if ( is_object( $tour_itinerary ) ) {
		$has_itinerary = $tour_itinerary->has_itinerary();
	}

	return $has_itinerary;
}

/**
 * Runs the current itinerary loop, used in a "while" statement
 * e.g  while(lsx_to_itinerary_loop()) {lsx_to_itinerary_loop_item();}
 *
 * @package       tour-operator
 * @subpackage    template-tags
 * @category      itinerary
 */
function lsx_to_itinerary_loop() {
	global $tour_itinerary;

	if ( is_object( $tour_itinerary ) ) {
		return $tour_itinerary->while_itinerary();
	} else {
		return false;
	}
}

/**
 * Sets up the current itinerary itinerary
 * e.g  while(lsx_to_itinerary_loop()) {lsx_to_itinerary_loop_item();}
 *
 * @package       tour-operator
 * @subpackage    template-tags
 * @category      itinerary
 */
function lsx_to_itinerary_loop_item() {
	global $tour_itinerary;

	if ( is_object( $tour_itinerary ) ) {
		$tour_itinerary->current_itinerary_item();
	}
}

/**
 * resets the itinerary loop.
 *
 * @package       tour-operator
 * @subpackage    template-tags
 * @category      itinerary
 */
function lsx_to_itinerary_loop_reset() {
	global $tour_itinerary;

	if ( is_object( $tour_itinerary ) ) {
		$tour_itinerary->reset_loop();
	}
}

/**
 * Outputs The current Itinerary title, can only be used in the itinerary loop.
 *
 * @package       tour-operator
 * @subpackage    template-tags
 * @category      itinerary
 */
function lsx_to_itinerary_title() {
	global $tour_itinerary;

	if ( $tour_itinerary && $tour_itinerary->has_itinerary && ! empty( $tour_itinerary->itinerary ) ) {
		if ( ! empty( $tour_itinerary->itinerary['title'] ) ) {
			$title = apply_filters( 'the_title', $tour_itinerary->itinerary['title'] );
			echo wp_kses_post( $title );
		}
	}
}

/**
 * Outputs The current Itinerary slug, can only be used in the itinerary loop
 * as an ID.
 *
 * @package       tour-operator
 * @subpackage    template-tags
 * @category      itinerary
 */
function lsx_to_itinerary_slug() {
	global $tour_itinerary;

	if ( $tour_itinerary && $tour_itinerary->has_itinerary && ! empty( $tour_itinerary->itinerary ) ) {
		if ( ! empty( $tour_itinerary->itinerary['title'] ) ) {
			echo wp_kses_post( sanitize_title( $tour_itinerary->itinerary['title'] ) );
		}
	}
}

/**
 * Outputs The current Itinerary Tagline, can only be used in the itinerary
 * loop.
 *
 * @package       tour-operator
 * @subpackage    template-tags
 * @category      itinerary
 */
function lsx_to_itinerary_tagline() {
	global $tour_itinerary;

	if ( $tour_itinerary && $tour_itinerary->has_itinerary && ! empty( $tour_itinerary->itinerary ) ) {
		if ( ! empty( $tour_itinerary->itinerary['tagline'] ) ) {
			echo wp_kses_post( apply_filters( 'the_title', $tour_itinerary->itinerary['tagline'] ) );
		}
	}
}

/**
 * Outputs The current Itinerary description, can only be used in the itinerary
 * loop.
 *
 * @package       tour-operator
 * @subpackage    template-tags
 * @category      itinerary
 */
function lsx_to_itinerary_description() {
	global $tour_itinerary;

	if ( $tour_itinerary && $tour_itinerary->has_itinerary && ! empty( $tour_itinerary->itinerary ) ) {
		if ( ! empty( $tour_itinerary->itinerary['description'] ) ) {
			echo wp_kses_post( apply_filters( 'the_content', $tour_itinerary->itinerary['description'] ) );
		}
	}
}

/**
 * Checks if the current itinerary item has a thumbnail.
 *
 * @package       tour-operator
 * @subpackage    template-tags
 * @category      itinerary
 */
function lsx_to_itinerary_has_thumbnail() {
	global $tour_itinerary;

	if ( $tour_itinerary && $tour_itinerary->has_itinerary ) {
		return true;
	}
}

/**
 * Outputs The current Itinerary thumbnail, can only be used in the itinerary
 * loop.
 *
 * @package       tour-operator
 * @subpackage    template-tags
 * @category      itinerary
 */
function lsx_to_itinerary_thumbnail() {
	global $tour_itinerary;
	$accommodation_id = '';
	$temp_id          = '';
	if ( $tour_itinerary && $tour_itinerary->has_itinerary && false !== $tour_itinerary->itinerary ) {
		$thumbnail_src = false;

		if ( ! empty( $tour_itinerary->itinerary['featured_image'] ) ) {
			$tour_itinerary->save_used_image( $tour_itinerary->itinerary['featured_image'] );
			$thumbnail = wp_get_attachment_image_src( $tour_itinerary->itinerary['featured_image'], 'lsx-thumbnail-square' );

			if ( is_array( $thumbnail ) ) {
				$thumbnail_src = $thumbnail[0];
			}
		} elseif ( ! empty( $tour_itinerary->itinerary['accommodation_to_tour'] ) ) {
			$accommodation_images = false;

			foreach ( $tour_itinerary->itinerary['accommodation_to_tour'] as $accommodation_id ) {
				$tour_itinerary->register_current_gallery( $accommodation_id );
				$current_image_id = false;

				// Try for a thumbnail first.
				$temp_id = get_post_thumbnail_id( $accommodation_id );

				if ( false === $temp_id || $tour_itinerary->is_image_used( $temp_id ) ) {
					$current_image_id = $tour_itinerary->find_next_image( $accommodation_id );
				} else {
					$current_image_id = $temp_id;
				}

				if ( false !== $current_image_id ) {
					$tour_itinerary->save_used_image( $current_image_id );
					$temp_src_array = wp_get_attachment_image_src( $current_image_id, 'lsx-thumbnail-square' );

					if ( is_array( $temp_src_array ) ) {
						$accommodation_images[] = $temp_src_array[0];
					}
				}
			}

			if ( false !== $accommodation_images ) {
				$thumbnail_src = $accommodation_images[0];
			}
		}

		// If it is the last day of the itinerary and there is no image, then use the featured image of the tour.
		if ( $tour_itinerary->index === $tour_itinerary->count && ( false === $thumbnail_src || '' === $thumbnail_src ) ) {

			$temp_id = get_post_thumbnail_id( $accommodation_id );

			if ( false !== $temp_id ) {
				$current_image_id = $tour_itinerary->find_next_image( $accommodation_id );
				$temp_src_array   = wp_get_attachment_image_src( $current_image_id, 'lsx-thumbnail-square' );
				if ( is_array( $temp_src_array ) ) {
					$thumbnail_src = $temp_src_array[0];
				}
			}
		}
		$thumbnail_src = apply_filters( 'lsx_to_itinerary_thumbnail_src', $thumbnail_src, $tour_itinerary->index, $tour_itinerary->count );

		// Check weather or not to display the placeholder.
		if ( false === $thumbnail_src || '' === $thumbnail_src ) {
			$thumbnail_src = \lsx\legacy\Placeholders::placeholder_url( null, 'tour', 'lsx-thumbnail-square' );
		}

		return $thumbnail_src;
	}
}

/**
 * Outputs The current Itinerary connected destinations, can only be used in
 * the itinerary loop.
 *
 * @package       tour-operator
 * @subpackage    template-tags
 * @category      itinerary
 */
function lsx_to_itinerary_destinations( $before = '', $after = '' ) {
	global $tour_itinerary;

	if ( $tour_itinerary && $tour_itinerary->has_itinerary && ! empty( $tour_itinerary->itinerary ) ) {
		if ( ! empty( $tour_itinerary->itinerary['destination_to_tour'] ) && is_array( $tour_itinerary->itinerary['destination_to_tour'] ) ) {
			echo wp_kses_post( $before . lsx_to_connected_list( $tour_itinerary->itinerary['destination_to_tour'], 'destination', true, ', ' ) . $after );
		}
	}
}

/**
 * Outputs The current Itinerary connected accommodation, can only be used in
 * the itinerary loop.
 *
 * @package       tour-operator
 * @subpackage    template-tags
 * @category      itinerary
 */
function lsx_to_itinerary_accommodation( $before = '', $after = '' ) {
	global $tour_itinerary;

	if ( $tour_itinerary && $tour_itinerary->has_itinerary && ! empty( $tour_itinerary->itinerary ) ) {
		if ( ! empty( $tour_itinerary->itinerary['accommodation_to_tour'] ) && is_array( $tour_itinerary->itinerary['accommodation_to_tour'] ) ) {
			echo wp_kses_post( $before . lsx_to_connected_list( $tour_itinerary->itinerary['accommodation_to_tour'], 'accommodation', true, ', ' ) . $after );
		}

		$meta_class = 'lsx-to-meta-data lsx-to-meta-data-';

		// Display the additional accommodation information.
		if ( ! empty( $tour_itinerary->itinerary['accommodation_to_tour'] ) && is_array( $tour_itinerary->itinerary['accommodation_to_tour'] ) ) {
			foreach ( $tour_itinerary->itinerary['accommodation_to_tour'] as $accommodation ) {
				lsx_to_accommodation_rating( '<span class="' . $meta_class . 'rating"><span class="lsx-to-meta-data-key">' . __( 'Rating', 'tour-operator' ) . ':</span> ', '</span>', $accommodation );
				the_terms( $accommodation, 'accommodation-type', '<span class="' . $meta_class . 'style"><span class="lsx-to-meta-data-key">' . __( 'Type', 'tour-operator' ) . ':</span> ', ', ', '</span>' );
				lsx_to_accommodation_special_interests( '<span class="' . $meta_class . 'special"><span class="lsx-to-meta-data-key">' . __( 'Special Interests', 'tour-operator' ) . ':</span> ', '</span>', $accommodation );
			}
		}
	}
}

/**
 * Outputs The current Itinerary connected activities, can only be used in the
 * itinerary loop.
 *
 * @package       tour-operator
 * @subpackage    template-tags
 * @category      itinerary
 */
function lsx_to_itinerary_activities( $before = '', $after = '' ) {
	global $tour_itinerary;

	if ( $tour_itinerary && $tour_itinerary->has_itinerary && ! empty( $tour_itinerary->itinerary ) ) {
		if ( ! empty( $tour_itinerary->itinerary['activity_to_tour'] ) && is_array( $tour_itinerary->itinerary['activity_to_tour'] ) ) {
			echo wp_kses_post( $before . lsx_to_connected_list( $tour_itinerary->itinerary['activity_to_tour'], 'activity', true, ', ' ) . $after );
		}
	}
}

/**
 * Outputs the 'itinerary' class.
 *
 * @param    $classes string or array
 *
 * @package       tour-operator
 * @subpackage    template-tags
 * @category      itinerary
 */
function lsx_to_itinerary_class( $classes = false ) {
	global $post;

	if ( false !== $classes ) {
		if ( ! is_array( $classes ) ) {
			$classes = explode( ' ', $classes );
		}

		$classes = apply_filters( 'lsx_to_itinerary_class', $classes, $post->ID );
	}

	echo 'class="' . esc_attr( implode( ' ', $classes ) ) . '"';
}


/**
 * Outputs the 'read more' button if needed.
 *
 * @package       tour-operator
 * @subpackage    template-tags
 * @category      itinerary
 */
function lsx_to_itinerary_read_more() {
	if ( lsx_to_itinerary_needs_read_more() ) {
		?>
		<div class="view-more text-center lsx-to-section-view-all">
			<a href="#" class="btn border-btn"><?php esc_html_e( 'Read More', 'tour-operator' ); ?></a>
		</div>
		<?php
	}
}

/**
 * checks if the read more should be outputted
 *
 * @package       tour-operator
 * @subpackage    template-tags
 * @category      itinerary
 */
function lsx_to_itinerary_needs_read_more() {
	return apply_filters( 'lsx_itinerary_needs_read_more', false );
}

/**
 * Gets the days included field
 *
 * @package       tour-operator
 * @subpackage    template-tags
 * @category      itinerary
 */
function lsx_to_itinerary_includes( $before = '', $after = '' ) {
	global $tour_itinerary;

	if ( $tour_itinerary && $tour_itinerary->has_itinerary && ! empty( $tour_itinerary->itinerary ) ) {
		if ( ! empty( $tour_itinerary->itinerary['included'] ) ) {
			echo wp_kses_post( $before . $tour_itinerary->itinerary['included'] . $after );
		}
	}
}

/**
 * Gets the days excluded field
 *
 * @package       tour-operator
 * @subpackage    template-tags
 * @category      itinerary
 */
function lsx_to_itinerary_excludes( $before = '', $after = '' ) {
	global $tour_itinerary;

	if ( $tour_itinerary && $tour_itinerary->has_itinerary && ! empty( $tour_itinerary->itinerary ) ) {
		if ( ! empty( $tour_itinerary->itinerary['excluded'] ) ) {
			echo wp_kses_post( $before . $tour_itinerary->itinerary['excluded'] . $after );
		}
	}
}

/**
 * Checks if the current accommodation has rooms
 *
 * @package       tour-operator
 * @subpackage    template-tags
 * @category      accommodation
 */
function lsx_to_accommodation_has_rooms() {
	global $rooms;

	$have_rooms = false;

	if ( null === $rooms ) {
		$rooms = new \lsx\legacy\Unit_Query();
	}

	if ( is_object( $rooms ) ) {
		$have_rooms = $rooms->have_query();
	}

	return $have_rooms;
}

/**
 * Runs the current room loop, used in a "while" statement
 * e.g  while(lsx_to_accommodation_room_loop())
 * {lsx_to_accommodation_room_loop_item();}
 *
 * @package       tour-operator
 * @subpackage    template-tags
 * @category      room
 */
function lsx_to_accommodation_room_loop() {
	global $rooms;

	if ( is_object( $rooms ) ) {
		return $rooms->while_query();
	} else {
		return false;
	}
}

/**
 * Sets up the current room
 * e.g  while(lsx_to_accommodation_room_loop())
 * {lsx_to_accommodation_room_loop_item();}
 *
 * @package       tour-operator
 * @subpackage    template-tags
 * @category      room
 */
function lsx_to_accommodation_room_loop_item( $type = false ) {
	global $rooms;

	if ( is_object( $rooms ) ) {
		return $rooms->current_queried_item( $type );
	} else {
		return false;
	}
}

/**
 * Outputs The current Rooms title
 *
 * @param        $before | string
 * @param        $after  | string
 * @param        $echo   | boolean
 *
 * @package       tour-operator
 * @subpackage    template-tags
 * @category      room
 */
function lsx_to_accommodation_room_title( $before = '', $after = '', $echo = true ) {
	global $rooms;

	if ( is_object( $rooms ) ) {
		$rooms->item_title( $before, $after, $echo );
	}
}

/**
 * Outputs The current Rooms Description
 *
 * @param        $before | string
 * @param        $after  | string
 * @param        $echo   | boolean
 *
 * @package       tour-operator
 * @subpackage    template-tags
 * @category      room
 */
function lsx_to_accommodation_room_description( $before = '', $after = '', $echo = true ) {
	global $rooms;

	if ( is_object( $rooms ) ) {
		$rooms->item_description( $before, $after, $echo );
	}
}

/**
 * Checks if the current room item has a thumbnail.
 *
 * @package       tour-operator
 * @subpackage    template-tags
 * @category      room
 */
function lsx_to_accommodation_room_has_thumbnail() {
	global $rooms;

	if ( $rooms && $rooms->have_query ) {
		return true;
	}
}

/**
 * Checks if the current type has units.
 *
 * @package       tour-operator
 * @subpackage    template-tags
 * @category      room
 */
function lsx_to_accommodation_check_type( $type = false ) {
	global $rooms;

	return $rooms->check_type( $type );
}

/**
 * Resets the loop
 *
 * @package       tour-operator
 * @subpackage    template-tags
 * @category      room
 */
function lsx_to_accommodation_reset_units_loop() {
	global $rooms;

	return $rooms->reset_loop();
}

/**
 * Outputs various units attached to the accommodation
 *
 * @package       tour-operator
 * @subpackage    template-tags
 * @category      unit
 */
function lsx_to_accommodation_units() {
	global $rooms;

	if ( lsx_to_accommodation_has_rooms() ) {
		$unit_types = array(
			'chalet' => esc_html__( 'Chalet', 'tour-operator' ),
			'room'   => esc_html__( 'Room', 'tour-operator' ),
			'spa'    => esc_html__( 'Spa', 'tour-operator' ),
			'tent'   => esc_html__( 'Tent', 'tour-operator' ),
			'villa'  => esc_html__( 'Villa', 'tour-operator' ),
		);

		foreach ( $unit_types as $type_key => $type_label ) {
			if ( lsx_to_accommodation_check_type( $type_key ) ) {
				?>
				<section id="<?php echo esc_attr( $type_key ); ?>s" class="lsx-to-section <?php lsx_to_collapsible_class( 'accommodation', false ); ?>">
					<h2 class="lsx-to-section-title lsx-to-collapse-title lsx-title" <?php lsx_to_collapsible_attributes_not_post( 'collapse-' . $type_key . 's' ); ?>><?php echo esc_html( lsx_to_get_post_type_section_title( 'accommodation', $type_key . 's', $type_label . 's' ) ); ?></h2>

					<div id="collapse-<?php echo esc_attr( $type_key ); ?>s" class="collapse in">
						<div class="collapse-inner">
							<div class="<?php echo esc_attr( $type_key ); ?>s-wrapper rooms-wrapper row">
								<?php while ( lsx_to_accommodation_room_loop() ) { ?>

									<?php
									if ( ! lsx_to_accommodation_room_loop_item( $type_key ) ) {
										continue;
									}
									?>

									<div class="col-xs-12 col-md-6">
										<article class="rooms-content">
											<?php if ( lsx_to_accommodation_room_has_thumbnail() && $rooms->item_thumbnails() ) { ?>
												<div class="rooms-thumbnail-wrap">
													<?php
														$images = $rooms->item_thumbnails();
														$count  = 0;

													foreach ( $images as $thumbnail_wide_src => $thumbnail_single_src ) {
														$count++;
														?>
															<a href="<?php echo esc_url( $thumbnail_single_src ); ?>" class="rooms-thumbnail 
																				<?php
																				if ( $count > 1 ) {
																					echo 'hidden';}
																				?>
															" style="background-image:url('<?php echo esc_url( $thumbnail_wide_src ); ?>')"></a>
															<?php
													}
													?>
												</div>
											<?php } ?>

											<div class="rooms-info">
												<?php lsx_to_accommodation_room_title( '<h5>', '</h5>' ); ?>
												<?php lsx_to_accommodation_room_description( '<div class="entry-content">', '</div>' ); ?>
											</div>
										</article>
									</div>

									<?php
								}
								lsx_to_accommodation_reset_units_loop();
								?>
							</div>
						</div>
					</div>
				</section>
				<?php
			}
		}
	}
}

/**
 * SCP Order Uninstall hook
 */
register_uninstall_hook( __FILE__, 'lsx_to_scporder_uninstall' );

function lsx_to_scporder_uninstall() {
	global $wpdb;

	if ( function_exists( 'is_multisite' ) && is_multisite() ) {
		$curr_blog = $wpdb->blogid;
		$blogids   = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

		foreach ( $blogids as $blog_id ) {
			switch_to_blog( $blog_id );
			lsx_to_scporder_uninstall_db();
		}

		switch_to_blog( $curr_blog );
	} else {
		lsx_to_scporder_uninstall_db();
	}
}

function lsx_to_scporder_uninstall_db() {
	global $wpdb;

	$result = $wpdb->query( "DESCRIBE $wpdb->terms `lsx_to_term_order`" );

	if ( $result ) {
		$result = $wpdb->query( "ALTER TABLE $wpdb->terms DROP `lsx_to_term_order`" );
	}

	delete_option( 'lsx_to_scporder_install' );
}

/**
 * Removes unnecesary menu items from the TO menu.
 *
 * @return void
 */
function lsx_to_remove_menu_pages() {
	remove_submenu_page( 'tour-operator', 'post-new.php?post_type=destination' );
	remove_submenu_page( 'tour-operator', 'post-new.php?post_type=tour' );
	remove_submenu_page( 'tour-operator', 'post-new.php?post_type=accommodation' );
	remove_submenu_page( 'tour-operator', 'post-new.php?post_type=activity' );
	remove_submenu_page( 'tour-operator', 'post-new.php?post_type=review' );
	remove_submenu_page( 'tour-operator', 'post-new.php?post_type=special' );
	remove_submenu_page( 'tour-operator', 'edit-tags.php?taxonomy=special-type' );
	remove_submenu_page( 'tour-operator', 'post-new.php?post_type=team' );
}
add_action( 'admin_init', 'lsx_to_remove_menu_pages' );
