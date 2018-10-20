<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once( 'interface-woocommerce-livechat.php' );
require_once( 'class-woocommerce-livechat.php' );

/**
 * WooCommerce_LiveChat_Admin class for managing LiveChat settings.
 */
class WooCommerce_LiveChat_Admin extends WooCommerce_LiveChat implements WooCommerce_LiveChat_Interface
{

    /**
     * @var object current user info
     */
    private $user_info;
    /**
     * @var string plugin version
     */
    private $plugin_version;

	/**
	 * @var integer|string timestamp from which review notice timeout count starts from
	 */
	protected $review_notice_start_timestamp = null;

	/**
	 * @var integer|string timestamp offset
	 */
	protected $review_notice_start_timestamp_offset = null;

	/**
	 * @var bool returns true if review notice was dismissed
	 */
	protected $review_notice_dismissed = false;

    /**
     * Set up base plugin info.
     */
    public function __construct() {
        $this->set_up_plugin_version();
    }

    /**
     * Set up base actions.
     */
    public function init() {
	    add_action('init', array($this, 'load_translations'));

	    // notice action
	    if( $this->check_review_notice_conditions() ) {
		    add_action( 'init', array($this, 'load_review_scripts') );
		    add_action( 'wp_ajax_wc_lc_review_dismiss', array($this, 'ajax_review_dismiss') );
		    add_action( 'wp_ajax_wc_lc_review_postpone', array($this, 'ajax_review_postpone') );
		    add_action( 'admin_notices', array( $this, 'show_review_notice' ) );
	    }

        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'wp_ajax_wc-livechat-update-settings', array( $this, 'update_settings' ) );
        add_action( 'wp_ajax_wc-livechat-check-cart', array( $this, 'check_cart' ) );
        add_action( 'wp_ajax_wc-livechat-js-urls', array( $this, 'js_urls' ) );
        add_action( 'wp_ajax_wc-livechat-script', array( $this, 'render_script' ) );

        if ( array_key_exists('page', $_GET) && 'wc-livechat' == $_GET['page'] ) {
            add_action( 'admin_head', array( $this, 'admin_head_block' ) );
        }
    }

	/**
	 * Make translation ready
	 */
	public function load_translations()
	{
		load_plugin_textdomain(
			'livechat-woocommerce',
			false,
			'livechat-woocommerce/languages'
		);
	}

    public function js_urls() {
        $this->get_renderer()->render(
            'admin-head-block-template.php',
            array(
                'set_settings_url'  => admin_url() . 'admin-ajax.php'
            )
        );

        wp_die();
    }

    /**
     * Render admin head block.
     */
    public function admin_head_block() {
        wp_enqueue_script( 'wc-livechat-data', admin_url() . 'admin-ajax.php?action=wc-livechat-js-urls&t=' . time() );
        wp_enqueue_script( 'wc-livechat', plugins_url('js/wc-livechat.js', __FILE__), array('jquery', 'wc-livechat-data'), $this->plugin_version, true );
        wp_enqueue_style( 'wc-livechat-style', plugins_url('css/style.css', __FILE__), array(), $this->plugin_version );
        wp_enqueue_style( 'wc-livechat-fonts', '//fonts.googleapis.com/css?family=Lato:300' );
    }

	/**
	 * Loads script for review notice
	 */
	public function load_review_scripts() {
		wp_enqueue_script('livechat-review', plugins_url('js/wc-livechat-review.js', __FILE__), 'jquery', $this->plugin_version, true);
	}

	/**
	 * Dismiss review notice AJAX
	 */
	public function ajax_review_dismiss() {
		update_option('wc-lc_review_notice_dismissed', true);
		echo "OK";
		wp_die();
	}

	/**
	 * Postpone review notice AJAX
	 */
	public function ajax_review_postpone() {
		update_option( 'wc-lc_review_notice_start_timestamp', time() );
		update_option( 'wc-lc_review_notice_start_timestamp_offset', 7 );
		echo "OK";
		wp_die();
	}

    /**
     * Add plugin to admin menu.
     */
    public function admin_menu() {
        add_submenu_page( 'woocommerce', 'LiveChat', 'LiveChat', 'manage_options', 'wc-livechat', array( $this, 'settings_action' ) );
    }

    /**
     * Update user settings (license id  and custom params)
     */
    public function update_settings() {
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
            if ( array_key_exists( 'licenseId', $_POST ) ) {
                $updated = $this->set_up_license_id( (int) $_POST['licenseId'] );
            } else if ( array_key_exists( 'licenseEmail', $_POST ) ) {
                $updated = $this->set_up_license_email( $_POST['licenseEmail'] );
            } else if ( array_key_exists( 'customDataSettings', $_POST ) ) {
                $settings = explode( ':', $_POST['customDataSettings'] );
                if ( 2 === count( $settings ) ) {
                    $updated = $this->update_custom_data_settings( $settings[0], $settings[1] );
                }
            }
        }

        echo ( $updated === true ) ? json_encode( 'ok' ) : json_encode( 'err' );
        wp_die();
    }

    /**
     * Returns plugin settings template depends on LiveChat user details.
     *
     * @return string
     */
    public function settings_action() {
        if ( array_key_exists( 'reset', $_GET ) && 1 == $_GET['reset'] ) {
            // Reset user email and redirect to plugin login/register page.
            $this->reset_settings();
            $redirect_url = ( false === strpos(wp_get_referer(), '&reset=1') ) ?
                wp_get_referer() : str_replace( '&reset=1', '', wp_get_referer() );
            if ( headers_sent() ) {
                die( '<script> location.replace("' . $redirect_url . '"); </script>' );
            }
            wp_redirect( $redirect_url );
            wp_die();
        }

        $user_email = $this->get_user_property( 'user_email' );
        $username = $this->get_user_property( 'user_login' );
	    $check_mobile = $this->check_mobile();

        if ( null === ( $license_id = $this->get_license() ) ) {
            // If there is no give license, render plugin login/register page.
            return $this->get_renderer()->render(
                'connect-account-template.php',
                array(
                	'check_mobile' => $check_mobile,
                    'username'  => $username,
                    'user_email' => $user_email,
                )
            );
        }

        return $this->get_renderer()->render(
            'settings-template.php',
            array(
	            'check_mobile'                  => $check_mobile,
                'settings'                      => $this->get_custom_data_settings(),
                'settings_products_count_key'   => self::LC_S_PRODUCST_COUNTS_KEY,
                'settings_products_key'         => self::LC_S_PRODUCTS_KEY,
                'settings_shipping_address_key' => self::LC_S_SHIPPING_ADDRESS_KEY,
                'settings_total_value_key'      => self::LC_S_TOTAL_VALUE_KEY,
                'settings_last_order_key'       => self::LC_S_LAST_ORDER_KEY,
                'settings_disable_mobile_key'   => self::LC_S_DISABLE_MOBILE_KEY,
                'settings_disable_sounds_key'   => self::LC_S_DISABLE_SOUNDS_KEY,
                'settings_disable_guests_key'   => self::LC_S_DISABLE_GUESTS_KEY,
                'license_email'                 => $this->get_license_email(),
	            'license_id'                    => $this->get_license(),
                'username'                      => $username,
                'user_email'                    => $user_email,
            )
        );
    }

    /**
     * Update custom data settings by given key and value.
     *
     * @param string $key
     * @param string $value
     * @return boolean
     */
    private function update_custom_data_settings( $key, $value ) {
        $current_settings       = $this->get_custom_data_settings();
        $current_settings[$key] = (boolean) $value;

        return update_option( self::LC_SETTINGS, $current_settings );
    }

    /**
     * Reset user license id.
     */
    private function reset_settings() {
        delete_option( self::LC_LICENSE_ID );
        delete_option( self::LC_LICENSE_EMAIL );
        delete_option( self::LC_SETTINGS );
    }

    /**
     * Set up license email
     *
     * @param string $license_email
     */
    private function set_up_license_email( $license_email ) {
	    update_option('wc-lc_review_notice_start_timestamp', time());
	    update_option('wc-lc_review_notice_start_timestamp_offset', 45);

        update_option( self::LC_LICENSE_EMAIL, $license_email );
    }

    /**
     * Returns user LiveChat license email.
     * @return string
     */
    private function get_license_email() {
        return get_option( self::LC_LICENSE_EMAIL, null );
    }

    /**
     * Set up user license id and turn on all custom data settings.
     *
     * @param integer $license_id
     * @return boolean
     */
    private function set_up_license_id( $license_id ) {
        // valid licenseId
        if (is_int( $license_id ) && $license_id >= 0) {
            // Turn on all custom data settings.
            $default_settings = array(
                self::LC_S_PRODUCST_COUNTS_KEY  => 1,
                self::LC_S_PRODUCTS_KEY         => 1,
                self::LC_S_SHIPPING_ADDRESS_KEY => 1,
                self::LC_S_TOTAL_VALUE_KEY      => 1,
                self::LC_S_LAST_ORDER_KEY       => 1,
	            self::LC_S_DISABLE_MOBILE_KEY   => 0,
	            self::LC_S_DISABLE_SOUNDS_KEY   => 0,
	            self::LC_S_DISABLE_GUESTS_KEY   => 0
            );

            update_option( self::LC_SETTINGS, $default_settings );
            // Set up license ID
            update_option( self::LC_LICENSE_ID, $license_id );
            return true;
        }

        return false;
    }

    /**
     * Returns user property.
     *
     * @param string $property_name
     * @param string $default
     * @return string
     */
    private function get_user_property( $property_name, $default = null ) {
        if ( null === $this->user_info ) {
            $this->user_info = wp_get_current_user();
        }

        return ( null !== ($res = $this->user_info->get( $property_name ) ) ) ? $res : $default;
    }

    /**
     * Set up plugin version.
     */
    private function set_up_plugin_version() {
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }

        $plugin_dir = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) . '/..' ) );

        if (
            array_key_exists('livechat.php', $plugin_dir) &&
            is_array($plugin_dir['livechat.php']) &&
            array_key_exists('Version', $plugin_dir['livechat.php'])
        ) {
            $this->plugin_version = $plugin_dir['livechat.php']['Version'];
        }
    }

	/**
	 * Check if review notice was dismissed.
	 */
	protected function check_if_review_notice_was_dismissed() {
		if (!$this->review_notice_dismissed)
		{
			$this->review_notice_dismissed = get_option('wc-lc_review_notice_dismissed');
		}

		return $this->review_notice_dismissed;
	}

	/**
	 * Check if review notice was dismissed.
	 */
	protected function check_if_license_is_active($license_number) {
		$url = 'https://api.livechatinc.com/v2/license/' . $license_number;
		try {
			if(function_exists('curl_init')) {
				$curl = curl_init($url);
				curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
				$response = curl_exec( $curl );
				$code     = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
				curl_close( $curl );

				if ( $code === 200 ) {
					return json_decode( $response )->license_active;
				} else {
					throw new Exception( $code );
				}
			} else if(ini_get('allow_url_fopen') === '1' || strtolower(ini_get('allow_url_fopen')) === 'on') {
				$options = array(
					'http' => array(
						'method'  => 'GET'
					),
				);
				$context  = stream_context_create($options);
				$result = file_get_contents($url, false, $context);
				return json_decode( $result )->license_active;
			}
		} catch(Exception $exception) {
			error_log(
				'check_if_license_is_active() error ' .
				$exception->getCode() .
				': ' .
				$exception->getMessage()
			);
		}
		return false;
	}

	/**
	 * Get timestamp for review notice.
	 */
	protected function get_review_notice_start_timestamp() {
		if (is_null($this->review_notice_start_timestamp))
		{
			$timestamp = get_option('wc-lc_review_notice_start_timestamp');
			// if timestamp was not set on install
			if (!$timestamp) {
				$timestamp = time();
				update_option('wc-lc_review_notice_start_timestamp', $timestamp); // set timestamp if not set on install
			}

			$this->review_notice_start_timestamp = $timestamp;
		}

		return $this->review_notice_start_timestamp;
	}

	/**
	 * Get timestamp offset for review notice.
	 */
	protected function get_review_notice_start_timestamp_offset() {
		if (is_null($this->review_notice_start_timestamp_offset))
		{
			$offset = get_option('wc-lc_review_notice_start_timestamp_offset');
			// if offset was not set on install
			if (!$offset) {
				$offset = 15;
				update_option('wc-lc_review_notice_start_timestamp_offset', $offset); // set shorter offset
			}

			$this->review_notice_start_timestamp_offset = $offset;
		}

		return $this->review_notice_start_timestamp_offset;
	}

	/**
	 * Check if review notice's conditions were fulfilled
	 */
	protected function check_review_notice_conditions() {
		$license_id = $this->get_license();

		if( !$this->check_if_review_notice_was_dismissed()) {
			if ( $license_id !== null && $this->check_if_license_is_active( $license_id ) ) {
				$secondsInDay = 60 * 60 * 24;
				$noticeTimeout = time() - $this->get_review_notice_start_timestamp();
				$timestampOffset = $this->get_review_notice_start_timestamp_offset();
				if ($noticeTimeout >= $secondsInDay * $timestampOffset) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Render review notice
	 */
	public function show_review_notice()
	{
		return $this->get_renderer()->render('review-notice-template.php');
	}
}
