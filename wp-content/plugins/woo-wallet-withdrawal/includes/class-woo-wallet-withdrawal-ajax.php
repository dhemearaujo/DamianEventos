<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('WooWalletWithdrawalAjax')) {

    class WooWalletWithdrawalAjax {

        public function __construct() {
            add_action('wp_ajax_woo_wallet_withdrawal_post_action', array($this, 'woo_wallet_withdrawal_post_action_callback'));
            add_action('wp_ajax_validate_woo_wallet_withdrawal', array($this, 'validate_woo_wallet_withdrawal'));
            add_action('wp_ajax_get_woo_wallet_withdrawal_details', array($this, 'get_woo_wallet_withdrawal_details'));
        }

        public function woo_wallet_withdrawal_post_action_callback() {
            check_ajax_referer('woo-wallet-withdrawal-post-type-action', 'security');
            $post_id = $_POST['post_id'];
            $row_action = $_POST['row_action'];
            $response = array(
                'status' => false,
                'redirect_url' => admin_url('edit.php?post_type=wallet_withdrawal')
            );
            switch ($row_action) {
                case 'approve':
                    $withdrawal_response = WOO_Wallet_Withdrawal_Post_Type::approve_withdrawal($post_id);
                    $response = array_merge($response, $withdrawal_response);
                    break;
                case 'reject':
                    $withdrawal_response = WOO_Wallet_Withdrawal_Post_Type::reject_withdrawal($post_id);
                    $response = array_merge($response, $withdrawal_response);
                    break;
                case 'pending':
                    $withdrawal_response = WOO_Wallet_Withdrawal_Post_Type::pending_withdrawal($post_id);
                    $response = array_merge($response, $withdrawal_response);
                    break;
                case 'delete':
                    $withdrawal_response = WOO_Wallet_Withdrawal_Post_Type::delete_withdrawal($post_id);
                    $response = array_merge($response, $withdrawal_response);
                    break;
            }
            wp_send_json($response);
        }
        
        public function validate_woo_wallet_withdrawal(){
            check_ajax_referer('validate-woo-wallet-withdrawal', 'security');
            $gateway = $_POST['gateway'];
            $user_id = get_current_user_id();
            $response = array(
                'notices' => array(),
                'is_valid' => true
            );
            if('bacs' === $gateway){
                if(!get_user_meta($user_id, '_bacs_account_name', true) || !get_user_meta($user_id, '_bacs_account_number', true)){
                    $response['notices'][] = sprintf(__('<a href="%s">Clique aqui</a> para configurar os detalhes da conta bancária.', 'woo-wallet-withdrawal'), wc_get_account_endpoint_url( 'edit-account' ) );
                    $response['is_valid'] = false;
                }
            } else if('paypal' === $gateway){
                if(!get_user_meta($user_id, '_woo_wallet_withdrawal_paypal_email', true)){
                    $response['notices'][] = sprintf(__('<a href="%s">Clique aqui</a> para configurar o e-mail do PayPal.', 'woo-wallet-withdrawal'), wc_get_account_endpoint_url( 'edit-account' ) );
                    $response['is_valid'] = false;
                }
            }
            
            wp_send_json(apply_filters('validate_woo_wallet_withdrawal_request', $response));
        }
        
        public function get_woo_wallet_withdrawal_details(){
            check_ajax_referer('woo-wallet-withdrawal-details', 'security');
            $withdraw_id = $_REQUEST['withdraw_id'];
            ob_start();
            woo_wallet_withdrawal()->get_template('admin/withdrawal-details.php', array('withdraw_id' => $withdraw_id));
            echo ob_get_clean();
            wp_die();
        }
    }

}
new WooWalletWithdrawalAjax();
