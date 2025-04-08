/*
 * Script run inside a Customizer preview frame.
 */
(function(exports) {
	const api = wp.customize;

	api.settingPreviewHandlers = {
		/**
		 * Preview changes to custom javascript.
		 *
		 * @param {string} value Custom JavaScript.
		 * @returns {void}
		 */
		custom_javascript: function(value) {
			document.getElementById('soderlind-custom-javascript').textContent = value;
		},
	};

	document.addEventListener('DOMContentLoaded', function() {
		api(
			`custom_javascript[${api.settings.theme.stylesheet}]`,
			function(setting) {
				setting.bind(api.settingPreviewHandlers.custom_javascript);
			},
		);

		api.trigger("preview-ready");
	});
})(wp);
