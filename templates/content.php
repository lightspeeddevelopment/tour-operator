<?php
/**
 * Generic (post, page, ...) Content Part (required on search results)
 *
 * @package  tour-operator
 */
?>

<?php lsx_entry_before(); ?>

<article <?php post_class( 'lsx-to-archive-container' ); ?>>
	<?php
		$thumbnail_id = get_post_thumbnail_id( get_the_ID() );
		$image_arr    = wp_get_attachment_image_src( $thumbnail_id, 'lsx-thumbnail-single' );

	if ( is_array( $image_arr ) ) {
		$image_src = $image_arr[0];
	}
	?>

	<div class="lsx-to-archive-thumb">
		<a href="<?php the_permalink(); ?>" style="background-image: url('<?php echo esc_url( $image_src ); ?>')">
			<?php lsx_thumbnail( 'lsx-thumbnail-wide' ); ?>
		</a>
	</div>

	<div class="lsx-to-archive-wrapper">
		<div class="lsx-to-archive-content">
			<h3 class="lsx-to-archive-content-title">
				<a href="<?php the_permalink(); ?>" title="<?php esc_html_e( 'Read more', 'tour-operator' ); ?>">
					<?php
						the_title();
						do_action( 'lsx_to_the_title_end', get_the_ID() );
					?>
				</a>
			</h3>

			<div <?php lsx_to_entry_class( 'entry-content' ); ?>>
										   <?php
											lsx_to_entry_content_top();
											the_excerpt();
											lsx_to_entry_content_bottom();
											?>
			</div>
		</div>
	</div>

	<?php if ( 'grid' === tour_operator()->archive_layout ) : ?>
		<a href="<?php the_permalink(); ?>" class="moretag"><?php esc_html_e( 'View more', 'tour-operator' ); ?></a>
	<?php endif; ?>

</article>

<?php
lsx_entry_after();
