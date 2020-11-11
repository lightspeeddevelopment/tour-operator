<?php
/**
 * LSX Customizer integration.
 *
 * @package     tour-operator
 * @subpackage  layout
 * @license     GPL3
 */

if ( ! defined( 'ABSPATH' ) ) {
	return; // Exit if accessed directly.
}

/**
 * New colors scheme for LSX Customizer.
 */
function lsx_to_customizer_colors_scheme( $array ) {
	$array['tour-operator'] = array(
		'label'  => esc_html__( 'LSX Tour Operator', 'tour-operator' ),
		'colors' => array(
			'button_background_color'                   => '#1098AD',
			'button_background_hover_color'             => '#0E8395',
			'button_text_color'                         => '#FFFFFF',
			'button_text_color_hover'                   => '#FFFFFF',
			'button_shadow'                             => '#0C7383',

			'button_cta_background_color'               => '#F7AE00',
			'button_cta_background_hover_color'         => '#EDA700',
			'button_cta_text_color'                     => '#FFFFFF',
			'button_cta_text_color_hover'               => '#FFFFFF',
			'button_cta_shadow'                         => '#AB7800',

			'top_menu_background_color'                 => '#F2F2F2',
			'top_menu_link_color'                       => '#1098AD',
			'top_menu_link_hover_color'                 => '#F7AE00',
			'top_menu_icon_color'                       => '#434343',
			'top_menu_icon_hover_color'                 => '#F7AE04',
			'top_menu_dropdown_color'                   => '#374750',
			'top_menu_dropdown_hover_color'             => '#2B3840',
			'top_menu_dropdown_link_color'              => '#FFFFFF',
			'top_menu_dropdown_link_hover_color'        => '#1098AD',

			'header_background_color'                   => '#FFFFFF',
			'header_link_color'                         => '#1098AD',
			'header_link_hover_color'                   => '#F7AE00',
			'header_description_color'                  => '#434343',

			'main_menu_background_color'                => '#FFFFFF',
			'main_menu_link_color'                      => '#515151',
			'main_menu_link_hover_color'                => '#1098AD',
			'main_menu_dropdown_background_color'       => '#374750',
			'main_menu_dropdown_background_hover_color' => '#2B3840',
			'main_menu_dropdown_link_color'             => '#FFFFFF',
			'main_menu_dropdown_link_hover_color'       => '#1098AD',

			'banner_background_color'                   => '#2B3840',
			'banner_text_color'                         => '#FFFFFF',
			'banner_text_image_color'                   => '#FFFFFF',
			'banner_breadcrumb_background_color'        => '#374750',
			'banner_breadcrumb_text_color'              => '#919191',
			'banner_breadcrumb_text_selected_color'     => '#FFFFFF',

			'background_color'                          => '#F6F6F6',
			'body_line_color'                           => '#DADDDF',
			'body_text_heading_color'                   => '#4A4A4A',
			'body_text_small_color'                     => '#919191',
			'body_text_color'                           => '#4A4A4A',
			'body_link_color'                           => '#1098AD',
			'body_link_hover_color'                     => '#F7AE00',
			'body_section_full_background_color'        => '#333333',
			'body_section_full_text_color'              => '#FFFFFF',
			'body_section_full_link_color'              => '#1098AD',
			'body_section_full_link_hover_color'        => '#F7AE00',
			'body_section_full_cta_background_color'    => '#1098AD',
			'body_section_full_cta_text_color'          => '#FFFFFF',
			'body_section_full_cta_link_color'          => '#374750',
			'body_section_full_cta_link_hover_color'    => '#F7AE00',

			'footer_cta_background_color'               => '#232222',
			'footer_cta_text_color'                     => '#FFFFFF',
			'footer_cta_link_color'                     => '#1098AD',
			'footer_cta_link_hover_color'               => '#F7AE00',

			'footer_widgets_background_color'           => '#333333',
			'footer_widgets_text_color'                 => '#FFFFFF',
			'footer_widgets_link_color'                 => '#1098AD',
			'footer_widgets_link_hover_color'           => '#F7AE00',

			'footer_background_color'                   => '#232222',
			'footer_text_color'                         => '#ffffff',
			'footer_link_color'                         => '#1098AD',
			'footer_link_hover_color'                   => '#F7AE00',
		),
	);

	return $array;
}
add_filter( 'lsx_customizer_colour_choices', 'lsx_to_customizer_colors_scheme' );
