<?php
/**
 * Template Tags
 *
 * @package   tour-operator
 * @author    LightSpeed
 * @license   {license}
 * @link
 * @copyright 2016 LightSpeedDevelopment
 */

if ( ! function_exists( 'lsx_to_videos' ) ) {
	/**
	 * Outputs the Tours Videos
	 *
	 * @param       $before | string
	 * @param       $after  | string
	 * @param       $echo   | boolean
	 * @return      string
	 *
	 * @package     tour-operator
	 * @subpackage  template-tags
	 * @category    tour
	 */
	function lsx_to_videos( $before = '', $after = '', $echo = true ) {
		global $columns;

		$videos       = get_post_meta( get_the_ID(), 'videos', false );
		$envira_video = get_post_meta( get_the_ID(), 'envira_video', true );
		$return       = false;

		if ( ( ! empty( $videos ) && is_array( $videos ) ) || ( function_exists( 'envira_gallery' ) && ! empty( $envira_video ) ) ) {
			if ( function_exists( 'envira_gallery' ) && ! empty( $envira_video ) ) {
				// Envira Gallery
				ob_start();
				envira_gallery( $envira_video );
				$return = ob_get_clean();
			} else {
				// Custom Gallery
				$carousel_id = rand( 20, 20000 );
				$columns     = 3;
				$interval    = 'false';
				$post_type   = 'video';
				$content     = '';

				$content .= '<div class="slider-container lsx-to-widget-items">';
				$content .= '<div id="slider-' . esc_attr( $carousel_id ) . '" class="lsx-to-slider">';
				$content .= '<div class="lsx-to-slider-wrap">';
				$content .= '<div class="lsx-to-slider-inner lsx-to-slider-video" data-interval="' . esc_attr( $interval ) . '" data-slick=\'{ "slidesToShow": ' . esc_attr( $columns ) . ', "slidesToScroll": ' . esc_attr( $columns ) . ' }\'>';

				foreach ( $videos as $video ) {
					$content .= '<div class="lsx-to-widget-item-wrap lsx-' . esc_attr( $post_type ) . '">';
					$content .= '<article class="type-video">';

					if ( isset( $video['url'] ) && '' !== $video['url'] ) {
						$content .= '<div class="lsx-to-widget-thumb embed-responsive embed-responsive-16by9">';

						$embed_key = 'to_videos_embed_' . sanitize_title( $video['url'] );
						$embed     = get_transient( $embed_key );

						if ( false === $embed ) {
							$embed = wp_oembed_get(
								$video['url'],
								array(
									'class' => 'embed-responsive-item',
								)
							);

							if ( ! empty( $embed ) ) {
								set_transient( $embed_key, $embed, ( 24 * 60 * 60 ) );
							}
						}

						$content .= $embed;
						$content .= '</div>';

						$has_content = ( isset( $video['title'] ) && '' !== $video['title'] ) || ( isset( $video['description'] ) && '' !== $video['description'] );

						if ( $has_content ) {
							$content .= '<div class="lsx-to-widget-content">';

							if ( isset( $video['title'] ) && '' !== $video['title'] ) {
								$content .= '<h4 class="lsx-to-widget-title text-center">' . $video['title'] . '</h4>';
							}

							if ( isset( $video['description'] ) && '' !== $video['description'] ) {
								$content .= apply_filters( 'the_content', $video['description'] );
							}

							$content .= '</div>';
						}
					}

					$content .= '</article>';
					$content .= '</div>';
				}

				$content .= '</div>';
				$content .= '</div>';
				$content .= '</div>';
				$content .= '</div>';

				$return = $content;
			}

			$protocol = is_ssl() ? 'https' : 'http';
			$return   = preg_replace( '/href="\/\//i', 'href="' . $protocol . '://', $return );

			$return = $before . $return . $after;

			if ( $echo ) {
				echo wp_kses_post( $return );
			} else {
				return $return;
			}
		}
	}
}
