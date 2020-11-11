<?php
/**
 * Tours Itinerary Query
 *
 * @package   Itinerary_Query
 * @author    LightSpeed
 * @license   GPL3
 * @link
 * @copyright 2016 LightSpeedDevelopment
 */

namespace lsx\legacy;

/**
 * Main plugin class.
 *
 * @package Itinerary_Query
 * @author  LightSpeed
 */
class Itinerary_Query {

	/**
	 * Holds class instance
	 *
	 * @since 1.0.0
	 *
	 * @var      object
	 */
	public $has_itinerary = false;

	/**
	 * Holds the array of Itineraries
	 *
	 * @since 1.0.0
	 *
	 * @var      array
	 */
	public $itineraries = false;

	/**
	 * Holds current itinerary
	 *
	 * @since 1.0.0
	 *
	 * @var      array
	 */
	public $itinerary = false;

	/**
	 * The Number of Itinerary Items
	 *
	 * @since 1.0.0
	 *
	 * @var      array
	 */
	public $count = 0;

	/**
	 * The Current Itinerary Index
	 *
	 * @since 1.0.0
	 *
	 * @var      array
	 */
	public $index = 0;

	/**
	 * Holds the current post_id
	 *
	 * @since 1.0.0
	 *
	 * @var      string
	 */
	public $post_id = false;

	/**
	 * Holds the an array of gallery ids from each accommodations attached to the itinerary.
	 *
	 * @since 1.0.0
	 *
	 * @var      string
	 */
	public $current_attachments = array();

	/**
	 * Holds the an array of attachment ids
	 *
	 * @since 1.0.0
	 *
	 * @var      string
	 */
	public $images_used = array();

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	public function __construct() {
		$this->post_id     = get_the_ID();
		$this->itineraries = get_post_meta( $this->post_id, 'itinerary', false );
		if ( is_array( $this->itineraries ) && ! empty( $this->itineraries ) ) {
			$this->has_itinerary = true;
			$this->count         = count( $this->itineraries );
		}
		add_filter( 'lsx_to_itinerary_thumbnail_src', array( $this, 'departure_day_image' ), 10, 3 );
	}

	/**
	 * A filter to set the content area to a small column on single
	 */
	public function has_itinerary() {
		return $this->has_itinerary;
	}

	/**
	 * Used in the While loop to cycle through the field array
	 */
	public function while_itinerary() {
		if ( $this->index < $this->count ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Sets the current itinerary item
	 */
	public function current_itinerary_item() {
		$this->itinerary = $this->itineraries[ $this->index ];
		$this->index++;
	}

	/**
	 * Pulls the current days accommodation and saves them for use incase an image has already been displayed.
	 *
	 * @param   $accommodation_id   | string
	 */
	public function register_current_gallery( $accommodation_id = false ) {
		if ( false !== $accommodation_id ) {
			$gallery = get_post_meta( $accommodation_id, 'gallery', false );
			$gallery = $this->append_room_images( $accommodation_id, $gallery );
			if ( false !== $gallery && ! empty( $gallery ) ) {
				$this->current_attachments[ $accommodation_id ] = $gallery;
			}
		}
	}

	/**
	 * Adds the room images to the list of possible items.
	 *
	 * @param   $accommodation_id   | string
	 * @param   $gallery    array
	 */
	public function append_room_images( $accommodation_id = false, $gallery = array() ) {
		if ( false !== $accommodation_id ) {

			$room_images = get_post_meta( $accommodation_id, 'units', false );
			if ( false !== $room_images && ! empty( $room_images ) ) {

				$append = array();
				foreach ( $room_images as $room ) {
					if ( isset( $room['gallery'] ) && is_array( $room['gallery'] ) && ! empty( $room['gallery'] ) ) {
						foreach ( $room['gallery'] as $image ) {
							$append[] = $image;
						}
					}
				}
				$gallery = array_merge( $gallery, $append );
			}
		}

		return $gallery;
	}

	/**
	 * Save the id of the images that have already been displayed.
	 *
	 * @param   $image_id   | string
	 */
	public function save_used_image( $image_id = false ) {
		if ( false !== $image_id ) {
			$this->images_used[] = $image_id;
		}
	}

	/**
	 * Check if the current image has been displayed already
	 *
	 * @param   $image_id   | string
	 * @return  boolean
	 */
	public function is_image_used( $image_id = false ) {
		if ( is_array( $this->images_used ) && in_array( $image_id, $this->images_used ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Finds another image from the accommodation gallery that hasnt been used.
	 *
	 * @param   $accommodation_id   | string
	 * @return  boolean | string
	 */
	public function find_next_image( $accommodation_id = false ) {
		$return = false;

		if ( false !== $accommodation_id && isset( $this->current_attachments[ $accommodation_id ] ) && ! empty( $this->current_attachments[ $accommodation_id ] ) && ! empty( $this->images_used ) ) {
			$images_left = array_diff( $this->current_attachments[ $accommodation_id ], $this->images_used );
			if ( is_array( $images_left ) && ! empty( $images_left ) ) {
				$images_left = array_values( $images_left );
				$return      = array_shift( $images_left );
			}
		}
		return $return;
	}

	/**
	 * Sets the current itinerary item
	 */
	public function reset_loop() {
		$this->index = 0;
	}

	/**
	 * Overwrites the departure days thumbanil with the tours featured image.
	 *
	 * @param $thumbnail_src string
	 * @param $index string
	 * @param $count string
	 * @return string
	 */

	function departure_day_image( $thumbnail_src = false, $index = 1, $count = 0 ) {
		if ( false !== stripos( $thumbnail_src, 'placeholder' ) && $count === $index ) {
			$thumbnail_src = get_the_post_thumbnail_url( get_the_ID() );
		}
		return $thumbnail_src;
	}
}
