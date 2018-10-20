<?php 
/**
* Plugin Name: Woo Product Multi Layout lite
* Plugin URI: https://edatastyle.com/product/woo-product-multi-layout-for-woocommerce/
* Description: A simple user interface for Display WooCommerce Product shortcode
* Version: 1.5
* Author: eDataStyle
* Author URI: http://edatastyle.com
* Text Domain: ED_MULTI_LANG
* Domain Path: /languages
*/
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

defined( 'ED_MULTI_PATH' )    or  define( 'ED_MULTI_PATH',    plugin_dir_path( __FILE__ ) );
defined( 'ED_MULTI_URL' )    or  define( 'ED_MULTI_URL',    plugin_dir_url( __FILE__ ) );
defined( 'ED_MULTI_PREFIX' )    or  define( 'ED_MULTI_PREFIX','ed-wc');
defined( 'ED_MULTI_SETTINGS' )    or  define( 'ED_MULTI_SETTINGS','ed_woo_table_view_settings');
defined( 'ED_MULTI_FILE' )    or  define( 'ED_MULTI_FILE', plugin_basename( __FILE__ ) );
defined( 'ED_PRO_LINK' )    or  define( 'ED_PRO_LINK', 'https://edatastyle.com/product/woo-product-multi-layout-for-woocommerce/' );
load_plugin_textdomain( 'ED_MULTI_LANG', false, plugin_dir_path(__FILE__) . 'languages/' ); 
// Check if WooCommerce is active

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	
	if ( file_exists( ED_MULTI_PATH . '/class/class-admin.php' )) {
		require_once ED_MULTI_PATH . '/class/class-admin.php';
	}
	if ( file_exists( ED_MULTI_PATH . '/class/class-views.php' )) {
		require_once ED_MULTI_PATH . '/class/class-views.php';
	}
	if ( file_exists( ED_MULTI_PATH . '/class/class-options.php' )) {
		require_once ED_MULTI_PATH . '/class/class-options.php';
	}
	if ( file_exists( ED_MULTI_PATH . '/class/class-override.php' )) {
		require_once ED_MULTI_PATH . '/class/class-override.php';
	}
	if ( file_exists( ED_MULTI_PATH . '/class/class-addons.php' )) {
		require_once ED_MULTI_PATH . '/class/class-addons.php';
	}
}else{
	if ( file_exists( ED_MULTI_PATH . '/class/class-notice.php' )) {
		require_once ED_MULTI_PATH . '/class/class-notice.php';
	}
}


