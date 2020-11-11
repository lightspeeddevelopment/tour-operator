<?php
/**
 * Backend actions for the LSX Tour Operator Plugin
 *
 * @package   \lsx\legacy\Admin
 * @author    LightSpeed
 * @license   GPL3
 * @link
 * @copyright 2016 LightSpeedDevelopment
 */

namespace lsx\legacy;

/**
 * Main plugin class.
 *
 * @package \lsx\legacy\Admin
 * @author  LightSpeed
 */
class Admin extends Tour_Operator {

	/**
	 * Holds the maps class
	 *
	 * @var object lsx\legacy\Videos_Admin();
	 */
	public $videos = '';

	/**
	 * Initialize the plugin by setting localization, filters, and
	 * administration functions.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	public function __construct() {
		$this->options = get_option( '_lsx-to_settings', false );
		$this->set_vars();

		$this->videos = Video::get_instance();

		add_action( 'init', array( $this, 'init' ) );
		// add_action( 'admin_menu', array( $this, 'register_menu_pages' ) );
		// add_action( 'custom_menu_order', array( $this, 'reorder_menu_pages' ) );
		// add_action( 'admin_head', array( $this, 'select_submenu_pages' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_stylescripts' ) );
		add_action( 'cmb_save_custom', array( $this, 'post_relations' ), 3, 20 );

		add_filter( 'plugin_action_links_' . plugin_basename( LSX_TO_CORE ), array( $this, 'add_action_links' ) );

		add_action( 'default_hidden_meta_boxes', array( $this, 'default_hidden_meta_boxes' ), 10, 2 );
		add_filter( 'upload_mimes', array( $this, 'allow_svgimg_types' ) );
	}

	/**
	 * Output the form field for this metadata when adding a new term
	 *
	 * @since 0.1.0
	 */
	public function init() {
		if ( is_admin() ) {
			$this->connections   = $this->create_post_connections();
			$this->single_fields = apply_filters( 'lsx_to_search_fields', array() );
			$this->taxonomies    = apply_filters( 'lsx_to_taxonomies', $this->taxonomies );

			add_filter( 'lsx_to_taxonomy_widget_taxonomies', array( $this, 'widget_taxonomies' ), 10, 1 );
			add_filter( 'lsx_taxonomy_admin_taxonomies', array( $this, 'widget_taxonomies_slugs' ), 10, 1 );

			if ( ! class_exists( 'LSX_Banners' ) && false !== $this->taxonomies ) {

				add_action( 'create_term', array( $this, 'save_meta' ), 10, 2 );
				add_action( 'edit_term', array( $this, 'save_meta' ), 10, 2 );

				foreach ( array_keys( $this->taxonomies ) as $taxonomy ) {
					add_action( "{$taxonomy}_edit_form_fields", array( $this, 'add_thumbnail_form_field' ), 1, 1 );
					add_action( "{$taxonomy}_edit_form_fields", array( $this, 'add_tagline_form_field' ), 3, 1 );
				}
			}

			add_filter( 'type_url_form_media', array( $this, 'change_attachment_field_button' ), 20, 1 );
		}
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @return    null
	 */
	public function enqueue_admin_stylescripts( $hook ) {
		$screen = get_current_screen();

		if ( ! is_object( $screen ) ) {
			return;
		}

		// TO Pages: Add-ons, Help, Settings and Welcome
		// WP Terms: create/edit term
		$allowed_pages = array(
			'tour-operator_page_to-help',
			'lsx-tour-operator_page_lsx-to-settings',
			'tour-operator_page_lsx-to-settings',
			'term.php',
		);
		if ( ! in_array( $hook, $allowed_pages ) ) {
			return;
		}

		wp_enqueue_script( 'media-upload' );
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_style( 'thickbox' );
		wp_enqueue_script( 'tour-operator-admin-script', LSX_TO_URL . 'assets/js/src/admin.js', array( 'jquery' ), LSX_TO_VER, true );
		wp_enqueue_style( 'tour-operator-admin-style', LSX_TO_URL . 'assets/css/admin.css', array(), LSX_TO_VER );
		wp_style_add_data( 'tour-operator-admin-style', 'rtl', 'replace' );

	}

	/**
	 * Display a custom menu page
	 */
	function menu_dashboard() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Dashboard', 'tour-operator' ); ?></h1>
		</div>
		<?php
	}

	/**
	 * Display the addons page
	 */
	function addons_page() {
		include LSX_TO_PATH . 'includes/partials/add-ons.php';
	}

	/**
	 * Display the help page
	 */
	function help_page() {
		include LSX_TO_PATH . 'includes/partials/help.php';
		include LSX_TO_PATH . 'includes/partials/add-ons.php';
	}

	/**
	 * Display the licenses page
	 */
	function licenses_page() {
		include LSX_TO_PATH . 'includes/partials/licenses.php';
	}

	/**
	 * Sets up the "post relations"
	 *
	 * @return    object
	 */
	public function post_relations( $post_id, $field, $value ) {
		if ( 'group' === $field['type'] && isset( $this->single_fields ) && array_key_exists( $field['id'], $this->single_fields ) ) {
			$delete_counter = array();

			foreach ( $this->single_fields[ $field['id'] ] as $fields_to_save ) {
				$delete_counter[ $fields_to_save ] = 0;
			}

			// Loop through each group in case of repeatable fields
			$relations          = false;
			$previous_relations = false;

			foreach ( $value as $group ) {
				// loop through each of the fields in the group that need to be saved and grab their values.
				foreach ( $this->single_fields[ $field['id'] ] as $fields_to_save ) {
					// Check if its an empty group
					if ( isset( $group[ $fields_to_save ] ) && ! empty( $group[ $fields_to_save ] ) ) {
						if ( $delete_counter[ $fields_to_save ] < 1 ) {
							// If this is a relation field, then we need to save the previous relations to remove any items if need be.
							if ( in_array( $fields_to_save, $this->connections ) ) {
								$previous_relations[ $fields_to_save ] = get_post_meta( $post_id, $fields_to_save, false );
							}

							delete_post_meta( $post_id, $fields_to_save );
							$delete_counter[ $fields_to_save ] ++;
						}

						// Run through each group
						foreach ( $group[ $fields_to_save ] as $field_value ) {
							if ( null !== $field_value ) {
								if ( 1 === $field_value ) {
									$field_value = true;
								}

								add_post_meta( $post_id, $fields_to_save, $field_value );

								// If its a related connection the save that
								if ( in_array( $fields_to_save, $this->connections ) ) {
									$relations[ $fields_to_save ][ $field_value ] = $field_value;
								}
							}
						}
					}
				}
			}//end of the repeatable group foreach

			// If we have relations, loop through them and save the meta
			if ( false !== $relations ) {
				foreach ( $relations as $relation_key => $relation_values ) {
					$temp_field = array(
						'id' => $relation_key,
					);

					$this->save_related_post( $post_id, $temp_field, $relation_values, $previous_relations[ $relation_key ] );
				}
			}
		} else {
			if ( in_array( $field['id'], $this->connections ) ) {
				$this->save_related_post( $post_id, $field, $value );
			}
		}
	}

	/**
	 * Save the reverse post relation.
	 *
	 * @return    null
	 */
	public function save_related_post( $post_id, $field, $value, $previous_values = false ) {
		$ids      = explode( '_to_', $field['id'] );
		$relation = $ids[1] . '_to_' . $ids[0];

		if ( in_array( $relation, $this->connections ) ) {
			if ( false === $previous_values ) {
				$previous_values = get_post_meta( $post_id, $field['id'], false );
			}

			if ( false !== $previous_values && ! empty( $previous_values ) ) {
				foreach ( $previous_values as $tr ) {
					delete_post_meta( $tr, $relation, $post_id );
				}
			}

			if ( is_array( $value ) ) {
				foreach ( $value as $v ) {
					if ( '' !== $v && null !== $v && false !== $v ) {
						add_post_meta( $v, $relation, $post_id );
					}
				}
			}
		}
	}

	/**
	 * Adds in the "settings" link for the plugins.php page
	 */
	public function add_action_links( $links ) {
		$mylinks = array(
			'<a href="' . admin_url( 'admin.php?page=lsx-to-settings' ) . '">' . esc_html__( 'Settings', 'tour-operator' ) . '</a>',
			'<a href="https://www.lsdev.biz/lsx/documentation/lsx-tour-operator/" target="_blank">' . esc_html__( 'Documentation', 'tour-operator' ) . '</a>',
			'<a href="https://www.lsdev.biz/lsx/support/" target="_blank">' . esc_html__( 'Support', 'tour-operator' ) . '</a>',
		);

		return array_merge( $links, $mylinks );
	}

	/**
	 * Output the form field for this metadata when adding a new term
	 *
	 * @since 0.1.0
	 */
	public function widget_taxonomies( $taxonomies ) {
		if ( false !== $this->taxonomies ) {
			$taxonomies = array_merge( $taxonomies, $this->taxonomies );
		}

		return $taxonomies;
	}

	/**
	 * Output the form field for this metadata when adding a new term
	 *
	 * @since 1.0.0
	 */
	public function widget_taxonomies_slugs( $taxonomies ) {
		if ( false !== $this->taxonomies ) {
			$taxonomies = array_merge( $taxonomies, array_keys( $this->taxonomies ) );
		}
		return $taxonomies;
	}

	/**
	 * Output the form field for this metadata when adding a new term
	 *
	 * @since 0.1.0
	 */
	public function add_thumbnail_form_field( $term = false ) {
		if ( is_object( $term ) ) {
			$value         = get_term_meta( $term->term_id, 'thumbnail', true );
			$image_preview = wp_get_attachment_image_src( $value, 'thumbnail' );

			if ( is_array( $image_preview ) ) {
				$image_preview = '<img src="' . esc_url( $image_preview[0] ) . '" width="' . $image_preview[1] . '" height="' . $image_preview[2] . '" class="alignnone size-thumbnail d wp-image-' . $value . '" />';
			}
		} else {
			$image_preview = false;
			$value         = false;
		}
		?>
		<tr class="form-field form-required term-thumbnail-wrap">
			<th scope="row"><label for="thumbnail"><?php esc_html_e( 'Featured Image', 'tour-operator' ); ?></label></th>
			<td>
				<input class="input_image_id" type="hidden" name="thumbnail" value="<?php echo wp_kses_post( $value ); ?>">
				<div class="thumbnail-preview">
					<?php echo wp_kses_post( $image_preview ); ?>
				</div>
				<a style="
				<?php
				if ( '' !== $value && false !== $value ) {
					?>
display:none;<?php } ?>" class="button-secondary lsx-thumbnail-image-add"><?php esc_html_e( 'Choose Image', 'tour-operator' ); ?></a>
				<a style="
				<?php
				if ( '' === $value || false === $value ) {
					?>
display:none;<?php } ?>" class="button-secondary lsx-thumbnail-image-remove"><?php esc_html_e( 'Remove Image', 'tour-operator' ); ?></a>
				<?php wp_nonce_field( 'lsx_to_save_term_thumbnail', 'lsx_to_term_thumbnail_nonce' ); ?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Saves the Taxnomy term banner image
	 *
	 * @since 0.1.0
	 *
	 * @param  int    $term_id
	 * @param  string $taxonomy
	 */
	public function save_meta( $term_id = 0, $taxonomy = '' ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! isset( $_POST['thumbnail'] ) || ! isset( $_POST['tagline'] ) ) {
			return;
		}

		if ( check_admin_referer( 'lsx_to_save_term_thumbnail', 'lsx_to_term_thumbnail_nonce' ) ) {
			if ( ! isset( $_POST['thumbnail'] ) ) {
				return;
			}

			$thumbnail_meta = sanitize_text_field( $_POST['thumbnail'] );
			$thumbnail_meta = ! empty( $thumbnail_meta ) ? $thumbnail_meta : '';

			if ( empty( $thumbnail_meta ) ) {
				delete_term_meta( $term_id, 'thumbnail' );
			} else {
				update_term_meta( $term_id, 'thumbnail', $thumbnail_meta );
			}
		}

		if ( check_admin_referer( 'lsx_to_save_term_tagline', 'lsx_to_term_tagline_nonce' ) ) {
			if ( ! isset( $_POST['tagline'] ) ) {
				return;
			}

			$meta = sanitize_text_field( $_POST['tagline'] );
			$meta = ! empty( $meta ) ? $meta : '';

			if ( empty( $meta ) ) {
				delete_term_meta( $term_id, 'tagline' );
			} else {
				update_term_meta( $term_id, 'tagline', $meta );
			}
		}
	}

	/**
	 * Output the form field for this metadata when adding a new term
	 *
	 * @since 0.1.0
	 */
	public function add_tagline_form_field( $term = false ) {
		if ( is_object( $term ) ) {
			$value = get_term_meta( $term->term_id, 'tagline', true );
		} else {
			$value = false;
		}
		?>
		<tr class="form-field form-required term-tagline-wrap">
			<th scope="row"><label for="tagline"><?php esc_html_e( 'Tagline', 'tour-operator' ); ?></label></th>
			<td>
				<input name="tagline" id="tagline" type="text" value="<?php echo wp_kses_post( $value ); ?>" size="40" aria-required="true">
			</td>
			<?php wp_nonce_field( 'lsx_to_save_term_tagline', 'lsx_to_term_tagline_nonce' ); ?>
		</tr>
		<?php
	}

	/**
	 * Change the "Insert into Post" button text when media modal is used for
	 * feature images
	 */
	public function change_attachment_field_button( $html ) {
		if ( isset( $_GET['feature_image_text_button'] ) ) {
			$html = str_replace( 'value="Insert into Post"', sprintf( 'value="%s"', esc_html__( 'Select featured image', 'tour-operator' ) ), $html );
		}

		return $html;
	}

	/**
	 * Allow SVG files for upload
	 */
	public function allow_svgimg_types( $mimes ) {
		$mimes['svg'] = 'image/svg+xml';
		$mimes['kml'] = 'image/kml+xml';

		return $mimes;
	}

	/**
	 * Hide a few of the meta boxes by default
	 */
	public function default_hidden_meta_boxes( $hidden, $screen ) {
		$post_type = $screen->post_type;

		if ( in_array( $post_type, $this->post_types ) ) {
			$hidden = array(
				'authordiv',
				'revisionsdiv',
				'slugdiv',
				'sharing_meta',
			);

			return $hidden;
		}

		return $hidden;
	}

}
