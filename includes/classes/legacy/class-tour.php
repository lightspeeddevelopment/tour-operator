<?php
/**
 * Tour Post Type Class
 *
 * @package   Tour
 * @author    LightSpeed
 * @license   GPL3
 * @link
 * @copyright 2016 LightSpeedDevelopment
 */

namespace lsx\legacy;

/**
 * Main plugin class.
 *
 * @package Tour
 * @author  LightSpeed
 */
class Tour {

	/**
	 * The slug for this plugin
	 *
	 * @since 1.0.0
	 * @var      string
	 */
	protected $slug = 'tour';

	/**
	 * Holds class instance
	 *
	 * @since 1.0.0
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Holds class instance
	 *
	 * @since 1.0.0
	 * @var      object
	 */
	public $search_fields = false;

	/**
	 * Holds the $page_links array while its being built on the single
	 * accommodation page.
	 *
	 * @since 0.0.1
	 * @var      array
	 */
	public $page_links = false;

	/**
	 * Holds the meal plan options
	 *
	 * @var array
	 */
	public $room_basis = array();

	/**
	 * Holds the meal plan options
	 *
	 * @var array
	 */
	public $drinks_basis = array();

	/**
	 * Initialize the plugin by setting localization, filters, and
	 * administration functions.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function __construct() {
		$this->options = get_option( '_lsx-to_settings', false );

		if ( false !== $this->options && isset( $this->options[ $this->slug ] ) && ! empty( $this->options[ $this->slug ] ) ) {
			$this->options = $this->options[ $this->slug ];
		} else {
			$this->options = false;
		}

		$this->room_basis = array(
			'None'                                 => esc_html__( 'None', 'tour-operator' ),
			'BedAndBreakfast'                      => esc_html__( 'B&amp;B: Bed and Breakfast', 'tour-operator' ),
			'RoomOnly'                             => esc_html__( 'Room Only', 'tour-operator' ),
			'SelfCatering'                         => esc_html__( 'Self Catering', 'tour-operator' ),
			'Lunch'                                => esc_html__( 'Lunch', 'tour-operator' ),
			'Dinner'                               => esc_html__( 'Dinner', 'tour-operator' ),
			'LunchAndDinner'                       => esc_html__( 'Lunch and Dinner', 'tour-operator' ),
			'BedBreakfastAndLunch'                 => esc_html__( 'Bed, Breakfast and Lunch', 'tour-operator' ),
			'DinnerBedAndBreakfast'                => esc_html__( 'Dinner, Bed and Breakfast', 'tour-operator' ),
			'HalfBoard'                            => esc_html__( 'Half Board - Dinner, Bed and Breakfast', 'tour-operator' ),
			'DinnerBedBreakfastAndActivities'      => esc_html__( 'Half Board Plus - Dinner, Bed, Breakfast and Activities', 'tour-operator' ),
			'DinnerBedBreakfastAndLunch'           => esc_html__( 'Full Board - Dinner, Bed, Breakfast and Lunch', 'tour-operator' ),
			'DinnerBedBreakfastLunchAndActivities' => esc_html__( 'Full Board Plus -  Dinner, Bed, Breakfast, Lunch and Activities', 'tour-operator' ),
			'AllInclusiveBedAndAllMeals'           => esc_html__( 'All Inclusive - Bed and All Meals', 'tour-operator' ),
			'FullyInclusive'                       => esc_html__( 'Fully Inclusive - Bed, All Meals, Fees and Activities', 'tour-operator' ),
			'ExclusiveClubPremierBenefits'         => esc_html__( 'Premier - Executive Club / Premier Benefits', 'tour-operator' ),
		);

		$this->drinks_basis = array(
			'None'                => esc_html__( 'None', 'tour-operator' ),
			'TeaCoffee'           => esc_html__( 'Tea and Coffee Only', 'tour-operator' ),
			'DrinksSoft'          => esc_html__( 'Tea, Coffee and Soft Drinks Only', 'tour-operator' ),
			'DrinksLocalBrands'   => esc_html__( 'All Local Brands (Spirits, Wine and Beers)', 'tour-operator' ),
			'DrinksExclSpirits'   => esc_html__( 'All Local Brands (excl Spirits)', 'tour-operator' ),
			'DrinksExclChampagne' => esc_html__( 'All Drinks (excl Champagne)', 'tour-operator' ),
			'DrinksExclPremium'   => esc_html__( 'All Drinks (excl Premium Brands)', 'tour-operator' ),
			'AllDrinks'           => esc_html__( 'All Drinks', 'tour-operator' ),
		);

		// activate property post type.
		add_action( 'init', array( $this, 'set_vars' ) );

		add_filter( 'lsx_to_entry_class', array( $this, 'entry_class' ) );

		add_filter( 'lsx_to_search_fields', array( $this, 'single_fields_indexing' ) );

		include 'class-itinerary-query.php';

		add_action( 'lsx_to_framework_tour_tab_general_settings_bottom', array( $this, 'general_settings' ), 10, 1 );

		add_filter( 'lsx_to_itinerary_class', array( $this, 'itinerary_class' ) );
		add_filter( 'lsx_to_itinerary_needs_read_more', array( $this, 'itinerary_needs_read_more' ) );

		$this->is_wetu_active = false;

		add_filter( 'lsx_to_custom_field_query', array( $this, 'price_filter' ), 10, 5 );

		add_filter( 'lsx_to_custom_field_query', array( $this, 'rating' ), 10, 5 );

		add_action( 'lsx_to_modal_meta', array( $this, 'content_meta' ) );

		add_filter( 'lsx_to_page_navigation', array( $this, 'page_links' ) );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0.0
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Test to see if wetu is active
	 *
	 * @return    null
	 */
	public function set_vars() {
		if ( class_exists( 'WETU_Importer' ) ) {
			$this->is_wetu_active = true;
		}
	}

	/**
	 * Adds the tour specific options
	 */
	public function general_settings() {
		?>
		<tr class="form-field">
			<th scope="row">
				<label for="description"><?php esc_html_e( 'Compress Itineraries', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input type="checkbox" {{#if shorten_itinerary}}
					   checked="checked" {{/if}} name="shorten_itinerary" />
				<small><?php esc_html_e( 'If you have many Itinerary entries on your tours, then you may want to shorten the length of the page with a "read more" button.', 'tour-operator' ); ?></small>
			</td>
		</tr>
		<tr class="form-field-wrap">
			<th scope="row">
				<label for="expiration_status"> <?php esc_html_e( 'Expiration Status', 'tour-operator' ); ?></label>
			</th>
			<td>
				<select value="{{expiration_status}}" name="expiration_status">
					<option value="draft" {{#is expiration_status value=""}}selected="selected"{{/is}} {{#is expiration_status value="draft"}} selected="selected"{{/is}}><?php esc_html_e( 'Draft', 'tour-operator' ); ?></option>
					<option value="delete" {{#is expiration_status value="delete"}} selected="selected"{{/is}}><?php esc_html_e( 'Delete', 'tour-operator' ); ?></option>
					<option value="private" {{#is expiration_status value="private"}} selected="selected"{{/is}}><?php esc_html_e( 'Private', 'tour-operator' ); ?></option>
				</select>
			</td>
		</tr>

		<?php
	}

	/**
	 * Sets up the "post relations"
	 *
	 * @since 1.0.0
	 * @return    object    A single instance of this class.
	 */
	public function single_fields_indexing( $search_fields ) {
		$search_fields['itinerary'] = array(
			'destination_to_tour',
			'activity_to_tour',
			'accommodation_to_tour',
		);

		return $search_fields;

	}

	/**
	 * A filter to set the content area to a small column on single
	 */
	public function entry_class( $classes ) {
		global $lsx_to_archive;

		if ( 1 !== $lsx_to_archive ) {
			$lsx_to_archive = false;
		}

		if ( is_main_query() && is_singular( $this->slug ) && false === $lsx_to_archive ) {
			$classes[] = 'col-xs-12 col-sm-12 col-md-7';
		}

		return $classes;
	}

	/**
	 * returns the itinerary metabox fields
	 */
	public function itinerary_fields() {
		$fields = array();

		$fields[] = array(
			'id'   => 'title',
			'name' => esc_html__( 'Title', 'tour-operator' ),
			'type' => 'text',
		);

		if ( ! class_exists( 'LSX_Banners' ) ) {
			$fields[] = array(
				'id'   => 'tagline',
				'name' => esc_html__( 'Tagline', 'tour-operator' ),
				'type' => 'text',
			);
		}

		$fields[] = array(
			'id'      => 'description',
			'name'    => esc_html__( 'Description', 'tour-operator' ),
			'type'    => 'wysiwyg',
			'options' => array(
				'editor_height' => '100',
			),
		);

		$fields[] = array(
			'id'        => 'featured_image',
			'name'      => esc_html__( 'Featured Image', 'tour-operator' ),
			'type'      => 'image',
			'show_size' => false,
		);

		$fields = apply_filters( 'lsx_to_tours_itinerary_fields', $fields );

		if ( post_type_exists( 'accommodation' ) ) {
			$fields[] = array(
				'id'         => 'accommodation_to_tour',
				'name'       => esc_html__( 'Accommodation related with this itinerary', 'tour-operator' ),
				'type'       => 'post_select',
				'use_ajax'   => false,
				'query'      => array(
					'post_type'      => 'accommodation',
					'nopagin'        => true,
					'posts_per_page' => '-1',
					'orderby'        => 'title',
					'order'          => 'ASC',
				),
				'repeatable' => true,
				'sortable'   => true,
				'allow_none' => true,
				'cols'       => 12,
			);
		}

		if ( post_type_exists( 'activity' ) ) {
			$fields[] = array(
				'id'         => 'activity_to_tour',
				'name'       => esc_html__( 'Activities related with this itinerary', 'tour-operator' ),
				'type'       => 'post_select',
				'use_ajax'   => false,
				'query'      => array(
					'post_type'      => 'activity',
					'nopagin'        => true,
					'posts_per_page' => '-1',
					'orderby'        => 'title',
					'order'          => 'ASC',
				),
				'repeatable' => true,
				'sortable'   => true,
				'allow_none' => true,
				'cols'       => 12,
			);
		}

		if ( post_type_exists( 'destination' ) ) {
			$fields[] = array(
				'id'         => 'destination_to_tour',
				'name'       => esc_html__( 'Destinations related with this itinerary', 'tour-operator' ),
				'type'       => 'post_select',
				'use_ajax'   => false,
				'query'      => array(
					'post_type'      => 'destination',
					'nopagin'        => true,
					'posts_per_page' => '-1',
					'orderby'        => 'title',
					'order'          => 'ASC',
				),
				'repeatable' => true,
				'sortable'   => true,
				'allow_none' => true,
				'cols'       => 12,
			);
		}

		if ( $this->is_wetu_active ) {
			$fields[] = array(
				'id'      => 'included',
				'name'    => esc_html__( 'Included', 'tour-operator' ),
				'type'    => 'wysiwyg',
				'options' => array(
					'editor_height' => '100',
				),
			);

			$fields[] = array(
				'id'      => 'excluded',
				'name'    => esc_html__( 'Excluded', 'tour-operator' ),
				'type'    => 'wysiwyg',
				'options' => array(
					'editor_height' => '100',
				),
			);
		}
		$fields[] = array(
			'id'      => 'drinks_basis',
			'name'    => esc_html__( 'Drinks Basis', 'tour-operator' ),
			'type'    => 'select',
			'options' => $this->drinks_basis,
		);
		$fields[] = array(
			'id'      => 'room_basis',
			'name'    => esc_html__( 'Room Basis', 'tour-operator' ),
			'type'    => 'select',
			'options' => $this->room_basis,
		);
		return $fields;
	}

	/**
	 * returns the itinerary metabox fields
	 */
	public function itinerary_class( $classes ) {
		global $tour_itinerary;

		if ( false !== $this->options && isset( $this->options['shorten_itinerary'] ) ) {
			if ( $tour_itinerary->index > 3 ) {
				$classes[] = 'hidden';
			}
		}

		return $classes;
	}

	/**
	 * Outputs the read more button if needed
	 */
	public function itinerary_needs_read_more( $return ) {
		if ( false !== $this->options && isset( $this->options['shorten_itinerary'] ) ) {
			$return = true;
		}

		return $return;
	}

	/**
	 * Adds in additional info for the price custom field
	 */
	public function price_filter( $html = '', $meta_key = false, $value = false, $before = '', $after = '' ) {
		if ( get_post_type() === 'tour' && 'price' === $meta_key ) {
			$value         = preg_replace( '/[^0-9,.]/', '', $value );
			$value         = ltrim( $value, '.' );
			$value         = str_replace( ',', '', $value );
			$value         = number_format( (int) $value, 2 );
			$tour_operator = tour_operator();
			$currency      = '';

			if ( is_object( $tour_operator ) && isset( $tour_operator->options['general'] ) && is_array( $tour_operator->options['general'] ) ) {

				if ( isset( $tour_operator->options['general']['currency'] ) && ! empty( $tour_operator->options['general']['currency'] ) ) {
					$currency = $tour_operator->options['general']['currency'];
					$currency = '<span class="currency-icon ' . strtolower( $currency ) . '">' . $currency . '</span>';
				}
			}

			$value = $currency . $value;
			$html  = $before . $value . $after;
		}
		return $html;
	}

	/**
	 * Filter and make the star ratings
	 */
	public function rating( $html = '', $meta_key = false, $value = false, $before = '', $after = '' ) {
		if ( get_post_type() === 'tour' && 'rating' === $meta_key ) {
			$ratings_array = false;
			$counter       = 5;

			while ( $counter > 0 ) {
				if ( $value >= 0 ) {
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

		return $html;
	}

	/**
	 * Outputs the tour meta on the modal
	 */
	public function content_meta() {
		if ( 'tour' === get_post_type() ) {
			?>
			<?php
				$meta_class = 'lsx-to-meta-data lsx-to-meta-data-';

				lsx_to_price( '<span class="' . $meta_class . 'price"><span class="lsx-to-meta-data-key">' . esc_html__( 'From price', 'tour-operator' ) . ':</span> ', '</span>' );
				lsx_to_duration( '<span class="' . $meta_class . 'duration"><span class="lsx-to-meta-data-key">' . esc_html__( 'Duration', 'tour-operator' ) . ':</span> ', '</span>' );
				the_terms( get_the_ID(), 'travel-style', '<span class="' . $meta_class . 'style"><span class="lsx-to-meta-data-key">' . esc_html__( 'Travel Style', 'tour-operator' ) . ':</span> ', ', ', '</span>' );
				lsx_to_connected_countries( '<span class="' . $meta_class . 'destinations"><span class="lsx-to-meta-data-key">' . esc_html__( 'Destinations', 'tour-operator' ) . ':</span> ', '</span>', true );

			if ( function_exists( 'lsx_to_connected_activities' ) ) {
				lsx_to_connected_activities( '<span class="' . $meta_class . 'activities"><span class="lsx-to-meta-data-key">' . esc_html__( 'Activities', 'tour-operator' ) . ':</span> ', '</span>' );
			}
			?>
			<?php
		}
	}

	/**
	 * Adds our navigation links to the accommodation single post
	 *
	 * @param $page_links array
	 *
	 * @return $page_links array
	 */
	public function page_links( $page_links ) {
		if ( is_singular( 'tour' ) ) {
			$this->page_links = $page_links;

			$this->get_map_link();
			$this->get_when_to_go();
			$this->get_itinerary_link();
			$this->get_include_link();
			$this->get_gallery_link();
			$this->get_videos_link();

			$this->get_related_specials_link();
			$this->get_related_reviews_link();
			$this->get_related_tours_link();
			$this->get_related_posts_link();

			$page_links = $this->page_links;
		}

		return $page_links;
	}

	/**
	 * Tests for the When To Go links and adds it to the $page_links variable
	 */
	public function get_when_to_go() {
		$best_time_to_visit = get_post_meta( get_the_ID(), 'best_time_to_visit', true );

		if ( ! empty( $best_time_to_visit ) && is_array( $best_time_to_visit ) ) {
			$this->page_links['when-to-go'] = esc_html__( 'When to Go', 'tour-operator' );
		}
	}

	/**
	 * Tests for the Itinerary links and adds it to the $page_links variable
	 */
	public function get_itinerary_link() {
		if ( lsx_to_has_itinerary() ) {
			$this->page_links['itinerary'] = esc_html__( 'Itinerary', 'tour-operator' );
		}
	}

	/**
	 * Tests for the Included / Not Included Block $page_links variable
	 */
	public function get_include_link() {
		$tour_included     = lsx_to_included( '', '', false );
		$tour_not_included = lsx_to_not_included( '', '', false );

		if ( null !== $tour_included || null !== $tour_not_included ) {
			$this->page_links['included-excluded'] = esc_html__( 'Included / Excluded', 'tour-operator' );
		}
	}

	/**
	 * Tests for the Google Map and returns a link for the section
	 */
	public function get_map_link() {
		if ( function_exists( 'lsx_to_has_map' ) && lsx_to_has_map() ) {
			$this->page_links['tour-map'] = esc_html__( 'Map', 'tour-operator' );
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
		$connected_specials = get_post_meta( get_the_ID(), 'special_to_tour', false );

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

			if ( is_array( $connected_specials ) && ! empty( $connected_specials ) ) {
				$this->page_links['special'] = esc_html__( 'Specials', 'tour-operator' );
			}
		}
	}

	/**
	 * Tests for the Related Reviews and returns a link for the section
	 */
	public function get_related_reviews_link() {
		$connected_reviews = get_post_meta( get_the_ID(), 'review_to_tour', false );

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
	 * Tests for the Related Posts and returns a link for the section
	 */
	public function get_related_posts_link() {
		$connected_posts = get_post_meta( get_the_ID(), 'post_to_tour', false );

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

	/**
	 * Tests for the Related Tours and returns a link for the section
	 */
	public function get_related_tours_link() {
		$taxonomy  = 'travel-style';
		$post_type = 'tour';

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
			$this->page_links['related-items'] = esc_html__( 'Tours', 'tour-operator' );
		}
	}

}
