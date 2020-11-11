<?php
/**
 * Structure the TO pages
 *
 * @package     tour-operator
 * @subpackage  layout
 * @license     GPL3
 */

/**
 * Archive
 */
add_action( 'lsx_entry_top', 'lsx_to_archive_entry_top' );

/**
 * Archive Post type Specific
 */
add_action( 'lsx_entry_top', 'lsx_to_accommodation_archive_entry_top', 15 );
add_action( 'lsx_entry_top', 'lsx_to_destination_archive_entry_top', 15 );
add_action( 'lsx_entry_top', 'lsx_to_tour_archive_entry_top', 15 );

add_action( 'lsx_entry_bottom', 'lsx_to_accommodation_archive_entry_bottom' );
add_action( 'lsx_entry_bottom', 'lsx_to_destination_archive_entry_bottom' );
add_action( 'lsx_entry_bottom', 'lsx_to_tour_archive_entry_bottom' );

add_action( 'lsx_content_bottom', 'lsx_to_destination_archive_content_bottom' );

/**
 * Single
 */
add_action( 'lsx_content_wrap_before', 'lsx_to_single_content_top', 110 );
add_action( 'lsx_entry_bottom', 'lsx_to_single_entry_bottom' );

/**
 * Single Post type Specific
 */
add_action( 'lsx_content_bottom', 'lsx_to_accommodation_single_content_bottom' );
add_action( 'lsx_content_bottom', 'lsx_to_destination_single_content_bottom' );
add_action( 'lsx_content_bottom', 'lsx_to_tour_single_content_bottom' );

add_action( 'lsx_to_fast_facts', 'lsx_to_accommodation_single_fast_facts' );
add_action( 'lsx_to_fast_facts', 'lsx_to_destination_single_fast_facts' );
add_action( 'lsx_to_fast_facts', 'lsx_to_tour_single_fast_facts' );

/**
 * Adds the template tags to the top of the content-accommodation
 *
 * @package     tour-operator
 * @subpackage  template-tag
 * @category    general
 */
function lsx_to_archive_entry_top() {
	global $lsx_to_archive;

	if ( in_array( get_post_type(), array_keys( lsx_to_get_post_types() ) ) && ( is_archive() || $lsx_to_archive ) ) { ?>
		<?php
			global $post;
			$post_type = get_post_type();
			$image_src = '';

			$has_single = ! lsx_to_is_single_disabled();
			$permalink  = '';

		if ( $has_single ) {
			$permalink = get_the_permalink();
		} elseif ( is_search() ) {
			$has_single = true;
			$permalink  = get_post_type_archive_link( $post_type ) . '#' . $post_type . '-' . $post->post_name;
		}

			$thumbnail_id = get_post_thumbnail_id( get_the_ID() );
			$image_arr    = wp_get_attachment_image_src( $thumbnail_id, 'lsx-thumbnail-single' );

		if ( is_array( $image_arr ) ) {
			$image_src = $image_arr[0];
		}
		?>

		<div class="lsx-to-archive-thumb">
			<a 
			<?php
			if ( $has_single ) {
				echo 'href="' . esc_url( $permalink ) . '"';}
			?>
			 style="background-image: url('<?php echo esc_url( $image_src ); ?>')">
				<?php
				if ( 'team' === get_post_type() ) {
					lsx_thumbnail( 'lsx-thumbnail-square' );
				} else {
					lsx_thumbnail( 'lsx-thumbnail-wide' );
				}
				?>
			</a>
		</div>

		<div class="lsx-to-archive-wrapper">
			<div class="lsx-to-archive-content">
				<h3 class="lsx-to-archive-content-title">
					<?php
					if ( $has_single ) {
						?>
<a href="<?php echo esc_url( $permalink ); ?>" title="<?php esc_html_e( 'Read more', 'tour-operator' ); ?>"><?php } ?>
						<?php
							the_title();
							do_action( 'lsx_to_the_title_end', get_the_ID() );
						?>
					<?php
					if ( $has_single ) {
						?>
</a><?php } ?>
				</h3>

				<?php lsx_to_tagline( '<p class="lsx-to-archive-content-tagline">', '</p>' ); ?>
		<?php
	}
}

/**
 * Adds the template tags to the top of the lsx_content_top action
 *
 * @package     tour-operator
 * @subpackage  template-tag
 * @category    general
 */
function lsx_to_single_content_top() {
	if ( is_singular( array_keys( lsx_to_get_post_types() ) ) ) {
		lsx_to_page_navigation();
	}
}

/**
 * Adds the template tags to the top of the content-{post-type}
 *
 * @package     tour-operator
 * @subpackage  template-tag
 * @category    general
 */
function lsx_to_single_entry_bottom() {
	global $lsx_to_archive;

	if ( is_singular( array_keys( lsx_to_get_post_types() ) ) && false === $lsx_to_archive ) {
		?>
		<div class="col-xs-12 col-sm-12 col-md-5 to-facts-wrapper">
			<?php lsx_to_fast_facts(); ?>

			<?php if ( lsx_to_has_enquiry_contact() ) : ?>
				<div class="lsx-to-contact-widget">
					<?php
					if ( function_exists( 'lsx_to_has_team_member' ) && lsx_to_has_team_member() ) {
						lsx_to_team_member_panel( '<div class="lsx-to-contact">', '</div>' );
					} else {
						lsx_to_enquiry_contact( '<div class="lsx-to-contact">', '</div>' );
					}

						lsx_to_enquire_modal();
					?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}

/**
 * Adds the template tags fast facts
 *
 * @package     tour-operator
 * @subpackage  template-tag
 * @category    general
 */
function lsx_to_accommodation_single_fast_facts() {
	if ( is_singular( 'accommodation' ) ) {
		?>
		<section style="display: none;" id="fast-facts">
			<div class="lsx-to-section-inner">
				<h3 class="lsx-to-section-title"><?php esc_html_e( 'Accommodation Summary', 'tour-operator' ); ?></h3>

				<div class="lsx-to-single-meta-data">
					<?php
						$meta_class = 'lsx-to-meta-data lsx-to-meta-data-';

						// lsx_to_price( '<span class="' . $meta_class . 'price"><span class="lsx-to-meta-data-key">' . esc_html__( 'From price', 'tour-operator' ) . ':</span> ', '</span>' );
						lsx_to_accommodation_rating( '<span class="' . $meta_class . 'rating"><span class="lsx-to-meta-data-key">' . esc_html__( 'Rating', 'tour-operator' ) . ':</span> ', '</span>' );
						lsx_to_connected_countries( '<span class="' . $meta_class . 'destinations"><span class="lsx-to-meta-data-key">' . esc_html__( 'Locations', 'tour-operator' ) . ':</span> ', '</span>' );
						the_terms( get_the_ID(), 'travel-style', '<span class="' . $meta_class . 'style"><span class="lsx-to-meta-data-key">' . esc_html__( 'Style', 'tour-operator' ) . ':</span> ', ', ', '</span>' );
						the_terms( get_the_ID(), 'accommodation-type', '<span class="' . $meta_class . 'type"><span class="lsx-to-meta-data-key">' . esc_html__( 'Type', 'tour-operator' ) . ':</span> ', ', ', '</span>' );
						lsx_to_accommodation_room_total( '<span class="' . $meta_class . 'rooms"><span class="lsx-to-meta-data-key">' . esc_html__( 'Rooms', 'tour-operator' ) . ':</span> ', '</span>' );
						lsx_to_accommodation_spoken_languages( '<span class="' . $meta_class . 'languages"><span class="lsx-to-meta-data-key">' . esc_html__( 'Spoken Languages', 'tour-operator' ) . ':</span> ', '</span>' );
						lsx_to_accommodation_activity_friendly( '<span class="' . $meta_class . 'friendly"><span class="lsx-to-meta-data-key">' . esc_html__( 'Friendly', 'tour-operator' ) . ':</span> ', '</span>' );
						lsx_to_accommodation_special_interests( '<span class="' . $meta_class . 'special"><span class="lsx-to-meta-data-key">' . esc_html__( 'Special Interests', 'tour-operator' ) . ':</span> ', '</span>' );
						the_terms( get_the_ID(), 'accommodation-brand', '<span class="' . $meta_class . 'brand"><span class="lsx-to-meta-data-key">' . esc_html__( 'Brands', 'tour-operator' ) . ':</span> ', ', ', '</span>' );

					if ( function_exists( 'lsx_to_connected_activities' ) ) {
						lsx_to_connected_activities( '<span class="' . $meta_class . 'activities"><span class="lsx-to-meta-data-key">' . esc_html__( 'Activities', 'tour-operator' ) . ':</span> ', '</span>' );
					}
					?>
				</div>
			</div>
		</section>
		<?php
	}
}

/**
 * Adds the template tags fast facts
 *
 * @package     tour-operator
 * @subpackage  template-tag
 * @category    general
 */
function lsx_to_destination_single_fast_facts() {
	if ( is_singular( 'destination' ) ) {
		?>
		<section style="display: none;" id="fast-facts">
			<div class="lsx-to-section-inner">
				<h3 class="lsx-to-section-title"><?php esc_html_e( 'Fast Facts', 'tour-operator' ); ?></h3>

				<div class="lsx-to-single-meta-data">
					<?php
						$meta_class = 'lsx-to-meta-data lsx-to-meta-data-';

						months_to_visit();

						destination_children( get_the_ID() );

						the_terms( get_the_ID(), 'travel-style', '<span class="' . $meta_class . 'style"><span class="lsx-to-meta-data-key">' . esc_html__( 'Travel Style', 'tour-operator' ) . ':</span> ', ', ', '</span>' );

					if ( function_exists( 'lsx_to_connected_activities' ) ) {
						lsx_to_connected_activities( '<span class="' . $meta_class . 'activities"><span class="lsx-to-meta-data-key">' . esc_html__( 'Activities', 'tour-operator' ) . ':</span> ', '</span>' );
					}
					?>
				</div>
			</div>
		</section>
		<?php
	}
}

/**
 * Adds the template tags fast facts
 *
 * @package     tour-operator
 * @subpackage  template-tag
 * @category    general
 */
function lsx_to_tour_single_fast_facts() {
	if ( is_singular( 'tour' ) ) {
		$highlights = get_post_meta( get_the_ID(), 'hightlights', true );
		?>
		<section style="display: none;" id="fast-facts">
			<div class="lsx-to-section-inner">
				<h3 class="lsx-to-section-title"><?php esc_html_e( 'Tour Summary', 'tour-operator' ); ?></h3>

				<div class="lsx-to-single-meta-data">
					<?php
						$meta_class = 'lsx-to-meta-data lsx-to-meta-data-';

						// lsx_to_price( '<span class="' . $meta_class . 'price"><span class="lsx-to-meta-data-key">' . esc_html__( 'From price', 'tour-operator' ) . ':</span> ', '</span>' );
						lsx_to_duration( '<span class="' . $meta_class . 'duration"><span class="lsx-to-meta-data-key">' . esc_html__( 'Duration', 'tour-operator' ) . ':</span> ', '</span>' );
						lsx_to_departure_point( '<span class="' . $meta_class . 'pin"><span class="lsx-to-meta-data-key">' . esc_html__( 'Departs from', 'tour-operator' ) . ':</span> ', '</span>' );
						lsx_to_end_point( '<span class="' . $meta_class . 'pin"><span class="lsx-to-meta-data-key">' . esc_html__( 'Ends in', 'tour-operator' ) . ':</span> ', '</span>' );
						lsx_to_connected_countries( '<span class="' . $meta_class . 'destinations"><span class="lsx-to-meta-data-key">' . esc_html__( 'Destinations', 'tour-operator' ) . ':</span> ', '</span>' );
						the_terms( get_the_ID(), 'travel-style', '<span class="' . $meta_class . 'style"><span class="lsx-to-meta-data-key">' . esc_html__( 'Travel Style', 'tour-operator' ) . ':</span> ', ', ', '</span>' );

					if ( function_exists( 'lsx_to_connected_activities' ) ) {
						lsx_to_connected_activities( '<span class="' . $meta_class . 'activities"><span class="lsx-to-meta-data-key">' . esc_html__( 'Activities', 'tour-operator' ) . ':</span> ', '</span>' );
					}
					?>
				</div>
			</div>
		</section>
		<?php if ( ! empty( $highlights ) ) : ?>
			<section id="highlights">
				<div class="lsx-to-section-inner">
					<h3 class="lsx-to-section-title"><?php esc_html_e( 'Highlights', 'tour-operator' ); ?></h3>

					<?php lsx_to_highlights(); ?>
				</div>
			</section>
		<?php endif; ?>
		<?php
	}
}

/**
 * Adds the template tags to the bottom of the single accommodation
 *
 * @package     tour-operator
 * @subpackage  template-tag
 * @category    accommodation
 */
function lsx_to_accommodation_single_content_bottom() {
	if ( is_singular( 'accommodation' ) ) {
		if ( function_exists( 'lsx_to_has_map' ) && lsx_to_has_map() ) {
			?>
			<section id="accommodation-map" class="lsx-to-section <?php lsx_to_collapsible_class( 'accommodation', false ); ?>">
				<h2 class="lsx-to-section-title lsx-to-collapse-title lsx-title hidden-lg" <?php lsx_to_collapsible_attributes_not_post( 'collapse-accommodation-map' ); ?>><?php esc_html_e( 'Map', 'tour-operator' ); ?></h2>

				<div id="collapse-accommodation-map" class="collapse in">
					<div class="collapse-inner">
						<?php lsx_to_map(); ?>
					</div>
				</div>
			</section>
			<?php
		}

		lsx_to_accommodation_units();

		lsx_to_accommodation_facilities( '<section id="facilities" class="lsx-to-section ' . lsx_to_collapsible_class() . '"><h2 class="lsx-to-section-title lsx-to-collapse-title lsx-title" ' . lsx_to_collapsible_attributes( 'collapse-facilities' ) . '>' . esc_html__( 'Facilities', 'tour-operator' ) . '</h2><div id="collapse-facilities" class="collapse in"><div class="collapse-inner"><div class="row facilities-wrapper">', '</div></div></div></section>' );

		lsx_to_included_block();

		lsx_to_gallery( '<section id="gallery" class="lsx-to-section ' . lsx_to_collapsible_class() . '"><h2 class="lsx-to-section-title lsx-to-collapse-title lsx-title" ' . lsx_to_collapsible_attributes( 'collapse-gallery' ) . '>' . esc_html__( 'Gallery', 'tour-operator' ) . '</h2><div id="collapse-gallery" class="collapse in"><div class="collapse-inner">', '</div></div></section>' );

		if ( function_exists( 'lsx_to_videos' ) ) {
			lsx_to_videos( '<section id="videos" class="lsx-to-section ' . lsx_to_collapsible_class() . '"><h2 class="lsx-to-section-title lsx-to-collapse-title lsx-title" ' . lsx_to_collapsible_attributes( 'collapse-videos' ) . '>' . esc_html__( 'Videos', 'tour-operator' ) . '</h2><div id="collapse-videos" class="collapse in"><div class="collapse-inner">', '</div></div></section>' );
		} elseif ( class_exists( 'Envira_Videos' ) ) {
			lsx_to_envira_videos( '<section id="videos" class="lsx-to-section ' . lsx_to_collapsible_class() . '"><h2 class="lsx-to-section-title lsx-to-collapse-title lsx-title" ' . lsx_to_collapsible_attributes( 'collapse-videos' ) . '>' . esc_html__( 'Videos', 'tour-operator' ) . '</h2><div id="collapse-videos" class="collapse in"><div class="collapse-inner">', '</div></div></section>' );
		}

		if ( function_exists( 'lsx_to_accommodation_specials' ) ) {
			lsx_to_accommodation_specials();
		}

		if ( function_exists( 'lsx_to_accommodation_reviews' ) ) {
			lsx_to_accommodation_reviews();
		}

		lsx_to_related_items( 'travel-style', '<section id="related-items" class="lsx-to-section ' . lsx_to_collapsible_class() . '"><h2 class="lsx-to-section-title lsx-to-collapse-title lsx-title" ' . lsx_to_collapsible_attributes( 'collapse-related-items' ) . '>' . lsx_to_get_post_type_section_title( 'accommodation', 'similar', 'Related Accommodation' ) . '</h2><div id="collapse-related-items" class="collapse in"><div class="collapse-inner">', '</div></div></section>' );

		$connected_tours = get_post_meta( get_the_ID(), 'tour_to_accommodation', false );

		if ( lsx_to_accommodation_display_connected_tours() && post_type_exists( 'tour' ) && is_array( $connected_tours ) && ! empty( $connected_tours ) ) {
			lsx_to_related_items( $connected_tours, '<section id="tours" class="lsx-to-section ' . lsx_to_collapsible_class() . '"><h2 class="lsx-to-section-title lsx-to-collapse-title lsx-title" ' . lsx_to_collapsible_attributes( 'collapse-related-items' ) . '>' . esc_html__( 'Related Tours', 'tour-operator' ) . '</h2><div id="collapse-related-items" class="collapse in"><div class="collapse-inner">', '</div></div></section>', true, 'tour' );
		}

		lsx_to_accommodation_posts();
	}
}

/**
 * Adds the template tags to the top of the content-accommodation
 *
 * @package     tour-operator
 * @subpackage  template-tag
 * @category    accommodation
 */
function lsx_to_accommodation_archive_entry_top() {
	global $lsx_to_archive;

	if ( 'accommodation' === get_post_type() && ( is_archive() || $lsx_to_archive ) ) {
		if ( is_search() || empty( tour_operator()->options[ get_post_type() ]['disable_entry_metadata'] ) ) {
			?>
			<div class="lsx-to-archive-meta-data lsx-to-archive-meta-data-grid-mode">
				<?php lsx_to_accommodation_meta(); ?>
			</div>
			<?php
		}
	}
}

/**
 * Adds the template tags to the bottom of the content-accommodation
 *
 * @package     tour-operator
 * @subpackage  template-tag
 * @category    accommodation
 */
function lsx_to_accommodation_archive_entry_bottom() {
	global $lsx_to_archive;

	if ( 'accommodation' === get_post_type() && ( is_archive() || $lsx_to_archive ) ) {
		?>
			</div>

			<?php if ( is_search() || empty( tour_operator()->options[ get_post_type() ]['disable_entry_metadata'] ) ) { ?>
				<div class="lsx-to-archive-meta-data lsx-to-archive-meta-data-list-mode">
					<?php lsx_to_accommodation_meta(); ?>
				</div>
			<?php } ?>
		</div>

		<?php $has_single = ! lsx_to_is_single_disabled(); ?>

		<?php if ( $has_single && 'grid' === tour_operator()->archive_layout ) : ?>
			<a href="<?php the_permalink(); ?>" class="moretag"><?php esc_html_e( 'View more', 'tour-operator' ); ?></a>
		<?php endif; ?>
		<?php
	}
}

/**
 * Adds the template tags to the bottom of the single destination
 *
 * @package     tour-operator
 * @subpackage  template-tag
 * @category    destination
 */
function lsx_to_destination_single_content_bottom() {
	if ( is_singular( 'destination' ) ) {
		if ( function_exists( 'lsx_to_has_map' ) && lsx_to_has_map() ) {
			?>
			<section id="destination-map" class="lsx-to-section <?php lsx_to_collapsible_class( 'destination', false ); ?>">
				<h2 class="lsx-to-section-title lsx-to-collapse-title lsx-title hidden-lg" <?php lsx_to_collapsible_attributes_not_post( 'collapse-destination-map' ); ?>><?php esc_html_e( 'Map', 'tour-operator' ); ?></h2>

				<div id="collapse-destination-map" class="collapse in">
					<div class="collapse-inner">
						<?php lsx_to_map(); ?>
					</div>
				</div>
			</section>
			<?php
		}

		lsx_to_destination_travel_info();

		lsx_to_country_regions();

		lsx_to_gallery( '<section id="gallery" class="lsx-to-section ' . lsx_to_collapsible_class() . '"><h2 class="lsx-to-section-title lsx-to-collapse-title lsx-title" ' . lsx_to_collapsible_attributes( 'collapse-gallery' ) . '>' . esc_html__( 'Gallery', 'tour-operator' ) . '</h2><div id="collapse-gallery" class="collapse in"><div class="collapse-inner">', '</div></div></section>' );

		if ( function_exists( 'lsx_to_videos' ) ) {
			lsx_to_videos( '<section id="videos" class="lsx-to-section ' . lsx_to_collapsible_class() . '"><h2 class="lsx-to-section-title lsx-to-collapse-title lsx-title" ' . lsx_to_collapsible_attributes( 'collapse-videos' ) . '>' . esc_html__( 'Videos', 'tour-operator' ) . '</h2><div id="collapse-videos" class="collapse in"><div class="collapse-inner">', '</div></div></section>' );
		} elseif ( class_exists( 'Envira_Videos' ) ) {
			lsx_to_envira_videos( '<section id="videos" class="lsx-to-section ' . lsx_to_collapsible_class() . '"><h2 class="lsx-to-section-title lsx-to-collapse-title lsx-title" ' . lsx_to_collapsible_attributes( 'collapse-videos' ) . '>' . esc_html__( 'Videos', 'tour-operator' ) . '</h2><div id="collapse-videos" class="collapse in"><div class="collapse-inner">', '</div></div></section>' );
		}

		lsx_to_destination_tours();

		lsx_to_region_accommodation();

		lsx_to_destination_activities();

		if ( function_exists( 'lsx_to_destination_specials' ) ) {
			lsx_to_destination_specials();
		}

		if ( function_exists( 'lsx_to_destination_vehicles' ) ) {
			lsx_to_destination_vehicles();
		}

		if ( function_exists( 'lsx_to_destination_reviews' ) ) {
			lsx_to_destination_reviews();
		}

		lsx_to_destination_posts();
	}
}

/**
 * Adds the template tags to the top of the content-destination.php
 *
 * @package     tour-operator
 * @subpackage  template-tag
 * @category    destination
 */
function lsx_to_destination_archive_entry_top() {
	global $lsx_to_archive;

	if ( 'destination' === get_post_type() && ( is_archive() || $lsx_to_archive ) ) {
		if ( is_search() || empty( tour_operator()->options[ get_post_type() ]['disable_entry_metadata'] ) ) {
			?>
			<div class="lsx-to-archive-meta-data lsx-to-archive-meta-data-grid-mode">
				<?php
					$meta_class = 'lsx-to-meta-data lsx-to-meta-data-';

					the_terms( get_the_ID(), 'travel-style', '<span class="' . $meta_class . 'style"><span class="lsx-to-meta-data-key">' . esc_html__( 'Travel Style', 'tour-operator' ) . ':</span> ', ', ', '</span>' );

				if ( function_exists( 'lsx_to_connected_activities' ) ) {
					lsx_to_connected_activities( '<span class="' . $meta_class . 'activities"><span class="lsx-to-meta-data-key">' . esc_html__( 'Activities', 'tour-operator' ) . ':</span> ', '</span>' );
				}
				?>
			</div>
			<?php
		}
	}
}

/**
 * Adds the template tags to the bottom of the content-destination.php
 *
 * @package     tour-operator
 * @subpackage  template-tag
 * @category    destination
 */
function lsx_to_destination_archive_entry_bottom() {
	global $lsx_to_archive;

	if ( 'destination' === get_post_type() && ( is_archive() || $lsx_to_archive ) ) {
		?>
			</div>

			<?php if ( is_search() || empty( tour_operator()->options[ get_post_type() ]['disable_entry_metadata'] ) ) { ?>
				<div class="lsx-to-archive-meta-data lsx-to-archive-meta-data-list-mode">
					<?php
						$meta_class = 'lsx-to-meta-data lsx-to-meta-data-';

						the_terms( get_the_ID(), 'travel-style', '<span class="' . $meta_class . 'style"><span class="lsx-to-meta-data-key">' . esc_html__( 'Travel Style', 'tour-operator' ) . ':</span> ', ', ', '</span>' );

					if ( function_exists( 'lsx_to_connected_activities' ) ) {
						lsx_to_connected_activities( '<span class="' . $meta_class . 'activities"><span class="lsx-to-meta-data-key">' . esc_html__( 'Activities', 'tour-operator' ) . ':</span> ', '</span>' );
					}
					?>
				</div>
			<?php } ?>
		</div>

		<?php $has_single = ! lsx_to_is_single_disabled(); ?>

		<?php if ( $has_single && 'grid' === tour_operator()->archive_layout ) : ?>
			<a href="<?php the_permalink(); ?>" class="moretag"><?php esc_html_e( 'View more', 'tour-operator' ); ?></a>
		<?php endif; ?>
		<?php
	}
}

/**
 * Adds the template tags to the bottom of the single-destination.php
 *
 * @package     tour-operator
 * @subpackage  template-tag
 * @category    destination
 */
function lsx_to_destination_archive_content_bottom() {
	global $lsx_to_archive;

	if ( 'destination' === get_post_type() && ( is_archive() || $lsx_to_archive ) ) {
		if ( function_exists( 'lsx_to_has_map' ) && lsx_to_has_map() ) {
			?>
			<section id="destination-map" class="lsx-to-section <?php lsx_to_collapsible_class( 'destination', false ); ?>">
				<?php if ( ! lsx_to_has_destination_banner_map() ) : ?>
					<h2 class="lsx-to-section-title lsx-to-collapse-title lsx-title hidden-lg" <?php lsx_to_collapsible_attributes( 'collapse-destination-map' ); ?>><?php esc_html_e( 'Map', 'tour-operator' ); ?></h2>
				<?php endif ?>

				<div id="collapse-destination-map" class="collapse in">
					<div class="collapse-inner">
						<?php lsx_to_map(); ?>
					</div>
				</div>
			</section>
			<?php
		}
	}
}

/**
 * Adds the template tags to the bottom of the single tour
 *
 * @package     tour-operator
 * @subpackage  template-tag
 * @category    tour
 */
function lsx_to_tour_single_content_bottom() {
	if ( is_singular( 'tour' ) ) {
		if ( function_exists( 'lsx_to_has_map' ) && lsx_to_has_map() ) {
			?>
			<section id="tour-map" class="lsx-to-section <?php lsx_to_collapsible_class( 'tour', false ); ?>">
				<h2 class="lsx-to-section-title lsx-to-collapse-title lsx-title hidden-lg" <?php lsx_to_collapsible_attributes_not_post( 'collapse-tour-map' ); ?>><?php esc_html_e( 'Map', 'tour-operator' ); ?></h2>

				<div id="collapse-tour-map" class="collapse in">
					<div class="collapse-inner">
						<?php lsx_to_map(); ?>
					</div>
				</div>
			</section>
			<?php
		}

		lsx_to_best_time_to_visit( '<section id="when-to-go" class="lsx-to-section ' . lsx_to_collapsible_class() . '"><h2 class="lsx-to-section-title lsx-to-collapse-title lsx-title" ' . lsx_to_collapsible_attributes( 'collapse-when-to-go' ) . '>' . esc_html__( 'When to Go', 'tour-operator' ) . '</h2><div id="collapse-when-to-go" class="collapse in"><div class="collapse-inner clearfix">', '</div></div></section>' );

		if ( lsx_to_has_itinerary() ) {
			$itinerary_count = 1;
			?>
			<section id="itinerary" class="lsx-to-section <?php lsx_to_collapsible_class( 'tour', false ); ?>">
				<h2 class="lsx-to-section-title lsx-to-collapse-title lsx-title" data-toggle="collapse" data-target="#collapse-itinerary" aria-expanded="false"><?php esc_html_e( 'Full Day by Day Itinerary', 'tour-operator' ); ?></h2>

				<div id="collapse-itinerary" class="collapse in">
					<div class="collapse-inner">
						<div class="row lsx-to-archive-items lsx-to-archive-template-list lsx-to-archive-template-image-<?php echo esc_attr( tour_operator()->archive_list_layout_image_style ); ?> itinerary-list">
							<?php while ( lsx_to_itinerary_loop() ) { ?>
								<?php
									lsx_to_itinerary_loop_item();
									$thumb = lsx_to_itinerary_thumbnail();
								?>

								<div id="day-<?php echo esc_attr( $itinerary_count ); ?>" <?php lsx_to_itinerary_class( 'lsx-to-archive-item itinerary-item col-xs-12' ); ?>>
									<div class="lsx-to-archive-container">
										<div class="lsx-to-archive-thumb">
											<div class="lsx-to-thumb-slot" style="background-image: url('<?php echo esc_url( $thumb ); ?>');">
												<?php echo wp_kses_post( '<img alt="thumbnail" class="attachment-responsive wp-post-image lsx-responsive" src="' . $thumb . '">' ); ?>
											</div>
										</div>

										<div class="lsx-to-archive-wrapper">
											<div class="lsx-to-archive-content">
												<h3 class="lsx-to-archive-content-title"><?php lsx_to_itinerary_title(); ?></h3>
												<p class="lsx-to-archive-content-tagline"><?php lsx_to_itinerary_tagline(); ?></p>

												<div class="entry-content">
													<?php lsx_to_itinerary_description(); ?>
												</div>
											</div>

											<div class="lsx-to-archive-meta-data">
												<?php
													$meta_class = 'lsx-to-meta-data lsx-to-meta-data-';

													lsx_to_itinerary_destinations( '<span class="' . $meta_class . 'destinations"><span class="lsx-to-meta-data-key">' . esc_html__( 'Destination', 'tour-operator' ) . ':</span> ', '</span>' );
													lsx_to_itinerary_accommodation( '<span class="' . $meta_class . 'accommodations"><span class="lsx-to-meta-data-key">' . esc_html__( 'Accommodation', 'tour-operator' ) . ':</span> ', '</span>' );
													lsx_to_itinerary_activities( '<span class="' . $meta_class . 'activities"><span class="lsx-to-meta-data-key">' . esc_html__( 'Activities', 'tour-operator' ) . ':</span> ', '</span>' );
													lsx_to_itinerary_includes( '<span class="' . $meta_class . 'day-includes"><span class="lsx-to-meta-data-key">' . esc_html__( 'Included', 'tour-operator' ) . ':</span> ', '</span>' );
													lsx_to_itinerary_excludes( '<span class="' . $meta_class . 'day-excludes"><span class="lsx-to-meta-data-key">' . esc_html__( 'Excluded', 'tour-operator' ) . ':</span> ', '</span>' );
												?>
											</div>
										</div>
									</div>
								</div>
								<?php
								$itinerary_count++;
							}
							?>
						</div>
					</div>
				</div>

				<?php lsx_to_itinerary_read_more(); ?>
			</section>
			<?php
		}

		lsx_to_included_block();

		lsx_to_gallery( '<section id="gallery" class="lsx-to-section ' . lsx_to_collapsible_class() . '"><h2 class="lsx-to-section-title lsx-to-collapse-title lsx-title" ' . lsx_to_collapsible_attributes( 'collapse-gallery' ) . '>' . esc_html__( 'Gallery', 'tour-operator' ) . '</h2><div id="collapse-gallery" class="collapse in"><div class="collapse-inner">', '</div></div></section>' );

		if ( function_exists( 'lsx_to_videos' ) ) {
			lsx_to_videos( '<section id="videos" class="lsx-to-section ' . lsx_to_collapsible_class() . '"><h2 class="lsx-to-section-title lsx-to-collapse-title lsx-title" ' . lsx_to_collapsible_attributes( 'collapse-videos' ) . '>' . esc_html__( 'Videos', 'tour-operator' ) . '</h2><div id="collapse-videos" class="collapse in"><div class="collapse-inner">', '</div></div></section>' );
		} elseif ( class_exists( 'Envira_Videos' ) ) {
			lsx_to_envira_videos( '<section id="videos" class="lsx-to-section ' . lsx_to_collapsible_class() . '"><h2 class="lsx-to-section-title lsx-to-collapse-title lsx-title" ' . lsx_to_collapsible_attributes( 'collapse-videos' ) . '>' . esc_html__( 'Videos', 'tour-operator' ) . '</h2><div id="collapse-videos" class="collapse in"><div class="collapse-inner">', '</div></div></section>' );
		}

		if ( function_exists( 'lsx_to_tour_specials' ) ) {
			lsx_to_tour_specials();
		}

		if ( function_exists( 'lsx_to_tour_reviews' ) ) {
			lsx_to_tour_reviews();
		}

		lsx_to_related_items( 'travel-style', '<section id="related-items" class="lsx-to-section ' . lsx_to_collapsible_class() . '"><h2 class="lsx-to-section-title lsx-to-collapse-title lsx-title" ' . lsx_to_collapsible_attributes( 'collapse-related-items' ) . '>' . lsx_to_get_post_type_section_title( 'tour', 'related', 'Related Tours' ) . '</h2><div id="collapse-related-items" class="collapse in"><div class="collapse-inner">', '</div></div></section>' );

		lsx_to_tour_posts();
	}
}

/**
 * Adds the template tags to the top of the content-tour.php
 *
 * @package     tour-operator
 * @subpackage  template-tag
 * @category    tour
 */
function lsx_to_tour_archive_entry_top() {
	global $lsx_to_archive;

	if ( 'tour' === get_post_type() && ( is_archive() || $lsx_to_archive ) ) {
		if ( is_search() || empty( tour_operator()->options[ get_post_type() ]['disable_entry_metadata'] ) ) {
			?>
			<div class="lsx-to-archive-meta-data lsx-to-archive-meta-data-grid-mode">
				<?php
					$meta_class = 'lsx-to-meta-data lsx-to-meta-data-';

					lsx_to_price( '<span class="' . $meta_class . 'price"><span class="lsx-to-meta-data-key">' . esc_html__( 'From price', 'tour-operator' ) . ':</span> ', '</span>' );
					lsx_to_duration( '<span class="' . $meta_class . 'duration"><span class="lsx-to-meta-data-key">' . esc_html__( 'Duration', 'tour-operator' ) . ':</span> ', '</span>' );
					the_terms( get_the_ID(), 'travel-style', '<span class="' . $meta_class . 'style"><span class="lsx-to-meta-data-key">' . esc_html__( 'Travel Style', 'tour-operator' ) . ':</span> ', ', ', '</span>' );
					lsx_to_connected_countries( '<span class="' . $meta_class . 'destinations"><span class="lsx-to-meta-data-key">' . esc_html__( 'Destinations', 'tour-operator' ) . ':</span> ', '</span>' );

				if ( function_exists( 'lsx_to_connected_activities' ) ) {
					lsx_to_connected_activities( '<span class="' . $meta_class . 'activities"><span class="lsx-to-meta-data-key">' . esc_html__( 'Activities', 'tour-operator' ) . ':</span> ', '</span>' );
				}
				?>
			</div>
			<?php
		}
	}
}

/**
 * Adds the template tags to the bottom of the content-tour.php
 *
 * @package     tour-operator
 * @subpackage  template-tag
 * @category    tour
 */
function lsx_to_tour_archive_entry_bottom() {
	global $lsx_to_archive;

	if ( 'tour' === get_post_type() && ( is_archive() || $lsx_to_archive ) ) {
		?>
			</div>

			<?php if ( is_search() || empty( tour_operator()->options[ get_post_type() ]['disable_entry_metadata'] ) ) { ?>
				<div class="lsx-to-archive-meta-data lsx-to-archive-meta-data-list-mode">
					<?php
						$meta_class = 'lsx-to-meta-data lsx-to-meta-data-';

						lsx_to_price( '<span class="' . $meta_class . 'price"><span class="lsx-to-meta-data-key">' . esc_html__( 'From price', 'tour-operator' ) . ':</span> ', '</span>' );
						lsx_to_duration( '<span class="' . $meta_class . 'duration"><span class="lsx-to-meta-data-key">' . esc_html__( 'Duration', 'tour-operator' ) . ':</span> ', '</span>' );
						the_terms( get_the_ID(), 'travel-style', '<span class="' . $meta_class . 'style"><span class="lsx-to-meta-data-key">' . esc_html__( 'Travel Style', 'tour-operator' ) . ':</span> ', ', ', '</span>' );
						lsx_to_connected_countries( '<span class="' . $meta_class . 'destinations"><span class="lsx-to-meta-data-key">' . esc_html__( 'Destinations', 'tour-operator' ) . ':</span> ', '</span>' );

					if ( function_exists( 'lsx_to_connected_activities' ) ) {
						lsx_to_connected_activities( '<span class="' . $meta_class . 'activities"><span class="lsx-to-meta-data-key">' . esc_html__( 'Activities', 'tour-operator' ) . ':</span> ', '</span>' );
					}
					?>
				</div>
			<?php } ?>
		</div>

		<?php $has_single = ! lsx_to_is_single_disabled(); ?>

		<?php if ( $has_single && 'grid' === tour_operator()->archive_layout ) : ?>
			<a href="<?php the_permalink(); ?>" class="moretag"><?php esc_html_e( 'View more', 'tour-operator' ); ?></a>
		<?php endif; ?>
		<?php
	}
}
