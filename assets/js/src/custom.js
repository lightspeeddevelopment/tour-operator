/**
 * Scripts
 *
 * @package    tour-operator
 * @subpackage scripts
 */

var lsx_to = Object.create( null );

if ( window.location.hash ) {
	( document.body || document.documentElement ).scrollIntoView();
	setTimeout( function() { ( document.body || document.documentElement ).scrollIntoView(); }, 1 );
};( function( $, window, document, undefined ) {

	'use strict';

	var $document     = $( document ),
		$window       = $( window ),
		window_height = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight,
		window_width  = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;

	/**
	 * Easing browser scroll on page load (document URL with hash).
	 *
	 * @package    tour-operator
	 * @subpackage scripts
	 */
	lsx_to.set_easing_scroll_on_page_load = function() {
		if ( window.location.hash ) {
			var $to = $( window.location.hash ),
				top = parseInt( $to.offset().top );

			top -= $( '#wpadminbar' ).length > 0 ? $( '#wpadminbar' ).outerHeight( true ) : 0;
			top -= $( '.top-menu-fixed #masthead' ).length > 0 ? $( '.top-menu-fixed #masthead' ).outerHeight( true ) : 0;
			top -= $( '.lsx-to-navigation' ).length > 0 ? $( '.lsx-to-navigation' ).outerHeight( true ) : 0;

			$( 'html, body' ).animate( { scrollTop: top }, 800 );
		}
	};

	/**
	 * Remove empty widgets.
	 *
	 * @package    tour-operator
	 * @subpackage scripts
	 */
	lsx_to.remove_empty_widgets = function() {
		$( '.widget.lsx-widget' ).each(
			function() {
				var $this = $( this );

				if ( '' === $.trim( $this.html() ) ) {
					  $this.closest( 'section' ).remove();
				}
			}
		);
	};

	/**
	 * Remove empty single fast facts.
	 *
	 * @package    tour-operator
	 * @subpackage scripts
	 */
	lsx_to.remove_empty_fast_facts = function() {
		$( '#fast-facts .lsx-to-single-meta-data' ).each(
			function() {
				var $this = $( this );

				if ( '' === $.trim( $this.html() ) ) {

					if ( 0 === $this.closest( '#fast-facts' ).siblings( '#highlights' ).length && 0 === $this.closest( '#fast-facts' ).siblings( '.lsx-to-contact-widget' ).length ) {
						$this.closest( '.col-xs-12' ).siblings( '.col-xs-12' ).attr( 'class', 'col-xs-12' );
						$this.closest( '.col-xs-12' ).remove();
					} else {
						// $this.closest( '#fast-facts' ).remove();
						$this.closest( '#fast-facts' ).addClass( 'empty-fact' );
					}
				} else {
					$this.closest( '#fast-facts' ).addClass( 'full-fact' );
				}
			}
		);
	};

	/**
	 * Add ul/li HTML tags to the wetu importer content.
	 *
	 * @package    tour-operator
	 * @subpackage scripts
	 */
	lsx_to.add_html_elements_to_include_and_exclude = function() {
		$( '#included-excluded .entry-content' ).each(
			function() {
				var _this = $( this ),
				_html     = _this.html();

				// Doesn't have HTML bullets
				// if ( false === /[•–-]/gi.test( _html ) ) {
				// return;
				// }

				// Doesn't have HTML <br> or <p>
				if ( -1 === _html.indexOf( '<br>' ) && -1 === _html.indexOf( '<p>' ) ) {
					  return;
				}

				// Has HTML <ul> or <li>
				if ( _html.indexOf( '<ul>' ) >= 0 && _html.indexOf( '<ul>' ) >= 0 ) {
					return;
				}

				// Add ul/li HTML tags to the wetu importer content
				_html = _html.replace( new RegExp( '</p>[\n\s]+<p>', 'g' ), '<br>' );
				_html = _html.replace( new RegExp( '</?p>', 'g' ), '' );
				_html = _html.replace( new RegExp( '<br>', 'g' ), '</li><li>' );
				_html = _html.replace( /[•–-]/gi, '' );
				_html = '<ul><li>' + _html + '</li></ul>';

				_this.html( _html );
			}
		);
	};

	/**
	 * Add extra HTML class to metadata tags.
	 *
	 * @package    tour-operator
	 * @subpackage scripts
	 */
	lsx_to.add_extra_class_to_meta = function() {
		$( '.meta' ).parent().each(
			function() {
				var $nodes = $( this ).children( '.meta' );
				$nodes.last().addClass( 'last-meta' );
			}
		);
	};

	/**
	 * Read more effect.
	 *
	 * @package    tour-operator
	 * @subpackage scripts
	 */
	lsx_to.set_read_more = function() {
		$( '.lsx-to-review-content .more-link, .lsx-to-team-content .more-link, .entry-content .more-link, .archive-description .more-link' ).each(
			function() {
				if ( 'Read More' === $( this ).html() ) {
					$( this ).closest( '.lsx-to-review-content, .lsx-to-team-content, .entry-content, .archive-description' ).each(
						function() {
							var visible = true;

							$( this ).children().each(
								function() {
									if ( 'Read More' === $( this ).find( '.more-link' ).html() ) {
										 visible = false;
									} else if ( ! visible && this.id !== 'sharing' ) {
										$( this ).hide();
									}
								}
							);
						}
					);

					$( this ).click(
						function( event ) {
							event.preventDefault();
							$( this ).hide();

							if ($( this ).hasClass( 'more-link-remove-p' ) ) {
									var html = '';

									$( this ).closest( '.lsx-to-review-content, .lsx-to-team-content, .entry-content, .archive-description' ).children().each(
										function() {
											$( this ).show();
										}
									);
							} else {
										  $( this ).closest( '.lsx-to-review-content, .lsx-to-team-content, .entry-content, .archive-description' ).children().show();
							}
						}
					);
				}
			}
		);
	};

	/**
	 * Read more (travel info) effect.
	 *
	 * @package    tour-operator
	 * @subpackage scripts
	 */
	lsx_to.set_read_more_travel_info = function() {
		$( '.moretag-travel-info' ).click(
			function( event ) {
				event.preventDefault();

				var $modal = $( '#lsx-modal-placeholder' ),
				$entry     = $( this ).closest( '.lsx-travel-info' ),
				title      = $entry.find( '.lsx-to-widget-title' ).html(),
				content    = $entry.find( '.travel-info-entry-content' ).html();

				$modal.find( '.modal-title' ).html( title );
				$modal.find( '.modal-body' ).html( content );

				$modal.modal();
			}
		);
	};

	/**
	 * Read more (itinerary) effect.
	 *
	 * @package    tour-operator
	 * @subpackage scripts
	 */
	lsx_to.set_read_more_itinerary = function() {
		$( '#itinerary .view-more a' ).click(
			function( event ) {
				event.preventDefault();
				$( this ).hide();

				$( this ).parents( '#itinerary' ).find( '.itinerary-item.hidden' ).each(
					function() {
						$( this ).removeClass( 'hidden' );
					}
				);
			}
		);
	};

	/**
	 * Slider - Pre build.
	 *
	 * @package    tour-operator
	 * @subpackage scripts
	 */
	lsx_to.pre_build_slider = function( $slider ) {
		$slider.on(
			'init',
			function( event, slick ) {
				if ( slick.options.arrows && slick.slideCount > slick.options.slidesToShow ) {
					$slider.addClass( 'slick-has-arrows' );
				}
			}
		);

		$slider.on(
			'setPosition',
			function( event, slick ) {
				if ( ! slick.options.arrows ) {
					$slider.removeClass( 'slick-has-arrows' );
				} else if ( slick.slideCount > slick.options.slidesToShow ) {
					$slider.addClass( 'slick-has-arrows' );
				}
			}
		);
	};

	/**
	 * Slider.
	 *
	 * @package    tour-operator
	 * @subpackage scripts
	 */
	lsx_to.build_slider = function( window_width ) {
		$( '.lsx-to-slider .lsx-to-slider-inner:not(.slider-disabled)' ).each(
			function() {
				var $this      = $( this ),
				interval       = $this.data( 'interval' ),
				autoplay       = false,
				autoplay_speed = 0;

				lsx_to.pre_build_slider( $this );

				if ( 'undefined' !== typeof interval && 'boolean' !== typeof interval ) {
					  interval = parseInt( interval );

					if ( ! isNaN( interval ) ) {
						autoplay       = true;
						autoplay_speed = interval;
					}
				}

				if ( 1 < $this.children.length ) {
					$this.slick(
						{
							draggable: lsx_to_params.slickSlider.desktop.draggable,
							infinite: lsx_to_params.slickSlider.desktop.infinite,
							swipe: lsx_to_params.slickSlider.desktop.swipe,
							cssEase: lsx_to_params.slickSlider.desktop.cssEase,
							dots: lsx_to_params.slickSlider.desktop.dots,
							slidesToShow: lsx_to_params.slickSlider.desktop.slidesToShow,
							slidesToScroll: lsx_to_params.slickSlider.desktop.slidesToScroll,
							autoplay: autoplay,
							autoplaySpeed: autoplay_speed,
							responsive: [
							 {
									breakpoint: 992,
									settings: {
										draggable: lsx_to_params.slickSlider.tablet.draggable,
										arrows: lsx_to_params.slickSlider.tablet.arrows,
										swipe: lsx_to_params.slickSlider.tablet.swipe,
									}
							},
							 {
									breakpoint: 768,
									settings: {
										slidesToShow:   lsx_to_params.slickSlider.mobile.slidesToShow,
										slidesToScroll: lsx_to_params.slickSlider.mobile.slidesToScroll,
										draggable:      lsx_to_params.slickSlider.mobile.draggable,
										arrows:         lsx_to_params.slickSlider.mobile.arrows,
										swipe:          lsx_to_params.slickSlider.mobile.swipe
									}
							}
							 ]
						}
					);
				}
			}
		);

		if ( window_width < 768 ) {
			$( '.gallery' ).not( '.slick-initialized' ).slick(
				{
					slide: 'dl',
					arrows: false,
					draggable: true,
					infinite: true,
					swipe: true,
					cssEase: 'ease-out',
					dots: true,
					autoplay: false,
					responsive: [
						{
							breakpoint: 99999,
							settings: 'unslick'
					},
						{
							breakpoint: 768,
							setting: {
								slidesToShow: 1,
								slidesToScroll: 1
							}
					}
						]
				}
			);
		}
	};

	/**
	 * Slider Lightbox.
	 *
	 * @package    tour-operator
	 * @subpackage scripts
	 */
	lsx_to.build_slider_lightbox = function() {
		$( '.single-tour-operator .gallery' ).slickLightbox(
			{
				caption: function( element, info ) {
					return $( element ).find( 'img' ).attr( 'alt' );
				}
			}
		);

		$( '.single-tour-operator .rooms-content' ).slickLightbox(
			{
				caption: function( element, info ) {
					return $( element ).find( 'img' ).attr( 'alt' );
				}
			}
		);
	};

	/**
	 * Anchor menu - Fixed.
	 *
	 * @package    tour-operator
	 * @subpackage scripts
	 */
	lsx_to.fix_anchor_menu = function() {
		$( '.lsx-to-navigation' ).scrollToFixed(
			{
				minWidth: 1200,
				zIndex: 100,

				marginTop: function () {
					var margin_top = 0;

					margin_top += $( '#wpadminbar' ).length > 0 ? $( '#wpadminbar' ).outerHeight() : 0;
					margin_top += $( '.top-menu-fixed #masthead' ).length > 0 ? $( '.top-menu-fixed #masthead' ).outerHeight( true ) : 0;

					return margin_top;
				}
			}
		);
	};

	/**
	 * Anchor menu - Scroll easing.
	 *
	 * @package    tour-operator
	 * @subpackage scripts
	 */
	lsx_to.set_anchor_menu_easing_scroll = function() {
		$( '.lsx-to-navigation .scroll-easing a' ).on(
			'click',
			function( event ) {
				event.preventDefault();

				var $from = $( this ),
				$to       = $( $from.attr( 'href' ) ),
				top       = parseInt( $to.offset().top );

				top -= $( '#wpadminbar' ).length > 0 ? $( '#wpadminbar' ).outerHeight( true ) : 0;
				top -= $( '.top-menu-fixed #masthead' ).length > 0 ? $( '.top-menu-fixed #masthead' ).outerHeight( true ) : 0;
				top -= $( '.lsx-to-navigation' ).length > 0 ? $( '.lsx-to-navigation' ).outerHeight( true ) : 0;

				if ( '#summary' === $from.attr( 'href' ) ) {
					top -= 85;
				} else {
					top -= parseInt( $to.data( 'extra-top' ) ? $to.data( 'extra-top' ) : '0' );
				}

				$( 'html, body' ).animate( { scrollTop: top }, 800 );
			}
		);
	};

	/**
	 * Anchor menu - Scroll spy.
	 *
	 * @package    tour-operator
	 * @subpackage scripts
	 */
	lsx_to.set_anchor_menu_scroll_spy = function() {
		var offset = 10;

		offset += $( '#wpadminbar' ).length > 0 ? $( '#wpadminbar' ).outerHeight( true ) : 0;
		offset += $( '.top-menu-fixed #masthead' ).length > 0 ? $( '.top-menu-fixed #masthead' ).outerHeight( true ) : 0;
		offset += $( '.lsx-to-navigation' ).length > 0 ? $( '.lsx-to-navigation' ).outerHeight( true ) : 0;

		$( 'body' ).scrollspy(
			{
				target: '.lsx-to-navigation',
				offset: offset
			}
		);
	};

	/**
	 * Hide collapse sections on init.
	 *
	 * @package    tour-operator
	 * @subpackage scripts
	 */
	lsx_to.build_collapse = function( window_width ) {
		$( '.lsx-to-collapse-section .collapse' ).not( '#collapse-summary' ).each(
			function() {
				var $this = $( this );

				$this.collapse( 'hide' );

				$this.on(
					'show.bs.collapse',
					function() {
						var $this = $( this ),
						$slider   = $this.find( '.lsx-to-slider .lsx-to-slider-inner:not(.slider-disabled)' );

						if ( $slider.length > 0 ) {
							 $slider.css( 'opacity', 0 );
						}

						if ( window_width < 768 ) {
								 $slider = $this.find( '.gallery' );

							if ( $slider.length > 0 ) {
								$slider.css( 'opacity', 0 );
							}
						}
					}
				);

				$this.on(
					'shown.bs.collapse',
					function() {
						var $this = $( this ),
						$slider   = $this.find( '.lsx-to-slider .lsx-to-slider-inner:not(.slider-disabled)' ),
						$map      = $this.find( '.lsx-map' );

						if ( $slider.length > 0 ) {
							  $slider.slick( 'setPosition' ).animate( { 'opacity': 1 }, 200 );
						}

						if ( window_width < 768 ) {
							 $slider = $this.find( '.gallery' );

							if ( $slider.length > 0 ) {
								$slider.slick( 'setPosition' ).animate( { 'opacity': 1 }, 200 );
							}
						}

						if ( $map.length > 0 ) {
							if ( undefined !== LSX_TO_Maps && $map.find( '.lsx-map-placeholder' ).length <= 0 ) {
								LSX_TO_Maps.initThis();
							}
						}
					}
				);
			}
		);
	};

	/**
	 * Add an extra class if a widget has only one item.
	 *
	 * @package    tour-operator
	 * @subpackage scripts
	 */
	lsx_to.to_widget_item_size = function() {
		$( '.lsx-to-widget-item-wrap:not(.slick-slide)' ).each(
			function() {
				if ( $( this ).find( '.lsx-to-archive-item' ).length == 1 ) {
					$( this ).addClass( 'lsx-to-widget-just-one-item' );
				}
			}
		);
	};

	/**
	 * On window resize.
	 *
	 * @package    lsx
	 * @subpackage scripts
	 */
	$window.resize(
		function() {

			window_height = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
			window_width  = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;

		}
	);

	/**
	 * On document ready.
	 *
	 * @package    lsx
	 * @subpackage scripts
	 */
	$document.ready(
		function() {
			lsx_to.remove_empty_widgets();
			lsx_to.remove_empty_fast_facts();
			lsx_to.add_html_elements_to_include_and_exclude();
			lsx_to.add_extra_class_to_meta();
			lsx_to.set_read_more();
			lsx_to.set_read_more_travel_info();
			lsx_to.set_read_more_itinerary();
			lsx_to.build_slider( window_width );
			lsx_to.to_widget_item_size();
		}
	);

	/**
	 * On window load.
	 *
	 * @package    lsx
	 * @subpackage scripts
	 */
	$window.load(
		function() {

			if ( window_width >= 1200 ) {
				  lsx_to.fix_anchor_menu();
				  lsx_to.set_anchor_menu_easing_scroll();
				  lsx_to.set_anchor_menu_scroll_spy();
			} else {
				// lsx-to-collapse-section
				lsx_to.build_collapse( window_width );
			}
			// lsx_to.set_easing_scroll_on_page_load();
			lsx_to.build_slider_lightbox();

		}
	);

} )( jQuery, window, document );
