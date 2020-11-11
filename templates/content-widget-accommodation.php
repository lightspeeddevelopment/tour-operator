<?php
/**
 * Accommodation Widget Content Part
 *
 * @package     tour-operator
 * @category    accommodation
 * @subpackage  widget
 */

global $disable_placeholder, $disable_text, $disable_view_more, $post;

$has_single = ! lsx_to_is_single_disabled();
$permalink  = '';

if ( $has_single ) {
	$permalink = get_the_permalink();
}
?>

<?php lsx_widget_entry_before(); ?>

<article <?php post_class(); ?>>

	<?php lsx_widget_entry_top(); ?>

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

		<?php lsx_widget_entry_content_top(); ?>

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

		<div class="lsx-to-widget-meta-data">
			<?php
				$meta_class = 'lsx-to-meta-data lsx-to-meta-data-';

				lsx_to_price( '<span class="' . $meta_class . 'price"><span class="lsx-to-meta-data-key">' . esc_html__( 'From price', 'tour-operator' ) . ':</span> ', '</span>' );
				the_terms( get_the_ID(), 'travel-style', '<span class="' . $meta_class . 'style"><span class="lsx-to-meta-data-key">' . esc_html__( 'Style', 'tour-operator' ) . ':</span> ', ', ', '</span>' );
				the_terms( get_the_ID(), 'accommodation-brand', '<span class="' . $meta_class . 'brand"><span class="lsx-to-meta-data-key">' . esc_html__( 'Brand', 'tour-operator' ) . ':</span> ', ', ', '</span>' );
				the_terms( get_the_ID(), 'accommodation-type', '<span class="' . $meta_class . 'type"><span class="lsx-to-meta-data-key">' . esc_html__( 'Type', 'tour-operator' ) . ':</span> ', ', ', '</span>' );
				lsx_to_connected_countries( '<span class="' . $meta_class . 'destinations"><span class="lsx-to-meta-data-key">' . esc_html__( 'Location', 'tour-operator' ) . ':</span> ', '</span>' );
				lsx_to_accommodation_room_total( '<span class="' . $meta_class . 'rooms"><span class="lsx-to-meta-data-key">' . esc_html__( 'Rooms', 'tour-operator' ) . ':</span> ', '</span>' );
				lsx_to_accommodation_rating( '<span class="' . $meta_class . 'rating"><span class="lsx-to-meta-data-key">' . esc_html__( 'Rating', 'tour-operator' ) . ':</span> ', '</span>' );
			?>
		</div>

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

		<?php lsx_widget_entry_content_bottom(); ?>

	</div>

	<?php lsx_widget_entry_bottom(); ?>

</article>
<?php lsx_widget_entry_after(); ?>
