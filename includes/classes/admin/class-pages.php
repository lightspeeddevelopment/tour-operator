<?php
/**
 * LSX Tour Operator - Pages Class
 *
 * @package   lsx
 * @author    LightSpeed
 * @license   GPL-2.0+
 * @link
 * @copyright 2017 LightSpeedDevelopment
 */

namespace lsx\admin;

/**
 * Class pages
 * Handles registration of pages
 *
 * @package lsx
 */
class Pages extends \lsx\Frame {

	/**
	 * Holds an type of object.
	 *
	 * @since   1.1.0
	 * @var     string
	 */
	protected $type = 'pages';

	/**
	 * Holds a List of pages to be created.
	 *
	 * @since   1.1.0
	 * @var     array
	 */
	private $pages;

	/**
	 * LSX Tour Operator constructor.
	 */
	public function __construct() {
		// Setup objects.
		add_action( 'admin_menu', array( $this, 'create_pages' ) );
		add_filter( 'parent_file', array( $this, 'select_submenu_pages' ) );

		parent::__construct();
	}

	/**
	 * Keep TO main menu item active for all subitems.
	 *
	 * @since 1.1.0
	 *
	 * @param string $parent_file The parent file slug.
	 *
	 * @return string The parent altered file.
	 */
	public function select_submenu_pages( $parent_file ) {
		global $submenu_file;
		if ( isset( $this->object[ $submenu_file ] ) ) {
			$parent_file = substr( $this->object[ $submenu_file ], 0, strpos( $this->object[ $submenu_file ], '_page_' ) );
		}

		return $parent_file;
	}

	/**
	 * Create main pages for plugin.
	 *
	 * @uses  "admin_menu" hook.
	 * @since 1.1.0
	 */
	public function create_pages() {
		do_action( 'lsx_to_register_menu_pages' );
		if ( ! empty( $this->pages['main'] ) ) {
			array_map(
				array(
					$this,
					'create_main_page',
				),
				$this->pages['main']
			);
		}
		if ( ! empty( $this->pages['sub'] ) ) {
			array_map(
				array(
					$this,
					'create_sub_pages',
				),
				$this->pages['sub']
			);
		}
	}

	/**
	 * Create main pages for plugin.
	 *
	 * @uses  "admin_menu" hook.
	 * @since 1.1.0
	 *
	 * @param string $slug The slug for the page.
	 */
	public function create_main_page( $slug ) {
		$config                = $this->configs[ $slug ];
		$this->object[ $slug ] = add_menu_page( $config['page_title'], $config['menu_title'], $config['capability'], $config['slug'], $config['callback'], $config['icon'], $config['menu_position'] );
	}

	/**
	 * Create sub pages for plugin.
	 *
	 * @uses  "admin_menu" hook.
	 * @since 1.1.0
	 *
	 * @param string $slug The slug for the page.
	 */
	public function create_sub_pages( $slug ) {
		$config                = $this->configs[ $slug ];
		$this->object[ $slug ] = add_submenu_page( $config['parent_slug'], $config['page_title'], $config['menu_title'], $config['capability'], $config['slug'], $config['callback'] );
	}

	/**
	 * Register a page for the plugin.
	 *
	 * @since 1.1.0
	 *
	 * @param string $slug   The Page slug.
	 * @param array  $config The Page config arguments.
	 */
	public function register_object( $slug, $config ) {
		$config = array_merge(
			array(
				'page_title'    => '',
				'menu_title'    => '',
				'capability'    => 'edit_options',
				'callback'      => '__return_null',
				'icon'          => '',
				'menu_position' => null,
				'slug'          => $slug,
				'parent_slug'   => null,
			),
			$config
		);
		if ( is_null( $config['parent_slug'] ) ) {
			$this->pages['main'][] = $config['slug'];
		} else {
			$this->pages['sub'][] = $config['slug'];
		}
		$this->configs[ $config['slug'] ] = $config;
	}
}
