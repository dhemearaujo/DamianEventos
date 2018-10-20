<?php

/**
 * Plugin Name: PayPal Plus Brasil
 * Description: Adicione o checkout transparente do PayPal ao seu checkout.
 * Version: 1.2
 * Author: PayPal
 * Author URI: https://paypal.com.br
 * Requires at least: 4.4
 * Tested up to: 4.9
 * Text Domain: ppp-brasil
 * Domain Path: /languages/
 * WC requires at least: 3.0
 * WC tested up to: 3.4
 */

// Exit if not in WordPress.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Check if class already exists before create.
if ( ! class_exists( 'WC_PPP_Brasil' ) ) {

	/**
	 * Class WC_PPP_Brasil.
	 */
	class WC_PPP_Brasil {

		public static $VERSION = '1.2';

		/**
		 * Current plugin instance.
		 * @var WC_PPP_Brasil
		 */
		private static $instance;

		/**
		 * WC_PPP_Brasil constructor.
		 */
		private function __construct() {
			// Load plugin text domain.
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
			// Include the necessary files.
			$this->includes();
			// Check if Extra Checkout Fields for Brazil is installed
			if ( is_admin() ) {
				add_action( 'admin_notices', array( $this, 'ecfb_missing_notice' ) );
				add_action( 'admin_notices', array( $this, 'woocommerce_wrong_version' ) );
				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array(
					$this,
					'plugin_action_links'
				) );
			}
			// Check if WC is compatible
			if ( ! self::woocommerce_incompatible() ) {
				// Add hook to include new gateways.
				add_action( 'plugins_loaded', array( $this, 'include_gateway' ) );
				// Add the payment methods.
				add_filter( 'woocommerce_payment_gateways', array( $this, 'add_payment_method' ) );
			}
		}

		/**
		 * Get the plugin instance.
		 * @return WC_PPP_Brasil
		 */
		public static function get_instance() {
			// Check if instance is not created, so create a new one.
			if ( ! self::$instance ) {
				self::$instance = new self;

			}

			return self::$instance;
		}

		/**
		 * Includes for the plugin.
		 */
		public function includes() {
			include_once dirname( __FILE__ ) . '/includes/functions.php';
		}

		/**
		 * Include the files for gateway.
		 */
		public function include_gateway() {
			// Check if WooCommerce is installed
			if ( class_exists( 'WC_Payment_Gateway' ) ) {
				include dirname( __FILE__ ) . '/includes/class-wc-ppp-brasil-gateway.php';
				include dirname( __FILE__ ) . '/includes/class-wc-ppp-brasil-metabox.php';
				if ( ! in_array( get_woocommerce_currency(), WC_PPP_Brasil::get_allowed_currencies() ) ) {
					add_action( 'admin_notices', array( $this, 'woocommerce_unavailable_currency' ) );
				}
			} else {
				add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
			}
		}

		/**
		 * Filter and add the payment method to WooCommerce.
		 *
		 * @param $methods array Already loaded gateways.
		 *
		 * @return array New loaded gateways.
		 */
		public function add_payment_method( $methods ) {
			$methods[] = 'WC_PPP_Brasil_Gateway';

			return $methods;
		}

		/**
		 * Load the plugin text domain for translation.
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'ppp-brasil', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * WooCommerce Extra Checkout Fields for Brazil notice.
		 */
		public function ecfb_missing_notice() {
			// Check if Extra Checkout Fields for Brazil is installed, but check if it's BRL.
			if ( pppbr_needs_cpf() && ! class_exists( 'Extra_Checkout_Fields_For_Brazil' ) ) {
				include dirname( __FILE__ ) . '/includes/views/html-notice-missing-ecfb.php';
			}
		}

		/**
		 * WooCommerce missing notice.
		 */
		public function woocommerce_missing_notice() {
			include dirname( __FILE__ ) . '/includes/views/html-notice-missing-woocommerce.php';
		}

		public function woocommerce_unavailable_currency() {
			include dirname( __FILE__ ) . '/includes/views/html-notice-woocommerce-unavailable-currency.php';
		}

		public function woocommerce_wrong_version() {
			if ( self::woocommerce_incompatible() ) {
				include dirname( __FILE__ ) . '/includes/views/html-notice-wrong-version-woocommerce.php';
			}
		}

		/**
		 * Action links.
		 *
		 * @param array $links Action links.
		 *
		 * @return array
		 */
		public function plugin_action_links( $links ) {
			$plugin_links = array();
			if ( ! self::woocommerce_incompatible() ) {
				$plugin_links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc-ppp-brasil-gateway' ) ) . '">' . __( 'Configurações', 'ppp-brasil' ) . '</a>';
			}

			return array_merge( $plugin_links, $links );
		}

		/**
		 * Return if WooCommerce is compatible or not.
		 * @return mixed
		 */
		public static function woocommerce_incompatible() {
			$version = get_option( 'woocommerce_version' );

			return version_compare( $version, '3.0.0', "<" );
		}

		public static function get_allowed_currencies() {
			return array( 'BRL', 'USD' );
		}

	}

	// Init the plugin.
	WC_PPP_Brasil::get_instance();

}