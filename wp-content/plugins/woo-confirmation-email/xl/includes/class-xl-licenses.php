<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Plugin licenses data class / we do not handle license activation and deactivation at this class
 *
 * @author XLPlugins
 * @package XLCore
 */
class XL_licenses {

	public $plugins_list;
	protected static $instance;

	public function __construct() {
		//calling appropriate hooks by identifying the request
		$this->maybe_submit();

		$this->maybe_deactivate();
	}

	public function get_plugins_list() {
		$this->plugins_list = apply_filters( 'xl_plugins_license_needed', array() );
	}

	public function get_data() {
		if ( ! is_null( $this->plugins_list ) ) {
			return $this->plugins_list;
		}
		$this->get_plugins_list();

		return $this->plugins_list;
	}

	/**
	 * Pass to submission
	 */
	public function maybe_submit() {
		if ( isset( $_POST['action'] ) && $_POST['action'] == "xl_activate-products" ) {
			do_action( 'xl_licenses_submitted', $_POST );
		}
	}

	/**
	 * Pass to deactivate hook
	 */
	public function maybe_deactivate() {
		if ( isset( $_GET['action'] ) && $_GET['action'] == "xl_deactivate-product" ) {
			do_action( 'xl_deactivate_request', $_GET );
		}
	}

	/**
	 * Creates and instance of the class
	 * @return XL_licenses
	 */
	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
}
