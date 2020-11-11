<?php
/**
 * Backend actions for the LSX TO Plugin
 *
 * @package   \lsx\admin
 * @author    LightSpeed
 * @license   GPL3
 * @link
 * @copyright 2019 LightSpeedDevelopment
 */

namespace lsx\admin;

/**
 * Main plugin class.
 *
 * @package lsx
 * @author  LightSpeed
 */
class Settings {

	/**
	 * Holds instance of the class
	 *
	 * @since   1.1.0
	 * @var     \lsx\admin\Admin
	 */
	private static $instance;

	/**
	 * Holds the tour operators options
	 *
	 * @since   1.1.0
	 * @var     array
	 */
	public $options;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	public function __construct() {
		$this->options = tour_operator()->options;
		add_filter( 'lsx_to_framework_settings_tabs', array( $this, 'register_settings_tabs' ) );

		if ( isset( $_GET['welcome-page'] ) ) {
			$display_page = sanitize_text_field( $_GET['welcome-page'] );
			$display_page = ! empty( $display_page ) ? $display_page : '';
		}

		if ( ! empty( $display_page ) ) {
			add_action( 'admin_menu', array( $this, 'create_welcome_page' ) );
		} else {
			add_action( 'init', array( $this, 'create_settings_page' ), 100 );
		}

		// Incase the API tab is being loaded via another plugin, we add all the API hooks to the LSX ones.
		add_action( 'lsx_framework_api_tab_content', array( $this, 'lsx_to_framework_api_patch' ) );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.1.0
	 * @return  Tour_Operator  A single instance
	 */
	public static function init() {

		// If the single instance hasn't been set, set it now.
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Returns the array of settings to the UIX Class
	 */
	public function create_settings_page() {
		if ( is_admin() ) {
			if ( ! class_exists( '\lsx_to\ui\uix' ) ) {
				include_once LSX_TO_PATH . 'vendor/uix/uix.php';
			}

			$pages = $this->settings_page_array();
			$uix   = \lsx_to\ui\uix::get_instance( 'lsx-to' );
			$uix->register_pages( $pages );

			foreach ( tour_operator()->legacy->post_types as $post_type => $label ) {
				add_action( 'lsx_to_framework_' . $post_type . '_tab_content', array( $this, 'general_settings' ), 5, 2 );
				add_action( 'lsx_to_framework_' . $post_type . '_tab_content', array( $this, 'archive_settings' ), 12, 2 );
				add_action( 'lsx_to_framework_' . $post_type . '_tab_content', array( $this, 'single_settings' ), 15, 2 );
			}

			add_action( 'lsx_to_framework_dashboard_tab_content', array( $this, 'dashboard_tab_content' ), 10, 1 );
			add_action( 'lsx_to_framework_display_tab_content', array( $this, 'display_tab_content' ), 10, 1 );
			add_action( 'lsx_to_framework_display_tab_content', array( $this, 'map_display_settings' ), 12, 1 );

			if ( ! empty( tour_operator()->legacy->post_types ) ) {
				foreach ( tour_operator()->legacy->post_types as $post_type => $label ) {
					if ( isset( tour_operator()->legacy->options[ $post_type ]['googlemaps_marker'] ) && '' !== tour_operator()->legacy->options[ $post_type ]['googlemaps_marker'] ) {
						tour_operator()->legacy->markers->post_types[ $post_type ] = tour_operator()->legacy->options[ $post_type ]['googlemaps_marker'];
					} else {
						tour_operator()->legacy->markers->post_types[ $post_type ] = LSX_TO_URL . 'assets/img/markers/' . $post_type . '-marker.png';
					}
					add_action( 'lsx_to_framework_' . $post_type . '_tab_content', array( $this, 'post_type_map_settings' ), 10, 2 );
				}
			}
		}
	}

	/**
	 * Add the welcome page
	 */
	public function create_welcome_page() {
		add_submenu_page( 'tour-operator', esc_html__( 'Settings', 'tour-operator' ), esc_html__( 'Settings', 'tour-operator' ), 'manage_options', 'lsx-to-settings', array( $this, 'welcome_page' ) );
	}

	/**
	 * Display the welcome page
	 */
	public function welcome_page() {
		include LSX_TO_PATH . 'includes/partials/welcome.php';
	}

	/**
	 * Returns the array of settings to the UIX Class in the lsx framework
	 */
	public function register_settings_tabs( $tabs ) {
		// This array is for the Admin Pages. each element defines a page that is seen in the admin.

		$post_types = apply_filters( 'lsx_to_post_types', tour_operator()->legacy->post_types );

		if ( false !== $post_types && ! empty( $post_types ) ) {
			foreach ( $post_types as $index => $title ) {
				$disabled = false;

				if ( ! in_array( $index, tour_operator()->legacy->active_post_types ) ) {
					$disabled = true;
				}

				$tabs[ $index ] = array(
					'page_title'       => '',
					'page_description' => '',
					'menu_title'       => $title,
					'template'         => apply_filters( 'lsx_to_settings_path', LSX_TO_PATH, $index ) . 'includes/partials/' . $index . '.php',
					'default'          => false,
					'disabled'         => $disabled,
				);
			}

			ksort( $tabs );
		}

		return $tabs;
	}

	/**
	 * Returns the array of settings to the UIX Class
	 */
	public function settings_page_array() {
		// This array is for the Admin Pages. each element defines a page that is seen in the admin

		$tabs = array( // tabs array are for setting the tab / section templates
			// each array element is a tab with the key as the slug that will be the saved object property
			'general' => array(
				'page_title'       => '',
				'page_description' => '',
				'menu_title'       => esc_html__( 'General', 'tour-operator' ),
				'template'         => LSX_TO_PATH . 'includes/partials/general.php',
				'default'          => true,
			),
		);

		$tabs['display'] = array(
			'page_title'       => '',
			'page_description' => '',
			'menu_title'       => esc_html__( 'Display', 'tour-operator' ),
			'template'         => LSX_TO_PATH . 'includes/partials/display.php',
			'default'          => false,
		);

		// if(in_array('LSX_Banners', get_declared_classes())){
		$tabs['api'] = array(
			'page_title'       => '',
			'page_description' => '',
			'menu_title'       => esc_html__( 'API', 'tour-operator' ),
			'template'         => LSX_TO_PATH . 'includes/partials/api.php',
			'default'          => false,
		);
		// }

		$posts_page = get_option( 'page_for_posts', false );

		if ( false === $posts_page ) {
			$tabs['post'] = array(
				'page_title'       => esc_html__( 'Posts', 'tour-operator' ),
				'page_description' => '',
				'menu_title'       => esc_html__( 'Posts', 'tour-operator' ),
				'template'         => LSX_TO_PATH . 'includes/partials/post.php',
				'default'          => false,
			);
		}

		$additional_tabs = false;
		$additional_tabs = apply_filters( 'lsx_to_framework_settings_tabs', $additional_tabs );

		if ( false !== $additional_tabs && is_array( $additional_tabs ) && ! empty( $additional_tabs ) ) {
			$tabs = array_merge( $tabs, $additional_tabs );
		}

		return array(
			'settings' => array(                                                         // this is the settings array. The key is the page slug
				'page_title'  => esc_html__( 'LSX Tour Operator Settings', 'tour-operator' ),                                                  // title of the page
				'menu_title'  => esc_html__( 'Settings', 'tour-operator' ),                                                  // title seen on the menu link
				'capability'  => 'manage_options',                                              // required capability to access page
				'icon'        => 'dashicons-book-alt',                                      // Icon or image to be used on admin menu
				'parent'      => 'tour-operator',                                         // Position priority on admin menu)
				'save_button' => esc_html__( 'Save Changes', 'tour-operator' ),                                                // If the page required saving settings, Set the text here.
				'tabs'        => $tabs,
			),
		);
	}

	/**
	 * outputs the display tabs settings
	 */
	public function display_tab_content( $subtab = 'basic' ) {
		if ( 'basic' === $subtab ) {
			if ( class_exists( 'LSX_Banners' ) && class_exists( 'Envira_Gallery' ) ) {
				?>
				<tr class="form-field">
					<th scope="row">
						<label for="description"><?php esc_html_e( 'Display galleries in the banner', 'tour-operator' ); ?></label>
					</th>
					<td>
						<input type="checkbox" {{#if enable_galleries_in_banner}} checked="checked" {{/if}} name="enable_galleries_in_banner" />
						<small><?php esc_html_e( 'Move the gallery on a page into the banner.', 'tour-operator' ); ?></small>
					</td>
				</tr>
				<?php
			}

			$this->modal_setting();
		}
		if ( 'advanced' === $subtab ) {
			?>
			<tr class="form-field">
				<th scope="row">
					<label for="description"><?php esc_html_e( 'Disable CSS', 'tour-operator' ); ?></label>
				</th>
				<td>
					<input type="checkbox" {{#if disable_css}} checked="checked" {{/if}} name="disable_css" />
					<small><?php esc_html_e( 'Disable the CSS if you want to include your own.', 'tour-operator' ); ?></small>
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row">
					<label for="description"><?php esc_html_e( 'Disable Javascript', 'tour-operator' ); ?></label>
				</th>
				<td>
					<input type="checkbox" {{#if disable_js}} checked="checked" {{/if}} name="disable_js" />
					<small><?php esc_html_e( 'Only disable the JS if you are debugging an error.', 'tour-operator' ); ?></small>
				</td>
			</tr>
			<?php
		}
	}

	/**
	 * outputs the dashboard tabs settings
	 *
	 * @param $tab string
	 * @return null
	 */
	public function dashboard_tab_content( $tab = 'general' ) {
		if ( 'general' !== $tab ) {
			return false;}
		?>
		<?php if ( ! class_exists( '\lsx\currencies\classes\Currencies' ) ) { ?>
			<tr class="form-field-wrap">
				<th scope="row">
					<label for="currency"> <?php esc_html_e( 'Currency', 'tour-operator' ); ?></label>
				</th>
				<td>
					<select value="{{currency}}" name="currency">
						<option value="USD" {{#is currency value=""}}selected="selected"{{/is}} {{#is currency value="USD"}} selected="selected"{{/is}}><?php esc_html_e( 'USD (united states dollar)', 'tour-operator' ); ?></option>
						<option value="GBP" {{#is currency value="GBP"}} selected="selected"{{/is}}><?php esc_html_e( 'GBP (british pound)', 'tour-operator' ); ?></option>
						<option value="ZAR" {{#is currency value="ZAR"}} selected="selected"{{/is}}><?php esc_html_e( 'ZAR (south african rand)', 'tour-operator' ); ?></option>
						<option value="NAD" {{#is currency value="NAD"}} selected="selected"{{/is}}><?php esc_html_e( 'NAD (namibian dollar)', 'tour-operator' ); ?></option>
						<option value="CAD" {{#is currency value="CAD"}} selected="selected"{{/is}}><?php esc_html_e( 'CAD (canadian dollar)', 'tour-operator' ); ?></option>
						<option value="EUR" {{#is currency value="EUR"}} selected="selected"{{/is}}><?php esc_html_e( 'EUR (euro)', 'tour-operator' ); ?></option>
						<option value="HKD" {{#is currency value="HKD"}} selected="selected"{{/is}}><?php esc_html_e( 'HKD (hong kong dollar)', 'tour-operator' ); ?></option>
						<option value="SGD" {{#is currency value="SGD"}} selected="selected"{{/is}}><?php esc_html_e( 'SGD (singapore dollar)', 'tour-operator' ); ?></option>
						<option value="NZD" {{#is currency value="NZD"}} selected="selected"{{/is}}><?php esc_html_e( 'NZD (new zealand dollar)', 'tour-operator' ); ?></option>
						<option value="AUD" {{#is currency value="AUD"}} selected="selected"{{/is}}><?php esc_html_e( 'AUD (australian dollar)', 'tour-operator' ); ?></option>
					</select>
				</td>
			</tr>
		<?php } ?>
		<tr class="form-field-wrap">
			<th scope="row">
				<label for="enquiry"><?php esc_html_e( 'General Enquiry', 'tour-operator' ); ?></label>
			</th>
			<?php
			if ( true === tour_operator()->legacy->show_default_form() ) {
				$forms         = tour_operator()->legacy->get_activated_forms();
				$selected_form = false;

				if ( isset( $this->options['general'] ) && isset( $this->options['general']['enquiry'] ) ) {
					$selected_form = $this->options['general']['enquiry'];
				}
				?>
				<td>
					<select value="{{enquiry}}" name="enquiry">
						<?php
						if ( false !== $forms && '' !== $forms ) {
							?>
							<option value="" {{#is enquiry value=""}}selected="selected"{{/is}}><?php esc_html_e( 'Select a form', 'tour-operator' ); ?></option>
							<?php
							foreach ( $forms as $form_id => $form_data ) {
								?>
								<option value="<?php echo esc_attr( $form_id ); ?>" 
														  <?php
															if ( $selected_form == $form_id ) {
																echo esc_attr( 'selected="selected"' ); }
															?>
  ><?php echo esc_html( $form_data ); ?></option>
								<?php
							}
						} else {
							?>
							<option value="" {{#is enquiry value=""}}selected="selected"{{/is}}><?php esc_html_e( 'You have no form available', 'tour-operator' ); ?></option>
						<?php } ?>
					</select>
				</td>
			<?php } else { ?>
				<td>
					<textarea class="description enquiry" name="enquiry" rows="10">{{#if enquiry}}{{{enquiry}}}{{/if}}</textarea>
				</td>
				<?php
			}
			?>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="disable_enquire_modal"><?php esc_html_e( 'Disable Enquire Modal', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input type="checkbox" {{#if disable_enquire_modal}} checked="checked" {{/if}} name="disable_enquire_modal" />
				<small><?php esc_html_e( 'This disables the enquire modal, and instead redirects to the link you provide below.', 'tour-operator' ); ?></small>
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="enquire_link"><?php esc_html_e( 'Enquire Link', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input type="text" {{#if enquire_link}} value="{{enquire_link}}" {{/if}} name="enquire_link" />
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="enquiry_contact_name"><?php esc_html_e( 'Enquire Contact Name', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input type="text" {{#if enquiry_contact_name}} value="{{enquiry_contact_name}}" {{/if}} id="enquiry_contact_name" name="enquiry_contact_name" />
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="enquiry_contact_email"><?php esc_html_e( 'Enquire Contact Email', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input type="text" {{#if enquiry_contact_email}} value="{{enquiry_contact_email}}" {{/if}} id="enquiry_contact_email" name="enquiry_contact_email" />
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="enquiry_contact_phone"><?php esc_html_e( 'Enquire Contact Phone', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input type="text" {{#if enquiry_contact_phone}} value="{{enquiry_contact_phone}}" {{/if}} id="enquiry_contact_phone" name="enquiry_contact_phone" />
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="enquiry_contact_image"> <?php esc_html_e( 'Enquire Contact Image', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input class="input_image_id" type="hidden" {{#if enquiry_contact_image_id}} value="{{enquiry_contact_image_id}}" {{/if}} name="enquiry_contact_image_id" />
				<input class="input_image" type="hidden" {{#if enquiry_contact_image}} value="{{enquiry_contact_image}}" {{/if}} name="enquiry_contact_image" />
				<div class="thumbnail-preview">
					{{#if enquiry_contact_image}}<img src="{{enquiry_contact_image}}" width="150" style="width:150px" />{{/if}}
				</div>
				<a {{#if enquiry_contact_image}}style="display:none;"{{/if}} class="button-secondary lsx-thumbnail-image-add"><?php esc_html_e( 'Choose Image', 'tour-operator' ); ?></a>
				<a {{#unless enquiry_contact_image}}style="display:none;"{{/unless}} class="button-secondary lsx-thumbnail-image-delete"><?php esc_html_e( 'Delete', 'tour-operator' ); ?></a>
			</td>
		</tr>
		<?php
	}

	/**
	 * Adds in the settings necessary for the archives
	 *
	 * @param $post_type string
	 * @param $tab string
	 * @return null
	 */
	public function general_settings( $post_type = false, $tab = false ) {
		if ( 'general' !== $tab ) {
			return false;
		}

		do_action( 'lsx_to_framework_' . $post_type . '_tab_general_settings_top', $post_type );
		?>
		<tr class="form-field-wrap">
			<th scope="row">
				<label for="enquiry"><?php esc_attr_e( 'General Enquiry', 'tour-operator' ); ?></label>
			</th>
			<?php
			if ( true === tour_operator()->legacy->show_default_form() ) {
				$forms         = tour_operator()->legacy->get_activated_forms();
				$selected_form = false;

				if ( isset( $this->options[ $post_type ] ) && isset( $this->options[ $post_type ]['enquiry'] ) ) {
					$selected_form = $this->options[ $post_type ]['enquiry'];
				}
				?>
				<td>
					<select value="{{enquiry}}" name="enquiry">
						<?php
						if ( false !== $forms && '' !== $forms ) {
							?>
							<option value="" {{#is enquiry value=""}}selected="selected"{{/is}}><?php esc_html_e( 'Select a form', 'tour-operator' ); ?></option>
							<?php
							foreach ( $forms as $form_id => $form_data ) {
								?>
								<option value="<?php echo esc_attr( $form_id ); ?>" 
														  <?php
															if ( $selected_form == $form_id ) {
																echo esc_attr( 'selected="selected"' ); }
															?>
  ><?php echo esc_html( $form_data ); ?></option>
								<?php
							}
						} else {
							?>
							<option value="" {{#is enquiry value=""}}selected="selected"{{/is}}><?php esc_html_e( 'You have no form available', 'tour-operator' ); ?></option>
						<?php } ?>
					</select>
				</td>
			<?php } else { ?>
				<td>
					<textarea class="description enquiry" name="enquiry" rows="10">{{#if enquiry}}{{{enquiry}}}{{/if}}</textarea>
				</td>
				<?php
			}
			?>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="disable_enquire_modal"><?php esc_attr_e( 'Disable Enquire Modal', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input type="checkbox" {{#if disable_enquire_modal}} checked="checked" {{/if}} name="disable_enquire_modal" />
				<small><?php esc_attr_e( 'This disables the enquire modal, and instead redirects to the link you provide below.', 'tour-operator' ); ?></small>
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="enquire_link"><?php esc_attr_e( 'Enquire Link', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input type="text" {{#if enquire_link}} value="{{enquire_link}}" {{/if}} name="enquire_link" />
			</td>
		</tr>
		<?php
		do_action( 'lsx_to_framework_' . $post_type . '_tab_general_settings_bottom', $post_type );
	}

	/**
	 * Adds in the settings necessary for the archives
	 *
	 * @param $post_type string
	 * @param $tab string
	 * @return null
	 */
	public function archive_settings( $post_type = false, $tab = false ) {
		if ( 'archives' !== $tab ) {
			return false;
		}
		?>
		<tr class="form-field">
			<th scope="row">
				<label for="disable_archives"><?php esc_html_e( 'Disable Archives', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input type="checkbox" {{#if disable_archives}} checked="checked" {{/if}} name="disable_archives" />
				<small><?php esc_html_e( 'This disables the "post type archive", if you create your own custom loop it will still work.', 'tour-operator' ); ?></small>
			</td>
		</tr>
		<?php
		if ( 'destination' === $post_type ) :
			?>
			<tr class="form-field">
				<th scope="row">
					<label for="continents_instead_countries"><?php esc_html_e( 'Display continents instead', 'tour-operator' ); ?></label>
				</th>
				<td>
					<input type="checkbox" {{#if continents_instead_countries}} checked="checked" {{/if}} name="continents_instead_countries" />
					<small><?php esc_html_e( 'Enable this to display replace the countries with continets on the destination archive.', 'tour-operator' ); ?></small>
				</td>
			</tr>

			<tr class="form-field">
				<th scope="row">
					<label for="group_items_by_continent"><?php esc_html_e( 'Group by Continent', 'tour-operator' ); ?></label>
				</th>
				<td>
					<input type="checkbox" {{#if group_items_by_continent}} checked="checked" {{/if}} name="group_items_by_continent" />
					<small><?php esc_html_e( 'This groups archive items by continent taxonomy and display the continent title.', 'tour-operator' ); ?></small>
				</td>
			</tr>

			<tr class="form-field">
				<th scope="row">
					<label for="sticky_archives"><?php esc_html_e( 'Sticky Destinations', 'tour-operator' ); ?></label>
				</th>
				<td>
					<select value="{{sticky_archives}}" name="sticky_archives">
						<option value="" {{#is sticky_archives value=""}}selected="selected"{{/is}}><?php esc_html_e( 'Disable', 'tour-operator' ); ?></option>
						<option value="sticky-only" {{#is sticky_archives value="sticky-only"}} selected="selected"{{/is}}><?php esc_html_e( 'Sticky items only', 'tour-operator' ); ?></option>
						<option value="sticky-first" {{#is sticky_archives value="sticky-first"}} selected="selected"{{/is}}><?php esc_html_e( 'Sticky items first', 'tour-operator' ); ?></option>
						<option value="sticky-last" {{#is sticky_archives value="sticky-last"}} selected="selected"{{/is}}><?php esc_html_e( 'Sticky items last', 'tour-operator' ); ?></option>
					</select>
				</td>
			</tr>
			<?php
		endif;
		?>
		<tr class="form-field">
			<th scope="row">
				<label for="disable_archive_pagination"><?php esc_html_e( 'Disable Pagination', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input type="checkbox" {{#if disable_archive_pagination}} checked="checked" {{/if}} name="disable_archive_pagination" />
				<small><?php esc_html_e( 'This disables the pagination on post type archive.', 'tour-operator' ); ?></small>
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="disable_entry_text"><?php esc_html_e( 'Disable Excerpt', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input type="checkbox" {{#if disable_entry_text}} checked="checked" {{/if}} name="disable_entry_text" />
				<small><?php esc_html_e( 'This disables the excerpt from entries on post type archive.', 'tour-operator' ); ?></small>
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="disable_entry_metadata"><?php esc_html_e( 'Disable Metadata', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input type="checkbox" {{#if disable_entry_metadata}} checked="checked" {{/if}} name="disable_entry_metadata" />
				<small><?php esc_html_e( 'This disables the metadata from entries on post type archive.', 'tour-operator' ); ?></small>
			</td>
		</tr>
		<tr class="form-field-wrap">
			<th scope="row">
				<label><?php esc_html_e( 'Grid/list layout', 'tour-operator' ); ?></label>
			</th>
			<td>
				<select value="{{grid_list_layout}}" name="grid_list_layout">
					<option value="" {{#is grid_list_layout value=""}}selected="selected"{{/is}}><?php esc_html_e( 'List', 'tour-operator' ); ?></option>
					<option value="grid" {{#is grid_list_layout value="grid"}} selected="selected"{{/is}}><?php esc_html_e( 'Grid', 'tour-operator' ); ?></option>
				</select>
			</td>
		</tr>
		<tr class="form-field-wrap">
			<th scope="row">
				<label><?php esc_html_e( 'List layout images', 'tour-operator' ); ?></label>
			</th>
			<td>
				<select value="{{list_layout_image_style}}" name="list_layout_image_style">
					<option value="" {{#is list_layout_image_style value=""}}selected="selected"{{/is}}><?php esc_html_e( 'Full-height', 'tour-operator' ); ?></option>
					<option value="max-height" {{#is list_layout_image_style value="max-height"}} selected="selected"{{/is}}><?php esc_html_e( 'Max-height', 'tour-operator' ); ?></option>
				</select>
			</td>
		</tr>
		<?php do_action( 'lsx_to_framework_' . $post_type . '_tab_archive_settings_top', $post_type ); ?>
		<tr class="form-field">
			<th scope="row">
				<label for="title"> <?php esc_html_e( 'Title', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input type="text" {{#if title}} value="{{title}}" {{/if}} name="title" />
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="tagline"> <?php esc_html_e( 'Tagline', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input type="text" {{#if tagline}} value="{{tagline}}" {{/if}} name="tagline" />
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="description"> <?php esc_html_e( 'Description', 'tour-operator' ); ?></label>
			</th>
			<td>
				<textarea class="description" name="description" rows="10">{{#if description}}{{{description}}}{{/if}}</textarea>
			</td>
		</tr>
		<?php
		do_action( 'lsx_to_framework_' . $post_type . '_tab_archive_settings_bottom', $post_type );
	}

	/**
	 * Adds in the settings necessary for the single
	 *
	 * @param $post_type string
	 * @param $tab string
	 * @return null
	 */
	public function single_settings( $post_type = false, $tab = false ) {
		if ( 'single' !== $tab ) {
			return false;
		}
		?>
		<tr class="form-field">
			<th scope="row">
				<label for="description"><?php esc_html_e( 'Disable Singles', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input type="checkbox" {{#if disable_single}} checked="checked" {{/if}} name="disable_single" />
				<small><?php esc_html_e( 'When disabled you will be redirected to the homepage when trying to access a single page.', 'tour-operator' ); ?></small>
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="description"><?php esc_html_e( 'Disable Collapsible Sections', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input type="checkbox" {{#if disable_collapsible}} checked="checked" {{/if}} name="disable_collapsible" />
				<small><?php esc_html_e( 'When disabled you will no longer be able to click to close and open the sections.', 'tour-operator' ); ?></small>
			</td>
		</tr>
		<?php
		if ( 'destination' === $post_type ) {
			?>
			<tr class="form-field">
				<th scope="row">
					<label for="sticky_countries"><?php esc_html_e( 'Sticky Countries', 'tour-operator' ); ?></label>
				</th>
				<td>
					<select value="{{sticky_countries}}" name="sticky_countries">
						<option value="" {{#is sticky_countries value=""}}selected="selected"{{/is}}><?php esc_html_e( 'Disable', 'tour-operator' ); ?></option>
						<option value="sticky-only" {{#is sticky_countries value="sticky-only"}} selected="selected"{{/is}}><?php esc_html_e( 'Sticky regions only', 'tour-operator' ); ?></option>
						<option value="sticky-first" {{#is sticky_countries value="sticky-first"}} selected="selected"{{/is}}><?php esc_html_e( 'Sticky regions first', 'tour-operator' ); ?></option>
						<option value="sticky-last" {{#is sticky_countries value="sticky-last"}} selected="selected"{{/is}}><?php esc_html_e( 'Sticky regions last', 'tour-operator' ); ?></option>
					</select>
				</td>
			</tr>
			<?php
		}
		do_action( 'lsx_to_framework_' . $post_type . '_tab_single_settings_top', $post_type );
		if ( 'tour' == $post_type || 'accommodation' == $post_type || 'destination' == $post_type || 'activity' == $post_type ) :
			?>
			<tr class="form-field">
				<th scope="row">
					<label for="section_title"><?php esc_html_e( 'Default Section Title', 'tour-operator' ); ?></label>
				</th>
				<td>
					<input type="text" {{#if section_title}} value="{{section_title}}" {{/if}} name="section_title" />
				</td>
			</tr>
			<?php if ( 'tour' == $post_type ) : ?>
				<tr class="form-field">
					<th scope="row">
						<label for="related_section_title"><?php esc_html_e( '"Related Tours" Section Title', 'tour-operator' ); ?></label>
					</th>
					<td>
						<input type="text" {{#if related_section_title}} value="{{related_section_title}}" {{/if}} name="related_section_title" />
					</td>
				</tr>
			<?php endif ?>
			<?php if ( 'accommodation' == $post_type ) : ?>
				<tr class="form-field">
					<th scope="row">
						<label for="brands_section_title"><?php esc_html_e( '"Accommodation Brands" Section Title', 'tour-operator' ); ?></label>
					</th>
					<td>
						<input type="text" {{#if brands_section_title}} value="{{brands_section_title}}" {{/if}} name="brands_section_title" />
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row">
						<label for="rooms_section_title"><?php esc_html_e( '"Rooms" Section Title', 'tour-operator' ); ?></label>
					</th>
					<td>
						<input type="text" {{#if rooms_section_title}} value="{{rooms_section_title}}" {{/if}} name="rooms_section_title" />
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row">
						<label for="similar_section_title"><?php esc_html_e( '"Similar Accommodations" Section Title', 'tour-operator' ); ?></label>
					</th>
					<td>
						<input type="text" {{#if similar_section_title}} value="{{similar_section_title}}" {{/if}} name="similar_section_title" />
					</td>
				</tr>
			<?php endif ?>
			<?php if ( 'destination' == $post_type ) : ?>
				<tr class="form-field">
					<th scope="row">
						<label for="countries_section_title"><?php esc_html_e( '"Countries" Section Title', 'tour-operator' ); ?></label>
					</th>
					<td>
						<input type="text" {{#if countries_section_title}} value="{{countries_section_title}}" {{/if}} name="countries_section_title" />
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row">
						<label for="regions_section_title"><?php esc_html_e( '"Regions" Section Title', 'tour-operator' ); ?></label>
					</th>
					<td>
						<input type="text" {{#if regions_section_title}} value="{{regions_section_title}}" {{/if}} name="regions_section_title" />
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row">
						<label for="travel_styles_section_title"><?php esc_html_e( '"Travel Styles" Section Title', 'tour-operator' ); ?></label>
					</th>
					<td>
						<input type="text" {{#if travel_styles_section_title}} value="{{travel_styles_section_title}}" {{/if}} name="travel_styles_section_title" />
					</td>
				</tr>
			<?php endif ?>
		<?php endif ?>

		<?php
		do_action( 'lsx_to_framework_' . $post_type . '_tab_single_settings_bottom', $post_type );
	}

	/**
	 * outputs the modal setting field
	 */
	public function modal_setting() {
		?>
		<tr class="form-field">
			<th scope="row">
				<label for="description"><?php esc_html_e( 'Enable Connected Modals', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input type="checkbox" {{#if enable_modals}} checked="checked" {{/if}} name="enable_modals" />
				<small><?php esc_html_e( 'Any connected item showing on a single will display a preview in a modal.', 'tour-operator' ); ?></small>
			</td>
		</tr>
		<?php
	}

	public function lsx_to_framework_api_patch( $tab = 'settings' ) {
		do_action( 'lsx_to_framework_api_tab_content', $tab );
	}

	/**
	 * outputs the display settings for the map tab.
	 *
	 * @param $tab string
	 * @return null
	 */
	public function map_display_settings( $tab = 'general' ) {
		if ( 'maps' === $tab ) {
			$this->disable_maps_checkbox();
			$this->map_marker_field();
			$this->cluster_marker_field();
			$this->start_end_marker_fields();
			$this->map_placeholder_settings_title();
			$this->enable_map_placeholder_checkbox();
			$this->map_placeholder_field();
			$this->fusion_tables_fields();
		}
	}

	/**
	 * Outputs the post type map settings.
	 *
	 * @param $tab string
	 * @return null
	 */
	public function post_type_map_settings( $post_type = '', $tab = 'general' ) {
		if ( 'placeholders' === $tab ) {
			$this->map_marker_field();
			$this->map_placeholder_field();
		}
	}

	/**
	 * Outputs the map placeholder field
	 */
	public function disable_maps_checkbox() {
		?>
		<tr class="form-field">
			<th scope="row">
				<label for="maps_disabled"><?php esc_html_e( 'Disable Maps', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input type="checkbox" {{#if maps_disabled}} checked="checked" {{/if}} name="maps_disabled" /> 
				<small><?php esc_html_e( 'This will disable maps on all post types.', 'tour-operator' ); ?></small>
			</td>
		</tr>	
		<?php
	}

	/**
	 * Outputs the map placeholder field header
	 */
	public function map_placeholder_settings_title() {
		?>
		<tr class="form-field">
			<th scope="row" colspan="2">
				<label>
					<h3><?php esc_html_e( 'Placeholder Settings', 'tour-operator' ); ?></h3>
				</label>
			</th>
		</tr>
		<?php
	}

	/**
	 * Outputs the map placeholder field
	 */
	public function enable_map_placeholder_checkbox() {
		?>
			
		<tr class="form-field">
			<th scope="row">
				<label for="map_placeholder_enabled"><?php esc_html_e( 'Enable Map Placeholder', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input type="checkbox" {{#if map_placeholder_enabled}} checked="checked" {{/if}} name="map_placeholder_enabled" /> 
				<small><?php esc_html_e( 'Enable a placeholder users will click to load the map.', 'tour-operator' ); ?></small>
			</td>
		</tr>	
		<?php
	}

	/**
	 * Outputs the map placeholder field
	 */
	public function map_placeholder_field() {
		?>
		<tr class="form-field map-placeholder">
			<th scope="row">
				<label for="banner"> <?php esc_html_e( 'Upload a map placeholder', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input class="input_image_id" type="hidden" {{#if map_placeholder}} value="{{map_placeholder}}" {{/if}} name="map_placeholder" />
				<input class="input_image" type="hidden" {{#if map_placeholder}} value="{{map_placeholder}}" {{/if}} name="map_placeholder" />
				<div class="thumbnail-preview">
					{{#if map_placeholder}}<img src="{{map_placeholder}}" width="480" style="color:black;" />{{/if}}
				</div>
				<a {{#if map_placeholder}}style="display:none;"{{/if}} class="button-secondary lsx-thumbnail-image-add"><?php esc_html_e( 'Choose Image', 'tour-operator' ); ?></a>
				<a {{#unless map_placeholder}}style="display:none;"{{/unless}} class="button-secondary lsx-thumbnail-image-delete"><?php esc_html_e( 'Delete', 'tour-operator' ); ?></a>
			</td>
		</tr>
		<tr class="form-field map-mobile-placeholder">
			<th scope="row">
				<label for="banner"> <?php esc_html_e( 'Upload a mobile map placeholder', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input class="input_image_id" type="hidden" {{#if map_mobile_placeholder}} value="{{map_mobile_placeholder}}" {{/if}} name="map_mobile_placeholder" />
				<input class="input_image" type="hidden" {{#if map_mobile_placeholder}} value="{{map_mobile_placeholder}}" {{/if}} name="map_mobile_placeholder" />
				<div class="thumbnail-preview">
					{{#if map_mobile_placeholder}}<img src="{{map_mobile_placeholder}}" width="150" style="color:black;" />{{/if}}
				</div>
				<a {{#if map_mobile_placeholder}}style="display:none;"{{/if}} class="button-secondary lsx-thumbnail-image-add"><?php esc_html_e( 'Choose Image', 'tour-operator' ); ?></a>
				<a {{#unless map_mobile_placeholder}}style="display:none;"{{/unless}} class="button-secondary lsx-thumbnail-image-delete"><?php esc_html_e( 'Delete', 'tour-operator' ); ?></a>
			</td>
		</tr>
		<?php
	}

	/**
	 * outputs the map marker upload field
	 */
	public function map_marker_field() {
		?>
		<tr class="form-field default-marker-wrap">
			<th scope="row">
				<label for="banner"> <?php esc_html_e( 'Choose a default marker', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input class="input_image_id" type="hidden" {{#if googlemaps_marker_id}} value="{{googlemaps_marker_id}}" {{/if}} name="googlemaps_marker_id" />
				<input class="input_image" type="hidden" {{#if googlemaps_marker}} value="{{googlemaps_marker}}" {{/if}} name="googlemaps_marker" />
				<div class="thumbnail-preview">
					{{#if googlemaps_marker}}<img src="{{googlemaps_marker}}" width="48" style="color:black;" />{{/if}}
				</div>
				<a {{#if googlemaps_marker}}style="display:none;"{{/if}} class="button-secondary lsx-thumbnail-image-add"><?php esc_html_e( 'Choose Image', 'tour-operator' ); ?></a>
				<a {{#unless googlemaps_marker}}style="display:none;"{{/unless}} class="button-secondary lsx-thumbnail-image-delete"><?php esc_html_e( 'Delete', 'tour-operator' ); ?></a>
			</td>
		</tr>
		<?php
	}

	/**
	 * outputs the cluster marker upload field
	 */
	public function cluster_marker_field() {
		?>
		<tr class="form-field default-cluster-small-wrap">
			<th scope="row">
				<label for="banner"> <?php esc_html_e( 'Choose a cluster marker', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input class="input_image_id" type="hidden" {{#if gmap_cluster_small_id}} value="{{gmap_cluster_small_id}}" {{/if}} name="gmap_cluster_small_id" />
				<input class="input_image" type="hidden" {{#if gmap_cluster_small}} value="{{gmap_cluster_small}}" {{/if}} name="gmap_cluster_small" />
				<div class="thumbnail-preview">
					{{#if gmap_cluster_small}}<img src="{{gmap_cluster_small}}" width="48" style="color:black;" />{{/if}}
				</div>
				<a {{#if gmap_cluster_small}}style="display:none;"{{/if}} class="button-secondary lsx-thumbnail-image-add"><?php esc_html_e( 'Choose Image', 'tour-operator' ); ?></a>
				<a {{#unless gmap_cluster_small}}style="display:none;"{{/unless}} class="button-secondary lsx-thumbnail-image-delete"><?php esc_html_e( 'Delete', 'tour-operator' ); ?></a>
			</td>
		</tr>
		<?php
	}

	/**
	 * outputs the start/end marker upload field
	 */
	public function start_end_marker_fields() {
		?>
		<tr class="form-field default-cluster-small-wrap">
			<th scope="row">
				<label for="banner"> <?php esc_html_e( 'Choose a start marker', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input class="input_image_id" type="hidden" {{#if gmap_marker_start_id}} value="{{gmap_marker_start_id}}" {{/if}} name="gmap_marker_start_id" />
				<input class="input_image" type="hidden" {{#if gmap_marker_start}} value="{{gmap_marker_start}}" {{/if}} name="gmap_marker_start" />
				<div class="thumbnail-preview">
					{{#if gmap_marker_start}}<img src="{{gmap_marker_start}}" width="48" style="color:black;" />{{/if}}
				</div>
				<a {{#if gmap_marker_start}}style="display:none;"{{/if}} class="button-secondary lsx-thumbnail-image-add"><?php esc_html_e( 'Choose Image', 'tour-operator' ); ?></a>
				<a {{#unless gmap_marker_start}}style="display:none;"{{/unless}} class="button-secondary lsx-thumbnail-image-delete"><?php esc_html_e( 'Delete', 'tour-operator' ); ?></a>
			</td>
		</tr>
		<tr class="form-field default-cluster-small-wrap">
			<th scope="row">
				<label for="banner"> <?php esc_html_e( 'Choose a end marker', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input class="input_image_id" type="hidden" {{#if gmap_marker_end_id}} value="{{gmap_marker_end_id}}" {{/if}} name="gmap_marker_end_id" />
				<input class="input_image" type="hidden" {{#if gmap_marker_end}} value="{{gmap_marker_end}}" {{/if}} name="gmap_marker_end" />
				<div class="thumbnail-preview">
					{{#if gmap_marker_end}}<img src="{{gmap_marker_end}}" width="48" style="color:black;" />{{/if}}
				</div>
				<a {{#if gmap_marker_end}}style="display:none;"{{/if}} class="button-secondary lsx-thumbnail-image-add"><?php esc_html_e( 'Choose Image', 'tour-operator' ); ?></a>
				<a {{#unless gmap_marker_end}}style="display:none;"{{/unless}} class="button-secondary lsx-thumbnail-image-delete"><?php esc_html_e( 'Delete', 'tour-operator' ); ?></a>
			</td>
		</tr>
		<?php
	}

	/**
	 * Outputs the map marker upload field
	 */
	public function fusion_tables_fields() {
		?>
		<tr class="form-field">
			<th scope="row" colspan="2">
				<label>
					<h3><?php esc_html_e( 'Fusion Tables Settings', 'tour-operator' ); ?></h3>
				</label>
			</th>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="fusion_tables_enabled"><?php esc_html_e( 'Enable Fusion Tables', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input type="checkbox" {{#if fusion_tables_enabled}} checked="checked" {{/if}} name="fusion_tables_enabled" />
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="title"><?php esc_html_e( 'Border Width', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input type="text" maxlength="2" {{#if fusion_tables_width_border}} value="{{fusion_tables_width_border}}" {{/if}} name="fusion_tables_width_border" />
				<br>
				<small>Default value: 2</small>
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="title"><?php esc_html_e( 'Border Colour', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input type="text" maxlength="7" {{#if fusion_tables_colour_border}} value="{{fusion_tables_colour_border}}" {{/if}} name="fusion_tables_colour_border" />
				<br>
				<small>Default value: #000000</small>
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="title"><?php esc_html_e( 'Background Colour', 'tour-operator' ); ?></label>
			</th>
			<td>
				<input type="text" maxlength="7" {{#if fusion_tables_colour_background}} value="{{fusion_tables_colour_background}}" {{/if}} name="fusion_tables_colour_background" />
				<br>
				<small>Default value: #000000</small>
			</td>
		</tr>
		<?php
	}
}
