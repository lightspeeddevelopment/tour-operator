<?php
/**
 * LSX Tour Operator - Objects Class
 *
 * @package   lsx
 * @author    LightSpeed
 * @license   GPL-2.0+
 * @link
 * @copyright 2017 LightSpeedDevelopment
 */

namespace lsx;

/**
 * Class pages
 * Handles registration of objects, like Pages, Taxonomies, Metaboxes, and
 * Post-Types.
 *
 * @package lsx
 */
abstract class Frame {

	/**
	 * Holds instances of the class
	 *
	 * @since   1.1.0
	 * @var     self[]
	 */
	protected static $instances;

	/**
	 * Holds an type of object.
	 *
	 * @since   1.1.0
	 * @var     string
	 */
	protected $type = 'object';

	/**
	 * Holds an array of the objects.
	 *
	 * @since   1.1.0
	 * @var     array
	 */
	protected $object;

	/**
	 * Holds an array of the configs.
	 *
	 * @since   1.1.0
	 * @var     array
	 */
	protected $configs;

	/**
	 * Object constructor.
	 */
	protected function __construct() {
		// Setup objects.
		$this->setup_objects();
	}

	/**
	 * Taxonomy object getter.
	 *
	 * @since 1.1.0
	 *
	 * @param string $tag The slug of the object to get.
	 *
	 * @return \WP_Taxonomy|null Taxonomy object if found, else null.
	 */
	public function __get( $tag ) {

		$object = null;
		if ( isset( $this->object[ $tag ] ) ) {
			$object = $this->object[ $tag ];
		}

		return $object;
	}

	/**
	 * Gets all taxonomies as an array.
	 *
	 * @since 1.1.0
	 * @return \WP_Taxonomy[] Array of all taxomonies.
	 */
	public function get_all() {
		return $this->object;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.1.0
	 * @return  self  A single instance
	 */
	public static function init() {

		// If the single instance hasn't been set, set it now.
		$class = get_called_class();
		if ( ! isset( self::$instances[ $class ] ) ) {
			self::$instances[ $class ] = new $class();
		}

		return self::$instances[ $class ];
	}

	/**
	 * Enqueue admin scripts and styles.
	 *
	 * @since  1.1.0
	 */
	public function script_style() {
		array_map( array( $this, 'enqueue_assets' ), $this->get_active() );
	}

	/**
	 * Enqueue admin scripts and styles.
	 *
	 * @since  1.1.0
	 *
	 * @param string $slug The slug of the object to enqueue asses for.
	 */
	public function enqueue_assets( $slug ) {
		$config = $this->configs[ $slug ];
		if ( ! empty( $config['assets'] ) ) {
			array_map(
				array(
					tour_operator(),
					'set_assets',
				),
				array_keys( $config['assets'] ),
				$config['assets']
			);
		}
	}

	/**
	 * Opens a location and gets the folders to check
	 *
	 * @since  1.1.0
	 * @access private
	 * @return array List of folders
	 */
	private function get_config_files() {

		$items = array();
		$path  = LSX_TO_PATH . 'includes/' . $this->type;
		// @codingStandardsIgnoreStart
		if ( $uid = opendir( $path ) ) {
			while ( ( $item = readdir( $uid ) ) !== false ) {
				if ( substr( $item, 0, 1 ) !== '.' ) {
					$key           = str_replace( '.php', '', str_replace( 'config-', '', $item ) );
					$items[ $key ] = include $path . '/' . $item;
				}
			}
			closedir( $uid );
		}
		// @codingStandardsIgnoreEnd

		return $items;
	}

	/**
	 * Gets an array of the taxonomies for the plugin.
	 *
	 * @since 1.1.0
	 * @return array Taxonomy config array.
	 */
	public function get_configs() {

		$objects = array();
		if ( file_exists( LSX_TO_PATH . 'includes/' . $this->type ) ) {
			$objects = $this->get_config_files();
		}

		/**
		 * Filter object configs.
		 *
		 * @since 1.1.0
		 *
		 * @param array $objects Internal objects array.
		 */
		return apply_filters( "lsx_get_{$this->type}_configs", $objects );
	}

	/**
	 * Gets a config array for a specific object.
	 *
	 * @since 1.1.0
	 *
	 * @param string $object The object to get config for.
	 *
	 * @return int The position of the object menu item.
	 */
	public function get_menu_position( $object ) {
		$config   = $this->get_config( $object );
		$position = 5;
		if ( ! empty( $config['menu_position'] ) ) {
			$position = $config['menu_position'];
		}

		return $position;
	}

	/**
	 * Gets a config array for a specific object.
	 *
	 * @since 1.1.0
	 *
	 * @param string $object The object to get config for.
	 *
	 * @return array Taxonomy config array.
	 */
	public function get_config( $object ) {
		$config = array();
		if ( ! empty( $this->configs[ $object ] ) ) {
			$config = $this->configs[ $object ];
		}

		return $config;
	}


	/**
	 * Setup the taxonomies for the plugin.
	 *
	 * @since 1.1.0
	 */
	public function setup_objects() {
		$this->configs = $this->get_configs();
		array_map(
			array(
				$this,
				'register_object',
			),
			array_keys( $this->configs ),
			$this->configs
		);
		add_action( 'admin_enqueue_scripts', array( $this, 'script_style' ) );

	}

	/**
	 * Checks if object is active.
	 *
	 * @since  1.1.0
	 */
	public function get_active() {
		$screen = get_current_screen();
		$active = array();
		if ( is_object( $screen ) ) {
			$active = array_keys( $this->object, $screen->id, true );
		}

		return $active;
	}

	/**
	 * Register a object for the plugin.
	 *
	 * @since 1.1.0
	 *
	 * @param string $slug   The object slug.
	 * @param array  $config The object config arguments.
	 */
	abstract protected function register_object( $slug, $config );

}
