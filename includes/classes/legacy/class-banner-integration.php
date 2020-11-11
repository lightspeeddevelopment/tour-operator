<?php
/**
 * Module Template.
 *
 * @package   Banner_Integration
 * @author    LightSpeed
 * @license   GPL3
 * @link
 * @copyright 2016 LightSpeedDevelopment
 */

namespace lsx\legacy;

/**
 * Main plugin class.
 *
 * @package Banner_Integration
 * @author  LightSpeed
 */
class Banner_Integration {

	/**
	 * Initialize the plugin by setting localization, filters, and
	 * administration functions.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	public function __construct( $post_types = array(), $taxonomies = array() ) {
		$this->post_types = $post_types;
		$this->taxonomies = $taxonomies;
		$this->options    = get_option( '_lsx-to_settings', false );

		add_action( 'init', array( $this, 'init' ) );
		add_filter( 'lsx_banner_placeholder_url', array( $this, 'banner_placeholder_url' ) );
	}

	/**
	 * Init
	 */
	public function init() {
		add_filter( 'lsx_banner_allowed_post_types', array( $this, 'enable_banners' ) );
		add_filter( 'lsx_banner_allowed_taxonomies', array( $this, 'enable_taxonomy_banners' ) );
		add_filter( 'lsx_banner_post_type_archive_url', array( $this, 'banner_archive_url' ) );
		add_action( 'lsx_banner_content', array( $this, 'posts_page_banner_tagline' ) );
		add_filter( 'lsx_banner_title', array( $this, 'banner_title' ), 100 );

		if ( false !== $this->options && ! isset( $this->options['display']['enable_galleries_in_banner'] ) ) {
			add_filter(
				'lsx_banners_envira_enable',
				function( $bool ) {
					return false;
				}
			);
		}
	}

	/**
	 * Enables the lsx banners
	 *
	 * @return    null
	 */
	public function enable_banners( $allowed_post_types ) {
		$allowed_post_types = array_merge( $allowed_post_types, $this->post_types );
		return $allowed_post_types;
	}

	/**
	 * Enables the lsx banners for taxonomies
	 *
	 * @return    null
	 */
	public function enable_taxonomy_banners( $allowed_taxonomies ) {
		$allowed_taxonomies = array_merge( $allowed_taxonomies, $this->taxonomies );

		return $allowed_taxonomies;
	}

	/**
	 * A filter that outputs the description for the post_type and taxonomy
	 * archives.
	 */
	public function banner_archive_url( $image = false ) {
		$tour_operator = tour_operator();

		if ( is_post_type_archive( $tour_operator->active_post_types ) && isset( $tour_operator->options[ get_post_type() ] ) ) {
			if ( isset( $tour_operator->options[ get_post_type() ]['banner'] ) && '' !== $tour_operator->options[ get_post_type() ]['banner'] ) {
				$image = $tour_operator->options[ get_post_type() ]['banner'];
			}
		}

		return $image;
	}

	/**
	 *  Picks the placeholder from a specific post type setting, if its there
	 */
	public function banner_placeholder_url( $image = false ) {
		$tour_operator = tour_operator();

		if ( isset( $tour_operator->options['general'] ) && isset( $tour_operator->options['general']['banner_placeholder'] ) && '' !== $tour_operator->options['general']['banner_placeholder'] ) {
			$image = $tour_operator->options['general']['banner_placeholder'];
		}

		if ( isset( $tour_operator->options[ get_post_type() ] ) && isset( $tour_operator->options[ get_post_type() ]['banner_placeholder'] ) && '' !== $tour_operator->options[ get_post_type() ]['banner_placeholder'] ) {
			$image = $tour_operator->options[ get_post_type() ]['banner_placeholder'];
		}

		return $image;
	}

	/**
	 * A filter that outputs the description for the post_type and taxonomy
	 * archives.
	 */
	public function posts_page_banner_tagline() {
		$tour_operator = tour_operator();

		if ( is_home() && isset( $tour_operator->options[ get_post_type() ] ) && isset( $tour_operator->options[ get_post_type() ]['tagline'] ) ) {
			$tagline = $tour_operator->options[ get_post_type() ]['tagline'];
			?>
			<p class="tagline"><?php echo wp_kses_post( $tagline ); ?></p>
			<?php
		}
	}

	/**
	 * A filter that outputs the title for the post_type_archives.
	 */
	public function banner_title( $title ) {
		$tour_operator = tour_operator();

		if ( is_post_type_archive() && isset( $tour_operator->options[ get_post_type() ] ) && isset( $tour_operator->options[ get_post_type() ]['title'] ) && '' !== $tour_operator->options[ get_post_type() ]['title'] ) {
			$title = '<h1 class="page-title">' . $tour_operator->options[ get_post_type() ]['title'] . '</h1>';
		}

		return $title;
	}

}
