<?php
/**
 * Plugin Name: Genesis Simple Share
 * Plugin URI: https://wordpress.org/plugins/genesis-simple-share/
 * Description: A simple sharing plugin using the Share script.
 * Version: 1.1.5
 * Author: StudioPress
 * Author URI: https://www.studiopress.com
 *
 * Text Domain: genesis-simple-share
 * Domain Path /languages/
 *
 * @package genesis-simple-share
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( esc_html__( 'Sorry, you are not allowed to access this page directly.', 'genesis-simple-share' ) );
}

define( 'GENESIS_SIMPLE_SHARE_VERSION', '1.1.5' );
define( 'GENESIS_SIMPLE_SHARE_PATH', plugin_dir_path( __FILE__ ) );
define( 'GENESIS_SIMPLE_SHARE_INC', plugin_dir_path( __FILE__ ) . '/includes/' );
define( 'GENESIS_SIMPLE_SHARE_URL', plugins_url( '', __FILE__ ) );

add_action( 'genesis_init', 'genesis_simple_share_init', 99 );

/**
 * Loads plugin text domain and required files. Uses genesis_init to ensure Genesis functions are available
 *
 * @since 0.1.0
 *
 * @uses GENESIS_SIMPLE_SHARE_INC
 */
function genesis_simple_share_init() {

	load_plugin_textdomain( 'genesis-simple-share', false, GENESIS_SIMPLE_SHARE_PATH . '/languages/' );

	if ( is_admin() && class_exists( 'Genesis_Admin_Boxes' ) ) {
		require_once GENESIS_SIMPLE_SHARE_INC . 'class-genesis-simple-share-boxes.php';
		require_once GENESIS_SIMPLE_SHARE_INC . 'class-genesis-simple-share-entry-meta.php';
	} else {
		require_once GENESIS_SIMPLE_SHARE_INC . 'class-genesis-simple-share-front-end.php';
	}
}
