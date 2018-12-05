<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
/**
 * Direct Bank transfer gateway for withdrawal 
 * from WooCommerce Wallet 
 *
 * @author subrata
 */
class WOO_Wallet_Gateway_Paypal extends WOO_Wallet_Payment_Gateway {

    /**
     * Constructor for the gateway.
     */
    public function __construct() {
        $this->id = 'paypal';
        $this->method_title = __('PayPal', 'woo-wallet-withdrawal');
    }
    
    public function process_payment($withdrawal) {
        return parent::process_payment($withdrawal);
    }

}
