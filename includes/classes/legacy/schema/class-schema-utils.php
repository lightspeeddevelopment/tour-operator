<?php
/**
 * Helper functions for the Schema class.
 *
 * @package LSX Tour Operator
 */
namespace lsx\legacy;

/**
 * Schema utility functions.
 *
 * @since 11.6
 */
class Schema_Utils {

	public static function get_option( $key = '', $tab = 'general' ) {
		$value         = '';
		$tour_operator = tour_operator();
		if ( isset( $tour_operator->options[ $tab ] ) && isset( $tour_operator->options[ $tab ][ $key ] ) ) {
			$value = $tour_operator->options[ $tab ][ $key ];
		}
		return $value;
	}

	/**
	 * Determines whether a given post type should have Review schema.
	 *
	 * @param string $post_type       Post type to check.
	 * @param string $comparison_type Post type to check against.
	 *
	 * @return bool True if it has schema, false if not.
	 */
	public static function is_type( $post_type = null, $comparison_type = null ) {
		if ( is_null( $comparison_type ) ) {
			return false;
		}

		if ( is_null( $post_type ) ) {
			$post_type = get_post_type();
		}

		/**
		 * Filter: 'wpseo_schema_$this->post_type_post_types' - Allow changing for which post types we output Review schema.
		 *
		 * @api string[] $post_types The post types for which we output Review.
		 */
		$post_types = apply_filters( 'wpseo_schema_' . $comparison_type . '_post_types', array( $comparison_type ) );

		return in_array( $post_type, $post_types );
	}

	/**
	 * Retrieve a users Schema ID.
	 *
	 * @param string               $place_id The Name of the Reviewer you need a for.
	 * @param string               $type the type of the place.
	 * @param WPSEO_Schema_Context $context A value object with context variables.
	 *
	 * @return string The user's schema ID.
	 */
	public static function get_places_schema_id( $place_id, $type, $context ) {
		$url = $context->site_url . '#/schema/' . strtolower( $type ) . '/' . wp_hash( $place_id . get_the_title( $place_id ) );
		return trailingslashit( $url );
	}

	/**
	 * Retrieve a users Schema ID.
	 *
	 * @param string               $name The Name of the Reviewer you need a for.
	 * @param WPSEO_Schema_Context $context A value object with context variables.
	 *
	 * @return string The user's schema ID.
	 */
	public static function get_subtrip_schema_id( $name, $context ) {
		$url = $context->site_url . '#/subtrip/' . wp_hash( $name . $context->id );
		return trailingslashit( $url );
	}

	/**
	 * Retrieve an offer Schema ID.
	 *
	 * @param string               $id      post ID of the place being added.
	 * @param WPSEO_Schema_Context $context A value object with context variables.
	 * @param string               $local   if the Schema is local true / false.
	 *
	 * @return string The user's schema ID.
	 */
	public static function get_offer_schema_id( $id, $context, $local = false ) {
		if ( false === $local ) {
			$url = $context->site_url;
		} else {
			$url = get_permalink( $context->id );
		}
		$url .= '#/schema/offer/';
		$url .= wp_hash( $id . get_the_title( $id ) );
		return trailingslashit( $url );
	}

	/**
	 * Retrieve an review Schema ID.
	 *
	 * @param string               $id      post ID of the place being added.
	 * @param WPSEO_Schema_Context $context A value object with context variables.
	 * @param string               $local   if the Schema is local true / false.
	 *
	 * @return string The user's schema ID.
	 */
	public static function get_review_schema_id( $id, $context, $local = false ) {
		if ( false === $local ) {
			$url = $context->site_url;
		} else {
			$url = get_permalink( $context->id );
		}
		$url .= '#/schema/review/';
		$url .= wp_hash( $id . get_the_title( $id ) );
		return trailingslashit( $url );
	}

	/**
	 * Retrieve an Article Schema ID.
	 *
	 * @param string               $id      post ID of the place being added.
	 * @param WPSEO_Schema_Context $context A value object with context variables.
	 * @param string               $local   if the Schema is local true / false.
	 *
	 * @return string The user's schema ID.
	 */
	public static function get_article_schema_id( $id, $context, $local = false ) {
		if ( false === $local ) {
			$url = get_permalink( $id ) . \WPSEO_Schema_IDs::ARTICLE_HASH;
		} else {
			$url = get_permalink( $context->id ) . '#/schema/article/' . wp_hash( $id . get_the_title( $id ) );
		}
		return trailingslashit( $url );
	}

	/**
	 * Retrieve a users Schema ID.
	 *
	 * @param string               $name The Name of the Reviewer you need a for.
	 * @param WPSEO_Schema_Context $context A value object with context variables.
	 *
	 * @return string The user's schema ID.
	 */
	public static function get_author_schema_id( $name, $email, $context ) {
		return $context->site_url . \WPSEO_Schema_IDs::PERSON_HASH . wp_hash( $name . $email );
	}

	/**
	 * Generates the place graph piece for the subtrip / Itinerary arrays.
	 *
	 * @param array                $data         subTrip / itinerary data.
	 * @param string               $type         The type in data to save the terms in.
	 * @param string               $post_id      The post ID of the current Place to add.
	 * @param WPSEO_Schema_Context $context      The post ID of the current Place to add.
	 * @param string               $contained_in The @id of the containedIn place.
	 *
	 * @return mixed array $data Place data.
	 */
	public static function add_place( $data, $type, $post_id, $context, $contained_in = false ) {
		$at_id = self::get_places_schema_id( $post_id, $type, $context );
		$place = array(
			'@type'       => $type,
			'@id'         => $at_id,
			'name'        => get_the_title( $post_id ),
			'description' => get_the_excerpt( $post_id ),
			'url'         => get_permalink( $post_id ),
		);
		if ( false !== $contained_in ) {
			$place['containedInPlace'] = array(
				'@type' => 'Country',
				'@id'   => $contained_in,
			);
		}
		$data[] = $place;
		return $data;
	}

	/**
	 * Generates the "review" graph piece for the subtrip / Itinerary arrays.
	 *
	 * @param array                $data         subTrip / itinerary data.
	 * @param string               $post_id      The post ID of the current Place to add.
	 * @param WPSEO_Schema_Context $context      The post ID of the current Place to add.
	 * @param array                $args         and array of parameter you want added to the offer.
	 * @param string               $local        if the Schema is local true / false.
	 *
	 * @return mixed array $data Place data.
	 */
	public static function add_review( $data, $post_id, $context, $args = array(), $local = false ) {
		$defaults = array(
			'@id'           => self::get_review_schema_id( $post_id, $context, $local ),
			'author'        => get_the_author_meta( 'display_name', get_post_field( 'post_author', $post_id ) ),
			'datePublished' => mysql2date( DATE_W3C, get_post_field( 'post_date_gmt', $post_id ), false ),
			'reviewRating'  => false,
		);
		$args     = wp_parse_args( $args, $defaults );
		$args     = apply_filters( 'lsx_to_schema_tour_review_args', $args );
		$offer    = array(
			'@type' => apply_filters( 'lsx_to_schema_tour_review_type', 'Review', $args ),
		);
		foreach ( $args as $key => $value ) {
			if ( false !== $value ) {
				$offer[ $key ] = $value;
			}
		}
		$data[] = $offer;
		return $data;
	}

	/**
	 * Generates the "review" graph piece for the subtrip / Itinerary arrays.
	 *
	 * @param array                $data         subTrip / itinerary data.
	 * @param string               $post_id      The post ID of the current Place to add.
	 * @param WPSEO_Schema_Context $context      The post ID of the current Place to add.
	 * @param array                $args         and array of parameter you want added to the offer.
	 * @param string               $local        if the Schema is local true / false.
	 *
	 * @return mixed array $data Place data.
	 */
	public static function add_article( $data, $post_id, $context, $args = array(), $local = false ) {
		$defaults = array(
			'@id'           => self::get_article_schema_id( $post_id, $context, $local ),
			'author'        => get_the_author_meta( 'display_name', get_post_field( 'post_author', $post_id ) ),
			'datePublished' => mysql2date( DATE_W3C, get_post_field( 'post_date_gmt', $post_id ), false ),
			'dateModified'  => mysql2date( DATE_W3C, get_post_field( 'post_modified_gmt', $post_id ), false ),
		);
		$args     = wp_parse_args( $args, $defaults );
		$args     = apply_filters( 'lsx_to_schema_tour_article_args', $args );
		$offer    = array(
			'@type' => apply_filters( 'lsx_to_schema_tour_article_type', 'Article', $args ),
		);
		foreach ( $args as $key => $value ) {
			if ( false !== $value ) {
				$offer[ $key ] = $value;
			}
		}
		$data[] = $offer;
		return $data;
	}

	/**
	 * Generates the "Offer" graph piece for the subtrip / Itinerary arrays.
	 *
	 * @param array                $data         subTrip / itinerary data.
	 * @param string               $post_id      The post ID of the current Place to add.
	 * @param WPSEO_Schema_Context $context      The post ID of the current Place to add.
	 * @param array                $args         and array of parameter you want added to the offer.
	 * @param string               $local        if the Schema is local true / false.
	 *
	 * @return mixed array $data Place data.
	 */
	public static function add_offer( $data, $post_id, $context, $args = array(), $local = false ) {
		$defaults = array(
			'@id'                => self::get_offer_schema_id( $post_id, $context, $local ),
			'price'              => false,
			'priceCurrency'      => false,
			'PriceSpecification' => false,
			'url'                => false,
			'availability'       => false,
			'category'           => __( 'Standard', 'tour-operator' ),
		);
		$args     = wp_parse_args( $args, $defaults );
		$args     = apply_filters( 'lsx_to_schema_accommodation_offer_args', $args );
		$offer    = array(
			'@type' => apply_filters( 'lsx_to_schema_accommodation_offer_type', 'Offer', $args ),
		);
		foreach ( $args as $key => $value ) {
			if ( false !== $value ) {
				$offer[ $key ] = $value;
			}
		}
		$data[] = $offer;
		return $data;
	}

	/**
	 * Adds an image node if the post has a featured image.
	 *
	 * @param array                $data         The Review data.
	 * @param WPSEO_Schema_Context $context      The post ID of the current Place to add.
	 *
	 * @return array $data The Review data.
	 */
	public static function add_image( $data, $context ) {
		if ( $context->has_image ) {
			$data['image'] = array(
				'@id' => $context->canonical . \WPSEO_Schema_IDs::PRIMARY_IMAGE_HASH,
			);
		}
		return $data;
	}

	/**
	 * Generates the itemReviewed schema
	 *
	 * @param  array  $items The array of IDS.
	 * @param  string $type The schema type.
	 * @return array $schema An array of the schema markup.
	 */
	public static function get_item_reviewed( $items = array(), $type = '' ) {
		$schema = array();
		if ( false !== $items && ! empty( $items ) && '' !== $type ) {
			array_unique( $items );
			foreach ( $items as $item ) {
				$title = get_the_title( $item );
				if ( '' !== $title ) {
					$item_schema = array(
						'@type' => $type,
						'name'  => $title,
					);
					$schema[]    = $item_schema;
				}
			}
		}
		return $schema;
	}

	/**
	 * Adds a term or multiple terms, comma separated, to a field.
	 *
	 * @param array  $data     Review data.
	 * @param string $post_id  The ID of the item to fetch terms
	 * @param string $key      The key in data to save the terms in.
	 * @param string $taxonomy The taxonomy to retrieve the terms from.
	 *
	 * @return mixed array $data Review data.
	 */
	public static function add_terms( $data, $post_id, $key, $taxonomy ) {
		$terms = get_the_terms( $post_id, $taxonomy );
		if ( is_array( $terms ) ) {
			$keywords = array();
			foreach ( $terms as $term ) {
				// We are checking against the WordPress internal translation.
				// @codingStandardsIgnoreLine
				if ( $term->name !== __( 'Uncategorized' ) ) {
					$keywords[] = $term->name;
				}
			}
			$data[ $key ] = implode( ',', $keywords );
		}

		return $data;
	}
}
