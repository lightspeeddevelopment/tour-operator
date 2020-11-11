<?php
/**
 * LSX Tour Operator - Destination Metabox config
 *
 * @package   tour_operator
 * @author    LightSpeed
 * @license   GPL-2.0+
 * @link
 * @copyright 2017 LightSpeedDevelopment
 */

$metabox = array(
	'title'  => esc_html__( 'LSX Tour Operator Plugin', 'tour-operator' ),
	'pages'  => 'destination',
	'fields' => array(),
);

$metabox['fields'][] = array(
	'id'   => 'featured',
	'name' => esc_html__( 'Featured', 'tour-operator' ),
	'type' => 'checkbox',
);

$metabox['fields'][] = array(
	'id'   => 'disable_single',
	'name' => esc_html__( 'Disable Single', 'tour-operator' ),
	'type' => 'checkbox',
);

$metabox['fields'][] = array(
	'id'      => 'sticky_order',
	'name'    => esc_html__( 'Sticky weight', 'tour-operator' ),
	'type'    => 'text',
	'default' => '',
	// 'desc'    => esc_html__( 'Stick an item on the archive page. Add a number to order the item.', 'tour-operator' ),
);

$metabox['fields'][] = array(
	'id'       => 'best_time_to_visit',
	'name'     => esc_html__( 'Best months to visit', 'tour-operator' ),
	'type'     => 'select',
	'multiple' => true,
	'options'  => array(
		'january'   => 'January',
		'february'  => 'February',
		'march'     => 'March',
		'april'     => 'April',
		'may'       => 'May',
		'june'      => 'June',
		'july'      => 'July',
		'august'    => 'August',
		'september' => 'September',
		'october'   => 'October',
		'november'  => 'November',
		'december'  => 'December',
	),
);

if ( ! class_exists( 'LSX_Banners' ) ) {
	$metabox['fields'][] = array(
		'id'   => 'tagline',
		'name' => esc_html__( 'Tagline', 'tour-operator' ),
		'type' => 'text',
	);
}

$metabox['fields'][] = array(
	'id'   => 'travel_info_title',
	'name' => esc_html__( 'Travel Info', 'tour-operator' ),
	'type' => 'title',
);

$metabox['fields'][] = array(
	'id'      => 'electricity',
	'name'    => esc_html__( 'Electricity', 'tour-operator' ),
	'type'    => 'wysiwyg',
	'options' => array(
		'editor_height' => '100',
	),
);

$metabox['fields'][] = array(
	'id'      => 'banking',
	'name'    => esc_html__( 'Banking', 'tour-operator' ),
	'type'    => 'wysiwyg',
	'options' => array(
		'editor_height' => '100',
	),
);

$metabox['fields'][] = array(
	'id'      => 'cuisine',
	'name'    => esc_html__( 'Cuisine', 'tour-operator' ),
	'type'    => 'wysiwyg',
	'options' => array(
		'editor_height' => '100',
	),
);

$metabox['fields'][] = array(
	'id'      => 'climate',
	'name'    => esc_html__( 'Climate', 'tour-operator' ),
	'type'    => 'wysiwyg',
	'options' => array(
		'editor_height' => '100',
	),
);

$metabox['fields'][] = array(
	'id'      => 'transport',
	'name'    => esc_html__( 'Transport', 'tour-operator' ),
	'type'    => 'wysiwyg',
	'options' => array(
		'editor_height' => '100',
	),
);

$metabox['fields'][] = array(
	'id'      => 'dress',
	'name'    => esc_html__( 'Dress', 'tour-operator' ),
	'type'    => 'wysiwyg',
	'options' => array(
		'editor_height' => '100',
	),
);

$metabox['fields'][] = array(
	'id'      => 'health',
	'name'    => esc_html__( 'Health', 'tour-operator' ),
	'type'    => 'wysiwyg',
	'options' => array(
		'editor_height' => '100',
	),
);

$metabox['fields'][] = array(
	'id'      => 'safety',
	'name'    => esc_html__( 'Safety', 'tour-operator' ),
	'type'    => 'wysiwyg',
	'options' => array(
		'editor_height' => '100',
	),
);

$metabox['fields'][] = array(
	'id'      => 'visa',
	'name'    => esc_html__( 'Visa', 'tour-operator' ),
	'type'    => 'wysiwyg',
	'options' => array(
		'editor_height' => '100',
	),
);

$metabox['fields'][] = array(
	'id'      => 'additional_info',
	'name'    => esc_html__( 'General', 'tour-operator' ),
	'type'    => 'wysiwyg',
	'options' => array(
		'editor_height' => '100',
	),
);

$metabox['fields'][] = array(
	'id'   => 'gallery_title',
	'name' => esc_html__( 'Gallery', 'tour-operator' ),
	'type' => 'title',
);

$metabox['fields'][] = array(
	'id'                  => 'gallery',
	'name'                => '',
	'type'                => 'image',
	'repeatable'          => true,
	'show_size'           => false,
	'sortable'            => true,
	'string-repeat-field' => esc_html__( 'Add new image', 'tour-operator' ),
);

if ( class_exists( 'Envira_Gallery' ) ) {
	$metabox['fields'][] = array(
		'id'   => 'envira_title',
		'name' => esc_html__( 'Envira Gallery', 'tour-operator' ),
		'type' => 'title',
	);

	$metabox['fields'][] = array(
		'id'         => 'envira_gallery',
		'name'       => esc_html__( 'Envira Gallery', 'tour-operator' ),
		'type'       => 'post_select',
		'use_ajax'   => false,
		'allow_none' => true,
		'query'      => array(
			'post_type'      => 'envira',
			'nopagin'        => true,
			'posts_per_page' => '-1',
			'orderby'        => 'title',
			'order'          => 'ASC',
		),
	);

	if ( class_exists( 'Envira_Videos' ) ) {
		$metabox['fields'][] = array(
			'id'         => 'envira_video',
			'name'       => esc_html__( 'Envira Video Gallery', 'tour-operator' ),
			'type'       => 'post_select',
			'use_ajax'   => false,
			'allow_none' => true,
			'query'      => array(
				'post_type'      => 'envira',
				'nopagin'        => true,
				'posts_per_page' => '-1',
				'orderby'        => 'title',
				'order'          => 'ASC',
			),
		);
	}
}

if ( ! isset( tour_operator()->options['display']['maps_disable'] ) && empty( tour_operator()->options['display']['maps_disable'] ) ) {
	$metabox['fields'][] = array(
		'id'   => 'location_title',
		'name' => esc_html__( 'Location', 'tour-operator' ),
		'type' => 'title',
	);
	$metabox['fields'][] = array(
		'id'   => 'disable_auto_zoom',
		'name' => esc_html__( 'Disable Auto Zoom', 'tour-operator' ),
		'type' => 'checkbox',
	);
	$google_api_key      = '';
	if ( isset( tour_operator()->options['api']['googlemaps_key'] ) && ! empty( tour_operator()->options['api']['googlemaps_key'] ) ) {
		$google_api_key = tour_operator()->options['api']['googlemaps_key'];
	}
	$metabox['fields'][] = array(
		'id'             => 'location',
		'name'           => esc_html__( 'Address', 'tour-operator' ),
		'type'           => 'gmap',
		'google_api_key' => $google_api_key,
	);
	$metabox['fields'][] = array(
		'id'         => 'map_placeholder',
		'name'       => esc_html__( 'Map Placeholder', 'tour-operator' ),
		'type'       => 'image',
		'repeatable' => false,
		'show_size'  => false,
	);
	$metabox['fields'][] = array(
		'id'         => 'map_mobile_placeholder',
		'name'       => esc_html__( 'Mobile Placeholder', 'tour-operator' ),
		'type'       => 'image',
		'repeatable' => false,
		'show_size'  => false,
	);
}

$metabox['fields'][] = array(
	'id'   => 'posts_title',
	'name' => esc_html__( 'Posts', 'tour-operator' ),
	'type' => 'title',
);

$metabox['fields'][] = array(
	'id'         => 'post_to_destination',
	'name'       => esc_html__( 'Posts related with this destination', 'tour-operator' ),
	'type'       => 'post_select',
	'use_ajax'   => false,
	'repeatable' => true,
	'allow_none' => true,
	'query'      => array(
		'post_type'      => 'post',
		'nopagin'        => true,
		'posts_per_page' => '-1',
		'orderby'        => 'title',
		'order'          => 'ASC',
	),
);

$metabox['fields'][] = array(
	'id'   => 'accommodation_title',
	'name' => esc_html__( 'Accommodation', 'tour-operator' ),
	'type' => 'title',
);

$metabox['fields'][] = array(
	'id'         => 'accommodation_to_destination',
	'name'       => esc_html__( 'Accommodation related with this destination', 'tour-operator' ),
	'type'       => 'post_select',
	'use_ajax'   => false,
	'repeatable' => true,
	'allow_none' => true,
	'query'      => array(
		'post_type'      => 'accommodation',
		'nopagin'        => true,
		'posts_per_page' => '-1',
		'orderby'        => 'title',
		'order'          => 'ASC',
	),
);

$metabox['fields'][] = array(
	'id'   => 'tours_title',
	'name' => esc_html__( 'Tours', 'tour-operator' ),
	'type' => 'title',
);

$metabox['fields'][] = array(
	'id'         => 'tour_to_destination',
	'name'       => esc_html__( 'Tours related with this destination', 'tour-operator' ),
	'type'       => 'post_select',
	'use_ajax'   => false,
	'repeatable' => true,
	'allow_none' => true,
	'query'      => array(
		'post_type'      => 'tour',
		'nopagin'        => true,
		'posts_per_page' => '-1',
		'orderby'        => 'title',
		'order'          => 'ASC',
	),
);

$metabox['fields'] = apply_filters( 'lsx_to_destination_custom_fields', $metabox['fields'] );

return $metabox;
