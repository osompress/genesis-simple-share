<?php
/*
Plugin Name: Genesis Simple Share
Plugin URI:

Description: A simple sharing plugin using the Share script.

Version: 1.0.9

Author: Rainmaker Digital LLC
Author URI: http://www.copyblogger.com

Text Domain: genesis-simple-share
Domain Path /languages/
*/

//* Prevent direct access to the plugin
if ( ! defined( 'ABSPATH' ) ) {
    die( __( 'Sorry, you are not allowed to access this page directly.', 'genesis-simple-share' ) );
}

define( 'GENESIS_SIMPLE_SHARE_LIB', dirname( __FILE__ ) . '/lib/' );

add_action( 'genesis_init', 'genesis_simple_share_init', 99 );
/**
 * Loads plugin text domain and required files. Uses genesis_init to ensure Genesis functions are available
 *
 * @since 0.1.0
 *
 * @uses GENESIS_SIMPLE_SHArE_LIB
 *
 */
function genesis_simple_share_init() {

	//* Load textdomain for translation
    load_plugin_textdomain( 'genesis-simple-share', false, basename( dirname( __FILE__ ) ) . '/languages/' );

	if ( is_admin() && class_exists( 'Genesis_Admin_Boxes' ) ) {
		require_once( GENESIS_SIMPLE_SHARE_LIB . 'admin.php' );
		require_once( GENESIS_SIMPLE_SHARE_LIB . 'post-meta.php' );
	}
	else
		require_once( GENESIS_SIMPLE_SHARE_LIB . 'front-end.php' );

	//require_once( GENESIS_SIMPLE_SHArE_LIB . 'functions.php' );

}
