<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (get_option('woo_wallet_withdrawal_license_activated') != 'Activated') {
    add_action('admin_notices', 'Woo_Wallet_Withdrawal_License::license_inactive_notice');
}

class Woo_Wallet_Withdrawal_License {
    
    public static $upgrade_url = WOO_WALLET_WITHDRAWAL_PLUGIN_SERVER_URL;
    public static $api_manager_license_version_name = 'woo_wallet_withdrawal_license_version';
    private static $license_software_product_id = WOO_WALLET_WITHDRAWAL_PLUGIN_TOKEN;
    public static $license_data_key = '_wallet_settings_extensions_withdrawal_license';
    public static $license_api_key = 'licence_key';
    public static $license_activation_email = 'license_email';
    public static $license_product_id_key = 'woo_wallet_withdrawal_license_product_id';
    public static $license_instance_key = 'woo_wallet_withdrawal_license_instance';
    public static $license_activated_key = 'woo_wallet_withdrawal_license_activated';
    public static $license_plugin_name = 'woo-wallet-withdrawal/woo-wallet-withdrawal.php';
    public static $license_renew_license_url = WOO_WALLET_WITHDRAWAL_PLUGIN_SERVER_URL . 'my-account';
    public static $license_software_version = WOO_WALLET_WITHDRAWAL_VERSION;
    public static $license_plugin_or_theme = 'plugin';
    public static $license_plugin_or_theme_mode = 'paid';
    public static $license_update_check = 'woo_wallet_withdrawal_update_check';
    public static $license_setting_option = '_wallet_settings_extensions_withdrawal_license';


    public $license_options;
    public $license_product_id;
    public $license_instance_id;
    public $license_domain;
    public $license_update_version;

    /**
     * @var The single instance of the class
     */
    protected static $_instance = null;

    public static function instance() {

        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function __construct() {

        /**
         * Set all software update data here
         */
        $this->license_options = get_option(self::$license_data_key);
        $this->license_product_id = get_option(self::$license_product_id_key);
        $this->license_instance_id = get_option(self::$license_instance_key);
        $this->license_domain = site_url(); 

        if (!$this->license_product_id) {
            $this->activation();
        }

        // Performs activations and deactivations of API License Keys
        self::load_class('class-woo-wallet-withdrawal-key-api.php');
        $this->api_manager_license_key = new Woo_Wallet_Withdrawal_Key_Api();

        // Checks for software updatess
        self::load_class('class-woo-wallet-withdrawal-update.php');

        /**
         * Check for software updates
         */
        if (!empty($this->license_options) && $this->license_options !== false) {

            new Woo_Wallet_Withdrawal_API_Manager_Update_API_Check(
                    self::$upgrade_url, self::$license_plugin_name, $this->license_product_id, $this->license_options[self::$license_api_key], $this->license_options[self::$license_activation_email], self::$license_renew_license_url, $this->license_instance_id, $this->license_domain, self::$license_software_version, self::$license_plugin_or_theme, 'woo-wallet-withdrawal'
            );
        }
    }

    /**
     * Generate the default data arrays
     */
    public function activation() {

        $global_options = array(
            self::$license_api_key => '',
            self::$license_activation_email => '',
        );

        update_option(self::$license_data_key, $global_options);
        // Generate a unique installation $instance id
        self::load_class('class-woo-wallet-withdrawal-api-manager-passwords.php');
        $Woo_Wallet_Withdrawal_API_Manager_Password = new Woo_Wallet_Withdrawal_API_Manager_Password();
        $instance = $Woo_Wallet_Withdrawal_API_Manager_Password->generate_password(12, false);

        $single_options = array(
            self::$license_product_id_key => self::$license_software_product_id,
            self::$license_instance_key => $instance,
            self::$license_activated_key => 'Deactivated',
        );

        foreach ($single_options as $key => $value) {
            update_option($key, $value);
        }

        $curr_ver = get_option(self::$api_manager_license_version_name);

        // checks if the current plugin version is lower than the version being installed
        if (version_compare(self::$license_software_version, $curr_ver, '>')) {
            // update the version
            update_option(self::$api_manager_license_version_name, WOO_WALLET_WITHDRAWAL_VERSION);
        }
    }

    /**
     * Deletes all data if plugin deactivated
     * @return void
     */
    public function uninstall() {
        global $blog_id;
        $this->license_key_deactivation();
        // Remove options
        if (is_multisite()) {
            switch_to_blog($blog_id);
            foreach (array(self::$license_data_key, self::$license_product_id_key, self::$license_instance_key, self::$license_activated_key) as $option) {
                delete_option($option);
            }

            restore_current_blog();
        } else {
            foreach (array(self::$license_data_key, self::$license_product_id_key, self::$license_instance_key, self::$license_activated_key) as $option) {
                delete_option($option);
            }
        }
    }

    /**
     * Deactivates the license on the API server
     * @return void
     */
    public function license_key_deactivation() {
        $activation_status = get_option(self::$license_activated_key);
        $api_email = $this->license_options[self::$license_activation_email];
        $api_key = $this->license_options[self::$license_api_key];
        $args = array(
            'email' => $api_email,
            'licence_key' => $api_key,
        );

        if ($activation_status == 'Activated' && $api_key != '' && $api_email != '') {
            $this->api_manager_license_key->deactivate($args); // reset license key activation
        }
    }

    /**
     * Displays an inactive notice when the software is inactive.
     */
    public static function license_inactive_notice() {
        ?>
        <?php if (!current_user_can('manage_options')) return; ?>
        <?php if (isset($_GET['page']) && 'woo-wallet-extensions' == $_GET['page']) return; ?>
        <div id="message" class="error">
            <p><?php printf(__('%sClique aqui%s para ativar a chave de licença da WooWallet e receber atualizações e suporte.', 'woo-wallet-withdrawal'), '<a href="' . esc_url(admin_url('admin.php?page=woo-wallet-extensions')) . '">', '</a>'); ?></p>
        </div>
        <?php
    }
    
    /**
     * Load API classes
     * @param string $class_name
     */
    public static function load_class($class_name = '') {
        if ('' != $class_name) {
            require_once (WOO_WALLET_WITHDRAWAL_ABSPATH . '/includes/license/' . esc_attr($class_name));
        }
    }

    /**
     * Manage API license 
     * @param array $args
     */
    public static function manage_api_license($args = array()) {
        // Performs activations and deactivations of API License Keys
        self::load_class('class-woo-wallet-withdrawal-key-api.php');
        $api_manager_license_key = new Woo_Wallet_Withdrawal_Key_Api();
        $activation_status = get_option(self::$license_activated_key);
        $api_key = get_option(self::$license_api_key);
        $current_api_key = $args[self::$license_api_key];
        if ('off' == $args['activation_status']) {

            // Plugin Activation
            if ($activation_status == 'Deactivated' || $activation_status == '' || $current_api_key != $api_key) {
                
                $activate_results = json_decode($api_manager_license_key->activate($args), true);

                if ($activate_results['activated'] == true) {
                    add_settings_error("", esc_attr("settings_admin_error"), __('Plugin activated. ', 'woo-wallet-withdrawal') . "{$activate_results['message']}.", 'updated');
                    update_option(self::$license_activated_key, 'Activated');
                }

                if ($activate_results == false) {
                    add_settings_error("", esc_attr("settings_admin_error"), __('Falha na conexão com o servidor da API de chave de licença. Tente mais tarde.', 'woo-wallet-withdrawal'), 'error');
                    update_option(self::$license_activated_key, 'Deactivated');
                }

                if (isset($activate_results['code'])) {

                    switch ($activate_results['code']) {
                        case '100':
                            add_settings_error("", esc_attr("settings_admin_error"), "{$activate_results['error']}. {$activate_results['additional info']}", 'error');
                            update_option(self::$license_activated_key, 'Deactivated');
                            break;
                        case '101':
                            add_settings_error("", esc_attr("settings_admin_error"), "{$activate_results['error']}. {$activate_results['additional info']}", 'error');
                            update_option(self::$license_activated_key, 'Deactivated');
                            break;
                        case '102':
                            add_settings_error("", esc_attr("settings_admin_error"), "{$activate_results['error']}. {$activate_results['additional info']}", 'error');
                            update_option(self::$license_activated_key, 'Deactivated');
                            break;
                        case '103':
                            add_settings_error("", esc_attr("settings_admin_error"), "{$activate_results['error']}. {$activate_results['additional info']}", 'error');
                            update_option(self::$license_activated_key, 'Deactivated');
                            break;
                        case '104':
                            add_settings_error("", esc_attr("settings_admin_error"), "{$activate_results['error']}. {$activate_results['additional info']}", 'error');
                            update_option(self::$license_activated_key, 'Deactivated');
                            break;
                        case '105':
                            add_settings_error("", esc_attr("settings_admin_error"), "{$activate_results['error']}. {$activate_results['additional info']}", 'error');
                            update_option(self::$license_activated_key, 'Deactivated');
                            break;
                        case '106':
                            add_settings_error("", esc_attr("settings_admin_error"), "{$activate_results['error']}. {$activate_results['additional info']}", 'error');
                            update_option(self::$license_activated_key, 'Deactivated');
                            break;
                    }
                }
            }
        } else {
            if ($activation_status == 'Activated') {
                if ($api_manager_license_key->deactivate($args)) {
                    update_option(self::$license_activated_key, 'Deactivated');
                    delete_option(self::$license_setting_option);
                    add_settings_error("", esc_attr("settings_admin_error"), __('Licença de plugin desativada.', 'woo-wallet-withdrawal'), 'updated');
                }
            }
        }
    }

}

new Woo_Wallet_Withdrawal_License();
