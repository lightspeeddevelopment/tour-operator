'use strict';

var infowindow,
	$gmap        = '',
	gmap_markers = [],
	icons        = [];

var LSX_TO_Maps = {

	initThis: function() {
		var $map = jQuery( '.lsx-map:eq(0)' );
		// console.log($map);

		var lat   = Number( $map.attr( 'data-lat' ) );
		var lng   = Number( $map.attr( 'data-long' ) );
		var zoom  = Number( $map.attr( 'data-zoom' ) );
		var type  = $map.attr( 'data-type' );
		this.type = type;

		var height       = Number( $map.attr( 'data-height' ) );
		var banner_class = $map.attr( 'data-class' );
		var icon_url     = $map.attr( 'data-icon' );

		var framework_url = $map.attr( 'data-url' );

		var cluster_small  = $map.attr( 'data-cluster-small' );
		var cluster_medium = $map.attr( 'data-cluster-medium' );
		var cluster_large  = $map.attr( 'data-cluster-large' );

		if ( 'undefined' == cluster_small && 'undefined' == cluster_medium && 'undefined' == cluster_large) {
			this.cluster_disable = true;
		} else {
			this.cluster_small   = cluster_small;
			this.cluster_medium  = cluster_medium;
			this.cluster_large   = cluster_large;
			this.cluster_disable = false;
		}

		// Fusion Tables
		this.fusion_tables_countries         = {};
		this.fusion_tables_colour_border     = $map.attr( 'data-fusion-tables-colour-border' );
		this.fusion_tables_width_border      = $map.attr( 'data-fusion-tables-width-border' );
		this.fusion_tables_colour_background = $map.attr( 'data-fusion-tables-colour-background' );

		// var container_html = '';
		var kml = false;

		// Sets the Framework URL
		this.framework_url = framework_url;

		// Create new LatLong Coordinates
		this.latlng = new google.maps.LatLng( lat, lng );

		// Set the Icon URL
		this.icon_url = new google.maps.MarkerImage(
			icon_url,
			null,
			null,
			null,
			new google.maps.Size( 32, 32 )
		);

		this.bounds = [];

		var $footerMap = jQuery( banner_class + ':eq(0)' );
		if ('.lsx-map-preview' != banner_class) {
			height = $footerMap.css( 'height' );
			// container_html = $footerMap.find('.container').html();
			$footerMap.find( '.container' ).hide();
		} else if ('route' === type && 'undefined' !== $map.attr( 'data-kml' )) {
			kml = $map.attr( 'data-kml' );
		} else {
			$footerMap.css( 'height',height );
		}

		var $container   = null;
		var $breadcrumbs = null;
		if ('.lsx-map-preview' != banner_class) {
			jQuery( banner_class ).addClass( 'gmap-banner' );

			$map.closest( 'section' ).hide();
			$map.closest( 'section' ).appendTo( 'footer.content-info' );

			if (jQuery( banner_class ).children( '.container' ).length == 1) {
				$container = jQuery( banner_class ).children( '.container' ).clone( true, true );
			}
			if (jQuery( banner_class ).children( '.breadcrumbs-container' ).length == 1) {
				$breadcrumbs = jQuery( banner_class ).children( '.breadcrumbs-container' ).clone( true, true );
			}
		}
		var snazzyMapsStyle = null,
			styledMap       = null;

		if ('undefined' !== typeof SnazzyDataForSnazzyMaps && 'undefined' !== typeof SnazzyDataForSnazzyMaps.json) {
			var snazzyMapsStyle = jQuery.parseJSON( SnazzyDataForSnazzyMaps.json ),
				styledMap       = new google.maps.StyledMapType( snazzyMapsStyle, {name: "Styled Map"} );
		}

		this.mapObj = new google.maps.Map(
			$footerMap[0],
			{
				zoom: zoom,
				maxZoom: 21,
				minZoom: 1,
				center: this.latlng,
				scrollwheel: false,
				draggable: true,
				mapTypeControl: false,
				overviewMapControl: false,
				panControl: false,
				rotateControl: false,
				mapTypeId: google.maps.MapTypeId.TERRAIN,
				mapTypeControlOptions: {
					mapTypeIds: [google.maps.MapTypeId.TERRAIN, 'map_style']
				}
			}
		);

		if (null !== snazzyMapsStyle && null !== styledMap) {
			this.mapObj.mapTypes.set( 'map_style', styledMap );
			this.mapObj.setMapTypeId( 'map_style' );
		}

		// Decide which method to draw on the map.
		if (false != kml && undefined != kml) {
			this.addRoute();
		} else {
			this.refreshMarkers();
			if ('route' == type && (false == kml || undefined == kml)) {
				this.drawRoute();
			}
		}

		// Do we fit to the screen or center the view.
		if ( ! $map.hasClass( 'disable-auto-zoom' ) && ( 'cluster' == type || ('route' == type && (false == kml || undefined == kml))) ) {

			if ( 1 < this.bounds.length ) {
				this.setBounds();
			} else {
				this.latlng = this.bounds[0];
				console.log( this.latlng );
				this.setCenter();
			}
			$footerMap.css( 'height',height );

		} else {
			this.setCenter();
		}

		this.resizeThis();

		// if('cluster' === type){
		// $footerMap.append(container_html);
		// }

		if ($container !== null) {
			jQuery( banner_class ).append( $container );
		}
		if ($breadcrumbs !== null) {
			$breadcrumbs.find( '.container' ).show();
			jQuery( banner_class ).after( $breadcrumbs );
		}

		$gmap = this.mapObj;
	},

	resizeThis: function() {
		if (google && google.maps) {
			google.maps.event.trigger( this.mapObj, "resize" );
		}
	},

	drawRoute: function() {
		var coordinates = [];

		if (jQuery( '.lsx-map-markers' ).length > 0) {
			jQuery( '.lsx-map-markers .map-data' ).each(
				function(){
					coordinates.push( {lat: Number( jQuery( this ).attr( 'data-lat' ) ), lng: Number( jQuery( this ).attr( 'data-long' ) )} );
				}
			);
		}

		var route = new google.maps.Polyline(
			{
				path: coordinates,
				geodesic: true,
				strokeColor: '#000000',
				strokeOpacity: 1.0,
				strokeWeight: 1.5
			}
		);
		route.setMap( this.mapObj );
	},

	generateRoute: function() {
		if (jQuery( '.lsx-map-markers' ).length > 0) {
			jQuery( '.lsx-map-markers .map-data' ).each(
				function(){
					coordinates.push( jQuery( this ).attr( 'data-lat' ) + ' ' + Number( jQuery( this ).attr( 'data-long' ) ) );
					// coordinates.push({lat: Number(jQuery(this).attr('data-lat')), lng: Number(jQuery(this).attr('data-long'))});
				}
			);
		}

		jQuery.get(
			'https://roads.googleapis.com/v1/snapToRoads',
			{
				interpolate: true,
				key: lsx_to_maps_params.apiKey,
				path: coordinates.join( '|' )
			},
			function(data) {
				/*processSnapToRoadResponse(data);
				drawSnappedPolyline();
				getAndDrawSpeedLimits();*/
			}
		);
	},

	// Fusion Tables (function 1)
	addFusionLayer: function() {
		if (jQuery( '#script-fusion-tables' ).length > 0) {
			jQuery( '#script-fusion-tables' ).remove();
		}

		var fusion_tables_countries = [];

		for (var i in this.fusion_tables_countries) {
			fusion_tables_countries.push( "'" + i + "'" );
		}

		var script       = document.createElement( 'script' ),
			url          = ['https://www.googleapis.com/fusiontables/v1/query?'],
			query        = 'SELECT Name, geometry FROM ' +
					'1N2LBk4JHwWpOY4d9fobIn27lfnZ5MDy-NoqqRpk ' +
					'WHERE Name IN (' + fusion_tables_countries.join() + ')',
			encodedQuery = encodeURIComponent( query ),
			body         = document.getElementsByTagName( 'body' )[0];

		url.push( 'sql=' );
		url.push( encodedQuery );
		url.push( '&callback=LSX_TO_Maps.drawFusionLayer' );
		url.push( '&key=' + lsx_to_maps_params.apiKey );

		script.id  = 'script-fusion-tables';
		script.src = url.join( '' );

		body.appendChild( script );
	},

	// Fusion Tables (function 2)
	drawFusionLayer: function(data) {
		var rows = data['rows'];

		for (var i in rows) {
			var newCoordinates = [],
				geometries     = rows[i][1]['geometries'];

			if (geometries) {
				for (var j in geometries) {
					newCoordinates.push( this.constructNewCoordinates( geometries[j] ) );
				}
			} else {
				newCoordinates = this.constructNewCoordinates( rows[i][1]['geometry'] );
			}

			var country = new google.maps.Polygon(
				{
					paths: newCoordinates,
					strokeColor: this.fusion_tables_colour_border,
					strokeOpacity: 0.6,
					strokeWeight: this.fusion_tables_width_border,
					fillColor: this.fusion_tables_colour_background,
					fillOpacity: 0.3,
					countryName: rows[i][0]
				}
			);

			google.maps.event.addListener(
				country,
				'mouseover',
				function() {
					this.setOptions( {fillOpacity: 0.6} );
				}
			);

			google.maps.event.addListener(
				country,
				'mouseout',
				function() {
					this.setOptions( {fillOpacity: 0.3} );
				}
			);

			google.maps.event.addListener(
				country,
				'click',
				function(event) {
					if (infowindow) {
						infowindow.close();
					}

					var country = LSX_TO_Maps.fusion_tables_countries[this.countryName];
					// country.content = (country.content).replace('<p>', '<p style="margin-bottom:0;margin-top:10px;">');

					infowindow = new google.maps.InfoWindow(
						{
							content:	'<div class="lsx-to-map-marker">' +
									'<img class="lsx-to-map-marker-img" src="' + country.thumbnail + '">' +
									'<div class="lsx-to-map-marker-content content-area">' +
										'<h4 class="lsx-to-map-marker-title">' + country.title + '</h4>' +
										country.content +
									'</div>' +
								'<br clear="all"/>'
						}
					);

					infowindow.setPosition( event.latLng );
					infowindow.open( $gmap );
				}
			);

			country.setMap( this.mapObj );
		}
	},

	// Fusion Tables (function 3)
	constructNewCoordinates: function(polygon) {
		var newCoordinates = [],
			coordinates    = polygon['coordinates'][0];

		for (var i in coordinates) {
			newCoordinates.push( new google.maps.LatLng( coordinates[i][1], coordinates[i][0] ) );
		}

		return newCoordinates;
	},

	setCenter: function() {
		this.mapObj.setCenter( this.latlng );
	},

	refreshMarkers: function() {
		gmap_markers = [];
		icons        = [];

		// Fusion Tables
		this.fusion_tables_countries = {};

		var bounds = [];
		var $this  = this;

		if (jQuery( '.lsx-map-markers' ).length) {
			var counter       = 0;
			var marker_length = jQuery( '.lsx-map-markers .map-data' ).length - 1;

			jQuery( '.lsx-map-markers .map-data' ).each(
				function(){
					var tempMarker = new google.maps.LatLng( Number( jQuery( this ).attr( 'data-lat' ) ), Number( jQuery( this ).attr( 'data-long' ) ) );
					bounds.push( tempMarker );

					if ('1' === jQuery( this ).attr( 'data-fusion-tables' )) {
						// Fusion Tables
						LSX_TO_Maps.fusion_tables_countries[jQuery( this ).attr( 'data-title' )] = {
							title: '<a href="' + jQuery( this ).attr( 'data-link' ) + '">' + jQuery( this ).attr( 'data-title' ) + '</a>',
							thumbnail: jQuery( this ).attr( 'data-thumbnail' ),
							content: jQuery( this ).html()
						};
					} else {
						gmap_markers.push( {marker:tempMarker,title:'<a target="_blank" rel="noopener noreferrer" href="' + jQuery( this ).attr( 'data-link' ) + '">' + jQuery( this ).attr( 'data-title' ) + '</a>',thumbnail:jQuery( this ).attr( 'data-thumbnail' ),content:jQuery( this ).html()} );

						var icon_url = jQuery( this ).attr( 'data-icon' );
						// console.log(icon_url);

						if ('route' == $this.type && (0 == counter || marker_length == counter)) {
							if (0 == counter) {
								icon_url = lsx_to_maps_params.start_marker;
							} else {
								icon_url = lsx_to_maps_params.end_marker;
							}
						}

						icons.push( icon_url );
						counter++;
					}
				}
			);

			for (var i = 0; i < gmap_markers.length; i++) {
				gmap_markers[i] = this.createMarker( gmap_markers[i],icons[i] );
			}

			this.bounds = bounds;

			if (true == this.cluster_disable && 'cluster' == this.type) {
				var styles = [{
					url: this.cluster_small,
					height: 52,
					width: 53,
					anchor: [0, 0],
					textColor: '#ffffff'
				}, {
					url: this.cluster_medium,
					height: 52,
					width: 53,
					anchor: [0, 0],
					textColor: '#ffffff'
				}, {
					url: this.cluster_large,
					height: 52,
					width: 53,
					anchor: [0, 0],
					textColor: '#ffffff'
				}];

				var options       = {
					styles: styles
				};
				var markerCluster = new MarkerClusterer( this.mapObj, gmap_markers, options );
			}

			// Fusion Tables
			if (Object.keys( LSX_TO_Maps.fusion_tables_countries ).length > 0) {
				this.addFusionLayer();
			}
		}
	},

	createMarker: function(position,icon) {
		var marker = new google.maps.Marker(
			{
				position: position.marker,
				map: this.mapObj,
				title: jQuery( position.title ).text(),
				icon: icon
			}
		);

		marker.addListener(
			'click',
			function() {
				if (infowindow) {
					infowindow.close();
				}

				// position.content = (position.content).replace('<p>', '<p style="margin-bottom:0;margin-top:10px;">');

				infowindow = new google.maps.InfoWindow(
					{
						content:	'<div class="lsx-to-map-marker">' +
								'<img class="lsx-to-map-marker-img" src="' + position.thumbnail + '">' +
								'<div class="lsx-to-map-marker-content content-area">' +
									'<h4 class="lsx-to-map-marker-title">' + position.title + '</h4>' +
									position.content +
								'</div>' +
							'<br clear="all"/>'
					}
				);

				google.maps.event.addListener(
					infowindow,
					'domready',
					function() {
						window.setTimeout(
							function() {
								$gmap.panBy( 0, -30 );
							},
							700
						);
					}
				);

				infowindow.open( $gmap, marker );
			}
		);

		return marker;
	},

	addRoute: function(kml) {
		var ctaLayer = new google.maps.KmlLayer(
			{
				url: kml,
				map: this.mapObj
			}
		);

		this.resizeThis();
	},

	setBounds: function() {
		if (google && google.maps) {
			// map: an instance of google.maps.Map object
			// latlng: an array of google.maps.LatLng objects
			var latlngbounds = new google.maps.LatLngBounds();
			for (var i = 0; i < this.bounds.length; i++) {
				latlngbounds.extend( this.bounds[i] );
			}
			this.mapObj.fitBounds( latlngbounds );
		}
	},

	watchMapTriggers: function() {
		jQuery( document ).on(
			'click',
			'.lsx-map-placeholder, .placeholder-text',
			function( event ) {
				jQuery.getScript(
					lsx_to_maps_params.google_url,
					function() {
						jQuery.getScript( lsx_to_maps_params.google_cluster_url );
						LSX_TO_Maps.initThis();
					}
				);
			}
		);
	},
};

jQuery( document ).ready(
	function($) {
		// console.log( lsx_to_maps_params );
		if ( jQuery( '.lsx-map' ).length > 0 ) {
			if ( '' === lsx_to_maps_params.placeholder_enabled ) {
				LSX_TO_Maps.initThis();
			} else {
				LSX_TO_Maps.watchMapTriggers();
			}
		}
	}
);
