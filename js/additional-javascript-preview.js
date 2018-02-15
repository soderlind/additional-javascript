/*
 * Script run inside a Customizer preview frame.
 */
(function( exports, $ ){
	var api = wp.customize;

	api.settingPreviewHandlers = {
		/**
		 * Preview changes to custom css.
		 *
		 * @param {string} value Custom CSS..
		 * @returns {void}
		 */
		custom_javascript: function( value ) {
			$( '#soderlind-custom-javascript' ).text( value );
		}
	};

	$( function() {
		api( 'custom_javascript[' + api.settings.theme.stylesheet + ']', function( setting ) {
			setting.bind( api.settingPreviewHandlers.custom_javascript );
		} );

		api.trigger( 'preview-ready' );
	});

})( wp, jQuery );