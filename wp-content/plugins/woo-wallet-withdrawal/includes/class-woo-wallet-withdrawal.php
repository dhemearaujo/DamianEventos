<?php

/**
 * Withdrawal plugin main class
 *
 * @author subrata
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

final class WOO_WALLET_WITHDRAWAL {

    /**
     * The single instance of the class.
     *
     * @var WOO_WALLET_WITHDRAWAL
     * @since 1.0.0
     */
    protected static $_instance = null;

    /**
     * WOO_Wallet_Withdrawal_Payment_gateways class instance
     * @var WOO_Wallet_Withdrawal_Payment_gateways
     */
    public $gateways = null;

    /**
     * Main instance
     * @return class object
     */
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Class constructor
     */
    public function __construct() {
        if (WOO_Wallet_Withdrawal_Dependencies::is_woo_wallet_active()) {
            $this->define_constants();
            $this->includes();
            $this->init_hooks();
            do_action('woo_wallet_withdrawal_loaded');
        } else {
            add_action('admin_notices', array($this, 'admin_notices'), 15);
        }
    }

    /**
     * Constants define
     */
    private function define_constants() {
        $this->define('WOO_WALLET_WITHDRAWAL_ABSPATH', dirname(WOO_WALLET_WITHDRAWAL_PLUGIN_FILE) . '/');
        $this->define('WOO_WALLET_WITHDRAWAL_PLUGIN_SERVER_URL', 'https://woowallet.in/');
        $this->define('WOO_WALLET_WITHDRAWAL_PLUGIN_TOKEN', 'woo-wallet-withdrawal');
        $this->define('WOO_WALLET_WITHDRAWAL_VERSION', '1.0.1');
    }

    /**
     * 
     * @param string $name
     * @param mixed $value
     */
    private function define($name, $value) {
        if (!defined($name)) {
            define($name, $value);
        }
    }

    /**
     * Check request
     * @param string $type
     * @return bool
     */
    private function is_request($type) {
        switch ($type) {
            case 'admin' :
                return is_admin();
            case 'ajax' :
                return defined('DOING_AJAX');
            case 'cron' :
                return defined('DOING_CRON');
            case 'frontend' :
                return (!is_admin() || defined('DOING_AJAX') ) && !defined('DOING_CRON');
        }
    }

    /**
     * load plugin files
     */
    public function includes() {
        include_once(WOO_WALLET_WITHDRAWAL_ABSPATH . 'includes/abstracts/abstract-woo-wallet-payment-gateway.php');
        include_once(WOO_WALLET_WITHDRAWAL_ABSPATH . 'includes/class-woo-wallet-withdrawal-payment-gateways.php');
        include_once(WOO_WALLET_WITHDRAWAL_ABSPATH . 'includes/class-woo-wallet-withdrawal-post-type.php');
        if ($this->is_request('admin')) {
            include_once(WOO_WALLET_WITHDRAWAL_ABSPATH . 'includes/class-woo-wallet-withdrawal-admin.php');
            include_once(WOO_WALLET_WITHDRAWAL_ABSPATH . 'includes/class-woo-wallet-withdrawal-license.php');
        }
        if ($this->is_request('frontend')) {
            include_once(WOO_WALLET_WITHDRAWAL_ABSPATH . 'includes/class-woo-wallet-withdrawal-frontend.php');
        }
        if ($this->is_request('ajax')) {
            include_once(WOO_WALLET_WITHDRAWAL_ABSPATH . 'includes/class-woo-wallet-withdrawal-ajax.php');
        }
    }

    /**
     * Plugin init
     */
    private function init_hooks() {
        // Set up localisation.
        $this->load_plugin_textdomain();
        $this->gateways = WOO_Wallet_Withdrawal_Payment_gateways::instance();
        add_action('init', array($this, 'init'));
    }

    /**
     * Load Localisation files.
     *
     * Note: the first-loaded translation file overrides any following ones if the same translation is present.
     *
     */
    public function load_plugin_textdomain() {
        $locale = is_admin() && function_exists('get_user_locale') ? get_user_locale() : get_locale();
        $locale = apply_filters('plugin_locale', $locale, 'woo-wallet-withdrawal');

        unload_textdomain('woo-wallet-withdrawal');
        load_textdomain('woo-wallet-withdrawal', WP_LANG_DIR . '/woo-wallet-withdrawal/woo-wallet-withdrawal-' . $locale . '.mo');
        load_plugin_textdomain('woo-wallet-withdrawal', false, plugin_basename(dirname(WOO_WALLET_WITHDRAWAL_PLUGIN_FILE)) . '/languages');
    }
    
    public function init(){
        add_rewrite_endpoint(get_option('woocommerce_woo_wallet_withdrawal_endpoint', 'woo-wallet-withdrawal'), EP_PAGES);
        if (!get_option('_wallet_withdrawal_enpoint_added')) {
            flush_rewrite_rules();
            update_option('_wallet_withdrawal_enpoint_added', true);
        }
        add_filter('woo_wallet_current_balance', array($this, 'woo_wallet_current_balance'), 5, 2);
        add_filter('woocommerce_email_classes', array($this, 'woocommerce_email_classes'));
    }
    
    /**
     * WooCommerce email loader
     * @param array $emails
     * @return array
     */
    public function woocommerce_email_classes($emails) {
        $emails['WOO_Wallet_Withdrawal_Request'] = include WOO_WALLET_WITHDRAWAL_ABSPATH . 'includes/emails/class-woo-wallet-withdrawal-request.php';
        $emails['WOO_Wallet_Withdrawal_Reject'] = include WOO_WALLET_WITHDRAWAL_ABSPATH . 'includes/emails/class-woo-wallet-withdrawal-reject.php';
        $emails['WOO_Wallet_Withdrawal_Approved'] = include WOO_WALLET_WITHDRAWAL_ABSPATH . 'includes/emails/class-woo-wallet-withdrawal-approve.php';
        return $emails;
    }
    
    public function woo_wallet_current_balance($balance, $user_id){
        $credit_args = array(
            'user_id' => $user_id,
            'where' => array(
                array(
                    'key' => 'type',
                    'value' => 'credit'
                )
            )
        );
        $debit_args = array(
            'user_id' => $user_id,
            'where' => array(
                array(
                    'key' => 'type',
                    'value' => 'debit'
                )
            )
        );
        $credit_amount = array_sum(wp_list_pluck(get_wallet_transactions($credit_args), 'amount'));
        $debit_amount = array_sum(wp_list_pluck(get_wallet_transactions($debit_args), 'amount'));
        $balance = $credit_amount - $debit_amount;
        return $balance;
    }

        /**
     * Load template
     * @param string $template_name
     * @param array $args
     * @param string $template_path
     * @param string $default_path
     */
    public function get_template($template_name, $args = array(), $template_path = '', $default_path = '') {
        if ($args && is_array($args)) {
            extract($args);
        }
        $located = $this->locate_template($template_name, $template_path, $default_path);
        include ($located);
    }

    /**
     * Locate template file
     * @param string $template_name
     * @param string $template_path
     * @param string $default_path
     * @return string
     */
    public function locate_template($template_name, $template_path = '', $default_path = '') {
        $default_path = apply_filters('woo_wallet_withdrawal_template_path', $default_path);
        if (!$template_path) {
            $template_path = 'woo-wallet-withdrawal';
        }
        if (!$default_path) {
            $default_path = WOO_WALLET_WITHDRAWAL_ABSPATH . 'templates/';
        }
        // Look within passed path within the theme - this is priority
        $template = locate_template(array(trailingslashit($template_path) . $template_name, $template_name));
        // Add support of third perty plugin
        $template = apply_filters('woo_wallet_locate_template', $template, $template_name, $template_path, $default_path);
        // Get default template
        if (!$template) {
            $template = $default_path . $template_name;
        }
        return $template;
    }
    
    /**
     * Plugin url
     * @return string path
     */
    public function plugin_url() {
        return untrailingslashit(plugins_url('/', WOO_WALLET_WITHDRAWAL_PLUGIN_FILE));
    }
    
    public function deactivation_hook(){
        $args = array(
            'email' => woo_wallet()->settings_api->get_option('license_email', Woo_Wallet_Withdrawal_License::$license_setting_option),
            'licence_key' => woo_wallet()->settings_api->get_option('licence_key', Woo_Wallet_Withdrawal_License::$license_setting_option),
            'activation_status' => 'on' 
        );
        Woo_Wallet_Withdrawal_License::manage_api_license($args);
    }

    /**
     * Display admin notice
     */
    public function admin_notices() {
        echo '<div class="error"><p>';
        _e('WooCommerce Wallet Withdrawal plugin requires <a href="http://wordpress.org/extend/plugins/woo-wallet/">WooCommerce Wallet</a> plugins to be active!', 'woo-wallet-withdrawal');
        echo '</p></div>';
    }

}
