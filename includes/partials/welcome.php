<?php
	$lsx_to_documentation = esc_url( 'https://www.lsdev.biz/documentation/tour-operator-plugin/' );
	$extensions_link      = esc_url( 'https://ww.lsdev.biz/product-category/tour-operator-extensions/' );
	$tour_operator_link   = esc_url( 'https://www.lsdev.biz/product/tour-operator-plugin/' );
	$release_notes_link   = esc_url( 'https://www.lsdev.biz/lsx/' );
	$lsx_to_news_link     = esc_url( 'https://www.lsdev.biz/tour-operators/' );
?>

<div class="wrap about-wrap">
	<div class="row">
		<div class="col-md-12 top-header">
			<h1 class="small"><?php esc_html_e( 'Welcome to the LSX Tour Operator Plugin', 'tour-operator' ); ?></h1>
			<p><?php esc_html_e( 'You are running version 1.0 - Thanks for Keeping up to date!', 'tour-operator' ); ?></p>
		</div>
	</div>

	<div class="row">
		<div class="col-md-8">
			<div class="video">
				<iframe src="https://player.vimeo.com/video/174546330" width="100%" height="390" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
			</div>

			<div class="box info">
				<div class="row">
					<div class="col-md-6">
						<div class="box-clean">
							<h3><?php esc_html_e( 'Getting started', 'tour-operator' ); ?></h3>
							<p><a href="<?php echo wp_kses_post( $lsx_to_documentation ); ?>" target="_blank"><?php esc_html_e( 'Check out the documentation', 'tour-operator' ); ?></a></p>
						</div>
					</div>

					<div class="col-md-6">
						<div class="box-clean">
							<h3><?php esc_html_e( 'Release notes', 'tour-operator' ); ?></h3>
							<p><a href="<?php echo wp_kses_post( $release_notes_link ); ?>" target="_blank"><?php esc_html_e( 'Get the latest on updates to the LSX Tour Operator plugin', 'tour-operator' ); ?></a></p>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="box-clean">
							<h3><?php esc_html_e( 'Looking for more features?', 'tour-operator' ); ?></h3>
							<p><a href="<?php echo wp_kses_post( $extensions_link ); ?>" target="_blank"><?php esc_html_e( 'Check out our suite of add ons', 'tour-operator' ); ?></a></p>
						</div>
					</div>

					<div class="col-md-6">
						<div class="box-clean">
							<h3><?php esc_html_e( 'News for Tour Operators', 'tour-operator' ); ?></h3>
							<p><a href="<?php echo wp_kses_post( $lsx_to_news_link ); ?>" target="_blank"><?php esc_html_e( 'Check out our blog posts geared towards helping Tour Operators succeed online', 'tour-operator' ); ?></a></p>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-md-4">
			<div class="box rate">
				<h2><?php esc_html_e( 'Help us keep the LSX Tour Operator core FREE', 'tour-operator' ); ?></h2>
				<p><?php esc_html_e( '5-star ratings help us to bring the LSX Tour Operator plugin to more users. The more users we have the more we get requests to add features to make the plugin even better for you. We couldn’t do it without your support.', 'tour-operator' ); ?></p>

				<p class="star-rating">
					<span><?php esc_html_e( 'Rate it 5 stars', 'tour-operator' ); ?> - </span>
					<div class="star star-full" aria-hidden="true"></div>
					<div class="star star-full" aria-hidden="true"></div>
					<div class="star star-full" aria-hidden="true"></div>
					<div class="star star-full" aria-hidden="true"></div>
					<div class="star star-full" aria-hidden="true"></div>
				</p>

				<div class="more-button">
					<a href="<?php echo wp_kses_post( $tour_operator_link ); ?>" target="_blank" class="button button-primary">
						<?php esc_html_e( 'Rate It!', 'tour-operator' ); ?>
					</a>
				</div>
			</div>

			<div class="box newsletter">
				<h2><?php esc_html_e( 'Please consider signing up to our Tourism Industry newsletter', 'tour-operator' ); ?></h2>

				<!-- Begin MailChimp Signup Form -->
				<?php // @codingStandardsIgnoreStart ?>
				<link href="//cdn-images.mailchimp.com/embedcode/classic-10_7.css" rel="stylesheet" type="text/css">
				<style type="text/css">
					#mc_embed_signup{background:#fff; clear:left; font:14px Helvetica,Arial,sans-serif; }
					/* Add your own MailChimp form style overrides in your site stylesheet or in this style block.
					   We recommend moving this block and the preceding CSS link to the HEAD of your HTML file. */
				</style>
				<?php // @codingStandardsIgnoreEnd ?>
				<div id="mc_embed_signup">
					<form action="//lsdev.us2.list-manage.com/subscribe/post?u=e50b2c5c82f4b42ea978af479&amp;id=92c36218e5" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
						<div id="mc_embed_signup_scroll">

							<div class="indicates-required"><span class="asterisk">*</span> indicates required</div>
							<div class="mc-field-group">
								<label for="mce-EMAIL">Email Address  <span class="asterisk">*</span>
								</label>
								<input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL">
							</div>
							<div class="mc-field-group">
								<label for="mce-FNAME">First Name </label>
								<input type="text" value="" name="FNAME" class="" id="mce-FNAME">
							</div>
							<div class="mc-field-group">
								<label for="mce-LNAME">Last Name </label>
								<input type="text" value="" name="LNAME" class="" id="mce-LNAME">
							</div>
							<div class="mc-field-group input-group">
								<strong>Interest Group </strong>
								<ul><li><input type="checkbox" value="1" name="group[46417][1]" id="mce-group[46417]-46417-0"><label for="mce-group[46417]-46417-0">Online Tourism</label></li>
									<li><input type="checkbox" value="2" name="group[46417][2]" id="mce-group[46417]-46417-1"><label for="mce-group[46417]-46417-1">WooCommerce</label></li>
									<li><input type="checkbox" value="4" name="group[46417][4]" id="mce-group[46417]-46417-2"><label for="mce-group[46417]-46417-2">WordPress Development</label></li>
								</ul>
							</div>
							<div id="mce-responses" class="clear">
								<div class="response" id="mce-error-response" style="display:none"></div>
								<div class="response" id="mce-success-response" style="display:none"></div>
							</div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
							<div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_e50b2c5c82f4b42ea978af479_92c36218e5" tabindex="-1" value=""></div>
							<div class="clear"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button"></div>
						</div>
					</form>
				</div>
				<?php // @codingStandardsIgnoreStart ?>
				<script type='text/javascript' src='//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js'></script>
				<script type='text/javascript'>(function($) {window.fnames = new Array(); window.ftypes = new Array();fnames[0]='EMAIL';ftypes[0]='email';fnames[1]='FNAME';ftypes[1]='text';fnames[2]='LNAME';ftypes[2]='text';}(jQuery));var $mcj = jQuery.noConflict(true);</script>
				<?php // @codingStandardsIgnoreEnd ?>
				<!--End mc_embed_signup-->
			</div>
		</div>
	</div>
</div>
