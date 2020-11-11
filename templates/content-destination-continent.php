<?php
/**
 * Destination-Continent Content Part
 *
 * @package  tour-operator
 * @category destination
 */

global $continent;
?>

<?php lsx_entry_before(); ?>

<article <?php post_class( 'lsx-to-archive-container' ); ?>>
	<?php
		$thumbnail_id = get_term_meta( $continent->term_id, 'thumbnail', true );

	if ( empty( $thumbnail_id ) ) {
		$image_src = 'https://placeholdit.imgix.net/~text?txtsize=38&txt=' . urlencode( get_bloginfo( 'name' ) ) . '&w=360&h=168';
	} else {
		$image_arr = wp_get_attachment_image_src( $thumbnail_id, 'lsx-thumbnail-wide' );

		if ( is_array( $image_arr ) ) {
			$image_src = $image_arr[0];
		}
	}
	?>

	<div class="lsx-to-archive-thumb">
		<a href="<?php echo esc_url( get_term_link( $continent ) ); ?>" style="background-image: url('<?php echo esc_url( $image_src ); ?>')">
			<img class="attachment-responsive wp-post-image lsx-responsive" src="<?php echo esc_url( $image_src ); ?>">
		</a>
	</div>

	<div class="lsx-to-archive-wrapper">
		<div class="lsx-to-archive-content">
			<h3 class="lsx-to-archive-content-title">
				<a href="<?php echo esc_url( get_term_link( $continent ) ); ?>" title="<?php esc_html_e( 'Read more', 'tour-operator' ); ?>">
					<?php echo esc_html( $continent->name ); ?>
				</a>
			</h3>
		</div>
	</div>

	<?php if ( 'grid' === tour_operator()->archive_layout ) : ?>
		<a href="<?php echo esc_url( get_term_link( $continent ) ); ?>" class="moretag"><?php esc_html_e( 'View more', 'tour-operator' ); ?></a>
	<?php endif; ?>

</article>

<?php
lsx_entry_after();
