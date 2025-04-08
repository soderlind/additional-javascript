=== Additional JavaScript ===
Contributors: PerS
Tags: javascript, customizer, code, custom code, js
Donate link: https://paypal.me/PerSoderlind
Requires at least: 6.3
Tested up to: 6.7
Stable tag: 1.1.0
Requires PHP: 8.2
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

Add additional JavaScript to your WordPress site using the WordPress Customizer - safely and with live preview.

== Description ==

Additional JavaScript allows you to add custom JavaScript to your WordPress site directly from the WordPress Customizer. With live preview functionality, you can see your JavaScript changes in real-time before publishing them to your site.

= Features =
* Add custom JavaScript through the familiar WordPress Customizer interface
* Live preview of JavaScript changes
* Secure implementation - only users with the 'unfiltered_html' capability (administrators) can edit JavaScript
* Revision history for your JavaScript code (uses WordPress post revisions)
* Clean, minimal interface focused on code editing

= Security =
This plugin restricts JavaScript editing to users with the 'unfiltered_html' capability, which by default is only granted to administrators on single site installations and super administrators on multisite installations.

== Installation ==

1. Upload the 'additional-javascript' folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Customizer → Additional JavaScript to add your custom JavaScript

== Usage ==

1. Navigate to Appearance → Customize in your WordPress admin area
2. Click on the "Additional JavaScript" section at the bottom of the customizer menu
3. Add your JavaScript code in the editor
4. See the live preview of your changes
5. Click "Publish" to apply your JavaScript to the site

== Frequently Asked Questions ==

= Who can add JavaScript using this plugin? =

Only users with the 'unfiltered_html' capability can add JavaScript using this plugin. By default, this is limited to administrators on single site installations and super administrators on multisite installations.

= Will this slow down my site? =

No, the plugin is designed to be lightweight and only loads the necessary scripts and styles when needed.

= Where is the JavaScript added on my site? =

The JavaScript is added at the end of the `<head>` section of your site with a priority of 110.

== Changelog ==

= 1.1.0 =
* Updated compatibility with WordPress 6.5
* Code improvements and optimization
* Enhanced security measures

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.1.0 =
This version includes compatibility updates for WordPress 6.5 and security enhancements.