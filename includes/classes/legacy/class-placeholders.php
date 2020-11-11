<?php
/**
 * Placeholders.
 *
 * @package   Placeholders
 * @author    LightSpeed https://lsdev.biz
 * @license   GPL3
 * @link
 */

namespace lsx\legacy;

/**
 * Plugin class.
 */
class Placeholders {

	/**
	 * The slug for this plugin
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'placeholders';

	/**
	 * The active post types
	 *
	 * @var      array
	 */
	protected $post_types = array();

	/**
	 * Holds class isntance
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Holds the check for thumb value
	 *
	 * @var      string
	 */
	protected $checking_for_thumb = false;

	/**
	 * Initialize the plugin by setting localization, filters, and
	 * administration functions.
	 */
	public function __construct( $post_types = false ) {

		if ( false !== $post_types ) {
			$this->post_types = $post_types;
		}

		$this->post_types[] = 'post';
		$this->post_types[] = 'page';

		add_action(
			'lsx_to_framework_display_tab_content',
			array(
				$this,
				'display_settings',
			),
			15,
			1
		);

		foreach ( $this->post_types as $post_type ) {
			add_action(
				'lsx_to_framework_' . $post_type . '_tab_content',
				array(
					$this,
					'placeholder_settings',
				),
				10,
				2
			);
		}

		if ( ! is_admin() ) {
			add_filter(
				'get_post_metadata',
				array(
					$this,
					'default_post_thumbnail',
				),
				11,
				3
			);
			add_filter(
				'get_term_metadata',
				array(
					$this,
					'default_term_thumbnail',
				),
				11,
				3
			);

			add_filter(
				'wp_get_attachment_image_src',
				array(
					$this,
					'super_placeholder_filter',
				),
				20,
				4
			);
			add_filter(
				'wp_calculate_image_srcset_meta',
				array(
					$this,
					'super_placeholder_srcset_filter',
				),
				20,
				4
			);
			add_filter(
				'wp_calculate_image_srcset',
				array(
					$this,
					'super_placeholder_calculate_image_srcset_filter',
				),
				20,
				5
			);
		}
	}

	/**
	 * Returns the placeholder call.
	 */
	public static function placeholder_url( $text_size = null, $post_type = null, $size = null ) {
		if ( null === $text_size ) {
			$text_size = 38;
		}

		if ( null === $post_type ) {
			$post_type = get_post_type();
		}

		if ( null === $size ) {
			$size = 'lsx-thumbnail-wide';
		}

		$options      = get_option( '_lsx-to_settings', false );
		$holdit_width = '';

		if ( is_array( $size ) && 2 === count( $size ) ) {
			$post_type    = 'general';
			$holdit_width = $size[0] . 'x' . $size[1];
		} else {
			switch ( $size ) {
				case 'thumbnail':
				case 'medium':
				case 'large':
				case 'full':
					$holdit_width = 'general';
					break;

				case 'lsx-thumbnail-single':
					$holdit_width = '750x350';
					break;

				case 'lsx-thumbnail-square':
					$holdit_width = '350x350';
					break;

				case 'lsx-banner':
					$holdit_width = '1920x600';
					$post_type    = 'banner';
					break;

				case 'lsx-thumbnail-wide':
				default:
					$holdit_width = '360x168';
					break;
			}
		}
		$placeholder    = LSX_TO_URL . 'assets/img/placeholders/placeholder-' . $post_type . '-' . $holdit_width . '.jpg';
		$placeholder_id = false;

		// First Check for a default, then check if there is one set by post type.
		if ( isset( $options['general'] ) && isset( $options['general']['default_placeholder_id'] ) && ! empty( $options['general']['default_placeholder_id'] ) ) {
			$placeholder_id = $options['general']['default_placeholder_id'];
		}

		if ( 'general' !== $post_type ) {
			if ( 'post' === $post_type ) {
				if ( isset( $options['general'] ) && isset( $options['general']['posts_placeholder_id'] ) && ! empty( $options['general']['posts_placeholder_id'] ) && '' !== $options['general']['posts_placeholder_id'] ) {
					$placeholder_id = $options['general']['posts_placeholder_id'];
				}
			} else {
				if ( isset( $options[ $post_type ] ) && isset( $options[ $post_type ]['featured_placeholder_id'] ) && ! empty( $options[ $post_type ]['featured_placeholder_id'] ) && '' !== $options[ $post_type ]['featured_placeholder_id'] ) {
					$placeholder_id = $options[ $post_type ]['featured_placeholder_id'];
				}
			}
		}

		if ( false !== $placeholder_id && '' !== $placeholder_id ) {
			$temp_src_array = wp_get_attachment_image_src( $placeholder_id, $size );
			if ( is_array( $temp_src_array ) && ! empty( $temp_src_array ) ) {
				$placeholder = $temp_src_array[0];
			}
		}

		return $placeholder;
	}

	/**
	 * The post default placeholder call.
	 */
	public function default_post_thumbnail( $meta, $post_id, $meta_key ) {
		$options = get_option( '_lsx-to_settings', false );

		// This ensures our "super" placeholder will always show.
		$placeholder = 'lsx-placeholder';
		if ( '_thumbnail_id' === $meta_key && false !== $options ) {

			$post_type = get_post_field( 'post_type', $post_id );

			// If the post types posts placeholder has been disabled then skip.
			if ( 'post' === $post_type && isset( $options['general'] ) && isset( $options['general']['disable_blog_placeholder'] ) ) {
				return $meta;
			}

			// First Check for a default, then check if there is one set by post type.
			if ( isset( $options['display'] ) && isset( $options['display']['default_placeholder_id'] ) && ! empty( $options['display']['default_placeholder_id'] ) ) {
				$placeholder = $options['display']['default_placeholder_id'];
			}
			if ( 'post' === $post_type ) {
				if ( isset( $options['display'] ) && isset( $options['display']['posts_placeholder_id'] ) && ! empty( $options['display']['posts_placeholder_id'] ) && '' !== $options['display']['posts_placeholder_id'] ) {
					$placeholder = $options['display']['posts_placeholder_id'];
				}
			} else {
				if ( isset( $options[ $post_type ] ) && isset( $options[ $post_type ]['featured_placeholder_id'] ) && ! empty( $options[ $post_type ]['featured_placeholder_id'] ) && '' !== $options[ $post_type ]['featured_placeholder_id'] ) {
					$placeholder = $options[ $post_type ]['featured_placeholder_id'];
				}
			}
		}

		if ( '_thumbnail_id' === $meta_key && false === $this->checking_for_thumb ) {
			$this->checking_for_thumb = true;
			$image                    = get_post_meta( $post_id, '_thumbnail_id', true );
			$this->checking_for_thumb = false;
			if ( ! empty( $image ) ) {
				return $meta;
			}

			// onlong but here it is. no ID

			return $placeholder;
		}

		return $meta;
	}

	/**
	 * The term default placeholder call.
	 */
	public function default_term_thumbnail( $meta, $post_id, $meta_key ) {

		if ( 'thumbnail' === $meta_key ) {
			$options     = get_option( '_lsx-to_settings', false );
			$placeholder = 'lsx-placeholder';

			// First Check for a default, then check if there is one set by post type.
			if ( false !== $options && isset( $options['display'] ) && isset( $options['display']['default_placeholder_id'] ) && ! empty( $options['display']['default_placeholder_id'] ) ) {
				$placeholder = $options['display']['default_placeholder_id'];
			}
		}

		if ( 'thumbnail' === $meta_key && false === $this->checking_for_thumb ) {
			$this->checking_for_thumb = true;
			$image                    = get_term_meta( $post_id, 'thumbnail', true );
			$this->checking_for_thumb = false;
			if ( false !== $image && '' !== $image && ! empty( $image ) ) {
				return $meta;
			}

			// onlong but here it is. no ID
			return $placeholder;
		}

		return $meta;
	}

	/**
	 * The term default placeholder call.
	 */
	public function super_placeholder_filter( $image, $attachment_id, $size, $icon ) {
		if ( '' === $attachment_id || false === $attachment_id ) {
			$image = array(
				$this->placeholder_url( null, null, $size ),
			);

			switch ( $size ) {
				case 'thumbnail':
				case 'medium':
				case 'large':
				case 'full':
					$image[] = get_option( "{$size}_size_w", 150 );
					$image[] = get_option( "{$size}_size_h", 150 );
					$image[] = true;
					break;

				case 'lsx-thumbnail-wide':
					$image[] = 360;
					$image[] = 168;
					$image[] = true;
					break;

				case 'lsx-thumbnail-square':
					$image[] = 350;
					$image[] = 350;
					$image[] = true;
					break;

				case 'lsx-banner':
					$image[] = 1920;
					$image[] = 600;
					$image[] = true;
					break;

				case 'lsx-thumbnail-single':
					$image[] = 750;
					$image[] = 350;
					$image[] = false;
					break;

				default:
					if ( is_array( $size ) ) {
						$image[] = $size[0];
						$image[] = $size[1];
						$image[] = true;
					}
					break;
			}

			$image = apply_filters( 'lsx_to_placeholder_url', $image );
		}

		return $image;
	}

	/**
	 * The term default placeholder call.
	 */
	public function super_placeholder_srcset_filter( $image_meta, $size_array, $image_src, $attachment_id ) {
		if ( '' === $attachment_id || false === $attachment_id ) {
			$sizes = array(
				'thumbnail'            => array(
					'file'      => $this->placeholder_url( null, null, 'thumbnail' ),
					'width'     => get_option( 'thumbnail_size_w', 150 ),
					'height'    => get_option( 'thumbnail_size_h', 150 ),
					'mime-type' => 'image/jpeg',
				),
				'medium'               => array(
					'file'      => $this->placeholder_url( null, null, 'medium' ),
					'width'     => get_option( 'medium_size_w', 300 ),
					'height'    => get_option( 'medium_size_h', 300 ),
					'mime-type' => 'image/jpeg',
				),
				'large'                => array(
					'file'      => $this->placeholder_url( null, null, 'large' ),
					'width'     => get_option( 'large_size_w', 1024 ),
					'height'    => get_option( 'large_size_h', 1024 ),
					'mime-type' => 'image/jpeg',
				),
				'full'                 => array(
					'file'      => $this->placeholder_url( null, null, 'full' ),
					'width'     => get_option( 'large_size_w', 1024 ),
					'height'    => get_option( 'large_size_h', 1024 ),
					'mime-type' => 'image/jpeg',
				),
				'lsx-thumbnail-single' => array(
					'file'      => $this->placeholder_url( null, null, 'lsx-thumbnail-single' ),
					'width'     => '750',
					'height'    => '350',
					'mime-type' => 'image/jpeg',
				),
				'lsx-thumbnail-wide'   => array(
					'file'      => $this->placeholder_url( null, null, 'lsx-thumbnail-wide' ),
					'width'     => '360',
					'height'    => '168',
					'mime-type' => 'image/jpeg',
				),
				'lsx-thumbnail-square' => array(
					'file'      => $this->placeholder_url( null, null, 'lsx-thumbnail-square' ),
					'width'     => '350',
					'height'    => '350',
					'mime-type' => 'image/jpeg',
				),
				'lsx-banner'           => array(
					'file'      => $this->placeholder_url( null, null, 'lsx-banner' ),
					'width'     => '1920',
					'height'    => '600',
					'mime-type' => 'image/jpeg',
				),
			);

			$image_meta = array(
				'width'  => '750',
				'height' => '350',
				'file'   => $this->placeholder_url( null, null, 'lsx-thumbnail-single' ),
				'sizes'  => $sizes,
			);
		}

		return $image_meta;
	}

	/**
	 * Overwrites the sources call
	 */
	public function super_placeholder_calculate_image_srcset_filter( $sources, $size_array, $image_src, $image_meta, $attachment_id ) {
		if ( '' === $attachment_id || false === $attachment_id ) {
			$sources = array(
				'1920' => array(
					'url'        => $this->placeholder_url( null, null, 'lsx-thumbnail-single' ),
					'descriptor' => 'w',
					'value'      => '1920',
				),
				'300'  => array(
					'url'        => $this->placeholder_url( null, null, 'lsx-thumbnail-wide' ),
					'descriptor' => 'w',
					'value'      => '300',
				),
				'768'  => array(
					'url'        => $this->placeholder_url( null, null, 'lsx-thumbnail-single' ),
					'descriptor' => 'w',
					'value'      => '768',
				),
				'1024' => array(
					'url'        => $this->placeholder_url( null, null, 'lsx-thumbnail-single' ),
					'descriptor' => 'w',
					'value'      => '1024',
				),
			);
		}

		return $sources;
	}

	/**
	 * The placeholder settings that output on the frameworks tabs.
	 */
	public function display_settings( $tab = false ) {

		if ( 'placeholders' !== $tab ) {
			return false;
		}

		if ( class_exists( 'LSX_Banners' ) ) { ?>
			<tr class="form-field banner-placeholder-wrap">
				<th scope="row">
					<label for="banner"> <?php esc_html_e( 'Banner Placeholder', 'tour-operator' ); ?></label>
				</th>
				<td>
					<input class="input_image_id" type="hidden" {{#if banner_placeholder_id}} value="{{banner_placeholder_id}}" {{/if}}
					name="banner_placeholder_id" />
					<input class="input_image" type="hidden" {{#if banner_placeholder}} value="{{banner_placeholder}}" {{/if}} name="banner_placeholder" />
					<div class="thumbnail-preview">
						{{#if banner_placeholder}}<img src="{{banner_placeholder}}" width="150"/>{{/if}}
					</div>
					<a {{#if banner_placeholder}}style="display:none;" {{/if}} class="button-secondary lsx-thumbnail-image-add"><?php esc_html_e( 'Choose Image', 'tour-operator' ); ?></a>
					<a {{#unless banner_placeholder}}style="display:none;" {{/unless}} class="button-secondary lsx-thumbnail-image-delete"><?php esc_html_e( 'Delete', 'tour-operator' ); ?></a>
				</td>
			</tr>
		<?php } ?>
		<tr class="form-field">
			<th scope="row">
				<label for="banner"> <?php esc_html_e( 'Archive Placeholder', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input class="input_image_id" type="hidden" {{#if default_placeholder_id}} value="{{default_placeholder_id}}" {{/if}}
				name="default_placeholder_id" />
				<input class="input_image" type="hidden" {{#if default_placeholder}} value="{{default_placeholder}}" {{/if}}
				name="default_placeholder" />
				<div class="thumbnail-preview">
					{{#if default_placeholder}}<img src="{{default_placeholder}}" width="150"/>{{/if}}
				</div>
				<a {{#if default_placeholder}}style="display:none;" {{/if}} class="button-secondary lsx-thumbnail-image-add"><?php esc_html_e( 'Choose Image', 'tour-operator' ); ?></a>
				<a {{#unless default_placeholder}}style="display:none;" {{/unless}} class="button-secondary lsx-thumbnail-image-delete"><?php esc_html_e( 'Delete', 'tour-operator' ); ?></a>
			</td>
		</tr>
		{{#unless disable_blog_placeholder}}
		<tr class="form-field">
			<th scope="row">
				<label for="posts_placeholder"> <?php esc_html_e( 'Blog Placeholder', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input class="input_image_id" type="hidden" {{#if posts_placeholder_id}} value="{{posts_placeholder_id}}" {{/if}} name="posts_placeholder_id" />
				<input class="input_image" type="hidden" {{#if posts_placeholder}} value="{{posts_placeholder}}" {{/if}}
				name="posts_placeholder" />
				<div class="thumbnail-preview">
					{{#if posts_placeholder}}<img src="{{posts_placeholder}}" width="150"/>{{/if}}
				</div>
				<a {{#if posts_placeholder}}style="display:none;" {{/if}} class="button-secondary lsx-thumbnail-image-add"><?php esc_html_e( 'Choose Image', 'tour-operator' ); ?></a>
				<a {{#unless posts_placeholder}}style="display:none;" {{/unless}} class="button-secondary lsx-thumbnail-image-delete"><?php esc_html_e( 'Delete', 'tour-operator' ); ?></a>
			</td>
		</tr>
		{{/unless}}
		<tr class="form-field">
			<th scope="row">
				<label for="description"><?php esc_html_e( 'Disable Placeholder on Blog Posts', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input type="checkbox" {{#if disable_blog_placeholder}} checked="checked" {{/if}} name="disable_blog_placeholder" />
				<small><?php esc_html_e( 'This disables the placeholder on blog posts.', 'tour-operator' ); ?></small>
			</td>
		</tr>
		<?php
	}

	/**
	 * The placeholder settings that output on the post type tabs.
	 *
	 * @param $post_type string
	 * @param $tab       string
	 *
	 * @return null
	 */
	public function placeholder_settings( $post_type = false, $tab = false ) {
		if ( 'placeholders' !== $tab ) {
			return false;
		}
		?>
		<?php if ( class_exists( 'LSX_Banners' ) ) { ?>
			<tr class="form-field banner-placeholder-wrap">
				<th scope="row">
					<label for="banner"> <?php esc_html_e( 'Banner', 'tour-operator' ); ?></label>
				</th>
				<td>
					<input class="input_image_id" type="hidden" {{#if banner_placeholder_id}} value="{{banner_placeholder_id}}" {{/if}}
					name="banner_placeholder_id" />
					<input class="input_image" type="hidden" {{#if banner_placeholder}} value="{{banner_placeholder}}" {{/if}} name="banner_placeholder" />
					<div class="thumbnail-preview">
						{{#if banner_placeholder}}<img src="{{banner_placeholder}}" width="150"/>{{/if}}
					</div>
					<a {{#if banner_placeholder}}style="display:none;" {{/if}} class="button-secondary lsx-thumbnail-image-add"><?php esc_html_e( 'Choose Image', 'tour-operator' ); ?></a>
					<a {{#unless banner_placeholder}}style="display:none;" {{/unless}} class="button-secondary lsx-thumbnail-image-delete"><?php esc_html_e( 'Delete', 'tour-operator' ); ?></a>
				</td>
			</tr>
		<?php } ?>

		<tr class="form-field featured-placeholder-wrap">
			<th scope="row">
				<label for="featured_placeholder"><?php esc_html_e( 'Archive Image', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input class="input_image_id" type="hidden" {{#if featured_placeholder_id}} value="{{featured_placeholder_id}}" {{/if}}
				name="featured_placeholder_id" />
				<input class="input_image" type="hidden" {{#if featured_placeholder}} value="{{featured_placeholder}}" {{/if}} name="featured_placeholder" />
				<div class="thumbnail-preview">
					{{#if featured_placeholder}}<img src="{{featured_placeholder}}" width="150"/>{{/if}}
				</div>
				<a {{#if featured_placeholder}}style="display:none;" {{/if}} class="button-secondary lsx-thumbnail-image-add"><?php esc_html_e( 'Choose Image', 'tour-operator' ); ?></a>
				<a {{#unless featured_placeholder}}style="display:none;" {{/unless}} class="button-secondary lsx-thumbnail-image-delete"><?php esc_html_e( 'Delete', 'tour-operator' ); ?></a>
			</td>
		</tr>
		<?php
	}
}
