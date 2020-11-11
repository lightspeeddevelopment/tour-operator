<?php
/**
 * All of the Tour Operators Actions.
 *
 * @package     tour-operator
 * @subpackage  layout
 * @license     GPL3
 */

$current_theme    = wp_get_theme();
$current_template = $current_theme->get_template();
$theme_name       = $current_theme->get( 'Name' );

if ( 'lsx' !== $current_template && 'LSX' !== $theme_name ) {
	/**
	 * Semantic <content> hooks
	 *
	 * $lsx_supports[] = 'content';
	 */

	function lsx_content_wrap_before() {
		do_action( 'lsx_content_wrap_before' );
	}

	function lsx_content_wrap_after() {
		do_action( 'lsx_content_wrap_after' );
	}

	function lsx_content_before() {
		do_action( 'lsx_content_before' );
	}

	function lsx_content_after() {
		do_action( 'lsx_content_after' );
	}

	function lsx_content_top() {
		do_action( 'lsx_content_top' );
	}

	function lsx_content_bottom() {
		do_action( 'lsx_content_bottom' );
	}

	function lsx_content_post_tags() {
		do_action( 'lsx_content_post_tags' );
	}

	function lsx_content_sharing() {
		do_action( 'lsx_content_sharing' );
	}

	/**
	 * Semantic <entry> hooks
	 *
	 * $lsx_supports[] = 'entry';
	 */

	function lsx_entry_before() {
		do_action( 'lsx_entry_before' );
	}

	function lsx_entry_after() {
		do_action( 'lsx_entry_after' );
	}

	function lsx_entry_top() {
		do_action( 'lsx_entry_top' );
	}

	function lsx_entry_bottom() {
		do_action( 'lsx_entry_bottom' );
	}

	/**
	 * .main classes
	 */
	function lsx_main_class() {
		return 'col-sm-12 ';
	}

	function lsx_thumbnail( $size = 'thumbnail', $image_src = false ) {
		echo wp_kses_post( lsx_get_thumbnail( $size, $image_src ) );
	}

	function lsx_get_thumbnail( $size, $image_src = false ) {
		global $wp_version;
		if ( false === $image_src ) {
			$post_id           = get_the_ID();
			$post_thumbnail_id = get_post_thumbnail_id( $post_id );
		} elseif ( false !== $image_src ) {
			if ( is_numeric( $image_src ) ) {
				$post_thumbnail_id = $image_src;
			} else {
				$post_thumbnail_id = lsx_get_attachment_id_from_src( $image_src );
			}
		}

		$size = apply_filters( 'lsx_thumbnail_size', $size );
		$img  = false;

		if ( 'lsx-thumbnail-single' === $size || 'lsx-thumbnail-wide' === $size || 'thumbnail' === $size ) {
			$srcset = false;
			$img    = wp_get_attachment_image_src( $post_thumbnail_id, $size );
			$img    = $img[0];
		} else {
			$srcset = true;
			$img    = wp_get_attachment_image_srcset( $post_thumbnail_id, $size );

			if ( false === $img ) {
				$srcset = false;
				$img    = wp_get_attachment_image_src( $post_thumbnail_id, $size );
				$img    = $img[0];
			}
		}

		if ( $srcset ) {
			$img = '<img alt="' . the_title_attribute( 'echo=0' ) . '" class="attachment-responsive wp-post-image lsx-responsive" srcset="' . esc_attr( $img ) . '" />';
		} else {
			$img = '<img alt="' . the_title_attribute( 'echo=0' ) . '" class="attachment-responsive wp-post-image lsx-responsive" src="' . esc_url( $img ) . '" />';
		}

		if ( version_compare( $wp_version, '5.5', '>=' ) ) {
			if ( $srcset ) {
				$img = '<img loading="lazy" alt="' . the_title_attribute( 'echo=0' ) . '" class="attachment-responsive wp-post-image lsx-responsive wp-lazy" srcset="' . esc_attr( $img ) . '" />';
			} else {
				$img = '<img loading="lazy" alt="' . the_title_attribute( 'echo=0' ) . '" class="attachment-responsive wp-post-image lsx-responsive wp-lazy" src="' . esc_url( $img ) . '" />';
			}
		}

		if ( ! version_compare( $wp_version, '5.5', '>=' ) ) {
			$img = apply_filters( 'lsx_lazyload_filter_images', $img );
		}
		return $img;
	}

	function lsx_get_attachment_id_from_src( $image_src ) {
		$post_id = wp_cache_get( $image_src, 'lsx_get_attachment_id_from_src' );

		if ( false === $post_id ) {
			global $wpdb;
			$post_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE guid='%s' LIMIT 1", $image_src ) );
			wp_cache_set( $image_src, $post_id, 'lsx_get_attachment_id_from_src', 3600 );
		}

		return $post_id;
	}
}

function lsx_to_content( $slug, $name = null ) {
	do_action( 'lsx_to_content', $slug, $name );
}

function lsx_to_modal_meta() {
	do_action( 'lsx_to_modal_meta' );
}

function lsx_to_entry_content_top() {
	do_action( 'lsx_to_entry_content_top' );
}

function lsx_to_entry_content_bottom() {
	do_action( 'lsx_to_entry_content_bottom' );
}

function lsx_to_widget_entry_content_top() {
	do_action( 'lsx_to_widget_entry_content_top' );
}

function lsx_to_widget_entry_content_bottom() {
	do_action( 'lsx_to_widget_entry_content_bottom' );
}

function lsx_to_fast_facts() {
	do_action( 'lsx_to_fast_facts' );
}
