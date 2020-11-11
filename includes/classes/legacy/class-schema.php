<?php
/**
 * Schema Class
 *
 * @package   LSX Tour Operator
 * @author    LightSpeed
 * @license   GPL3
 * @link
 * @copyright 2019 LightSpeedDevelopment
 */

namespace lsx\legacy;

/**
 * Main plugin class.
 *
 * @package Schema
 * @author  LightSpeed
 */
class Schema {

	/**
	 * Holds instances of the class
	 *
	 * @var instance
	 **/
	protected static $instance;

	/**
	 * Constructor
	 */
	public function __construct() {
		if ( interface_exists( 'WPSEO_Graph_Piece' ) ) {
			require_once LSX_TO_PATH . 'includes/classes/legacy/schema/class-schema-utils.php';
			require_once LSX_TO_PATH . 'includes/classes/legacy/schema/class-lsx-to-schema-graph-piece.php';
			require_once LSX_TO_PATH . 'includes/classes/legacy/schema/class-lsx-to-tour-schema.php';
			require_once LSX_TO_PATH . 'includes/classes/legacy/schema/class-lsx-to-accommodation-schema.php';
			require_once LSX_TO_PATH . 'includes/classes/legacy/schema/class-lsx-to-destination-schema.php';
			add_filter( 'wpseo_schema_graph_pieces', array( $this, 'add_graph_pieces' ), 11, 2 );
		}
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0.0
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Adds Schema pieces to our output.
	 *
	 * @param array                 $pieces  Graph pieces to output.
	 * @param \WPSEO_Schema_Context $context Object with context variables.
	 *
	 * @return array $pieces Graph pieces to output.
	 */
	public function add_graph_pieces( $pieces, $context ) {
		$pieces[] = new \LSX_TO_Tour_Schema( $context );
		$pieces[] = new \LSX_TO_Accommodation_Schema( $context );
		$pieces[] = new \LSX_TO_Destination_Schema( $context );
		return $pieces;
	}

	/**
	 * Creates the schema for the tour post type
	 *
	 * @since 1.0.0
	 */
	public function tour_single_schema() {
		if ( is_singular( 'tour' ) ) {
			$start_val = get_post_meta( get_the_ID(), 'booking_validity_start', false );
			$end_val   = get_post_meta( get_the_ID(), 'booking_validity_end', false );

			if ( ! empty( $des_list ) ) {
				foreach ( $des_list as $single_destination ) {
					$i++;
					$url_option       = get_the_permalink() . '#destination-' . $i;
					$destination_name = get_the_title( $single_destination );
					$schema_day       = array(
						'@type'           => 'PostalAddress',
						'addressLocality' => $destination_name,
					);
					$des_schema[]     = $schema_day;
				}
			}
			$meta   = array(
				array(
					'address'   => $des_schema,
					'telephone' => '0216713090',
				),
			);
			$output = wp_json_encode( $meta, JSON_UNESCAPED_SLASHES );
			?>
			<script type="application/ld+json">
				<?php echo wp_kses_post( $output ); ?>
			</script>
			<?php
		}
	}


	/**
	 * Creates the schema for the destination post type
	 *
	 * @since 1.0.0
	 */
	public function destination_single_schema() {
		if ( is_singular( 'destination' ) ) {
			$dest_travel_styles      = get_the_terms( get_the_ID(), 'travel-style' );
			$destination_travel      = array();
			$destination_name        = get_the_title();
			$destination_url         = get_the_permalink();
			$destination_description = wp_strip_all_tags( get_the_content() );
			$address_accommodation   = get_post_meta( get_the_ID(), 'location', true );
			$street_address          = $address_accommodation['address'];
			$lat_address             = $address_accommodation['lat'];
			$long_address            = $address_accommodation['long'];

			if ( ! empty( $dest_travel_styles ) ) {
				foreach ( $dest_travel_styles as $single_travel_style ) {
					$destination_travel[] = $single_travel_style->name;
				}
			}
			global $post;

			$args = array(
				'post_parent'    => $post->ID,
				'posts_per_page' => -1,
				'post_type'      => 'destination',
			);

				$the_query   = new \WP_Query( $args );
				$the_regions = array();

			if ( $the_query->have_posts() ) {
				while ( $the_query->have_posts() ) {
					$the_query->the_post();
					$region_title       = get_the_title();
					$region_description = wp_strip_all_tags( get_the_content() );

					$region_list   = array(
						'@type'       => 'TouristAttraction',
						'name'        => $region_title,
						'description' => $region_description,
					);
					$the_regions[] = $region_list;
				}
			}

			wp_reset_postdata();

			$meta   = array(
				'@context'      => 'http://schema.org',
				'@type'         => 'TouristDestination',
				'name'          => $destination_name,
				'address'       => $street_address,
				'description'   => $destination_description,
				'touristType'   => $destination_travel,
				'url'           => $destination_url,
				'geo'           => array(
					'@type'     => 'GeoCoordinates',
					'latitude'  => $lat_address,
					'longitude' => $long_address,
				),
				'containsPlace' => $the_regions,
			);
			$output = wp_json_encode( $meta, JSON_UNESCAPED_SLASHES );
			?>
			<script type="application/ld+json">
				<?php echo wp_kses_post( $output ); ?>
			</script>
			<?php
		}
	}

	/**
	 * Creates the schema for the accommodation post type
	 *
	 * @since 1.0.0
	 */
	public function accommodation_single_schema() {
		if ( is_singular( 'accommodation' ) ) {
			$i                             = 0;
			$spoken_languages              = get_post_meta( get_the_ID(), 'spoken_languages', false );
			$checkin_accommodation         = get_post_meta( get_the_ID(), 'checkin_time', false );
			$checkout_accommodation        = get_post_meta( get_the_ID(), 'checkout_time', false );
			$accommodation_expert_id       = get_post_meta( get_the_ID(), 'team_to_accommodation', true );
			$address_accommodation         = get_post_meta( get_the_ID(), 'location', true );
			$street_address                = $address_accommodation['address'];
			$accommodation_expert          = get_the_title( $accommodation_expert_id );
			$title_accommodation           = get_the_title();
			$url_accommodation             = get_the_permalink();
			$description_accommodation     = wp_strip_all_tags( get_the_content() );
			$image_accommodation           = get_the_post_thumbnail_url( get_the_ID(), 'full' );
			$rating_accommodation          = get_post_meta( get_the_ID(), 'rating', true );
			$rooms_accommodation           = get_post_meta( get_the_ID(), 'number_of_rooms', true );
			$destinations_in_accommodation = get_post_meta( get_the_ID(), 'destination_to_accommodation', false );
			$country                       = get_the_title( $destinations_in_accommodation[0] );
			$region_destinations           = get_the_title( $destinations_in_accommodation[1] );
			$price_accommodation           = get_post_meta( get_the_ID(), 'price', true );
			$price_val                     = lsx_currencies()->base_currency;

			foreach ( $spoken_languages as $language ) {
				foreach ( $language as $morelanguage ) {
					$i++;
					$url_option        = get_the_permalink() . '#language-' . $i;
					$language_list     = array(
						'@type' => 'language',
						'@id'   => $url_option,
						'name'  => $morelanguage,
					);
					$final_lang_list[] = $language_list;
				}
			}

			$meta   = array(
				'availableLanguage' => $final_lang_list,
				'address'           => array(
					'addressCountry' => $country,
					'addressRegion'  => $region_destinations,
					'streetAddress'  => $street_address,
				),
				'checkinTime'       => $checkin_accommodation,
				'checkoutTime'      => $checkout_accommodation,
				'employee'          => $accommodation_expert,
				'image'             => $image_accommodation,
				'name'              => $title_accommodation,
				'numberOfRooms'     => $rooms_accommodation,
				'priceRange'        => $price_val . $price_accommodation,
				'url'               => $url_accommodation,
				'telephone'         => '+18666434336',
			);
			$output = wp_json_encode( $meta, JSON_UNESCAPED_SLASHES );
			?>
			<script type="application/ld+json">
				<?php echo wp_kses_post( $output ); ?>
			</script>
			<?php
		}
	}
}
