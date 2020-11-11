<?php
/**
 * LSX Tour Operator - Admin Main Class
 *
 * @package   lsx
 * @author    LightSpeed
 * @license   GPL-2.0+
 * @link
 * @copyright 2017 LightSpeedDevelopment
 */

namespace lsx\admin;

/**
 * Class Admin
 *
 * @package lsx\admin
 */
class Admin {

	/**
	 * Holds instance of the class
	 *
	 * @since   1.1.0
	 * @var     \lsx\admin\Admin
	 */
	private static $instance;
	/**
	 * Holds suffixes of admin pages.
	 *
	 * @since   1.1.0
	 * @var     array
	 */
	public $pages;

	/**
	 * LSX Tour Operator Admin constructor.
	 */
	public function __construct() {
		// Setup Admin.
		$this->setup();
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.1.0
	 * @return  \lsx\Taxonomies  A single instance
	 */
	public static function init() {

		// If the single instance hasn't been set, set it now.
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Setup admin pages for the plugin.
	 *
	 * @since 1.1.0
	 */
	public function register_pages() {
		array_map(
			array(
				$this,
				'register_post_type_page',
			),
			tour_operator()->post_types->get_all()
		);
		array_map(
			array(
				$this,
				'register_taxonomy_page',
			),
			tour_operator()->taxonomies->get_all()
		);

	}

	/**
	 * Register a single post type sub page.
	 *
	 * @param \WP_Post_type $post_type The post type to register a page for.
	 */
	public function register_post_type_page( $post_type ) {
		$args = array(
			'parent_slug' => 'tour-operator',
			'page_title'  => $post_type->labels->add_new_item,
			'menu_title'  => $post_type->labels->add_new_item,
			'capability'  => 'edit_posts',
			'type'        => 'sub',
			'callback'    => null,
		);
		tour_operator()->pages->register_object( 'post-new.php?post_type=' . $post_type->name, $args );
	}

	/**
	 * Register a single taxonomy sub page.
	 *
	 * @param \WP_Taxonomy $taxonomy The taxonomy to register a page for.
	 */
	public function register_taxonomy_page( $taxonomy ) {
		$args = array(
			'parent_slug' => 'tour-operator',
			'page_title'  => $taxonomy->label,
			'menu_title'  => $taxonomy->label,
			'capability'  => 'edit_posts',
			'type'        => 'sub',
			'callback'    => null,
		);
		tour_operator()->pages->register_object( 'edit-tags.php?taxonomy=' . $taxonomy->name, $args );
	}

	/**
	 * Prepare custom menu ordering.
	 *
	 * @since 1.1.0
	 *
	 * @param bool $has_custom Flag to use custom sorting.
	 *
	 * @return bool The original flag to maintain compatibility.
	 */
	public function prepare_menu_order( $has_custom ) {
		global $submenu;
		if ( ! empty( $submenu['tour-operator'] ) ) {
			$submenu['tour-operator'] = $this->reorder_menu_pages( $submenu['tour-operator'] );
		}

		return $has_custom;
	}

	/**
	 * Checks if an item is a post type menu item or not.
	 *
	 * @since 1.1.0
	 *
	 * @param array $item The menu item to check.
	 *
	 * @return bool If item is a post type menu item or not.
	 */
	private function is_post_type_menu_item( $item ) {
		return false !== strpos( $item[2], 'edit.php' ) || false !== strpos( $item[2], 'post-new.php' );
	}

	/**
	 * Checks if an item is a taxonomy menu item or not.
	 *
	 * @since 1.1.0
	 *
	 * @param array $item The menu item to check.
	 *
	 * @return bool If item is a post type menu item or not.
	 */
	private function is_taxonomy_menu_item( $item ) {
		return false !== strpos( $item['2'], 'edit-tags.php' );
	}

	/**
	 * Checks if an item is a page menu item or not.
	 *
	 * @since 1.1.0
	 *
	 * @param array $item The menu item to check.
	 *
	 * @return bool If item is a post type menu item or not.
	 */
	private function is_page_menu_item( $item ) {
		return false === strpos( $item[2], '?' );
	}

	/**
	 * Sorts a post type menu set.
	 *
	 * @since 1.1.0
	 *
	 * @param array $items The menu items to sort.
	 *
	 * @return array New sorted array of items.
	 */
	private function sort_post_type_menu( $items ) {
		$new_submenu = array();

		foreach ( $items as $item ) {
			$parts = parse_url( $item[2] );
			parse_str( $parts['query'], $query );
			$type_key = $query['post_type'];

			if ( is_object( tour_operator()->post_types->{$type_key} ) ) {
				$menu_position = tour_operator()->post_types->{$type_key}->menu_position;
			} else {
				$menu_position = 80;
			}

			if ( isset( $new_submenu[ $menu_position ] ) ) {
				$menu_position ++;
			}

			$new_submenu[ $menu_position ] = $item;
		}

		return $new_submenu;
	}

	/**
	 * Sorts a taxonomy menu set.
	 *
	 * @since 1.1.0
	 *
	 * @param array $items The menu items to sort.
	 *
	 * @return array New sorted array of items.
	 */
	private function sort_taxonomy_menu( $items ) {
		$new_submenu = array();
		foreach ( $items as $item ) {
			$taxonomy                      = str_replace( 'edit-tags.php?taxonomy=', '', $item['2'] );
			$menu_position                 = tour_operator()->taxonomies->get_menu_position( $taxonomy );
			$new_submenu[ $menu_position ] = $item;
		}

		return $new_submenu;
	}

	/**
	 * Sorts a page menu set.
	 *
	 * @since 1.1.0
	 *
	 * @param array $items The menu items to sort.
	 *
	 * @return array New sorted array of items.
	 */
	private function sort_page_menu( $items ) {
		$new_submenu = array();
		foreach ( $items as $item ) {
			$menu_position = tour_operator()->pages->get_menu_position( $item[2] );
			if ( isset( $new_submenu[ $menu_position ] ) ) {
				$menu_position ++;
			}
			$new_submenu[ $menu_position ] = $item;
		}

		return $new_submenu;
	}

	/**
	 * Reorder custom menu pages.
	 * - [10] Destinations
	 * - [+1] Add Destination
	 * - [20] Tours
	 * - [+1] Add Tour
	 * - [22] Travel Styles
	 * - [30] Accommodation
	 * - [+1] Add Accommodation
	 * - [32] Accommodation Types
	 * - [33] Brands
	 * - [34] Locations
	 * - [35] Facilities
	 * - [40] Team
	 * - [50] Activities
	 * - [60] Reviews
	 * - [70] Specials
	 * - [72] Special Types
	 * - [80] Vehicles
	 * - [90] Settings
	 * - [91] Help
	 * - [92] Add-ons
	 */
	public function reorder_menu_pages( $items ) {

		$menu  = array();
		$menu += $this->sort_post_type_menu(
			array_filter(
				$items,
				array(
					$this,
					'is_post_type_menu_item',
				)
			)
		);
		$menu += $this->sort_taxonomy_menu(
			array_filter(
				$items,
				array(
					$this,
					'is_taxonomy_menu_item',
				)
			)
		);
		$menu += $this->sort_page_menu(
			array_filter(
				$items,
				array(
					$this,
					'is_page_menu_item',
				)
			)
		);
		ksort( $menu );

		return $menu;

	}

	/**
	 * Setup admin hooks.
	 *
	 * @since 1.1.0
	 */
	private function setup() {
		add_filter( 'custom_menu_order', array( $this, 'prepare_menu_order' ) );
		$this->register_pages();
	}
}
