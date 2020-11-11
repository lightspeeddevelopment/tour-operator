<?php
/**
 * The Trip Schema for Tours
 *
 * @package tour-operator
 */

/**
 * Returns schema Review data.
 *
 * @since 10.2
 */
class LSX_TO_Tour_Schema extends LSX_TO_Schema_Graph_Piece {

	/**
	 * Constructor.
	 *
	 * @param \WPSEO_Schema_Context $context A value object with context variables.
	 */
	public function __construct( WPSEO_Schema_Context $context ) {
		$this->post_type        = 'tour';
		$this->itinerary_fields = array(
			'accommodation_to_tour' => 'Accommodation',
			'destination_to_tour'   => 'State',
		);
		parent::__construct( $context );
	}

	/**
	 * Returns Review data.
	 *
	 * @return array $data Review data.
	 */
	public function generate() {
		$data = array(
			'@type'            => array(
				'Trip',
				'Product',
			),
			'@id'              => $this->context->canonical . '#tour',
			'name'             => $this->post->post_title,
			'description'      => wp_strip_all_tags( $this->post->post_content ),
			'url'              => $this->post_url,
			'mainEntityOfPage' => array(
				'@id' => $this->context->canonical . WPSEO_Schema_IDs::WEBPAGE_HASH,
			),
			'mpn'              => $this->context->id,
		);

		if ( $this->context->site_represents_reference ) {
			$data['provider'] = $this->context->site_represents_reference;
			$data['brand']    = $this->context->site_represents_reference;
		}

		$wetu_ref = get_post_meta( $this->context->id, 'lsx_wetu_ref', true );
		if ( false !== $wetu_ref && '' !== $wetu_ref ) {
			$data['sku']        = $wetu_ref;
			$data['identifier'] = $wetu_ref;
		}
		$data = \lsx\legacy\Schema_Utils::add_image( $data, $this->context );
		$data = $this->add_itinerary( $data );
		$data = $this->add_sub_trips( $data );
		$data = $this->add_offers( $data );
		$data = $this->add_reviews( $data );
		$data = $this->add_articles( $data );
		return $data;
	}

	/**
	 * Adds the itinerary destinations as an itemList
	 *
	 * @param array $data Trip data.
	 *
	 * @return array $data Trip data.
	 */
	private function add_itinerary( $data ) {
		$places       = array();
		$destinations = get_post_meta( $this->context->id, 'destination_to_tour', false );
		if ( ! empty( $destinations ) ) {
			foreach ( $destinations as $destination ) {
				if ( '' !== $destination ) {
					$parent = wp_get_post_parent_id( $destination );
					if ( false === $parent || 0 === $parent ) {
						$places                          = \lsx\legacy\Schema_Utils::add_place( $places, 'Country', $destination, $this->context );
						$this->place_ids[ $destination ] = \lsx\legacy\Schema_Utils::get_places_schema_id( $destination, 'Country', $this->context );
					} else {
						$places                          = \lsx\legacy\Schema_Utils::add_place( $places, 'State', $destination, $this->context );
						$this->place_ids[ $destination ] = \lsx\legacy\Schema_Utils::get_places_schema_id( $destination, 'State', $this->context );
					}
				}
			}
			if ( ! empty( $places ) ) {
				$data['itinerary'] = $places;
			}
		}

		return $data;
	}

	/**
	 * Adds the itinerary destinations as an itemList
	 *
	 * @param array $data Trip data.
	 *
	 * @return array $data Trip data.
	 */
	private function add_sub_trips( $data ) {
		/**
		 * Filter: 'wpseo_schema_tour_sub_trips_meta_key' - Allow changing the custom field meta_key used to assign the Sub Trips to a post type Trip data.
		 *
		 * @api string $meta_key The chosen meta_key.
		 */
		$meta_key = apply_filters( 'wpseo_schema_tour_sub_trips_meta_key', 'itinerary' );
		return $this->add_subtrip_days( $data, 'subTrip', $meta_key );
	}

	/**
	 * Adds the days of the subTrip to the dta.
	 *
	 * @param array  $data     Trip data.
	 * @param string $key      The key in data to save the terms in.
	 * @param string $taxonomy The taxonomy to retrieve the terms from.
	 *
	 * @return mixed array $data Review data.
	 */
	private function add_subtrip_days( $data, $key, $meta_key ) {
		$itinerary  = get_post_meta( get_the_ID(), 'itinerary', false );
		$list_array = array();
		$i          = 0;

		if ( ! empty( $itinerary ) ) {
			foreach ( $itinerary as $day ) {
				$i++;
				$schema = array(
					'@type'       => 'Trip',
					'@id'         => \lsx\legacy\Schema_Utils::get_subtrip_schema_id( $day['title'], $this->context ),
					'name'        => $day['title'],
					'description' => wp_strip_all_tags( $day['description'] ),
				);

				$itinerary_fields = apply_filters( 'lsx_to_schema_tour_sub_trips_additional_fields', $this->itinerary_fields );
				$places           = $this->add_subtrip_places( $itinerary_fields, $day );
				if ( ! empty( $places ) ) {
					$schema['itinerary'] = $places;
				}

				$list_array[] = $schema;
			}

			$data[ $key ] = $list_array;
		}
		return $data;
	}

	/**
	 * Adds in the accommodation and destination Place if found.
	 *
	 * @param array  $itinerary_fields The array of fields to loop through.
	 * @param string $day The itinrary day array to grab the post_ids from.
	 *
	 * @return array $palces Places data.
	 */
	private function add_subtrip_places( $itinerary_fields, $day ) {
		$places = array();
		if ( ! empty( $itinerary_fields ) ) {
			foreach ( $itinerary_fields as $key => $type ) {

				if ( isset( $day[ $key ] ) && '' !== $day[ $key ] && ! empty( $day[ $key ] ) ) {
					foreach ( $day[ $key ] as $place_id ) {
						if ( '' !== $place_id ) {
							// Here we are linking the regions to the country.
							$contained_in = false;
							$parent_id    = wp_get_post_parent_id( $place_id );
							if ( false !== $parent_id && ! empty( $this->place_ids ) && isset( $this->place_ids[ $parent_id ] ) ) {
								$contained_in = $this->place_ids[ $parent_id ];
							}
							$places                       = \lsx\legacy\Schema_Utils::add_place( $places, $type, $place_id, $this->context, $contained_in );
							$this->place_ids[ $place_id ] = \lsx\legacy\Schema_Utils::get_places_schema_id( $place_id, $type, $this->context );
						}
					}
				}
			}
		}
		return $places;
	}
}
