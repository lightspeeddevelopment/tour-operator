<?php
/**
 * Destination Archive
 *
 * @package  tour-operator
 * @category destination
 */

get_header(); ?>

	<?php lsx_content_wrap_before(); ?>

	<div id="primary" class="content-area col-sm-12 <?php echo esc_attr( lsx_main_class() ); ?>">

		<?php lsx_content_before(); ?>

		<main id="main" class="site-main" role="main">

			<?php lsx_content_top(); ?>

			<?php if ( have_posts() ) : ?>

				<?php
					$header_before            = '';
					$group_items_by_continent = false;

				if ( isset( tour_operator()->options['destination'] ) && isset( tour_operator()->options['destination']['group_items_by_continent'] ) ) {
					$group_items_by_continent = true;
				}

					$count = 0;
				?>

				<?php
				while ( have_posts() ) :
					the_post();
					?>

					<?php
					if ( true === $group_items_by_continent ) {
						$header_current = get_the_terms( get_the_id(), 'continent' );
						$header_html    = '';

						if ( ! is_wp_error( $header_current ) && is_array( $header_current ) && ! empty( $header_current ) ) {
							$header_current = $header_current[0]->name;

							if ( $header_before !== $header_current ) {
								$header_html   = '<h3 class="lsx-to-archive-items-separator lsx-title">' . $header_current . '</h3>';
								$header_before = $header_current;
							}
						}
					}

						$count++;
					?>

					<?php if ( $count > 1 && true === $group_items_by_continent && ! empty( $header_html ) ) { ?>
						</div>
					<?php } ?>

					<?php if ( 1 === $count || ( true === $group_items_by_continent && ! empty( $header_html ) ) ) { ?>
						<?php if ( true === $group_items_by_continent && ! empty( $header_html ) ) { ?>
							<?php echo wp_kses_post( $header_html ); ?>
						<?php } ?>

						<div class="row lsx-to-archive-items lsx-to-archive-template-<?php echo esc_attr( tour_operator()->archive_layout ); ?> lsx-to-archive-template-image-<?php echo esc_attr( tour_operator()->archive_list_layout_image_style ); ?>">
					<?php } ?>

					<div class="<?php echo esc_attr( lsx_to_archive_class( 'lsx-to-archive-item' ) ); ?>">
						<?php lsx_to_content( 'content', 'destination' ); ?>
					</div>

					<?php if ( $GLOBALS['wp_query']->post_count === $count ) { ?>
						</div>
					<?php } ?>

				<?php endwhile; ?>

			<?php else : ?>

				<?php get_template_part( 'partials/content', 'none' ); ?>

			<?php endif; ?>

			<?php lsx_content_bottom(); ?>

		</main><!-- #main -->

		<?php lsx_content_after(); ?>

	</div><!-- #primary -->

	<?php lsx_content_wrap_after(); ?>

<?php // get_sidebar(); ?>

<?php
get_footer();
