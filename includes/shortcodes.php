<?php
/**
 * LSX Tour Operator Helper Functions
 *
 * @package   tour_operator
 * @author    LightSpeed
 * @license   GPL-2.0+
 * @copyright 2017 LightSpeed
 */

/**
 * Adds a shortcode to emulate Post Type widget.
 */
function lsx_to_post_type_widget_shortcode( $atts ) {
	ob_start();

	the_widget( 'lsx\legacy\Widget', $atts );

	$content = ob_get_clean();
	$content = preg_replace( '/<!--[^>]+-->/', '', $content );

	return $content;
}

add_shortcode( 'lsx_to_post_type_widget', 'lsx_to_post_type_widget_shortcode' );

/**
 * Adds a shortcode to emulate Taxonomy widget.
 */
function lsx_to_taxonomy_widget_shortcode( $atts ) {
	ob_start();

	the_widget( 'lsx\legacy\Taxonomy_Widget', $atts );

	$content = ob_get_clean();
	$content = preg_replace( '/<!--[^>]+-->/', '', $content );

	return $content;
}

add_shortcode( 'lsx_to_taxonomy_widget', 'lsx_to_taxonomy_widget_shortcode' );

/**
 * TO Archive Shortcode.
 */
function lsx_to_archive_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'layout'    => 'list',
			'post_type' => 'tour',
			'orderby'   => 'date',
			'order'     => 'DESC',
			'limit'     => 10,
			'include'   => '',
			'featured'  => false,
		),
		$atts,
		'lsx_to_archive'
	);

	if ( ! empty( $include ) ) {
		$include = explode( ',', $include );

		$args = array(
			'post_type'      => $atts['post_type'],
			'posts_per_page' => $atts['limit'],
			'post__in'       => $atts['include'],
			'orderby'        => 'post__in',
			'order'          => $atts['order'],
		);
	} else {
		$args = array(
			'post_type'      => $atts['post_type'],
			'posts_per_page' => $atts['limit'],
			'orderby'        => $atts['orderby'],
			'order'          => $atts['order'],
		);
	}

	if ( ! empty( $featured ) ) {
		$args['meta_key']   = 'featured';
		$args['meta_value'] = 1;
	}

	if ( 'none' !== $orderby ) {
		$args['disabled_custom_post_order'] = true;
	}

	$query = new \WP_Query( $args );

	ob_start();

	if ( $query->have_posts() ) : ?>

		<div class="row lsx-to-archive-items lsx-to-archive-template-<?php echo esc_attr( $atts['layout'] ); ?>">

			<?php
				global $post, $lsx_to_archive;

				$temp           = $lsx_to_archive;
				$lsx_to_archive = 1;
			?>

			<?php
			while ( $query->have_posts() ) :
				$query->the_post();
				?>

				<div class="<?php echo esc_attr( lsx_to_archive_class( 'lsx-to-archive-item' ) ); ?>">
					<?php lsx_to_content( 'content', get_post_type() ); ?>
				</div>

			<?php endwhile; ?>

			<?php
				$lsx_to_archive = $temp;
				wp_reset_postdata();
			?>

		</div>

		<?php
	endif;

	$content = ob_get_clean();
	$content = preg_replace( '/<!--[^>]+-->/', '', $content );

	return $content;
}

add_shortcode( 'lsx_to_archive', 'lsx_to_archive_shortcode' );
