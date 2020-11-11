<?php
/**
 * Tours Unit Query
 *
 * @package   Unit_Query
 * @author    LightSpeed
 * @license   GPL3
 * @link
 * @copyright 2016 LightSpeedDevelopment
 */

namespace lsx\legacy;

/**
 * Main plugin class.
 *
 * @package Unit_Query
 * @author  LightSpeed
 */
class Unit_Query {

	/**
	 * Holds class instance
	 *
	 * @since 1.0.0
	 * @var      object
	 */
	public $have_query = false;

	/**
	 * Holds the array of items
	 *
	 * @since 1.0.0
	 * @var      array
	 */
	public $queried_items = false;

	/**
	 * Holds the array of section titles
	 *
	 * @since 1.0.0
	 * @var      array
	 */
	public $titles = false;

	/**
	 * Holds current queried item
	 *
	 * @since 1.0.0
	 * @var      array
	 */
	public $query_item = false;

	/**
	 * The Number of Queried Items
	 *
	 * @since 1.0.0
	 * @var      array
	 */
	public $count = 0;

	/**
	 * The Current Query Index
	 *
	 * @since 1.0.0
	 * @var      array
	 */
	public $index = 0;

	/**
	 * Holds the current post_id
	 *
	 * @since 1.0.0
	 * @var      string
	 */
	public $post_id = false;

	/**
	 * Initialize the plugin by setting localization, filters, and
	 * administration functions.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	public function __construct( $type = false ) {
		$this->post_id       = get_the_ID();
		$this->queried_items = get_post_meta( $this->post_id, 'units', false );
		if ( is_array( $this->queried_items ) && ! empty( $this->queried_items ) ) {
			$this->have_query = true;
			$this->count      = count( $this->queried_items );

			foreach ( $this->queried_items as $item ) {
				if ( isset( $item['type'] ) ) {
					$this->titles[ $item['type'] ] = 1;
				}
			}
		}
	}

	/**
	 * A filter to set the content area to a small column on single
	 */
	public function have_query() {
		return $this->have_query;
	}

	/**
	 * Used in the While loop to cycle through the field array
	 */
	public function while_query() {
		if ( $this->index < $this->count ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Sets the current itinerary item
	 */
	public function current_queried_item( $type = false ) {
		$return = false;
		if ( false === $type || $type === $this->queried_items[ $this->index ]['type'] ) {
			$this->query_item = $this->queried_items[ $this->index ];
			$return           = true;
		}
		$this->index ++;

		return $return;
	}

	/**
	 * Sets the current itinerary item
	 */
	public function reset_loop() {
		$this->index = 0;
	}

	/**
	 * Checks if the current type provided exists
	 */
	public function check_type( $type = false ) {
		if ( false !== $type && false !== $this->titles && isset( $this->titles[ $type ] ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Outputs the current items "title" field
	 */
	public function item_title( $before = '', $after = '', $echo = false ) {
		if ( $this->have_query && false !== $this->query_item ) {
			if ( false !== $this->query_item['title'] ) {
				$return = $before . apply_filters( 'the_title', $this->query_item['title'] ) . $after;
				if ( $echo ) {
					echo wp_kses_post( $return );
				} else {
					return $return;
				}
			}
		}
	}

	/**
	 * Outputs the current items "description" field
	 */
	public function item_description( $before = '', $after = '', $echo = false ) {
		if ( $this->have_query && false !== $this->query_item ) {
			if ( false !== $this->query_item['description'] ) {
				$return = $before . apply_filters( 'the_content', $this->query_item['description'] ) . $after;
				if ( $echo ) {
					echo wp_kses_post( $return );
				} else {
					return $return;
				}
			}
		}
	}

	/**
	 * Outputs the current items "gallery" field
	 */
	public function item_thumbnail() {
		if ( $this->have_query && false !== $this->query_item ) {
			$thumbnail_src = false;
			$thumbnail_src = apply_filters( 'lsx_to_accommodation_room_thumbnail', $thumbnail_src );

			if ( false !== $this->query_item['gallery'] ) {
				$images    = array_values( $this->query_item['gallery'] );
				$thumbnail = wp_get_attachment_image_src( $images[0], 'lsx-thumbnail-wide' );

				if ( is_array( $thumbnail ) ) {
					$thumbnail_src = $thumbnail[0];
				}
			}

			if ( false === $thumbnail_src || '' === $thumbnail_src ) {
				$thumbnail_src = \lsx\legacy\Placeholders::placeholder_url( null, 'accommodation' );
			}

			return $thumbnail_src;
		}
	}

	/**
	 * Outputs the current items "gallery" field
	 */
	public function item_thumbnails() {
		if ( $this->have_query && false !== $this->query_item ) {
			$images_return = array();

			if ( isset( $this->query_item['gallery'] ) && false !== $this->query_item['gallery'] ) {
				$images = array_values( $this->query_item['gallery'] );

				foreach ( $images as $key => $value ) {
					$thumbnail_src = false;
					$thumbnail_src = apply_filters( 'lsx_to_accommodation_room_thumbnail', $thumbnail_src );

					$thumbnail = wp_get_attachment_image_src( $value, 'lsx-thumbnail-wide' );

					if ( is_array( $thumbnail ) ) {
						$thumbnail_src = $thumbnail[0];
					}

					if ( ! empty( $thumbnail_src ) ) {
						$thumbnail_single = wp_get_attachment_image_src( $value, 'lsx-thumbnail-single' );

						if ( is_array( $thumbnail_single ) ) {
							$thumbnail_single_src = $thumbnail_single[0];
						}

						if ( ! empty( $thumbnail_single_src ) ) {
							$images_return[ $thumbnail_src ] = $thumbnail_single_src;
						}
					}
				}
			}

			return $images_return;
		}
	}
}
