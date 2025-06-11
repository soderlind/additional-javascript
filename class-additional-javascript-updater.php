<?php
namespace Soderlind\Customizer;

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

/**
 * Plugin updater class for WP Loupe
 * 
 * Handles plugin updates directly from GitHub using the plugin-update-checker library
 *
 * @package Soderlind\Plugin\WPLoupe
 * @since 0.1.2
 */
class Additional_JavaScript_Updater {
	/**
	 * @var string GitHub repository URL
	 */
	private $github_url = 'https://github.com/soderlind/additional-javascript';

	/**
	 * @var string Branch to check for updates
	 */
	private $branch = 'main';

	/**
	 * @var string Regex pattern to match the plugin zip file name
	 */
	private $name_regex = '/additional-javascript\.zip/';

	/**
	 * @var string The plugin slug
	 */
	private $plugin_slug = 'additional-javascript';

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'setup_updater' ) );
	}

	/**
	 * Set up the update checker using GitHub integration
	 */
	public function setup_updater() {
		$update_checker = PucFactory::buildUpdateChecker(
			$this->github_url,
			ADDITIONAL_JAVASCRIPT_FILE,
			$this->plugin_slug
		);

		$update_checker->setBranch( $this->branch );
		$update_checker->getVcsApi()->enableReleaseAssets( $this->name_regex );
	}
}
