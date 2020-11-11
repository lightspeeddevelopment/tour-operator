/*
 * admin.js
 */

jQuery( document ).ready(
	function() {

		/*
		* Choose Image
		*/
		if (undefined === window.lsx_thumbnail_image_add) {
			jQuery( document ).on(
				'click',
				'.lsx-thumbnail-image-add',
				function(e) {
					e.preventDefault();
					e.stopPropagation();

					tb_show( 'Choose a Featured Image', 'media-upload.php?type=image&feature_image_text_button=1&TB_iframe=1' );

					var $this = jQuery( this ),
					$td       = $this.parent( 'td' );

					window.send_to_editor = function(html) {
						var $image      = jQuery( html ).is( 'img' ) ? jQuery( html ) : jQuery( 'img', html ),
						image_thumbnail = $image.html(),
						image_src       = $image.attr( 'src' ),
						image_class;

						image_class = $image.attr( 'class' );
						image_class = image_class.split( 'wp-image-' );

						$td.find( '.thumbnail-preview, .banner-preview' ).append( '<img width="150" src="' + image_src + '" />' );
						$td.find( 'input.input_image' ).val( image_src );
						$td.find( 'input.input_image_id' ).val( image_class[1] );
						$this.hide();
						$td.find( '.lsx-thumbnail-image-delete, .lsx-thumbnail-image-remove' ).show();

						tb_remove();
					}

					return false;
				}
			);

			window.lsx_thumbnail_image_add = true;
		}

		/*
		* Delete Image
		*/
		if (undefined === window.lsx_thumbnail_image_delete) {
			jQuery( document ).on(
				'click',
				'.lsx-thumbnail-image-delete, .lsx-thumbnail-image-remove',
				function(e) {
					e.preventDefault();
					e.stopPropagation();

					var $this = jQuery( this ),
					$td       = $this.parent( 'td' );

					$td.find( 'input.input_image_id' ).val( '' );
					$td.find( 'input.input_image' ).val( '' );
					$td.find( '.thumbnail-preview, .banner-preview' ).html( '' );
					$this.hide();
					$td.find( '.lsx-thumbnail-image-add' ).show();

					return false;
				}
			);

			window.lsx_thumbnail_image_delete = true;
		}

		/*
		* Subtabs navigation
		*/
		if (undefined === window.lsx_thumbnail_subtabs_nav) {
			jQuery( document ).on(
				'click',
				'.ui-tab-nav a',
				function(e) {
					e.preventDefault();
					e.stopPropagation();

					var $this = jQuery( this );

					jQuery( '.ui-tab-nav a.active' ).removeClass( 'active' );
					$this.addClass( 'active' );
					jQuery( '.ui-tab.active' ).removeClass( 'active' );
					$this.closest( '.uix-field-wrapper' ).find( $this.attr( 'href' ) ).addClass( 'active' );

					return false;
				}
			);

			window.lsx_thumbnail_subtabs_nav = true;
		}

		jQuery( document ).on(
			'click',
			'.lsx-to-theme-notice .notice-dismiss',
			function() {
				jQuery.ajax(
					{
						url: ajaxurl,
						data: {
							action: 'lsx_to_theme_notice_dismiss'
						}
					}
				);
			}
		);

	}
);
