<?php
/**
 * LSX Tour Operator - Accommodation Metabox config
 *
 * @package   tour_operator
 * @author    LightSpeed
 * @license   GPL-2.0+
 * @link
 * @copyright 2017 LightSpeedDevelopment
 */

$metabox = array(
	'title'  => esc_html__( 'LSX Tour Operator Plugin', 'tour-operator' ),
	'pages'  => 'accommodation',
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

if ( ! class_exists( 'LSX_Banners' ) ) {
	$metabox['fields'][] = array(
		'id'   => 'tagline',
		'name' => esc_html__( 'Tagline', 'tour-operator' ),
		'type' => 'text',
	);
}

if ( class_exists( '\lsx\legacy\Field_Pattern' ) ) {
	$metabox['fields'] = array_merge( $metabox['fields'], \lsx\legacy\Field_Pattern::price() );
}

$metabox['fields'][] = array(
	'id'      => 'price_type',
	'name'    => esc_html__( 'Price Type', 'tour-operator' ),
	'type'    => 'select',
	'options' => array(
		'none'                         => 'Select a type',
		'per_person_per_night'         => esc_html__( 'Per Person Per Night', 'tour-operator' ),
		'per_person_sharing'           => esc_html__( 'Per Person Sharing', 'tour-operator' ),
		'per_person_sharing_per_night' => esc_html__( 'Per Person Sharing Per Night', 'tour-operator' ),
		'total_percentage'             => esc_html__( 'Percentage Off Your Price.', 'tour-operator' ),
	),
);

$metabox['fields'][] = array(
	'id'      => 'included',
	'name'    => esc_html__( 'Included', 'tour-operator' ),
	'type'    => 'wysiwyg',
	'options' => array(
		'editor_height' => '100',
	),
);

$metabox['fields'][] = array(
	'id'      => 'not_included',
	'name'    => esc_html__( 'Not Included', 'tour-operator' ),
	'type'    => 'wysiwyg',
	'options' => array(
		'editor_height' => '100',
	),
);

if ( class_exists( 'LSX_TO_Team' ) ) {
	$metabox['fields'][] = array(
		'id'         => 'team_to_accommodation',
		'name'       => esc_html__( 'Accommodation Expert', 'tour-operator' ),
		'type'       => 'post_select',
		'use_ajax'   => false,
		'allow_none' => true,
		'query'      => array(
			'post_type'      => 'team',
			'nopagin'        => true,
			'posts_per_page' => '-1',
			'orderby'        => 'title',
			'order'          => 'ASC',
		),
	);
}

if ( ! isset( tour_operator()->options['display']['maps_disable'] ) && empty( tour_operator()->options['display']['maps_disable'] ) ) {
	$metabox['fields'][] = array(
		'id'   => 'location_title',
		'name' => esc_html__( 'Location', 'tour-operator' ),
		'type' => 'title',
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


$fast_facts_fields = array(
	array(
		'id'   => 'fast_facts_title',
		'name' => esc_html__( 'Fast Facts', 'tour-operator' ),
		'type' => 'title',
	),

	array(
		'id'      => 'rating_type',
		'name'    => esc_html__( 'Rating Type', 'tour-operator' ),
		'type'    => 'select',
		'options' => array(
			'Unspecified'      => esc_html__( 'Unspecified', 'tour-operator' ),
			'TGCSA'            => esc_html__( 'TGCSA', 'tour-operator' ),
			'Hotelstars Union' => esc_html__( 'Hotelstars Union', 'tour-operator' ),
		),
	),

	array(
		'id'         => 'rating',
		'name'       => esc_html__( 'Rating', 'tour-operator' ),
		'type'       => 'radio',
		'options'    => array( '0', '1', '2', '3', '4', '5' ),
		'allow_none' => true,
	),

	array(
		'id'   => 'number_of_rooms',
		'name' => esc_html__( 'Number of Rooms', 'tour-operator' ),
		'type' => 'text',
	),

	array(
		'id'   => 'checkin_time',
		'name' => esc_html__( 'Check-in Time', 'tour-operator' ),
		'type' => 'time',
	),

	array(
		'id'   => 'checkout_time',
		'name' => esc_html__( 'Check-out Time', 'tour-operator' ),
		'type' => 'time',
	),

	array(
		'id'   => 'minimum_child_age',
		'name' => esc_html__( 'Minimum Child Age', 'tour-operator' ),
		'type' => 'text',
	),

	array(
		'id'       => 'spoken_languages',
		'name'     => esc_html__( 'Spoken Languages', 'tour-operator' ),
		'type'     => 'select',
		'multiple' => true,
		'options'  => array(
			'afrikaans'  => esc_html__( 'Afrikaans', 'tour-operator' ),
			'chinese'    => esc_html__( 'Chinese', 'tour-operator' ),
			'dutch'      => esc_html__( 'Dutch', 'tour-operator' ),
			'english'    => esc_html__( 'English', 'tour-operator' ),
			'flemish'    => esc_html__( 'Flemish', 'tour-operator' ),
			'french'     => esc_html__( 'French', 'tour-operator' ),
			'german'     => esc_html__( 'German', 'tour-operator' ),
			'indian'     => esc_html__( 'Indian', 'tour-operator' ),
			'italian'    => esc_html__( 'Italian', 'tour-operator' ),
			'japanese'   => esc_html__( 'Japanese', 'tour-operator' ),
			'portuguese' => esc_html__( 'Portuguese', 'tour-operator' ),
			'russian'    => esc_html__( 'Russian', 'tour-operator' ),
			'spanish'    => esc_html__( 'Spanish', 'tour-operator' ),
			'swahili'    => esc_html__( 'Swahili', 'tour-operator' ),
			'xhosa'      => esc_html__( 'Xhosa', 'tour-operator' ),
			'zulu'       => esc_html__( 'Zulu', 'tour-operator' ),
		),
	),

	array(
		'id'       => 'suggested_visitor_types',
		'name'     => esc_html__( 'Friendly', 'tour-operator' ),
		'type'     => 'select',
		'multiple' => true,
		'options'  => array(
			'business'   => esc_html__( 'Business', 'tour-operator' ),
			'children'   => esc_html__( 'Children', 'tour-operator' ),
			'disability' => esc_html__( 'Disability', 'tour-operator' ),
			'leisure'    => esc_html__( 'Leisure', 'tour-operator' ),
			'luxury'     => esc_html__( 'Luxury', 'tour-operator' ),
			'pet'        => esc_html__( 'Pet', 'tour-operator' ),
			'romance'    => esc_html__( 'Romance', 'tour-operator' ),
			'vegetarian' => esc_html__( 'Vegetarian', 'tour-operator' ),
			'weddings'   => esc_html__( 'Weddings', 'tour-operator' ),
		),
	),

	array(
		'id'       => 'special_interests',
		'name'     => esc_html__( 'Special Interests', 'tour-operator' ),
		'type'     => 'select',
		'multiple' => true,
		'options'  => array(
			'adventure'              => esc_html__( 'Adventure', 'tour-operator' ),
			'battlefields'           => esc_html__( 'Battlefields', 'tour-operator' ),
			'beach_coastal'          => esc_html__( 'Beach / Coastal', 'tour-operator' ),
			'big-5'                  => esc_html__( 'Big 5', 'tour-operator' ),
			'birding'                => esc_html__( 'Birding', 'tour-operator' ),
			'cycling'                => esc_html__( 'Cycling', 'tour-operator' ),
			'fishing'                => esc_html__( 'Fishing', 'tour-operator' ),
			'flora'                  => esc_html__( 'Flora', 'tour-operator' ),
			'golf'                   => esc_html__( 'Golf', 'tour-operator' ),
			'gourmet'                => esc_html__( 'Gourmet', 'tour-operator' ),
			'hiking'                 => esc_html__( 'Hiking', 'tour-operator' ),
			'history-and-culture'    => esc_html__( 'History & Culture', 'tour-operator' ),
			'indigenous-culture-art' => esc_html__( 'Indigenous Culture / Art', 'tour-operator' ),
			'leisure'                => esc_html__( 'Leisure', 'tour-operator' ),
			'nature-relaxation'      => esc_html__( 'Nature Relaxation', 'tour-operator' ),
			'shopping'               => esc_html__( 'Shopping', 'tour-operator' ),
			'sports'                 => esc_html__( 'Sports', 'tour-operator' ),
			'star-gazing'            => esc_html__( 'Star Gazing', 'tour-operator' ),
			'watersports'            => esc_html__( 'Watersports', 'tour-operator' ),
			'wildlife'               => esc_html__( 'Wildlife', 'tour-operator' ),
			'wine'                   => esc_html__( 'Wine', 'tour-operator' ),
		),
	),
);

$metabox['fields'] = array_merge( $metabox['fields'], $fast_facts_fields );

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
		'query'      => array(
			'post_type'      => 'envira',
			'nopagin'        => true,
			'posts_per_page' => '-1',
			'orderby'        => 'title',
			'order'          => 'ASC',
		),
		'allow_none' => true,
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

$metabox['fields'][] = array(
	'id'   => 'units_title',
	'name' => esc_html__( 'Units', 'tour-operator' ),
	'type' => 'title',
);

$metabox['fields'][] = array(
	'id'         => 'units',
	'name'       => '',
	'type'       => 'group',
	'repeatable' => true,
	'sortable'   => true,
	'desc'       => '',
	'fields'     => array(
		array(
			'id'      => 'type',
			'name'    => esc_html__( 'Type', 'tour-operator' ),
			'type'    => 'select',
			'options' => \lsx\legacy\Accommodation::get_instance()->unit_types,
		),
		array(
			'id'   => 'title',
			'name' => esc_html__( 'Title', 'tour-operator' ),
			'type' => 'text',
		),
		array(
			'id'      => 'description',
			'name'    => esc_html__( 'Description', 'tour-operator' ),
			'type'    => 'textarea',
			'options' => array(
				'editor_height' => '100',
			),
		),
		array(
			'id'   => 'price',
			'name' => esc_html__( 'Price', 'tour-operator' ),
			'type' => 'text',
		),
		array(
			'id'         => 'gallery',
			'name'       => esc_html__( 'Gallery', 'tour-operator' ),
			'type'       => 'image',
			'repeatable' => true,
			'show_size'  => false,
		),
	),
);

$metabox['fields'][] = array(
	'id'   => 'posts_title',
	'name' => esc_html__( 'Posts', 'tour-operator' ),
	'type' => 'title',
);

$metabox['fields'][] = array(
	'id'         => 'post_to_accommodation',
	'name'       => esc_html__( 'Posts related with this accommodation', 'tour-operator' ),
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
	'id'   => 'destinations_title',
	'name' => esc_html__( 'Destinations', 'tour-operator' ),
	'type' => 'title',
);

$metabox['fields'][] = array(
	'id'         => 'destination_to_accommodation',
	'name'       => esc_html__( 'Destinations related with this accommodation', 'tour-operator' ),
	'type'       => 'post_select',
	'use_ajax'   => false,
	'repeatable' => true,
	'allow_none' => true,
	'query'      => array(
		'post_type'      => 'destination',
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
	'id'         => 'tour_to_accommodation',
	'name'       => esc_html__( 'Tours related with this accommodation', 'tour-operator' ),
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

$metabox['fields'] = apply_filters( 'lsx_to_accommodation_custom_fields', $metabox['fields'] );

return $metabox;
