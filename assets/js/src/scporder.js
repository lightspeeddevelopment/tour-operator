(function($){

	$( 'table.posts #the-list, table.pages #the-list' ).sortable(
		{
			'items': 'tr',
			'axis': 'y',
			'helper': fixHelper,

			'update' : function(e, ui) {
				$.post(
					scporderjs_params.ajax_url,
					{
						action: 'update-menu-order',
						security: scporderjs_params.ajax_nonce,
						order: $( '#the-list' ).sortable( 'serialize' ),
					}
				);
			}
		}
	);

	$( 'table.tags #the-list' ).sortable(
		{
			'items': 'tr',
			'axis': 'y',
			'helper': fixHelper,

			'update' : function(e, ui) {
				$.post(
					scporderjs_params.ajax_url,
					{
						action: 'update-menu-order-tags',
						security: scporderjs_params.ajax_nonce,
						order: $( '#the-list' ).sortable( 'serialize' ),
					}
				);
			}
		}
	);

	var fixHelper = function(e, ui) {
		ui.children().children().each(
			function() {
				$( this ).width( $( this ).width() );
			}
		);

		return ui;
	};

})( jQuery );
