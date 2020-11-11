<?php
/**
 * Destination Class
 *
 * @package   Destination
 * @author    LightSpeed
 * @license   GPL3
 * @link
 * @copyright 2016 LightSpeedDevelopment
 */

namespace lsx\legacy;

/**
 * Main plugin class.
 *
 * @package Destination
 * @author  LightSpeed
 */
class Destination {

	/**
	 * The slug for this plugin
	 *
	 * @since 1.0.0
	 * @var      string
	 */
	protected $slug = 'destination';

	/**
	 * Holds class instance
	 *
	 * @since 1.0.0
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Holds the option screen prefix
	 *
	 * @since 1.0.0
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

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
	 * @since  1.0.0
	 * @access private
	 */
	private function __construct() {
		$this->options = get_option( '_lsx-to_settings', false );

		if ( ! is_admin() ) {
			add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
		}
		add_filter( 'lsx_to_entry_class', array( $this, 'entry_class' ) );

		add_action( 'lsx_to_map_meta', array( $this, 'content_meta' ) );
		add_action( 'lsx_to_modal_meta', array( $this, 'content_meta' ) );

		add_filter( 'lsx_to_page_navigation', array( $this, 'page_links' ) );

		add_filter( 'lsx_to_parents_only', array( $this, 'filter_countries' ) );

		add_action( 'lsx_to_framework_destination_tab_general_settings_bottom', array( $this, 'general_settings' ), 10, 1 );
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
	 * Set the main query to pull through only the top level destinations.
	 *
	 * @param $query object
	 *
	 * @return object
	 */
	function pre_get_posts( $query ) {
		if ( $query->is_main_query() && $query->is_post_type_archive( $this->slug ) ) {

			$query->set( 'post_parent', '0' );

			$sticky_posts = '';
			if ( is_array( $this->options ) && isset( $this->options[ $this->slug ] ) && isset( $this->options[ $this->slug ]['sticky_archives'] ) ) {
				$sticky_posts = $this->options[ $this->slug ]['sticky_archives'];
				switch ( $sticky_posts ) {
					case 'sticky-only':
						$meta_query = array(
							array(
								'key'     => 'sticky_order',
								'value'   => '0',
								'compare' => '!=',
							),
						);
						$query->set( 'meta_query', $meta_query );
						$query->set( 'meta_key', 'sticky_order' );
						$query->set( 'orderby', 'meta_value_num' );
						$query->set( 'order', 'DESC' );
						break;

					case 'sticky-first':
						$query->set( 'meta_key', 'sticky_order' );
						$query->set(
							'orderby',
							array(
								'meta_value_num' => 'DESC',
								'menu_order'     => 'ASC',
							)
						);
						break;

					case 'sticky-last':
						$query->set( 'meta_key', 'sticky_order' );
						$query->set(
							'orderby',
							array(
								'menu_order'     => 'DESC',
								'meta_value_num' => 'ASC',
							)
						);
						break;

					default:
						break;
				}
			}
		}

		return $query;
	}

	/**
	 * A filter to set the content area to a small column on single
	 *
	 * @param $classes array
	 *
	 * @return array
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
	 * Outputs the destination meta
	 */
	public function content_meta() {
		if ( 'destination' === get_post_type() ) { ?>
			<?php
				$meta_class = 'lsx-to-meta-data lsx-to-meta-data-';

				the_terms( get_the_ID(), 'travel-style', '<span class="' . $meta_class . 'style"><span class="lsx-to-meta-data-key">' . esc_html__( 'Travel Style', 'tour-operator' ) . ':</span> ', ', ', '</span>' );

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
		if ( is_singular( 'destination' ) ) {
			$this->page_links = $page_links;

			$this->get_map_link();
			$this->get_travel_info_link();
			$this->get_region_link();
			$this->get_gallery_link();
			$this->get_videos_link();

			$this->get_related_tours_link();

			if ( ! lsx_to_item_has_children( get_the_ID(), 'destination' ) ) {
				$this->get_related_accommodation_link();
				$this->get_related_activities_link();
			}

			$this->get_related_specials_link();
			$this->get_related_reviews_link();
			$this->get_related_posts_link();

			$page_links = $this->page_links;
		}

		return $page_links;
	}


	/**
	 * Adds the Travel Information to the $page_links variable
	 */
	public function get_travel_info_link() {
		$electricity = get_post_meta( get_the_ID(), 'electricity', true );
		$banking     = get_post_meta( get_the_ID(), 'banking', true );
		$cuisine     = get_post_meta( get_the_ID(), 'cuisine', true );
		$climate     = get_post_meta( get_the_ID(), 'climate', true );
		$transport   = get_post_meta( get_the_ID(), 'transport', true );
		$dress       = get_post_meta( get_the_ID(), 'dress', true );

		if ( ! empty( $electricity ) || ! empty( $banking ) || ! empty( $cuisine ) || ! empty( $climate ) || ! empty( $transport ) || ! empty( $dress ) ) {
			$this->page_links['travel-info'] = esc_html__( 'Information', 'tour-operator' );
		}
	}

	/**
	 * Adds the Region to the $page_links variable
	 */
	public function get_region_link() {
		if ( lsx_to_item_has_children( get_the_ID(), 'destination' ) ) {
			$this->page_links['regions'] = esc_html__( 'Regions', 'tour-operator' );
		}
	}

	/**
	 * Tests Regions adds them to the $page_links variable
	 */
	public function get_region_links() {
		$tour_operator = tour_operator();

		if ( have_posts() ) :
			if ( ! isset( $tour_operator->search )
				 || empty( $tour_operator->search )
				 || false === $tour_operator->search->options
				 || ! isset( $tour_operator->search->options['destination']['enable_search'] )
			) :
				while ( have_posts() ) :
					the_post();
					$slug                      = sanitize_title( the_title( '', '', false ) );
					$this->page_links[ $slug ] = the_title( '', '', false );
				endwhile;
			endif;
			wp_reset_query();
		endif;
	}

	/**
	 * Tests for the Google Map and returns a link for the section
	 */
	public function get_map_link() {
		if ( function_exists( 'lsx_to_has_map' ) && lsx_to_has_map() ) {
			if ( ! function_exists( 'lsx_to_has_destination_banner_map' ) || ! lsx_to_has_destination_banner_map() ) {
				$this->page_links['destination-map'] = esc_html__( 'Map', 'tour-operator' );
			}
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
	 * Tests for the Related Tours and returns a link for the section
	 */
	public function get_related_tours_link() {
		$connected_tours = get_post_meta( get_the_ID(), 'tour_to_destination', false );

		if ( post_type_exists( 'tour' ) && is_array( $connected_tours ) && ! empty( $connected_tours ) ) {
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
	 * Tests for the Related Accommodation and returns a link for the section
	 */
	public function get_related_accommodation_link() {
		$connected_accommodation = get_post_meta( get_the_ID(), 'accommodation_to_destination', false );

		if ( post_type_exists( 'accommodation' ) && is_array( $connected_accommodation ) && ! empty( $connected_accommodation ) ) {
			$connected_accommodation = new \WP_Query(
				array(
					'post_type'      => 'accommodation',
					'post__in'       => $connected_accommodation,
					'post_status'    => 'publish',
					'nopagin'        => true,
					'posts_per_page' => '-1',
					'fields'         => 'ids',
				)
			);

			$connected_accommodation = $connected_accommodation->posts;

			if ( is_array( $connected_accommodation ) && ! empty( $connected_accommodation ) ) {
				$this->page_links['accommodation'] = esc_html__( 'Accommodation', 'tour-operator' );
			}
		}
	}

	/**
	 * Tests for the Related Activities and returns a link for the section
	 */
	public function get_related_activities_link() {
		$connected_activities = get_post_meta( get_the_ID(), 'activity_to_destination', false );

		if ( post_type_exists( 'activity' ) && is_array( $connected_activities ) && ! empty( $connected_activities ) ) {
			$connected_activities = new \WP_Query(
				array(
					'post_type'      => 'activity',
					'post__in'       => $connected_activities,
					'post_status'    => 'publish',
					'nopagin'        => true,
					'posts_per_page' => '-1',
					'fields'         => 'ids',
				)
			);

			$connected_activities = $connected_activities->posts;

			if ( is_array( $connected_activities ) && ! empty( $connected_activities ) ) {
				$this->page_links['activities'] = esc_html__( 'Activities', 'tour-operator' );
			}
		}
	}

	/**
	 * Tests for the Related Reviews and returns a link for the section
	 */
	public function get_related_specials_link() {
		$connected_specials = get_post_meta( get_the_ID(), 'special_to_destination', false );

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
		$connected_reviews = get_post_meta( get_the_ID(), 'review_to_destination', false );

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
	 * Only return the upper lever countries
	 */
	public function filter_countries( $countries = array() ) {
		global $wpdb;

		if ( ! empty( $countries ) ) {
			$new_items           = array();
			$formatted_countries = implode( ',', $countries );

			// @codingStandardsIgnoreStart
			$results = $wpdb->get_results( "
				SELECT ID,post_parent
				FROM {$wpdb->posts}
				WHERE ID IN ({$formatted_countries})
			" );
			// @codingStandardsIgnoreEnd

			if ( ! empty( $results ) ) {
				foreach ( $results as $result ) {
					if ( 0 === $result->post_parent || '0' === $result->post_parent ) {
						$new_items[] = $result->ID;
					}
				}
				$countries = $new_items;
			}
		}

		return $countries;
	}

	/**
	 * Tests for the Related Posts and returns a link for the section
	 */
	public function get_related_posts_link() {
		$connected_posts = get_post_meta( get_the_ID(), 'post_to_destination', false );

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
	 * Displays the destination specific settings
	 *
	 * @param $post_type string
	 * @param $tab       string
	 *
	 * @return null
	 */
	public function general_settings() {
		if ( class_exists( 'LSX_Banners' ) ) {
			?>
		<tr class="form-field -wrap">
			<th scope="row">
				<label for="enable_banner_map"><?php esc_html_e( 'Display the map in the banner', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input type="checkbox" {{#if enable_banner_map}} checked="checked" {{/if}} name="enable_banner_map" />
			</td>
		</tr>
		<tr class="form-field -wrap">
			<th scope="row">
				<label for="disable_banner_map_cluster"><?php esc_html_e( 'Disable Banner Map Cluster', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input type="checkbox" {{#if disable_banner_map_cluster}} checked="checked" {{/if}} name="disable_banner_map_cluster" />
			</td>
		</tr>

			<?php
		}
	}
}
