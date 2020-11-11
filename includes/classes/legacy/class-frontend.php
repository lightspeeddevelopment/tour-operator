<?php
/**
 * Frontend actions for the LSX TO Plugin
 *
 * @package   Frontend
 * @author    LightSpeed
 * @license   GPL3
 * @link
 * @copyright 2016 LightSpeedDevelopment
 */

namespace lsx\legacy;

/**
 * Main plugin class.
 *
 * @package Frontend
 * @author  LightSpeed
 */
class Frontend extends Tour_Operator {

	/**
	 * This holds the class OBJ of \lsx\legacy\Template_Redirects
	 */
	public $redirects = false;

	/**
	 * Enable Modals
	 *
	 * @since 1.0.0
	 * @var      boolean|Frontend
	 */
	public $enable_modals = false;

	/**
	 * Holds the modal ids for output in the footer
	 *
	 * @since 1.0.0
	 * @var      array|Frontend
	 */
	public $modal_ids = array();

	/**
	 * Holds the maps class
	 *
	 * @var      object
	 */
	public $maps = array();

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

		add_filter( 'post_class', array( $this, 'replace_class' ), 10, 1 );
		add_filter( 'body_class', array( $this, 'replace_class' ), 10, 1 );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_stylescripts' ), 10 );
		add_action( 'wp_head', array( $this, 'wp_head' ), 10 );
		add_filter( 'body_class', array( $this, 'body_class' ), 15, 1 );

		if ( ! is_admin() ) {
			add_filter( 'pre_get_posts', array( $this, 'travel_style_post_types' ), 10, 1 );
			add_action( 'pre_get_posts', array( $this, 'disable_pagination_on_archive' ) );
			add_filter( 'posts_orderby', array( $this, 'enable_continent_taxonomy_order' ), 10, 2 );
		}

		add_filter( 'lsx_to_connected_list_item', array( $this, 'add_modal_attributes' ), 10, 3 );
		add_action( 'wp_footer', array( $this, 'output_modals' ), 10 );
		add_filter( 'lsx_to_tagline', array( $this, 'get_tagline' ), 1, 3 );

		// add_filter( 'the_terms', array( $this, 'links_new_window' ), 10, 2 );

		$this->redirects = new Template_Redirects( LSX_TO_PATH, array_keys( $this->base_post_types ), array_keys( $this->base_taxonomies ) );
		$this->maps      = Maps::get_instance();

		add_filter( 'get_the_archive_title', array( $this, 'get_the_archive_title' ), 100 );

		// Redirects if disabled
		add_action( 'template_redirect', array( $this, 'redirect_singles' ) );
		add_action( 'template_redirect', array( $this, 'redirect_archive' ) );

		// Readmore
		add_filter( 'excerpt_more_p', array( $this, 'remove_read_more_link' ) );
		add_filter( 'the_content', array( $this, 'modify_read_more_link' ) );
		remove_filter( 'term_description', 'wpautop' );
		add_filter( 'term_description', array( $this, 'modify_term_description' ) );

		add_action( 'lsx_to_widget_entry_content_top', array( $this, 'enable_crop_excerpt' ) );
		add_action( 'lsx_to_widget_entry_content_bottom', array( $this, 'disable_crop_excerpt' ) );

		add_action( 'lsx_to_entry_content_top', array( $this, 'enable_crop_excerpt' ) );
		add_action( 'lsx_to_entry_content_bottom', array( $this, 'disable_crop_excerpt' ) );

		if ( is_admin() ) {
			add_filter( 'lsx_customizer_colour_selectors_body', array( $this, 'customizer_to_body_colours_handler' ), 15, 2 );
			add_filter( 'lsx_customizer_colour_selectors_main_menu', array( $this, 'customizer_to_main_menu_colours_handler' ), 15, 2 );
		}

		add_filter( 'lsx_fonts_css', array( $this, 'customizer_to_fonts_handler' ), 15 );
		add_filter( 'wpseo_breadcrumb_links', array( $this, 'wpseo_breadcrumb_links' ), 20 );
	}

	/**
	 * A filter to replace anything with '-TO_POST_TYPE' by
	 * '-lsx-to-TO_POST_TYPE'
	 */
	public function replace_class( $classes ) {
		foreach ( $this->active_post_types as $key1 => $value1 ) {
			foreach ( $classes as $key2 => $value2 ) {
				$classes[ $key2 ] = str_replace( "-{$value1}", "-lsx-to-{$value1}", $value2 );
			}
		}

		return $classes;
	}

	/**
	 * Initate some boolean flags
	 */
	public function wp_head() {
		if ( ( is_singular( $this->active_post_types ) || is_post_type_archive( $this->active_post_types ) )
			 && false !== $this->options
			 && isset( $this->options['display']['enable_modals'] )
			 && 'on' === $this->options['display']['enable_modals']
		) {
			$this->enable_modals = true;
		}

		if ( ( is_post_type_archive( $this->active_post_types ) ) || ( is_tax( array_keys( $this->taxonomies ) ) ) ) {
			add_filter( 'use_default_gallery_style', '__return_false' );

			if ( ! class_exists( 'LSX_Banners' ) ) {
				remove_action( 'lsx_content_wrap_before', 'lsx_global_header' );
				add_action( 'lsx_content_wrap_before', 'lsx_to_global_header', 100 );
			}

			add_action( 'lsx_content_wrap_before', 'lsx_to_archive_description', 100 );
			add_filter( 'lsx_to_archive_description', array( $this, 'get_post_type_archive_description' ), 1, 3 );

			add_action( 'lsx_content_top', array( $this, 'archive_taxonomy_content_part' ), 100 );

			// LSX default pagination
			add_action( 'lsx_content_bottom', array( 'lsx\legacy\Frontend', 'lsx_default_pagination' ) );
		}

		if ( is_singular( $this->active_post_types ) ) {
			add_filter( 'use_default_gallery_style', '__return_false' );

			if ( ! class_exists( 'LSX_Banners' ) ) {
				remove_action( 'lsx_content_wrap_before', 'lsx_global_header' );
				add_action( 'lsx_content_wrap_before', 'lsx_to_global_header', 100 );
			}
		}
	}

	/**
	 * Taxonomy Archive content part.
	 */
	public function archive_taxonomy_content_part() {
		if ( is_tax( array_keys( $this->taxonomies ) ) && have_posts() ) {
			lsx_to_content( 'content', get_queried_object()->taxonomy );
		}
	}

	/**
	 * a filter to overwrite the links with modal tags.
	 */
	public function add_modal_attributes( $html, $post_id, $link ) {
		if ( true === $this->enable_modals && true === $link ) {
			$html = '<a data-toggle="modal" data-target="#lsx-modal-' . $post_id . '" href="#">' . get_the_title( $post_id ) . '</a>';

			if ( ! in_array( $post_id, $this->modal_ids ) ) {
				$this->modal_ids[] = $post_id;
			}
		}

		return $html;
	}

	/**
	 * a filter to overwrite the links with modal tags.
	 */
	public function output_modals() {
		global $lsx_to_archive, $post;

		if ( true === $this->enable_modals && ! empty( $this->modal_ids ) ) {
			$temp           = $lsx_to_archive;
			$lsx_to_archive = 1;

			foreach ( $this->modal_ids as $post_id ) {
				$post = get_post( $post_id );
				?>
				<div class="lsx-modal modal fade" id="lsx-modal-<?php echo esc_attr( $post_id ); ?>" tabindex="-1" role="dialog">
					<div class="modal-dialog">
						<div class="modal-content">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<div class="modal-body">
								<?php lsx_to_content( 'content', 'modal' ); ?>
							</div>
						</div>
					</div>
				</div>
				<?php
			}

			$lsx_to_archive = $temp;
			wp_reset_postdata();
		}
		?>
		<div class="lsx-modal modal fade" id="lsx-modal-placeholder" tabindex="-1" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<div class="modal-header">
						<h4 class="modal-title"></h4>
					</div>
					<div class="modal-body"></div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @return    null
	 */
	public function enqueue_stylescripts() {
		$has_slick          = wp_script_is( 'slick', 'queue' );
		$has_slick_lightbox = wp_script_is( 'slick-lightbox', 'queue' );
		if ( defined( 'SCRIPT_DEBUG' ) ) {
			$prefix = 'src/';
			$suffix = '';
		} else {
			$prefix = '';
			$suffix = '.min';
		}

		if ( ! isset( $this->options['display']['disable_js'] ) ) {
			if ( ! $has_slick ) {
				wp_enqueue_script( 'slick', LSX_TO_URL . 'assets/js/vendor/slick.min.js', array( 'jquery' ), LSX_TO_VER, true );
			}

			if ( ! $has_slick_lightbox ) {
				wp_enqueue_script( 'slick-lightbox', LSX_TO_URL . 'assets/js/vendor/slick-lightbox.min.js', array( 'jquery', 'slick' ), LSX_TO_VER, true );
			}

			wp_enqueue_script( 'tour-operator-script', LSX_TO_URL . 'assets/js/' . $prefix . 'custom' . $suffix . '.js', array( 'jquery', 'slick', 'slick-lightbox'/*, 'fixto'*/ ), LSX_TO_VER, true );

			$param_array = array(
				'slickSlider' => array(
					'desktop' => array(
						'draggable'      => false,
						'infinite'       => true,
						'swipe'          => false,
						'cssEase'        => 'ease-out',
						'dots'           => true,
						'slidesToShow'   => 3,
						'slidesToScroll' => 3,
					),
					'tablet'  => array(
						'draggable' => true,
						'arrows'    => false,
						'swipe'     => true,
					),
					'mobile'  => array(
						'slidesToShow'   => 1,
						'slidesToScroll' => 1,
						'draggable'      => true,
						'arrows'         => false,
						'swipe'          => true,
					),
				),
			);
			$param_array = apply_filters( 'lsx_to_js_params', $param_array );
			wp_localize_script( 'tour-operator-script', 'lsx_to_params', $param_array );
		}

		if ( ! isset( $this->options['display']['disable_css'] ) ) {
			if ( ! $has_slick ) {
				wp_enqueue_style( 'slick', LSX_TO_URL . 'assets/css/vendor/slick.css', array(), LSX_TO_VER );
			}

			if ( ! $has_slick_lightbox ) {
				wp_enqueue_style( 'slick-lightbox', LSX_TO_URL . 'assets/css/vendor/slick-lightbox.css', array( 'slick' ), LSX_TO_VER );
			}

			wp_enqueue_style( 'tour-operator-style', LSX_TO_URL . 'assets/css/style.css', array( 'lsx_main' ), LSX_TO_VER );
			wp_style_add_data( 'tour-operator-style', 'rtl', 'replace' );
		}
	}

	/**
	 * Set the main query to pull through only the top level destinations.
	 */
	public function travel_style_post_types( $query ) {
		if ( $query->is_main_query() && $query->is_tax( array( 'travel-style' ) ) ) {
			$query->set( 'post_type', array( 'tour', 'accommodation' ) );
		}

		return $query;
	}

	/**
	 * Disable pagination.
	 */
	public function disable_pagination_on_archive( $query ) {
		if ( $query->is_main_query() && $query->is_post_type_archive( array_keys( $this->post_types ) ) ) {
			$queried_post_type = get_query_var( 'post_type' );

			if ( isset( $this->options[ $queried_post_type ] ) && isset( $this->options[ $queried_post_type ]['disable_archive_pagination'] ) ) {
				$query->set( 'posts_per_page', -1 );
			}
		}
	}

	/**
	 * Enable continent taxonomy order.
	 */
	public function enable_continent_taxonomy_order( $orderby, $query ) {
		global $wpdb;

		if ( $query->is_main_query() && $query->is_post_type_archive( 'destination' ) ) {
			if ( isset( $this->options['destination'] ) && isset( $this->options['destination']['group_items_by_continent'] ) ) {
				$new_orderby = "(
					SELECT GROUP_CONCAT(lsx_to_term_order ORDER BY lsx_to_term_order ASC)
					FROM $wpdb->term_relationships
					INNER JOIN $wpdb->term_taxonomy USING (term_taxonomy_id)
					INNER JOIN $wpdb->terms USING (term_id)
					WHERE $wpdb->posts.ID = object_id
					AND taxonomy = 'continent'
					GROUP BY object_id
				) ";

				$new_orderby .= ( 'ASC' == strtoupper( $query->get( 'order' ) ) ) ? 'ASC' : 'DESC';
				$orderby      = $new_orderby . ', ' . $orderby;
			}
		}

		return $orderby;
	}

	/**
	 * Add a some classes so we can style.
	 */
	public function body_class( $classes ) {
		global $post;
		if ( false !== $this->post_types && is_singular( array_keys( $this->post_types ) ) ) {
			$classes[] = 'single-tour-operator';
		} elseif ( false !== $this->post_types && is_post_type_archive( array_keys( $this->post_types ) ) ) {
			$classes[] = 'archive-tour-operator';
		} elseif ( false !== $this->taxonomies && is_tax( array_keys( $this->taxonomies ) ) ) {
			$classes[] = 'archive-tour-operator';
		}

		return $classes;
	}

	/**
	 * add target="_blank" to the travel style links
	 */
	// public function links_new_window( $terms, $taxonomy ) {
	// if ( 'travel-style' === $taxonomy || 'accommodation-type' === $taxonomy ) {
	// $terms = str_replace( '<a', '<a target="_blank"', $terms );
	// }

	// return $terms;
	// }

	/**
	 * Remove the "Archives:" from the post type archives.
	 *
	 * @param    $title
	 *
	 * @return    $title
	 */
	public function get_the_archive_title( $title ) {
		if ( is_post_type_archive( array_keys( $this->post_types ) ) ) {
			$title = post_type_archive_title( '', false );
		}

		if ( is_tax( array_keys( $this->taxonomies ) ) ) {
			$title = single_term_title( '', false );
		}

		return $title;
	}

	/**
	 * Redirect the single links to the homepage if the single is set to be
	 * disabled.
	 *
	 * @param    $template
	 *
	 * @return    $template
	 */
	public function redirect_singles() {
		$queried_post_type = get_query_var( 'post_type' );

		if ( is_singular() && false !== $this->options && isset( $this->options[ $queried_post_type ] ) && isset( $this->options[ $queried_post_type ]['disable_single'] ) ) {
			wp_redirect( get_post_type_archive_link( $queried_post_type ), 301 );
			exit;
		}

		if ( is_singular() ) {
			$single_desabled = get_post_meta( get_the_ID(), 'disable_single', true );

			if ( ! empty( $single_desabled ) ) {
				wp_redirect( get_post_type_archive_link( $queried_post_type ), 301 );
				exit;
			}
		}
	}

	/**
	 * Redirect the archive links to the homepage if the disable archive is set
	 * to be disabled.
	 *
	 * @param    $template
	 *
	 * @return    $template
	 */
	public function redirect_archive() {
		$queried_post_type = get_query_var( 'post_type' );

		if ( is_post_type_archive() && false !== $this->options && isset( $this->options[ $queried_post_type ] ) && isset( $this->options[ $queried_post_type ]['disable_archives'] ) ) {
			wp_redirect( home_url(), 301 );
			exit;
		}
	}

	/**
	 * Remove the read more link.
	 */
	public function remove_read_more_link( $excerpt_more ) {
		$post_type = get_post_type();

		if ( isset( tour_operator()->options[ $post_type ] ) ) {
			global $post;

			$has_single = ! lsx_to_is_single_disabled();
			$permalink  = '';

			if ( $has_single ) {
				$permalink = get_the_permalink();
			} elseif ( ! is_post_type_archive( $post_type ) ) {
				$has_single = true;
				$permalink  = get_post_type_archive_link( $post_type ) . '#' . $post_type . '-' . $post->post_name;
			}

			if ( ! empty( $permalink ) ) {
				$excerpt_more = '<p><a class="moretag" href="' . esc_url( $permalink ) . '">' . esc_html__( 'View more', 'lsx' ) . '</a></p>';
			} else {
				$excerpt_more = '';
			}
		}

		return $excerpt_more;
	}

	/**
	 * Modify the read more link
	 *
	 * @param        string $content
	 *
	 * @return    string $content
	 */
	public function modify_read_more_link( $content ) {
		$content = str_replace( '<span id="more-' . esc_attr( get_the_ID() ) . '"></span>', '<a class="lsx-to-more-link more-link" data-collapsed="true" href="' . esc_url( get_permalink() ) . '">' . esc_html__( 'Read More', 'tour-operator' ) . '</a>', $content );

		return $content;
	}

	/**
	 * Enable: crop huge excerpts o archive and widget items.
	 */
	public function enable_crop_excerpt() {
		add_filter( 'get_the_excerpt', array( $this, 'crop_excerpt' ), 1, 15 );
	}

	/**
	 * Disable: crop huge excerpts on archive and widget items.
	 */
	public function disable_crop_excerpt() {
		remove_filter( 'get_the_excerpt', array( $this, 'crop_excerpt' ), 15 );
	}

	/**
	 * Crop huge excerpts on archive and widget items.
	 */
	public function crop_excerpt( $wpse_excerpt ) {
		global $post;

		if ( empty( $wpse_excerpt ) ) {
			$wpse_excerpt = get_the_content( '' );
		}

		if ( ! empty( $wpse_excerpt ) ) {
			$wpse_excerpt = strip_shortcodes( $wpse_excerpt );
			$wpse_excerpt = apply_filters( 'the_content', $wpse_excerpt );
			$wpse_excerpt = str_replace( ']]>', ']]>', $wpse_excerpt );
			$wpse_excerpt = strip_tags( $wpse_excerpt, apply_filters( 'excerpt_strip_tags', '<h1>,<h2>,<h3>,<h4>,<h5>,<h6>,<a>,<button>,<blockquote>,<p>,<br>,<b>,<strong>,<i>,<u>,<ul>,<ol>,<li>,<span>,<div>' ) );

			$excerpt_word_count = 25;
			$excerpt_word_count = apply_filters( 'excerpt_length', $excerpt_word_count );

			$tokens         = array();
			$excerpt_output = '';
			$has_more       = false;
			$count          = 0;

			preg_match_all( '/(<[^>]+>|[^<>\s]+)\s*/u', $wpse_excerpt, $tokens );

			foreach ( $tokens[0] as $token ) {
				if ( $count >= $excerpt_word_count ) {
					$excerpt_output .= trim( $token );
					$has_more        = true;
					break;
				}

				$count++;
				$excerpt_output .= $token;
			}

			$wpse_excerpt = trim( force_balance_tags( $excerpt_output ) );

			if ( $has_more ) {
				$excerpt_end = '<a class="moretag" href="' . esc_url( get_permalink() ) . '">' . esc_html__( 'More', 'lsx' ) . '</a>';
				$excerpt_end = apply_filters( 'excerpt_more', ' ' . $excerpt_end );

				$pos = strrpos( $wpse_excerpt, '</' );

				if ( false !== $pos ) {
					// Inside last HTML tag
					$wpse_excerpt = substr_replace( $wpse_excerpt, $excerpt_end, $pos, 0 ); /* Add read more next to last word */
				} else {
					// After the content
					$wpse_excerpt .= $excerpt_end; /*Add read more in new paragraph */
				}
			}
		}

		return $wpse_excerpt;
	}

	/**
	 * Modify term_description to use the_content filter
	 *
	 * @param        string $content
	 *
	 * @return    string $content
	 */
	public function modify_term_description( $content ) {
		$more_link_text = esc_html__( 'Read More', 'tour-operator' );
		$output         = '';

		if ( preg_match( '/<!--more(.*?)?-->/', $content, $matches ) ) {
			$content = explode( $matches[0], $content, 2 );

			if ( ! empty( $matches[1] ) && ! empty( $more_link_text ) ) {
				$more_link_text = strip_tags( wp_kses_no_null( trim( $matches[1] ) ) );
			}
		} else {
			$content = array( $content );
		}

		$teaser  = $content[0];
		$output .= $teaser;

		if ( count( $content ) > 1 ) {
			$output .= "<a class=\"btn btn-default more-link\" data-collapsed=\"true\" href=\"#more-000\">{$more_link_text}</a>" . $content[1];
		}

		$output = apply_filters( 'the_content', $output );

		return $output;
	}

	/**
	 * Outputs LSX default pagination.
	 */
	public static function lsx_default_pagination() {
		lsx_paging_nav();
	}

	/**
	 * Handle fonts that might be change by LSX Customiser
	 */
	public function customizer_to_fonts_handler( $css_fonts ) {
		global $wp_filesystem;

		$css_fonts_file = LSX_TO_PATH . '/assets/css/to-fonts.css';

		if ( file_exists( $css_fonts_file ) ) {
			if ( empty( $wp_filesystem ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
				WP_Filesystem();
			}

			if ( $wp_filesystem ) {
				$css_fonts .= $wp_filesystem->get_contents( $css_fonts_file );
			}
		}

		return $css_fonts;
	}

	/**
	 * Handle body colours that might be change by LSX Customiser
	 */
	public function customizer_to_body_colours_handler( $css, $colors ) {
		$css .= '
			@import "' . LSX_TO_PATH . '/assets/css/scss/customizer-to-body-colours";

			/**
			 * LSX Customizer - Body (Tour Operators)
			 */
			@include customizer-to-body-colours (
				$bg:   		' . $colors['background_color'] . ',
				$breaker:   ' . $colors['body_line_color'] . ',
				$color:    	' . $colors['body_text_color'] . ',
				$link:    	' . $colors['body_link_color'] . ',
				$hover:    	' . $colors['body_link_hover_color'] . ',
				$small:    	' . $colors['body_text_small_color'] . '
			);
		';

		return $css;
	}

	/**
	 * Handle main menu colours that might be change by LSX Customiser
	 */
	public function customizer_to_main_menu_colours_handler( $css, $colors ) {
		$css .= '
			@import "' . LSX_TO_PATH . '/assets/css/scss/customizer-to-main-menu-colours";

			/**
			 * LSX Customizer - Main Menu (Tour Operators)
			 */
			@include customizer-to-main-menu-colours (
				$dropdown:            ' . $colors['main_menu_dropdown_background_color'] . ',
				$dropdown-hover:      ' . $colors['main_menu_dropdown_background_hover_color'] . ',
				$dropdown-link:       ' . $colors['main_menu_dropdown_link_color'] . ',
				$dropdown-link-hover: ' . $colors['main_menu_dropdown_link_hover_color'] . '
			);
		';

		return $css;
	}

	/**
	 * Add continent item to the breadcrumb.
	 */
	public function wpseo_breadcrumb_links( $crumbs ) {
		if ( is_tax( 'continent' ) ) {
			$crumbs = $this->continent_breadcrumb_links( $crumbs );
		}

		if ( is_singular( 'destination' ) ) {
			$crumbs = $this->destination_breadcrumb_links( $crumbs );
		}

		if ( is_singular( 'accommodation' ) ) {
			$crumbs = $this->accommodation_breadcrumb_links( $crumbs );
		}

		if ( is_singular( 'tour' ) ) {
			$crumbs = $this->tour_breadcrumb_links( $crumbs );
		}

		return $crumbs;
	}

	/**
	 * The Breadcrumbs Links for the continents.
	 *
	 * @param array $crumbs
	 * @return array
	 */
	public function continent_breadcrumb_links( $crumbs ) {
		$destination_breadcrumb = array(
			'text' => esc_html__( 'Destinations', 'tour-operator' ),
			'url'  => get_post_type_archive_link( 'destination' ),
		);

		array_splice( $crumbs, 1, 0, array( $destination_breadcrumb ) );
		return $crumbs;
	}

	/**
	 * The Breadcrumbs Links for the Destinations.
	 *
	 * @param array $crumbs
	 * @return array
	 */
	public function destination_breadcrumb_links( $crumbs ) {
		global $post;
		$continents = wp_get_post_terms( $post->ID, 'continent' );
		if ( empty( $continents ) || ! is_array( $continents ) ) {
			global $post;

			if ( ! empty( $post->post_parent ) ) {
				$continents = wp_get_post_terms( $post->post_parent, 'continent' );
			}
		}

		if ( ! empty( $continents ) && is_array( $continents ) ) {
			foreach ( $continents as $key => $continent ) {
				$continent_breadcrumb = array(
					'text' => $continent->name,
					'url'  => get_term_link( $continent ),
				);

				array_splice( $crumbs, 2, 0, array( $continent_breadcrumb ) );
				break;
			}
		}
		return $crumbs;
	}

	/**
	 * The Breadcrumbs Links for the Tours and Accommodation.
	 *
	 * @param array $crumbs
	 * @return array
	 */
	public function accommodation_breadcrumb_links( $crumbs ) {
		$new_crumbs           = array(
			array(
				'text' => esc_attr__( 'Home', 'tour-operator' ),
				'url'  => home_url(),
			),
			array(
				'text' => esc_attr__( 'Accommodation', 'tour-operator' ),
				'url'  => get_post_type_archive_link( 'accommodation' ),
			),
		);
		$current_destinations = get_post_meta( get_the_ID(), 'destination_to_accommodation', false );

		$all_destinations = array();
		if ( false !== $current_destinations && ! empty( $current_destinations ) ) {

			$country = false;
			$regions = array();

			foreach ( $current_destinations as $current_destination ) {
				$all_destinations[] = get_post( $current_destination );
			}

			// Find the country
			foreach ( $all_destinations as $destination_index => $destination ) {
				if ( 0 === $destination->post_parent || '0' === $destination->post_parent ) {
					$new_crumbs[] = array(
						'text' => $destination->post_title,
						'url'  => get_permalink( $destination->ID ),
					);
					unset( $all_destinations[ $destination_index ] );
				}
			}

			// Find the region
			if ( ! empty( $all_destinations ) ) {
				foreach ( $all_destinations as $destination_index => $destination ) {
					$new_crumbs[] = array(
						'text' => $destination->post_title,
						'url'  => get_permalink( $destination->ID ),
					);
				}
			}
		}
		$new_crumbs[] = array(
			'text' => get_the_title(),
			'url'  => get_permalink(),
		);
		$crumbs       = $new_crumbs;
		return $crumbs;
	}

	/**
	 * The Breadcrumbs Links for the Tours and Accommodation.
	 *
	 * @param array $crumbs
	 * @return array
	 */
	public function tour_breadcrumb_links( $crumbs ) {
		$new_crumbs = array(
			array(
				'text' => esc_attr__( 'Home', 'tour-operator' ),
				'url'  => home_url(),
			),
			array(
				'text' => esc_attr__( 'Tour', 'tour-operator' ),
				'url'  => get_post_type_archive_link( 'tour' ),
			),
		);
		$region     = get_post_meta( get_the_ID(), 'departs_from', false );
		if ( false !== $region && isset( $region[0] ) ) {
			$country = wp_get_post_parent_id( $region[0] );
			if ( false !== $country && '' !== $country ) {
				$new_crumbs[] = array(
					'text' => get_the_title( $country ),
					'url'  => get_permalink( $country ),
				);
			}
			$new_crumbs[] = array(
				'text' => get_the_title( $region[0] ),
				'url'  => get_permalink( $region[0] ),
			);
		}
		$new_crumbs[] = array(
			'text' => get_the_title(),
			'url'  => get_permalink(),
		);
		$crumbs       = $new_crumbs;
		return $crumbs;
	}
}
