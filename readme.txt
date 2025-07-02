=== Additional JavaScript ===
Contributors: PerS
Tags: javascript, customizer, code, custom code, js
Donate link: https://paypal.me/PerSoderlind
Requires at least: 6.5
Tested up to: 6.8
Stable tag: 1.1.4
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

1. **Quick Install**

   * Download [`additional-javascript.zip`](https://github.com/soderlind/additional-javascript/releases/latest/download/additional-javascript.zip)
   * Upload via  Plugins > Add New > Upload Plugin
   * Activate the plugin.

2. **Updates**
   * Plugin updates are handled automatically via GitHub. No need to manually download and install updates.



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

= 1.1.4 =
* Enhanced class loading for the GitHub plugin updater.

= 1.1.3 =
* Use generic [WordPress Plugin GitHub Updater](https://github.com/soderlind/wordpress-plugin-gitHub-updater?tab=readme-ov-file#wordpress-plugin-github-updater)

= 1.1.2 =
* Minor code improvements

= 1.1.1 =
* Add plugin updater

= 1.1.0 =
* Updated compatibility with WordPress 6.5
* Code improvements and optimization
* Enhanced security measures

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.1.0 =
This version includes compatibility updates for WordPress 6.5 and security enhancements.