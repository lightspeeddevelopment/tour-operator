<?php
/**
 * Generic (post, page, ...) Widget Content Part
 *
 * @package  tour-operator
 */

global $disable_placeholder, $disable_text;

?>
<article <?php post_class(); ?>>
	<?php if ( empty( $disable_placeholder ) ) { ?>
		<div class="lsx-to-widget-thumb">
			<a href="<?php the_permalink(); ?>">
				<?php lsx_thumbnail( 'lsx-thumbnail-wide' ); ?>
			</a>
		</div>
	<?php } ?>

	<div class="lsx-to-widget-content">
		<h4 class="lsx-to-widget-title text-center">
			<a href="<?php the_permalink(); ?>">
				<?php the_title(); ?>
			</a>
		</h4>
		<?php
			ob_start();
			lsx_to_widget_entry_content_top();
			the_excerpt();
			lsx_to_widget_entry_content_bottom();
			$excerpt = ob_get_clean();

		if ( empty( $disable_text ) && ! empty( $excerpt ) ) {
			echo wp_kses_post( $excerpt );
			lsx_to_tagline( '<p class="lsx-to-widget-tagline text-center">', '</p>' );
		} elseif ( $has_single ) {
			?>
			<p><a href="<?php echo esc_url( $permalink ); ?>" class="moretag"><?php esc_html_e( 'View more', 'tour-operator' ); ?></a></p>
			<?php
		}

		?>
	</div>
</article>
