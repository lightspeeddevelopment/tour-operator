<?php
/**
 * @package   Tour_Operator
 * @author    LightSpeed
 * @license   GPL3
 * @link
 * @copyright 2016 LightSpeed
 **/

namespace lsx\legacy;

use stdClass;

// Setup the post connections.
class Tour_Operator {

	/**
	 * Holds class instance
	 *
	 * @since 1.0.0
	 * @var      \lsx\Tour_Operator
	 */
	protected static $instance = null;

	/**
	 * Holds the array of options
	 *
	 * @since 1.0.0
	 * @var      array
	 */
	public $options = false;

	/**
	 * Holds the LSX_TO_Framework class
	 *
	 * @since 1.0.0
	 * @var      object
	 */
	public $framework = false;

	/**
	 * Holds the array of post_types
	 *
	 * @since 1.0.0
	 * @var      array
	 */
	public $base_post_types = array();

	/**
	 * Holds the array of post_types
	 *
	 * @since 1.0.0
	 * @var      array
	 */
	public $post_types = array();

	/**
	 * Holds the array of post_types_singular
	 *
	 * @since 1.0.0
	 * @var      array
	 */
	public $post_types_singular = array();

	/**
	 * Holds the array of taxonomies
	 *
	 * @since 1.0.0
	 * @var      array
	 */
	public $base_taxonomies = array();

	/**
	 * Holds the array of taxonomies
	 *
	 * @since 1.0.0
	 * @var      array
	 */
	public $taxonomies = array();
	/**
	 * Holds the array of taxonomies in Plural form.
	 *
	 * @since 1.0.0
	 * @var      array
	 */
	public $taxonomies_plural = array();

	/**
	 * Holds the array of active post_types
	 *
	 * @since 1.0.0
	 * @var      array
	 */
	public $active_post_types = array();

	/**
	 * Holds the array of connections from posts to posts
	 *
	 * @since 1.0.0
	 * @var      array
	 */
	public $connections = null;
	/**
	 * Holds the array of single fields.
	 *
	 * @since 1.0.0
	 * @var      array
	 */
	public $single_fields = null;

	/**
	 * Flags is out WETU Importer Plugin active.
	 *
	 * @since 1.0.0
	 * @var      array
	 */
	public $is_wetu_active = false;

	/**
	 * Holds an array of the post types you can assign Map Markers to.
	 *
	 * @since 1.1.5
	 *
	 * @var array
	 */
	public $map_post_type = array();

	/**
	 * Holds an array of the marker URLs
	 *
	 * @since 1.1.5
	 *
	 * @var object
	 */
	public $markers = false;

	/**
	 * Holds the Google API Key
	 *
	 * @var string
	 */
	public $google_api_key = false;

	/**
	 * Holds the textdomain slug.
	 *
	 * @var      array
	 */
	public $plugin_slug = 'tour-operator';

	/**
	 * Holds the Schema Class
	 *
	 * @var \lsx\legacy\Schema();
	 */
	public $schema = false;

	/**
	 * Initialize the plugin by setting localization, filters, and
	 * administration functions.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function __construct() {
		add_action( 'init', array( $this, 'disable_deprecated' ), 0 );
		add_action( 'admin_init', array( $this, 'compatible_version_check' ) );
		add_action( 'plugins_loaded', array( $this, 'trigger_schema' ), 10 );

		// Theme compatibility check.
		add_action( 'admin_notices', array( $this, 'compatible_theme_check' ) );
		add_action( 'wp_ajax_lsx_to_theme_notice_dismiss', array( $this, 'theme_notice_dismiss' ) );

		// Don't run anything else in the plugin, if we're on an incompatible PHP version.
		if ( ! self::compatible_version() ) {
			return;
		}

		// Start sort engine.
		new SCPO_Engine();

		new Post_Expirator();

		// Set the options.
		$this->options = get_option( '_lsx-to_settings', false );
		$this->set_vars();

		// Make TO last plugin to load.
		add_action( 'activated_plugin', array( $this, 'activated_plugin' ) );

		// Add our action to init to set up our vars first.
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'init', array( $this, 'require_post_type_classes' ), 1 );
		add_action( 'wp', array( $this, 'set_archive_layout' ) );
		// Allow extra tags and attributes to wp_kses_post().
		add_filter(
			'wp_kses_allowed_html',
			array(
				$this,
				'wp_kses_allowed_html',
			),
			10,
			2
		);
		// Allow extra protocols to wp_kses_post().
		add_filter(
			'kses_allowed_protocols',
			array(
				$this,
				'kses_allowed_protocols',
			)
		);
		// Allow extra style attributes to wp_kses_post().
		add_filter( 'safe_style_css', array( $this, 'safe_style_css' ) );

		// init admin object.
		$this->admin = new Admin();
		// init frontend object.
		$this->frontend = new Frontend();
		add_action(
			'lsx_to_content',
			array(
				$this->frontend->redirects,
				'content_part',
			),
			10,
			2
		);

		// init placeholders.
		$this->placeholders = new Placeholders( array_keys( $this->post_types ) );

		add_action( 'widgets_init', array( $this, 'register_widget' ) );

		// These need to run after the plugins have all been read.
		$this->lsx_banners = new Banner_Integration( array_keys( $this->post_types ), array_keys( $this->taxonomies ) );

		// Integrations.
		$this->lsx_to_search_integration();

		add_action(
			'admin_init',
			array(
				$this,
				'register_activation_hook_check',
			)
		);
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

	public function trigger_schema() {
		// Initiates the Schema.
		$this->schema = Schema::get_instance();
	}

	/**
	 * On plugin activation
	 *
	 * @since 1.0.0
	 */
	public static function register_activation_hook() {
		self::compatible_version_check_on_activation();

		if ( ! is_network_admin() && ! isset( $_GET['activate-multi'] ) ) {
			set_transient( '_tour_operators_flush_rewrite_rules', 1, 30 );
		}

		if ( ! is_network_admin() && ! isset( $_GET['activate-multi'] ) ) {
			set_transient( '_tour_operators_welcome_redirect', 1, 30 );
		}
	}

	/**
	 * On plugin activation (check)
	 *
	 * @since 1.0.0
	 */
	public function register_activation_hook_check() {
		if ( ! get_transient( '_tour_operators_flush_rewrite_rules' ) && ! get_transient( '_tour_operators_welcome_redirect' ) ) {
			return;
		}

		if ( get_transient( '_tour_operators_flush_rewrite_rules' ) ) {
			delete_transient( '_tour_operators_flush_rewrite_rules' );
			flush_rewrite_rules();
		}

		if ( get_transient( '_tour_operators_welcome_redirect' ) ) {
			delete_transient( '_tour_operators_welcome_redirect' );
			wp_safe_redirect( 'admin.php?page=lsx-to-settings&welcome-page=1' );
			exit();
		}
	}

	/**
	 * Disables any deprecated plugins.
	 *
	 * @return void
	 */
	public function disable_deprecated() {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		if ( defined( 'LSX_TO_MAPS_CORE' ) && is_plugin_active( plugin_basename( LSX_TO_MAPS_CORE ) ) ) {
			deactivate_plugins( plugin_basename( LSX_TO_MAPS_CORE ) );
		}
		if ( defined( 'LSX_TO_VIDEOS_CORE' ) && is_plugin_active( plugin_basename( LSX_TO_VIDEOS_CORE ) ) ) {
			deactivate_plugins( plugin_basename( LSX_TO_VIDEOS_CORE ) );
		}
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since 0.0.1
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'tour-operator', false, basename( LSX_TO_PATH ) . '/languages' );
	}

	/**
	 * Sets the variables for the class
	 */
	public function set_vars() {
		$this->post_types = array(
			'destination'   => esc_html__( 'Destinations', 'tour-operator' ),
			'accommodation' => esc_html__( 'Accommodation', 'tour-operator' ),
			'tour'          => esc_html__( 'Tours', 'tour-operator' ),
		);

		$this->post_types_singular = array(
			'destination'   => esc_html__( 'Destination', 'tour-operator' ),
			'accommodation' => esc_html__( 'Accommodation', 'tour-operator' ),
			'tour'          => esc_html__( 'Tour', 'tour-operator' ),
		);

		$this->base_post_types     = $this->post_types;
		$this->post_types          = apply_filters( 'lsx_to_post_types', $this->post_types );
		$this->post_types_singular = apply_filters( 'lsx_to_post_types_singular', $this->post_types_singular );
		$this->active_post_types   = array_keys( $this->post_types );

		$this->taxonomies = array(
			'travel-style'        => __( 'Travel Style', 'tour-operator' ),
			'accommodation-brand' => __( 'Brand', 'tour-operator' ),
			'accommodation-type'  => __( 'Accommodation Type', 'tour-operator' ),
			'facility'            => __( 'Facility', 'tour-operator' ),
			'continent'           => __( 'Continent', 'tour-operator' ),
			'region'              => __( 'Region', 'tour-operator' ),
		);

		$this->taxonomies_plural = array(
			'travel-style'        => __( 'Travel Styles', 'tour-operator' ),
			'accommodation-brand' => __( 'Brands', 'tour-operator' ),
			'accommodation-type'  => __( 'Accommodation Types', 'tour-operator' ),
			'facility'            => __( 'Facilities', 'tour-operator' ),
			'continent'           => __( 'Continents', 'tour-operator' ),
			'region'              => __( 'Regions', 'tour-operator' ),
		);

		$this->base_taxonomies   = $this->taxonomies;
		$this->taxonomies        = apply_filters( 'lsx_to_framework_taxonomies', $this->taxonomies );
		$this->taxonomies_plural = apply_filters( 'lsx_to_framework_taxonomies_plural', $this->taxonomies_plural );

		$this->set_map_vars();
	}

	/**
	 * Set the map related variables.
	 *
	 * @return void
	 */
	public function set_map_vars() {
		$this->map_post_types = array( 'accommodation', 'activity', 'destination' );
		$this->markers        = new \stdClass();

		if ( ( false !== $this->options && isset( $this->options['api']['googlemaps_key'] ) ) || defined( 'GOOGLEMAPS_API_KEY' ) ) {
			if ( ! defined( 'GOOGLEMAPS_API_KEY' ) ) {
				$this->google_api_key = $this->options['api']['googlemaps_key'];
			} else {
				$this->google_api_key = GOOGLEMAPS_API_KEY;
			}
		} else {
			$this->google_api_key = false;
		}

		if ( isset( $this->options['display']['googlemaps_marker'] ) && '' !== $this->options['display']['googlemaps_marker'] ) {
			$this->markers->default_marker = $this->options['display']['googlemaps_marker'];
		} else {
			$this->markers->default_marker = LSX_TO_URL . 'assets/img/markers/gmaps-mark.svg';
		}

		if ( isset( $this->options['display']['gmap_cluster_small'] ) && '' !== $this->options['display']['gmap_cluster_small'] ) {
			$this->markers->cluster_small = $this->options['display']['gmap_cluster_small'];
		} else {
			$this->markers->cluster_small = LSX_TO_URL . 'assets/img/markers/m1.png';
		}

		if ( isset( $this->options['display']['gmap_cluster_medium'] ) && '' !== $this->options['display']['gmap_cluster_medium'] ) {
			$this->markers->cluster_medium = $this->options['display']['gmap_cluster_medium'];
		} else {
			$this->markers->cluster_medium = LSX_TO_URL . 'assets/img/markers/m2.png';
		}

		if ( isset( $this->options['display']['gmap_cluster_large'] ) && '' !== $this->options['display']['gmap_cluster_large'] ) {
			$this->markers->cluster_large = $this->options['display']['gmap_cluster_large'];
		} else {
			$this->markers->cluster_large = LSX_TO_URL . 'assets/img/markers/m3.png';
		}

		if ( isset( $this->options['display']['gmap_marker_start'] ) && '' !== $this->options['display']['gmap_marker_start'] ) {
			$this->markers->start = $this->options['display']['gmap_marker_start'];
		} else {
			$this->markers->start = LSX_TO_URL . 'assets/img/markers/start-marker.png';
		}

		if ( isset( $this->options['display']['gmap_marker_end'] ) && '' !== $this->options['display']['gmap_marker_end'] ) {
			$this->markers->end = $this->options['display']['gmap_marker_end'];
		} else {
			$this->markers->end = LSX_TO_URL . 'assets/img/markers/end-marker.png';
		}
	}

	/**
	 * Sets the layout variable for the class.
	 */
	public function set_archive_layout() {
		$settings_tab = false;

		if ( is_post_type_archive( $this->active_post_types ) ) {
			$settings_tab = get_query_var( 'post_type' );
		} elseif ( is_singular( $this->active_post_types ) ) {
			$settings_tab = get_query_var( 'post_type' );
		} elseif ( is_tax( array_keys( $this->taxonomies ) ) ) {
			$taxonomy = get_query_var( 'taxonomy' );

			switch ( $taxonomy ) {
				case 'travel-style':
					$settings_tab = 'tour';
					break;

				case 'accommodation-type':
				case 'accommodation-brand':
				case 'facility':
					$settings_tab = 'accommodation';
					break;

				case 'continent':
				case 'region':
					$settings_tab = 'destination';
					break;
			}
		}

		$settings_tab = apply_filters( 'lsx_to_settings_current_tab', $settings_tab );

		if ( ! empty( $settings_tab ) ) {
			$archive_layout                  = '';
			$archive_list_layout_image_style = '';

			if ( isset( $this->options[ $settings_tab ] ) && isset( $this->options[ $settings_tab ]['grid_list_layout'] ) ) {
				$archive_layout = $this->options[ $settings_tab ]['grid_list_layout'];
			}
			if ( empty( $archive_layout ) ) {
				$archive_layout = 'list';
			}
			$archive_layout = apply_filters( 'lsx_to_archive_layout', $archive_layout, $settings_tab );

			if ( isset( $this->options[ $settings_tab ] ) && isset( $this->options[ $settings_tab ]['list_layout_image_style'] ) ) {
				$archive_list_layout_image_style = $this->options[ $settings_tab ]['list_layout_image_style'];
			}

			if ( empty( $archive_list_layout_image_style ) ) {
				$archive_list_layout_image_style = 'full-height';
			}

			$this->archive_layout = $archive_layout;

			$this->archive_list_layout_image_style = $archive_list_layout_image_style;
		}
	}

	/**
	 * Register the \lsx\legacy\Widget
	 */
	public function register_widget() {
		register_widget( 'lsx\legacy\Widget' );
		register_widget( 'lsx\legacy\Taxonomy_Widget' );
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since 0.0.1
	 */
	public function get_active_post_types() {
		return $this->active_post_types;
	}

	/**
	 * Requires the post type classes
	 *
	 * @since 0.0.1
	 */
	public function require_post_type_classes() {
		foreach ( $this->post_types as $post_type => $label ) {
			if ( class_exists( "lsx\\legacy\\{$post_type}" ) ) {
				$this->$post_type = call_user_func_array( array( "lsx\\legacy\\{$post_type}", 'get_instance' ), array() );
			}
		}

		$this->connections   = $this->create_post_connections();
		$this->single_fields = apply_filters( 'lsx_to_search_fields', array() );
	}

	/**
	 * Generates the post_connections used in the metabox fields
	 */
	public function create_post_connections() {
		$connections = array();
		$post_types  = apply_filters( 'lsx_to_post_types', $this->post_types );

		foreach ( $post_types as $key_a => $values_a ) {
			foreach ( $this->post_types as $key_b => $values_b ) {
				// Make sure we dont try connect a post type to itself.
				if ( $key_a !== $key_b ) {
					$connections[] = $key_a . '_to_' . $key_b;
				}
			}
		}

		return $connections;
	}

	/**
	 * Include the post type for the search integration
	 */
	public function lsx_to_search_integration() {
		add_filter( 'lsx_to_search_post_types', array( $this, 'post_types_filter' ) );
		add_filter( 'lsx_to_search_taxonomies', array( $this, 'taxonomies_filter' ) );
	}

	/**
	 * Adds our post types to an array via a filter.
	 *
	 * @param array $post_types List of posttypes being used.
	 *
	 * @return array Altered list of post types with added items.
	 */
	public function post_types_filter( $post_types ) {
		if ( is_array( $post_types ) ) {
			$post_types = array_merge( $post_types, $this->post_types );
		} else {
			$post_types = $this->post_types;
		}

		return $post_types;
	}

	/**
	 * Returns the post types for use in the addons.
	 *
	 * @return array List of taxonomies.
	 */
	public function get_taxonomies() {
		return apply_filters( 'lsx_to_framework_taxonomies', $this->taxonomies );
	}

	/**
	 * Returns the post types for use in the addons.
	 *
	 * @return array List of post types.
	 */
	public function get_post_types() {
		return $this->post_types;
	}

	/**
	 * Adds our taxonomies to an array via a filter
	 *
	 * @param array $taxonomies List of taxonomies to add to.
	 *
	 * @return array List of altered taxonomies.
	 */
	public function taxonomies_filter( $taxonomies ) {
		if ( is_array( $taxonomies ) ) {
			$taxonomies = array_merge( $taxonomies, $this->taxonomies );
		} else {
			$taxonomies = $this->taxonomies;
		}

		return $taxonomies;
	}

	/**
	 * A filter that outputs the tagline for the current page.
	 *
	 * @param string|bool $tagline Tagline to use or false to use internal.
	 * @param string      $before  Before code.
	 * @param string      $after   After code.
	 *
	 * @return string HTML tagline string.
	 */
	public function get_tagline( $tagline = false, $before = '', $after = '' ) {
		$post_id = get_the_ID();

		if ( ! empty( $post_id ) ) {
			$post_id       = get_the_ID();
			$tagline_value = get_post_meta( $post_id, 'banner_subtitle', true );

			if ( false !== $tagline_value ) {
				$tagline = $tagline_value;
			} else {
				$tagline_value = get_post_meta( $post_id, 'tagline', true );

				if ( false !== $tagline_value ) {
					$tagline = $tagline_value;
				}
			}
		} else {
			$post_type = get_query_var( 'post_type' );

			if ( is_post_type_archive( $this->active_post_types ) && isset( $this->options[ $post_type ] ) && isset( $this->options[ $post_type ]['tagline'] ) ) {
				$tagline = $this->options[ $post_type ]['tagline'];
			} elseif ( is_tax( array_keys( $this->taxonomies ) ) ) {
				$taxonomy_tagline = get_term_meta( get_queried_object_id(), 'tagline', true );
				if ( false !== $taxonomy_tagline && '' !== $taxonomy_tagline ) {
					$tagline = $taxonomy_tagline;
				}
			}
		}

		if ( false !== $tagline && '' !== $tagline ) {
			$tagline = $before . $tagline . $after;
		}

		return $tagline;
	}

	/**
	 * A filter that outputs the description for the post_type archives.
	 */
	public function get_post_type_archive_description( $description = false, $before = '', $after = '' ) {
		if ( is_post_type_archive( $this->active_post_types ) && isset( $this->options[ get_post_type() ] ) && isset( $this->options[ get_post_type() ]['description'] ) && '' !== $this->options[ get_post_type() ]['description'] ) {
			$description = $this->options[ get_post_type() ]['description'];
			// $description = $this->apply_filters_the_content( $description );
			$description = $before . $description . $after;
		}

		return $description;
	}

	/**
	 * Return any content with "read more" button and filtered by the_content
	 */
	public function apply_filters_the_content( $content = '', $more_link_text = 'Read More', $link = '' ) {
		$output = '';

		if ( preg_match( '/<!--more(.*?)?-->/', $content, $matches ) ) {
			$content = explode( $matches[0], $content, 2 );

			if ( ! empty( $matches[1] ) && ! empty( $more_link_text ) ) {
				$more_link_text = strip_tags( wp_kses_no_null( trim( $matches[1] ) ) );
			}
		} else {
			$content = array( $content );
		}

		$output .= $content[0];

		if ( count( $content ) > 1 ) {
			if ( empty( $link ) ) {
				$output .= "<a class=\"btn btn-default more-link\" data-collapsed=\"true\" href=\"#more-000\">{$more_link_text}</a>" . $content[1];
			} else {
				$output .= "<a class=\"btn btn-default more-link\" href=\"{$link}\">{$more_link_text}</a>";
			}
		}

		$output = apply_filters( 'the_content', $output );

		return $output;
	}

	/**
	 * Allow extra tags and attributes to wp_kses_post()
	 */
	public function wp_kses_allowed_html( $allowedtags, $context ) {
		if ( ! isset( $allowedtags['i'] ) ) {
			$allowedtags['i'] = array();
		}

		$allowedtags['i']['aria-hidden'] = true;

		if ( ! isset( $allowedtags['span'] ) ) {
			$allowedtags['span'] = array();
		}

		$allowedtags['span']['aria-hidden'] = true;

		if ( ! isset( $allowedtags['button'] ) ) {
			$allowedtags['button'] = array();
		}

		$allowedtags['button']['aria-label']   = true;
		$allowedtags['button']['data-dismiss'] = true;

		if ( ! isset( $allowedtags['li'] ) ) {
			$allowedtags['li'] = array();
		}

		$allowedtags['li']['data-target']   = true;
		$allowedtags['li']['data-slide-to'] = true;

		if ( ! isset( $allowedtags['a'] ) ) {
			$allowedtags['a'] = array();
		}

		$allowedtags['a']['data-toggle']             = true;
		$allowedtags['a']['data-target']             = true;
		$allowedtags['a']['data-slide']              = true;
		$allowedtags['a']['data-collapsed']          = true;
		$allowedtags['a']['data-envira-caption']     = true;
		$allowedtags['a']['data-envira-retina']      = true;
		$allowedtags['a']['data-thumbnail']          = true;
		$allowedtags['a']['data-mobile-thumbnail']   = true;
		$allowedtags['a']['data-envirabox-type']     = true;
		$allowedtags['a']['data-video-width']        = true;
		$allowedtags['a']['data-video-height']       = true;
		$allowedtags['a']['data-video-aspect-ratio'] = true;

		if ( ! isset( $allowedtags['h2'] ) ) {
			$allowedtags['h2'] = array();
		}

		$allowedtags['h2']['data-target'] = true;
		$allowedtags['h2']['data-toggle'] = true;

		if ( ! isset( $allowedtags['div'] ) ) {
			$allowedtags['div'] = array();
		}

		$allowedtags['div']['aria-labelledby']                      = true;
		$allowedtags['div']['data-interval']                        = true;
		$allowedtags['div']['data-icon']                            = true;
		$allowedtags['div']['data-id']                              = true;
		$allowedtags['div']['data-class']                           = true;
		$allowedtags['div']['data-long']                            = true;
		$allowedtags['div']['data-lat']                             = true;
		$allowedtags['div']['data-zoom']                            = true;
		$allowedtags['div']['data-link']                            = true;
		$allowedtags['div']['data-thumbnail']                       = true;
		$allowedtags['div']['data-title']                           = true;
		$allowedtags['div']['data-type']                            = true;
		$allowedtags['div']['data-cluster-small']                   = true;
		$allowedtags['div']['data-cluster-medium']                  = true;
		$allowedtags['div']['data-cluster-large']                   = true;
		$allowedtags['div']['data-fusion-tables']                   = true;
		$allowedtags['div']['data-fusion-tables-colour-border']     = true;
		$allowedtags['div']['data-fusion-tables-width-border']      = true;
		$allowedtags['div']['data-fusion-tables-colour-background'] = true;
		$allowedtags['div']['itemscope']                            = true;
		$allowedtags['div']['itemtype']                             = true;
		$allowedtags['div']['data-row-height']                      = true;
		$allowedtags['div']['data-justified-margins']               = true;
		$allowedtags['div']['data-slick']                           = true;

		// Envirta Gallery tags
		//
		$allowedtags['div']['data-envira-id']      = true;
		$allowedtags['div']['data-gallery-config'] = true;
		$allowedtags['div']['data-gallery-images'] = true;
		$allowedtags['div']['data-gallery-theme']  = true;
		$allowedtags['div']['data-envira-columns'] = true;

		if ( ! isset( $allowedtags['img'] ) ) {
			$allowedtags['img'] = array();
		}

		$allowedtags['img']['data-envira-index']      = true;
		$allowedtags['img']['data-envira-caption']    = true;
		$allowedtags['img']['data-envira-gallery-id'] = true;
		$allowedtags['img']['data-envira-item-id']    = true;
		$allowedtags['img']['data-envira-src']        = true;
		$allowedtags['img']['data-envira-srcset']     = true;

		if ( ! isset( $allowedtags['input'] ) ) {
			$allowedtags['input'] = array();
		}

		$allowedtags['input']['type']         = true;
		$allowedtags['input']['id']           = true;
		$allowedtags['input']['name']         = true;
		$allowedtags['input']['value']        = true;
		$allowedtags['input']['size']         = true;
		$allowedtags['input']['checked']      = true;
		$allowedtags['input']['onclick']      = true;
		$allowedtags['input']['class']        = true;
		$allowedtags['input']['placeholder']  = true;
		$allowedtags['input']['autocomplete'] = true;

		if ( ! isset( $allowedtags['select'] ) ) {
			$allowedtags['select'] = array();
		}

		$allowedtags['select']['name']     = true;
		$allowedtags['select']['id']       = true;
		$allowedtags['select']['disabled'] = true;
		$allowedtags['select']['onchange'] = true;

		if ( ! isset( $allowedtags['option'] ) ) {
			$allowedtags['option'] = array();
		}

		$allowedtags['option']['value']    = true;
		$allowedtags['option']['selected'] = true;

		if ( ! isset( $allowedtags['iframe'] ) ) {
			$allowedtags['iframe'] = array();
		}

		$allowedtags['iframe']['src']             = true;
		$allowedtags['iframe']['width']           = true;
		$allowedtags['iframe']['height']          = true;
		$allowedtags['iframe']['frameborder']     = true;
		$allowedtags['iframe']['allowfullscreen'] = true;
		$allowedtags['iframe']['style']           = true;

		if ( ! isset( $allowedtags['noscript'] ) ) {
			$allowedtags['noscript'] = array();
		}

		return $allowedtags;
	}

	/**
	 * Allow extra protocols to wp_kses_post()
	 */
	public function kses_allowed_protocols( $allowedprotocols ) {
		$allowedprotocols[] = 'tel';

		return $allowedprotocols;
	}

	/**
	 * Allow extra style attributes to wp_kses_post()
	 */
	public function safe_style_css( $allowedstyles ) {
		$allowedstyles[] = 'display';
		$allowedstyles[] = 'background-image';

		return $allowedstyles;
	}

	/**
	 * checks which plugin is active, and grabs those forms.
	 */
	public function show_default_form() {
		if ( class_exists( 'Caldera_Forms_Forms' ) || class_exists( 'GFForms' ) || class_exists( 'Ninja_Forms' ) || class_exists( 'WPForms' ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * checks which plugin is active, and grabs those forms.
	 */
	public function get_activated_forms() {
		$all_forms = false;

		if ( class_exists( 'WPForms' ) ) {
			$all_forms = $this->get_wpforms();
		} elseif ( class_exists( 'Ninja_Forms' ) ) {
			$all_forms = $this->get_ninja_forms();
		} elseif ( class_exists( 'GFForms' ) ) {
			$all_forms = $this->get_gravity_forms();
		} elseif ( class_exists( 'Caldera_Forms_Forms' ) ) {
			$all_forms = $this->get_caldera_forms();
		}

		return $all_forms;
	}



	/**
	 * gets the current wpforms
	 */
	public function get_wpforms() {
		global $wpdb;
		$forms = false;

		$args = array(
			'post_type'     => 'wpforms',
			'orderby'       => 'id',
			'order'         => 'ASC',
			'no_found_rows' => true,
			'nopaging'      => true,
		);

		$posts = get_posts( $args );

		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				$forms[ $post->ID ] = $post->post_title;
			}
		} else {
			$forms = false;
		}
		return $forms;
	}

	/**
	 * gets the currenct ninja forms
	 */
	public function get_ninja_forms() {
		global $wpdb;

		$results = $wpdb->get_results( "SELECT id,title FROM {$wpdb->prefix}nf3_forms" );
		$forms   = false;

		if ( ! empty( $results ) ) {
			foreach ( $results as $form ) {
				$forms[ $form->id ] = $form->title;
			}
		}

		return $forms;
	}

	/**
	 * gets the currenct gravity forms
	 */
	public function get_gravity_forms() {
		global $wpdb;

		$results = \RGFormsModel::get_forms( null, 'title' );
		$forms   = false;

		if ( ! empty( $results ) ) {
			foreach ( $results as $form ) {
				$forms[ $form->id ] = $form->title;
			}
		}

		return $forms;
	}

	/**
	 * gets the currenct caldera forms
	 */
	public function get_caldera_forms() {
		global $wpdb;

		$results = \Caldera_Forms_Forms::get_forms( true );
		$forms   = false;

		if ( ! empty( $results ) ) {
			foreach ( $results as $form => $form_data ) {
				$forms[ $form ] = $form_data['name'];
			}
		}

		return $forms;
	}

	/**
	 * Make TO last plugin to load.
	 */
	public function activated_plugin() {
		$plugins = get_option( 'active_plugins' );

		if ( false != $plugins ) {
			$search = preg_grep( '/.*\/tour-operator\.php/', $plugins );
			$key    = array_search( $search, $plugins );

			if ( is_array( $search ) && count( $search ) ) {
				foreach ( $search as $key => $path ) {
					array_splice( $plugins, $key, 1 );
					array_push( $plugins, $path );
					update_option( 'active_plugins', $plugins );
				}
			}
		}
	}

	/**
	 * Check if the PHP version is compatible.
	 *
	 * @since 1.0.2
	 */
	public static function compatible_version() {
		if ( version_compare( PHP_VERSION, '7.0', '<' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * The backup sanity check, in case the plugin is activated in a weird way,
	 * or the versions change after activation.
	 *
	 * @since 1.0.2
	 */
	public function compatible_version_check() {
		if ( ! self::compatible_version() ) {
			if ( is_plugin_active( plugin_basename( LSX_TO_CORE ) ) ) {
				deactivate_plugins( plugin_basename( LSX_TO_CORE ) );
				add_action( 'admin_notices', array( $this, 'compatible_version_notice' ) );

				if ( isset( $_GET['activate'] ) ) {
					unset( $_GET['activate'] );
				}
			}
		}
	}

	/**
	 * Display the notice related with the older version from PHP.
	 *
	 * @since 1.0.2
	 */
	public function compatible_version_notice() {
		$class   = 'notice notice-error';
		$message = esc_html__( 'LSX Tour Operator Plugin requires PHP 7.0 or higher.', 'tour-operator' );
		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_html( $class ), esc_html( $message ) );
	}

	/**
	 * The primary sanity check, automatically disable the plugin on activation
	 * if it doesn't meet minimum requirements.
	 *
	 * @since 1.0.2
	 */
	public static function compatible_version_check_on_activation() {
		if ( ! self::compatible_version() ) {
			deactivate_plugins( plugin_basename( LSX_TO_CORE ) );
			wp_die( esc_html__( 'LSX Tour Operator Plugin requires PHP 7.0 or higher.', 'tour-operator' ) );
		}
	}

	/**
	 * Check if the theme is compatible.
	 *
	 * @since 1.1.0
	 */
	public static function compatible_theme() {
		$current_theme    = wp_get_theme();
		$current_template = $current_theme->get_template();
		$theme_name       = $current_theme->get( 'Name' );

		if ( 'lsx' !== $current_template && 'LSX' !== $theme_name ) {
			return false;
		}

		return true;
	}

	/**
	 * Adds an admin notice (requires LSX).
	 *
	 * @since 1.1.0
	 */
	public function compatible_theme_check() {
		if ( ! self::compatible_theme() ) {
			if ( is_plugin_active( plugin_basename( LSX_TO_CORE ) ) ) {
				if ( empty( get_option( 'lsx-to-theme-notice-dismissed' ) ) ) {
					add_action( 'admin_notices', array( $this, 'compatible_theme_notice' ), 199 );
				}
			}
		}
	}

	/**
	 * Display an admin notice (requires LSX).
	 *
	 * @since 1.1.0
	 */
	public function compatible_theme_notice() {
		?>
			<div class="lsx-to-theme-notice notice notice-error is-dismissible">
				<p>
					<?php
						printf(
							/* Translators: 1: HTML open tag link, 2: HTML close tag link */
							esc_html__( 'The LSX Tour Operator Plugin requires the free LSX Theme be installed and active. Please download LSX Theme from %1$sWordPress.org%2$s to get started with your LSX Tour Operator Plugin.', 'tour-operator' ),
							'<a href="https://wordpress.org/themes/lsx/" target="_blank">',
							'</a>'
						);
					?>
				</p>
				<p><a href="https://wordpress.org/themes/lsx/" class="button" style="text-decoration: none;"><?php esc_attr_e( 'Download LSX Theme', 'tour-operator' ); ?></a></p>
			</div>
		<?php
	}

	/**
	 * Dismiss the admin notice (requires LSX).
	 *
	 * @since 1.1.0
	 */
	public function theme_notice_dismiss() {
		update_option( 'lsx-to-theme-notice-dismissed', '1' );
		wp_die();
	}

}
