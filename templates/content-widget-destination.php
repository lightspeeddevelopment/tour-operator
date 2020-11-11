<?php
/**
 * Activity Widget Content Part
 *
 * @package     tour-operator
 * @category    activity
 * @subpackage  widget
 */

global $disable_placeholder, $disable_text, $disable_view_more, $post;

$has_single = ! lsx_to_is_single_disabled();
$permalink  = '';

if ( $has_single ) {
	$permalink = get_the_permalink();
}
?>
<article <?php post_class(); ?>>
	<?php if ( empty( $disable_placeholder ) ) { ?>
		<div class="lsx-to-widget-thumb">
			<?php
			if ( $has_single ) {
				?>
<a href="<?php echo esc_url( $permalink ); ?>"><?php } ?>
				<?php lsx_thumbnail( 'lsx-thumbnail-wide' ); ?>
			<?php
			if ( $has_single ) {
				?>
</a><?php } ?>
		</div>
	<?php } ?>

	<div class="lsx-to-widget-content">
		<h4 class="lsx-to-widget-title text-center">
			<?php
			if ( $has_single ) {
				?>
<a href="<?php echo esc_url( $permalink ); ?>"><?php } ?>
				<?php the_title(); ?>
			<?php
			if ( $has_single ) {
				?>
</a><?php } ?>
		</h4>

		<?php
			// if ( empty( $disable_text ) ) {
			// lsx_to_tagline( '<p class="lsx-to-widget-tagline text-center">', '</p>' );
			// }
		?>

		<?php
			ob_start();
			lsx_to_widget_entry_content_top();
			the_excerpt();
			lsx_to_widget_entry_content_bottom();
			$excerpt = ob_get_clean();
		if ( empty( $disable_text ) && ! empty( $excerpt ) ) {
			echo wp_kses_post( $excerpt );
		} elseif ( $has_single && true !== $disable_view_more && '1' !== $disable_view_more ) {
			?>
				<p class="moretag-wrapper"><a href="<?php echo esc_url( $permalink ); ?>" class="moretag"><?php esc_html_e( 'View more', 'tour-operator' ); ?></a></p>
				<?php
		}
		?>
	</div>
</article>
