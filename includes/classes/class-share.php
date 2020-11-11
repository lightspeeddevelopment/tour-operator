<?php
/**
 * LSX shared Core for sharing assets.
 *
 * @package   share
 * @author    LightSpeed
 * @license   GPL-2.0+
 * @link
 * @copyright 2017 LightSpeedDevelopment
 */

namespace lsx;

/**
 * Share class.
 *
 * @package lsx
 * @author  LightSpeed
 */
class Share {

	/**
	 * Holds the shared instance
	 *
	 * @since  1.1.0
	 * @access protected
	 * @var      share
	 */
	protected static $instance = null;

	/**
	 * Active style assets for rendering in header
	 *
	 * @since  1.1.0
	 * @access protected
	 * @var      string
	 */
	protected $active_styles;

	/**
	 * Runs after assets have been enqueued.
	 *
	 * @since  1.1.0
	 * @access public
	 *
	 * @param string $styles Styles to register for render.
	 */
	public function set_active_styles( $styles ) {
		$this->active_styles .= $styles;
	}

	/**
	 * Runs after active assets have been set.
	 *
	 * @since  1.1.0
	 * @access protected
	 */
	public function render_active_styles() {

		if ( ! empty( $this->active_styles ) ) {
			echo '<style type="text/css" media="screen" id="tour-operator-share-styles">';
			echo $this->active_styles;// WPCS: XSS ok.
			echo '</style>';
		}
	}

	/**
	 * Return an instance of this class.
	 *
	 * @codeCoverageIgnore
	 * @since 1.1.0
	 * @return self A single instance of the share class.
	 */
	public static function init() {

		// If the single instance hasn't been set, set it now.
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

	/**
	 * Constructor. Sets up action to render styles.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		add_action(
			'admin_enqueue_scripts',
			array(
				$this,
				'render_active_styles',
			),
			100
		);
		add_action(
			'wp_print_styles',
			array(
				$this,
				'render_active_styles',
			),
			100
		);
	}
}
