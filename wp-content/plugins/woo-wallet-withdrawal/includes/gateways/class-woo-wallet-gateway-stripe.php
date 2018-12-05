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
class WOO_Wallet_Gateway_Stripe extends WOO_Wallet_Payment_Gateway {

    /**
     * Constructor for the gateway.
     */
    public function __construct() {
        $this->id = 'stripe';
        $this->method_title = __('Stripe', 'woo-wallet-withdrawal');
    }
    
    public function process_payment($withdrawal) {
        return parent::process_payment($withdrawal);
    }

}
