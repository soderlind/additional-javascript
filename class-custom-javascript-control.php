<?php
namespace Soderlind\Customizer;

/**
 * From customize/class-wp-customize-custom-css-setting.php, adjusted for javascript
 */
/**
 * Custom Setting to handle Soderlind Custom JavaScript.
 *
 * @since 4.7.0
 *
 * @see WP_Customize_Setting
 */
final class Soderlind_Customize_Custom_JavaScript_Setting extends \WP_Customize_Setting {

	/**
	 * The setting type.
	 *
	 * @since 4.7.0
	 * @var string
	 */
	public $type = 'custom_javascript';

	/**
	 * Setting Transport
	 *
	 * @since 4.7.0
	 * @var string
	 */
	public $transport = 'postMessage';

	/**
	 * Capability required to edit this setting.
	 *
	 * @since 4.7.0
	 * @var string
	 */
	public $capability = 'unfiltered_html';

	/**
	 * Stylesheet
	 *
	 * @since 4.7.0
	 * @var string
	 */
	public $stylesheet = '';

	/**
	 * Soderlind_Customize_Custom_JavaScript_Setting constructor.
	 *
	 * @since 4.7.0
	 *
	 * @throws \Exception If the setting ID does not match the pattern `custom_javascript[$stylesheet]`.
	 *
	 * @param \WP_Customize_Manager $manager The Customize Manager class.
	 * @param string                $id      An specific ID of the setting. Can be a
	 *                                       theme mod or option name.
	 * @param array                 $args    Setting arguments.
	 */
	public function __construct( $manager, $id, $args = [] ) {
		parent::__construct( $manager, $id, $args );
		if ( 'custom_javascript' !== $this->id_data[ 'base' ] ) {
			throw new \Exception( 'Setting ID must have custom_javascript as its base.' );
		}
		if ( 1 !== count( $this->id_data[ 'keys' ] ) || empty( $this->id_data[ 'keys' ][ 0 ] ) ) {
			throw new \Exception( 'Setting ID must contain a single stylesheet key.' );
		}
		$this->stylesheet = $this->id_data[ 'keys' ][ 0 ];
	}

	/**
	 * Add filter to preview post value.
	 *
	 * @since 4.7.9
	 *
	 * @return bool False when preview short-circuits due no change needing to be previewed.
	 */
	public function preview() {
		if ( $this->is_previewed ) {
			return false;
		}
		$this->is_previewed = true;
		add_filter( 'soderlind_get_custom_javascript', [ $this, 'filter_previewed_wp_get_custom_javascript' ], 9, 2 );
		return true;
	}

	/**
	 * Filter `soderlind_get_custom_javascript` for applying the customized value.
	 *
	 * This is used in the preview when `soderlind_get_custom_javascript()` is called for rendering the styles.
	 *
	 * @since 4.7.0
	 * @see soderlind_get_custom_javascript()
	 *
	 * @param string $javascript        Original JavaScript.
	 * @param string $stylesheet Current stylesheet.
	 * @return string JavaScript.
	 */
	public function filter_previewed_wp_get_custom_javascript( $javascript, $stylesheet ) {
		if ( $stylesheet === $this->stylesheet ) {
			$customized_value = $this->post_value( null );
			if ( ! is_null( $customized_value ) ) {
				$javascript = $customized_value;
			}
		}
		return $javascript;
	}

	/**
	 * Fetch the value of the setting. Will return the previewed value when `preview()` is called.
	 *
	 * @since 4.7.0
	 * @see WP_Customize_Setting::value()
	 *
	 * @return string
	 */
	public function value() {
		if ( $this->is_previewed ) {
			$post_value = $this->post_value( null );
			if ( null !== $post_value ) {
				return $post_value;
			}
		}
		$id_base = $this->id_data[ 'base' ];
		$value   = '';
		$post    = soderlind_get_custom_javascript_post( $this->stylesheet );
		if ( $post ) {
			$value = $post->post_content;
		}
		if ( empty( $value ) ) {
			$value = $this->default;
		}

		/** This filter is documented in wp-includes/class-wp-customize-setting.php */
		$value = apply_filters( "customize_value_{$id_base}", $value, $this );

		return $value;
	}

	/**
	 * Validate JavaScript.
	 *
	 * Checks for imbalanced braces, brackets, and comments.
	 * Notifications are rendered when the customizer state is saved.
	 *
	 * @since 4.7.0
	 * @since 4.9.0 Checking for balanced characters has been moved client-side via linting in code editor.
	 *
	 * @param string $javascript The input string.
	 * @return true|WP_Error True if the input was validated, otherwise WP_Error.
	 */
	public function validate( $javascript ) {
		$validity = new \WP_Error();

		// if ( preg_match( '#</?\w+#', $javascript ) ) {
		// $validity->add( 'illegal_markup', __( 'Markup is not allowed in JavaScript.' ) );
		// }

		if ( empty( $validity->errors ) ) {
			$validity = parent::validate( $javascript );
		}
		return $validity;
		// return true;
	}

	/**
	 * Store the JavaScript setting value in the custom_javascript custom post type for the stylesheet.
	 *
	 * @since 4.7.0
	 *
	 * @param string $javascript The input value.
	 * @return int|false The post ID or false if the value could not be saved.
	 */
	public function update( $javascript ) {
		if ( empty( $javascript ) ) {
			$javascript = '';
		}

		$r = soderlind_update_custom_javascript_post(
			$javascript,
			[ 
				'stylesheet' => $this->stylesheet,
			]
		);

		if ( $r instanceof WP_Error ) {
			return false;
		}
		$post_id = $r->ID;

		// Cache post ID in theme mod for performance to avoid additional DB query.
		if ( $this->manager->get_stylesheet() === $this->stylesheet ) {
			set_theme_mod( 'custom_javascript_post_id', $post_id );
		}

		return $post_id;
	}
}
