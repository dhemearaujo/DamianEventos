<?php

/**
 * WooCommerce API Manager API Key Class
 *
 * @package Update API Manager/Key Handler
 * @since 1.3
 *
 */
if (!defined('ABSPATH')) {
    exit;
}

class Woo_Wallet_Withdrawal_Key_Api {

    // API Key URL
    public function create_software_api_url($args) {

        $api_url = add_query_arg('wc-api', 'am-software-api', Woo_Wallet_Withdrawal_License::$upgrade_url);

        return $api_url . '&' . http_build_query($args);
    }

    public function activate($args) {

        $defaults = array(
            'request' => 'activation',
            'product_id' => get_option(Woo_Wallet_Withdrawal_License::$license_product_id_key),
            'instance' => get_option(Woo_Wallet_Withdrawal_License::$license_instance_key),
            'platform' => site_url(),
            'software_version' => Woo_Wallet_Withdrawal_License::$license_software_version
        );

        $args = wp_parse_args($defaults, $args);

        $target_url = self::create_software_api_url($args);

        $request = wp_remote_get($target_url);

        if (is_wp_error($request) || wp_remote_retrieve_response_code($request) != 200) {
            // Request failed
            return false;
        }

        $response = wp_remote_retrieve_body($request);

        return $response;
    }

    public function deactivate($args) {

        $defaults = array(
            'request' => 'deactivation',
            'product_id' => get_option(Woo_Wallet_Withdrawal_License::$license_product_id_key),
            'instance' => get_option(Woo_Wallet_Withdrawal_License::$license_instance_key),
            'platform' => site_url()
        );

        $args = wp_parse_args($defaults, $args);

        $target_url = $this->create_software_api_url($args);

        $request = wp_remote_get($target_url);

        if (is_wp_error($request) || wp_remote_retrieve_response_code($request) != 200) {
            // Request failed
            return false;
        }

        $response = wp_remote_retrieve_body($request);

        return $response;
    }

    /**
     * Checks if the software is activated or deactivated
     * @param  array $args
     * @return array
     */
    public function status($args) {

        $defaults = array(
            'request' => 'status',
            'product_id' => get_option(Woo_Wallet_Withdrawal_License::$license_product_id_key),
            'instance' => get_option(Woo_Wallet_Withdrawal_License::$license_instance_key),
            'platform' => site_url()
        );

        $args = wp_parse_args($defaults, $args);

        $target_url = self::create_software_api_url($args);

        $request = wp_remote_get($target_url);

        if (is_wp_error($request) || wp_remote_retrieve_response_code($request) != 200) {
            // Request failed
            return false;
        }

        $response = wp_remote_retrieve_body($request);

        return $response;
    }

}
