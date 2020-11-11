<?php
/**
 * LSX Tour Operator - Accommodation-brand taxonomy config
 *
 * @package   tour_operator
 * @author    LightSpeed
 * @license   GPL-2.0+
 * @link
 * @copyright 2017 LightSpeedDevelopment
 */

$taxonomy = array(
	'object_types'  => 'accommodation',
	'menu_position' => 33,
	'args'          => array(
		'hierarchical'        => true,
		'labels'              => array(
			'name'              => esc_html__( 'Brands', 'tour-operator' ),
			'singular_name'     => esc_html__( 'Brand', 'tour-operator' ),
			'search_items'      => esc_html__( 'Search Brands', 'tour-operator' ),
			'all_items'         => esc_html__( 'Brands', 'tour-operator' ),
			'parent_item'       => esc_html__( 'Parent Brand', 'tour-operator' ),
			'parent_item_colon' => esc_html__( 'Parent Brand:', 'tour-operator' ),
			'edit_item'         => esc_html__( 'Edit Brand', 'tour-operator' ),
			'update_item'       => esc_html__( 'Update Brand', 'tour-operator' ),
			'add_new_item'      => esc_html__( 'Add New Brand', 'tour-operator' ),
			'new_item_name'     => esc_html__( 'New Brand', 'tour-operator' ),
			'menu_name'         => esc_html__( 'Brands', 'tour-operator' ),
		),
		'show_ui'             => true,
		'public'              => true,
		'show_tagcloud'       => false,
		'exclude_from_search' => true,
		'show_admin_column'   => true,
		'query_var'           => true,
		'rewrite'             => array(
			'slug' => 'brand',
		),
	),
);

return $taxonomy;
