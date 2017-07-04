<?php
/**
 * Modal Content
 *
 * @package 	tour-operator
 * @category	modals
 */
?>
<article>
	<div class="thumbnail">
		<a href="<?php the_permalink(); ?>">
			<?php lsx_thumbnail( 'lsx-thumbnail-single' ); ?>
		</a>
	</div>

	<h4 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>

	<div class="entry-content">
		<?php lsx_to_modal_meta(); ?>

		<?php
			ob_start();
			the_excerpt();
			$excerpt = ob_get_clean();
			$excerpt = str_replace( 'moretag', 'moretag btn cta-btn', $excerpt );
			echo wp_kses_post( $excerpt );
		?>
	</div><!-- .entry-content -->
</article><!-- #post-## -->
