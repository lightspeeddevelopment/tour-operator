<?php
/**
 * LSX Tour Operator - help page config
 *
 * @package   tour_operator
 * @author    LightSpeed
 * @license   GPL-2.0+
 * @link
 * @copyright 2017 LightSpeedDevelopment
 */

$page = array(
	'page_title'    => esc_html__( 'Help', 'tour-operator' ),
	'menu_title'    => esc_html__( 'Help', 'tour-operator' ),
	'capability'    => 'manage_options',
	'icon'          => 'dashicons-book-alt',
	'parent_slug'   => 'tour-operator',
	'slug'          => 'to-help',
	'menu_position' => 91,
	'callback'      => function () {
		include LSX_TO_PATH . 'includes/partials/help.php';
		include LSX_TO_PATH . 'includes/partials/add-ons.php';
	},
);

return $page;
