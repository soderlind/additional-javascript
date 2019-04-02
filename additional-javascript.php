<?php
/**
 * Additional JavaScript
 *
 * @package     Additional_JavaScript
 * @author      Per Soderlind
 * @copyright   2018 Per Soderlind
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: Additional JavaScript
 * Plugin URI:  https://github.com/soderlind/additional-javascript
 * GitHub Plugin URI: https://github.com/soderlind/additional-javascript
 * Description: Add additional JavaScript using the WordPress Customizer.
 * Version:     0.0.1
 * Author:      Per Soderlind
 * Author URI:  https://soderlind.no
 * Text Domain: additional-javascript
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */
namespace Soderlind\Customizer;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

add_action( 'init', __NAMESPACE__ . '\register_post_type_javascript', 0 );
add_action( 'wp_head', __NAMESPACE__ . '\soderlind_custom_javascript_cb', 110 );
add_action( 'customize_register', __NAMESPACE__ . '\register_additional_javascript' );
add_action( 'customize_preview_init', __NAMESPACE__ . '\customize_preview_additional_javascript' );
add_action( 'customize_controls_enqueue_scripts', __NAMESPACE__ . '\on_customize_controls_enqueue_scripts' );


function register_post_type_javascript() {

	register_post_type(
		'custom_javascript', array(
			'labels'           => array(
				'name'          => __( 'Custom JavaScript' ),
				'singular_name' => __( 'Custom JavaScript' ),
			),
			'public'           => false,
			'hierarchical'     => false,
			'rewrite'          => false,
			'query_var'        => false,
			'delete_with_user' => false,
			'can_export'       => true,
			// '_builtin'         => true, /* internal use only. don't use this when registering your own post type. */
			'supports'         => array( 'title', 'revisions' ),
			'capabilities'     => array(
				'delete_posts'           => 'edit_theme_options',
				'delete_post'            => 'edit_theme_options',
				'delete_published_posts' => 'edit_theme_options',
				'delete_private_posts'   => 'edit_theme_options',
				'delete_others_posts'    => 'edit_theme_options',
				'edit_post'              => 'unfiltered_html',
				'edit_posts'             => 'unfiltered_html',
				'edit_others_posts'      => 'unfiltered_html',
				'edit_published_posts'   => 'unfiltered_html',
				'read_post'              => 'read',
				'read_private_posts'     => 'read',
				'publish_posts'          => 'edit_theme_options',
			),
		)
	);
}


/**
 * Render the Custom JavaScript style element.
 *
 * @since 4.7.0
 */
function soderlind_custom_javascript_cb() {
	$javascript = soderlind_get_custom_javascript();
	if ( $javascript || is_customize_preview() ) {
		?>
		<script id="soderlind-custom-javascript">
			<?php echo $javascript; // ?>
		</script>
		<?php
	}
}


function register_additional_javascript( $wp_customize ) {
	$wp_customize->add_section(
		'custom_javascript', array(
			'title'    => _x( 'Additional JavaScript', 'customizer menu', 'dss-wp' ),
			'priority' => 999,
		)
	);

	require_once dirname( __FILE__ ) . '/class-custom-javascript-control.php';
	$custom_javascript_setting = new Soderlind_Customize_Custom_JavaScript_Setting(
		$wp_customize, sprintf( 'custom_javascript[%s]', get_stylesheet() ), array(
			'capability' => 'unfiltered_html',
			'default'    => '',
		)
	);

	$wp_customize->add_setting( $custom_javascript_setting );
	$control = new \WP_Customize_Code_Editor_Control(
		$wp_customize, 'custom_javascript', array(
			'label'     => 'Custom Javascript',
			'code_type' => 'application/javascript',
			'settings'  => array( 'default' => $custom_javascript_setting->id ),
			'section'   => 'custom_javascript', // Site Identity section
		)
	);
	$wp_customize->add_control( $control );
}

/**
 * Fetch the `custom_javascript` post for a given theme.
 *
 * @since 4.7.0
 *
 * @param string $stylesheet Optional. A theme object stylesheet name. Defaults to the current theme.
 * @return WP_Post|null The custom_javascript post or null if none exists.
 */
function soderlind_get_custom_javascript_post( $stylesheet = '' ) {
	if ( empty( $stylesheet ) ) {
		$stylesheet = get_stylesheet();
	}

	$custom_javascript_query_vars = array(
		'post_type'              => 'custom_javascript',
		'post_status'            => get_post_stati(),
		'name'                   => sanitize_title( $stylesheet ),
		'posts_per_page'         => 1,
		'no_found_rows'          => true,
		'cache_results'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
		'lazy_load_term_meta'    => false,
	);

	$post = null;
	if ( get_stylesheet() === $stylesheet ) {
		$post_id = get_theme_mod( 'custom_javascript_post_id' );

		if ( $post_id > 0 && get_post( $post_id ) ) {
			$post = get_post( $post_id );
		}

		// `-1` indicates no post exists; no query necessary.
		if ( ! $post && -1 !== $post_id ) {
			$query = new \WP_Query( $custom_javascript_query_vars );
			$post  = $query->post;
			/*
				* Cache the lookup. See soderlind_update_custom_javascript_post().
				* @todo This should get cleared if a custom_javascript post is added/removed.
				*/
			set_theme_mod( 'custom_javascript_post_id', $post ? $post->ID : -1 );
		}
	} else {
		$query = new \WP_Query( $custom_javascript_query_vars );
		$post  = $query->post;
	}

	return $post;
}

/**
 * Fetch the saved Custom JavaScript content for rendering.
 *
 * @since 4.7.0
 *
 * @param string $stylesheet Optional. A theme object stylesheet name. Defaults to the current theme.
 * @return string The Custom JavaScript Post content.
 */
function soderlind_get_custom_javascript( $stylesheet = '' ) {
	$javascript = '';

	if ( empty( $stylesheet ) ) {
		$stylesheet = get_stylesheet();
	}

	$post = soderlind_get_custom_javascript_post( $stylesheet );
	if ( $post ) {
		$javascript = $post->post_content;
	}

	/**
 * Filters the Custom JavaScript Output into the <head>.
 *
 * @since 4.7.0
 *
 * @param string $javascript    JavaScript pulled in from the Custom JavaScript CPT.
 * @param string $stylesheet    The theme stylesheet name.
 */
	$javascript = apply_filters( 'soderlind_get_custom_javascript', $javascript, $stylesheet );

	return $javascript;
}

/**
 * Update the `custom_javascript` post for a given theme.
 *
 * Inserts a `custom_javascript` post when one doesn't yet exist.
 *
 * @since 4.7.0
 *
 * @param string $javascript JavaScript, stored in `post_content`.
 * @param array  $args {
 *     Args.
 *
 *     @type string $preprocessed Pre-processed JavaScript, stored in `post_content_filtered`. Normally empty string. Optional.
 *     @type string $stylesheet   Stylesheet (child theme) to update. Optional, defaults to current theme/stylesheet.
 * }
 * @return WP_Post|WP_Error Post on success, error on failure.
 */
function soderlind_update_custom_javascript_post( $javascript, $args = array() ) {
	$args = wp_parse_args(
		$args, array(
			'preprocessed' => '',
			'stylesheet'   => get_stylesheet(),
		)
	);

	$data = array(
		'javascript'   => $javascript,
		'preprocessed' => $args['preprocessed'],
	);

	/**
	* Filters the `javascript` (`post_content`) and `preprocessed` (`post_content_filtered`) args for a `custom_javascript` post being updated.
	*
	* This filter can be used by plugin that offer JavaScript pre-processors, to store the original
	* pre-processed JavaScript in `post_content_filtered` and then store processed JavaScript in `post_content`.
	* When used in this way, the `post_content_filtered` should be supplied as the setting value
	* instead of `post_content` via a the `customize_value_custom_javascript` filter, for example:
	*
	* <code>
	* add_filter( 'customize_value_custom_javascript', function( $value, $setting ) {
	*     $post = soderlind_get_custom_javascript_post( $setting->stylesheet );
	*     if ( $post && ! empty( $post->post_content_filtered ) ) {
	*         $javascript = $post->post_content_filtered;
	*     }
	*     return $javascript;
	* }, 10, 2 );
	* </code>
	*
	* @since 4.7.0
	* @param array $data {
	*     Custom JavaScript data.
	*
	*     @type string $javascript          JavaScript stored in `post_content`.
	*     @type string $preprocessed Pre-processed JavaScript stored in `post_content_filtered`. Normally empty string.
	* }
	* @param array $args {
	*     The args passed into `wp_update_custom_javascript_post()` merged with defaults.
	*
	*     @type string $javascript          The original JavaScript passed in to be updated.
	*     @type string $preprocessed The original preprocessed JavaScript passed in to be updated.
	*     @type string $stylesheet   The stylesheet (theme) being updated.
	* }
	*/
	$data = apply_filters( 'soderlind_update_custom_javascript_data', $data, array_merge( $args, compact( 'javascript' ) ) );

	$post_data = array(
		'post_title'            => $args['stylesheet'],
		'post_name'             => sanitize_title( $args['stylesheet'] ),
		'post_type'             => 'custom_javascript',
		'post_status'           => 'publish',
		'post_content'          => $data['javascript'],
		'post_content_filtered' => $data['preprocessed'],
	);

	// Update post if it already exists, otherwise create a new one.
	$post = soderlind_get_custom_javascript_post( $args['stylesheet'] );
	if ( $post ) {
		$post_data['ID'] = $post->ID;
		$r               = wp_update_post( wp_slash( $post_data ), true );
	} else {
		$r = wp_insert_post( wp_slash( $post_data ), true );

		if ( ! is_wp_error( $r ) ) {
			if ( get_stylesheet() === $args['stylesheet'] ) {
				set_theme_mod( 'custom_javascript_post_id', $r );
			}

			// Trigger creation of a revision. This should be removed once #30854 is resolved.
			if ( 0 === count( wp_get_post_revisions( $r ) ) ) {
				wp_save_post_revision( $r );
			}
		}
	}

	if ( is_wp_error( $r ) ) {
		return $r;
	}
	return get_post( $r );
}

function customize_preview_additional_javascript() {
	$handle = 'customize-preview-additional-javascript';
	$src    = plugins_url( '/js/additional-javascript-preview.js', __FILE__ );
	$deps   = [ 'customize-preview', 'jquery' ];
	wp_enqueue_script( $handle, $src, $deps, rand(), true );
}

function on_customize_controls_enqueue_scripts() {
	$suffix = function_exists( 'is_rtl' ) && is_rtl() ? '-rtl' : '';
	$handle = "custom-javascript${suffix}";
	$src    = plugins_url( "/css/customize-controls-custom-javascript${suffix}.css", __FILE__ );
	$deps   = [ 'customize-controls' ];
	wp_enqueue_style( $handle, $src, $deps );
}
