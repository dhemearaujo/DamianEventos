<?php

/*
 * Plugin Name: WooCommerce Wallet Withdrawal
 * Plugin URI: http://woowallet.in/
 * Description: Withdraw wallet credits as cash.
 * Author: Subrata Mal
 * Author URI: https://profiles.wordpress.org/subratamal
 * Version: 1.0.1
 * Requires at least: 4.4
 * Tested up to: 4.9.4
 * WC requires at least: 3.0
 * WC tested up to: 3.3
 * 
 * Text Domain: woo-wallet-withdrawal
 * Domain Path: /languages/
 */

if (!defined('ABSPATH')) {
    exit;
}
// Define WOO_WALLET_WITHDRAWAL_PLUGIN_FILE.
if (!defined('WOO_WALLET_WITHDRAWAL_PLUGIN_FILE')) {
    define('WOO_WALLET_WITHDRAWAL_PLUGIN_FILE', __FILE__);
}

// include dependencies file
if(!class_exists('Woo_Wallet_Withdrawal_Dependencies')){
    include_once dirname(__FILE__) . '/includes/class-woo-wallet-withdrawal-dependencies.php';
}

// Include the main class.
if (!class_exists('WOO_WALLET_WITHDRAWAL')) {
    include_once dirname(__FILE__) . '/includes/class-woo-wallet-withdrawal.php';
}

function woo_wallet_withdrawal(){
    return WOO_WALLET_WITHDRAWAL::instance();
}
if (WOO_Wallet_Withdrawal_Dependencies::is_woo_wallet_active()) {
    register_deactivation_hook(__FILE__, array(woo_wallet_withdrawal(), 'deactivation_hook'));
}

$GLOBALS['woo_wallet_withdrawal'] = woo_wallet_withdrawal();