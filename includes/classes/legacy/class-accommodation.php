<?php
/**
 * Accommodation Class, this registers the post type and adds certain filters
 * for layout.
 *
 * @package    \lsx\legacy\Accommodation
 * @author     LightSpeed Team
 * @license    GPL3
 * @link
 * @copyright  2015  LightSpeed Team
 */

namespace lsx\legacy;

/**
 * Plugin class.
 *
 * @package  Accommodation
 * @author   LightSpeed Team
 */
class Accommodation {

	/**
	 * The slug for this plugin
	 *
	 * @since 0.0.1
	 * @var      string
	 */
	protected $slug = 'accommodation';

	/**
	 * Holds class isntance
	 *
	 * @since 0.0.1
	 * @var      object|\lsx\legacy\Accommodation
	 */
	protected static $instance = null;

	/**
	 * If Wetu is active
	 *
	 * @since 0.0.1
	 * @var      boolean
	 */
	public $is_wetu_active = false;

	/**
	 * Holds and array of the Unit types available (slug => key)
	 *
	 * @since 0.0.1
	 * @var      array
	 */
	public $unit_types = false;

	/**
	 * Holds the $page_links array while its being built on the single
	 * accommodation page.
	 *
	 * @since 0.0.1
	 * @var      array
	 */
	public $page_links = false;

	/**
	 * Initialize the plugin by setting localization, filters, and
	 * administration functions.
	 *
	 * @since  0.0.1
	 * @access private
	 */
	private function __construct() {
		$this->is_wetu_active          = false;
		$this->display_connected_tours = false;

		$this->options = get_option( '_lsx-to_settings', false );

		$this->unit_types = array(
			'chalet' => esc_html__( 'Chalet', 'tour-operator' ),
			'room'   => esc_html__( 'Room', 'tour-operator' ),
			'spa'    => esc_html__( 'Spa', 'tour-operator' ),
			'tent'   => esc_html__( 'Tent', 'tour-operator' ),
			'villa'  => esc_html__( 'Villa', 'tour-operator' ),
		);

		// activate property post type
		add_action( 'lsx_to_framework_accommodation_tab_general_settings_bottom', array( $this, 'general_settings' ), 10, 1 );
		add_action( 'lsx_to_framework_accommodation_tab_single_settings_bottom', array( $this, 'single_settings' ), 10, 1 );

		add_filter( 'lsx_to_entry_class', array( $this, 'entry_class' ) );

		add_filter( 'lsx_to_custom_field_query', array( $this, 'price_filter' ), 5, 10 );

		add_filter( 'lsx_to_custom_field_query', array( $this, 'rating' ), 5, 10 );

		include 'class-unit-query.php';

		add_action( 'lsx_to_map_meta', 'lsx_to_accommodation_meta' );
		add_action( 'lsx_to_modal_meta', 'lsx_to_accommodation_meta' );

		add_filter( 'lsx_to_page_navigation', array( $this, 'page_links' ) );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 0.0.1
	 * @return    object|\lsx\legacy\Accommodation    A single instance of this
	 *                                           class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Adds the accommodation specific options
	 */
	public function general_settings() {
		?>
		<tr class="form-field">
			<th scope="row">
				<label for="contact_details_disabled"><?php esc_html_e( 'Disable "Contact Details" panel', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input type="checkbox" {{#if contact_details_disabled}} checked="checked" {{/if}} name="contact_details_disabled" />
			</td>
		</tr>
		<?php
	}

	/**
	 * Adds the accommodation single specific options
	 */
	public function single_settings() {
		?>
		<tr class="form-field">
			<th scope="row">
				<label for="display_connected_tours"><?php esc_html_e( 'Display Connected Tours', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input type="checkbox" {{#if display_connected_tours}} checked="checked" {{/if}} name="display_connected_tours" />
				<small><?php esc_html_e( 'This will replace the related accommodation with the connected tours instead.', 'tour-operator' ); ?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Returns thedisplay connected tours boolean
	 */
	public function display_connected_tours() {
		return $this->display_connected_tours;
	}

	/**
	 * A filter to set the content area to a small column on single
	 */
	function entry_class( $classes ) {
		global $post;

		if ( is_main_query() && is_singular( $this->slug ) ) {
			$classes[] = 'col-xs-12 col-sm-12 col-md-7';
		}

		return $classes;
	}

	/**
	 * Adds in additional info for the price custom field
	 */
	public function price_filter( $html = '', $meta_key = false, $value = false, $before = '', $after = '' ) {
		if ( get_post_type() === 'accommodation' && 'price' === $meta_key ) {
			$price_type    = get_post_meta( get_the_ID(), 'price_type', true );
			$value         = preg_replace( '/[^0-9,.]/', '', $value );
			$value         = ltrim( $value, '.' );
			$value         = str_replace( ',', '', $value );
			$value         = number_format( (int) $value, 2 );
			$tour_operator = tour_operator();
			$currency      = '';

			if ( is_object( $tour_operator ) && isset( $tour_operator->options['general'] ) && is_array( $tour_operator->options['general'] ) ) {
				if ( isset( $tour_operator->options['general']['currency'] ) && ! empty( $tour_operator->options['general']['currency'] ) ) {
					$currency = $tour_operator->options['general']['currency'];
					$currency = '<span class="currency-icon ' . mb_strtolower( $currency ) . '">' . $currency . '</span>';
				}
			}

			switch ( $price_type ) {
				case 'per_person_per_night':
				case 'per_person_sharing':
				case 'per_person_sharing_per_night':
					$value = $currency . $value . ' ' . ucwords( str_replace( '_', ' ', $price_type ) );
					break;

				case 'total_percentage':
					$value .= '% ' . esc_html__( 'Off', 'tour-operator' );
					$before = str_replace( esc_html__( 'From price', 'tour-operator' ), '', $before );
					break;

				case 'none':
				default:
					$value = $currency . $value;
					break;
			}

			$html = $before . $value . $after;
		}

		return $html;
	}

	/**
	 * Filter and make the star ratings
	 */
	public function rating( $html = '', $meta_key = false, $value = false, $before = '', $after = '' ) {
		if ( get_post_type() === 'accommodation' && 'rating' === $meta_key ) {
			$ratings_array = array();
			$counter       = 5;
			$html          = '';
			if ( 0 !== (int) $value ) {
				while ( $counter > 0 ) {
					if ( (int) $value > 0 ) {
						$ratings_array[] = '<i class="fa fa-star"></i>';
					} else {
						$ratings_array[] = '<i class="fa fa-star-o"></i>';
					}

					$counter --;
					$value --;
				}
				$rating_type        = get_post_meta( get_the_ID(), 'rating_type', true );
				$rating_description = '';

				if ( false !== $rating_type && '' !== $rating_type && esc_html__( 'Unspecified', 'tour-operator' ) !== $rating_type ) {
					$rating_description = ' <small>(' . $rating_type . ')</small>';
				}
				$html = $before . implode( '', $ratings_array ) . $rating_description . $after;
			}
		}
		return $html;
	}

	/**
	 * Adds our navigation links to the accommodation single post
	 *
	 * @param $page_links array
	 *
	 * @return $page_links array
	 */
	public function page_links( $page_links ) {
		if ( is_singular( 'accommodation' ) ) {
			$this->page_links = $page_links;

			$this->get_map_link();
			$this->get_unit_page_links();
			$this->get_facility_link();
			$this->get_gallery_link();
			$this->get_videos_link();

			$this->get_related_specials_link();
			$this->get_related_reviews_link();
			$this->get_related_accommodation_link();
			$this->get_related_tours_link();
			$this->get_related_posts_link();

			$page_links = $this->page_links;
		}

		return $page_links;
	}

	/**
	 * Tests for the Unit links and adds them to the $page_links variable
	 */
	public function get_unit_page_links() {
		$links = false;
		if ( lsx_to_accommodation_has_rooms() ) {
			$return = false;

			foreach ( $this->unit_types as $type_key => $type_label ) {
				if ( lsx_to_accommodation_check_type( $type_key ) ) {
					$this->page_links[ $type_key . 's' ] = lsx_to_get_post_type_section_title( 'accommodation', $type_key . 's', $type_label . 's' );
				}
			}
		}
	}

	/**
	 * Tests for the Facilities and returns a link for the section
	 */
	public function get_facility_link() {
		$facilities = wp_get_object_terms( get_the_ID(), 'facility' );

		if ( ! empty( $facilities ) && ! is_wp_error( $facilities ) ) {
			$this->page_links['facilities'] = esc_html__( 'Facilities', 'tour-operator' );
		}
	}

	/**
	 * Tests for the Google Map and returns a link for the section
	 */
	public function get_map_link() {
		if ( function_exists( 'lsx_to_has_map' ) && lsx_to_has_map() ) {
			$this->page_links['accommodation-map'] = esc_html__( 'Map', 'tour-operator' );
		}
	}

	/**
	 * Tests for the Gallery and returns a link for the section
	 */
	public function get_gallery_link() {
		$gallery_ids    = get_post_meta( get_the_ID(), 'gallery', false );
		$envira_gallery = get_post_meta( get_the_ID(), 'envira_gallery', true );

		if ( ( ! empty( $gallery_ids ) && is_array( $gallery_ids ) ) || ( function_exists( 'envira_gallery' ) && ! empty( $envira_gallery ) && false === lsx_to_enable_envira_banner() ) ) {
			if ( function_exists( 'envira_gallery' ) && ! empty( $envira_gallery ) && false === lsx_to_enable_envira_banner() ) {
				// Envira Gallery
				$this->page_links['gallery'] = esc_html__( 'Gallery', 'tour-operator' );
				return;
			} else {
				if ( function_exists( 'envira_dynamic' ) ) {
					// Envira Gallery - Dynamic
					$this->page_links['gallery'] = esc_html__( 'Gallery', 'tour-operator' );
					return;
				} else {
					// WordPress Gallery
					$this->page_links['gallery'] = esc_html__( 'Gallery', 'tour-operator' );
					return;
				}
			}
		}
	}

	/**
	 * Tests for the Videos and returns a link for the section
	 */
	public function get_videos_link() {
		$videos_id = false;

		if ( class_exists( 'Envira_Videos' ) ) {
			$videos_id = get_post_meta( get_the_ID(), 'envira_video', true );
		}

		if ( empty( $videos_id ) && function_exists( 'lsx_to_videos' ) ) {
			$videos_id = get_post_meta( get_the_ID(), 'videos', true );
		}

		if ( ! empty( $videos_id ) ) {
			$this->page_links['videos'] = esc_html__( 'Videos', 'tour-operator' );
		}
	}

	/**
	 * Tests for the Related Reviews and returns a link for the section
	 */
	public function get_related_specials_link() {
		$connected_specials = get_post_meta( get_the_ID(), 'special_to_accommodation', false );

		if ( post_type_exists( 'special' ) && is_array( $connected_specials ) && ! empty( $connected_specials ) ) {
			$connected_specials = new \WP_Query(
				array(
					'post_type'      => 'special',
					'post__in'       => $connected_specials,
					'post_status'    => 'publish',
					'nopagin'        => true,
					'posts_per_page' => '-1',
					'fields'         => 'ids',
				)
			);

			$connected_specials = $connected_specials->posts;

			if ( is_array( $connected_specials ) && ! empty( $connected_specials ) ) {
				$this->page_links['special'] = esc_html__( 'Specials', 'tour-operator' );
			}
		}
	}

	/**
	 * Tests for the Related Reviews and returns a link for the section
	 */
	public function get_related_reviews_link() {
		$connected_reviews = get_post_meta( get_the_ID(), 'review_to_accommodation', false );

		if ( post_type_exists( 'review' ) && is_array( $connected_reviews ) && ! empty( $connected_reviews ) ) {
			$connected_reviews = new \WP_Query(
				array(
					'post_type'      => 'review',
					'post__in'       => $connected_reviews,
					'post_status'    => 'publish',
					'nopagin'        => true,
					'posts_per_page' => '-1',
					'fields'         => 'ids',
				)
			);

			$connected_reviews = $connected_reviews->posts;

			if ( is_array( $connected_reviews ) && ! empty( $connected_reviews ) ) {
				$this->page_links['review'] = esc_html__( 'Reviews', 'tour-operator' );
			}
		}
	}

	/**
	 * Tests for the Related Accommodation and returns a link for the section
	 */
	public function get_related_accommodation_link() {
		$taxonomy  = 'travel-style';
		$post_type = 'accommodation';

		$filters = array();

		$filters['post_type']      = $post_type;
		$filters['posts_per_page'] = 15;
		$filters['post__not_in']   = array( get_the_ID() );
		$filters['orderby']        = 'rand';

		$terms = wp_get_object_terms( get_the_ID(), $taxonomy );

		if ( is_array( $terms ) && ! empty( $terms ) ) {
			$filters['tax_query'] = array(
				array(
					'taxonomy' => $taxonomy,
					'field'    => 'slug',
					'terms'    => array(),
				),
			);

			foreach ( $terms as $term ) {
				$filters['tax_query'][0]['terms'][] = $term->slug;
			}
		}

		$related_query = new \WP_Query( $filters );

		if ( $related_query->have_posts() ) {
			$this->page_links['related-items'] = esc_html__( 'Accommodation', 'tour-operator' );
		}
	}

	/**
	 * Tests for the Related Tours and returns a link for the section
	 */
	public function get_related_tours_link() {
		$connected_tours = get_post_meta( get_the_ID(), 'tour_to_accommodation', false );

		if ( lsx_to_accommodation_display_connected_tours() && is_array( $connected_tours ) && ! empty( $connected_tours ) ) {
			$connected_tours = new \WP_Query(
				array(
					'post_type'      => 'tour',
					'post__in'       => $connected_tours,
					'post_status'    => 'publish',
					'nopagin'        => true,
					'posts_per_page' => '-1',
					'fields'         => 'ids',
				)
			);

			$connected_tours = $connected_tours->posts;

			if ( is_array( $connected_tours ) && ! empty( $connected_tours ) ) {
				$this->page_links['tours'] = esc_html__( 'Tours', 'tour-operator' );
			}
		}
	}

	/**
	 * Tests for the Related Posts and returns a link for the section
	 */
	public function get_related_posts_link() {
		$connected_posts = get_post_meta( get_the_ID(), 'post_to_accommodation', false );

		if ( is_array( $connected_posts ) && ! empty( $connected_posts ) ) {
			$connected_posts = new \WP_Query(
				array(
					'post_type'      => 'post',
					'post__in'       => $connected_posts,
					'post_status'    => 'publish',
					'nopagin'        => true,
					'posts_per_page' => '-1',
					'fields'         => 'ids',
				)
			);

			$connected_posts = $connected_posts->posts;

			if ( is_array( $connected_posts ) && ! empty( $connected_posts ) ) {
				$this->page_links['posts'] = esc_html__( 'Posts', 'tour-operator' );
			}
		}
	}

}
