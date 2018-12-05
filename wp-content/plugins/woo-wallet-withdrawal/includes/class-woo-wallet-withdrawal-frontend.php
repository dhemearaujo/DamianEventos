<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Plugin frontend class
 *
 * @author subrata
 */
class WOO_Wallet_Withdrawal_Frontend {

    /**
     * Class Constructor
     */
    public function __construct() {
        add_action('wp_enqueue_scripts', array(&$this, 'enqueue_frontend_scripts'));
        add_filter('woocommerce_get_query_vars', array($this, 'add_woocommerce_query_vars'));
        add_filter('woocommerce_endpoint_woo-wallet-withdrawal_title', array($this, 'woocommerce_endpoint_title'));
        add_action('woocommerce_account_woo-wallet-withdrawal_endpoint', array($this, 'woo_wallet_withdrawal_endpoint_content'));
        add_action('wp_loaded', array($this, 'init_wp_loaded'));
        add_action('woo_wallet_menu_items', array($this, 'woo_wallet_menu_items'));
        add_action('woocommerce_edit_account_form', array($this, 'woocommerce_edit_account_form_callback'));
        add_action('woocommerce_save_account_details', array($this, 'woocommerce_save_account_details'));
        add_action('woo_wallet_shortcode_action', array($this, 'woo_wallet_shortcode_action'));
    }

    public function enqueue_frontend_scripts() {
        wp_register_style('woo-wallet-jquery-ui-css', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
        wp_register_script('woo-wallet-withdrawal', woo_wallet_withdrawal()->plugin_url() . '/assets/js/wallet-withdrawal.js', array('jquery'), WOO_WALLET_WITHDRAWAL_VERSION);
        wp_localize_script('woo-wallet-withdrawal', 'woo_wallet_withdrawal_param', array('ajax_url' => admin_url('admin-ajax.php'), 'validate_request_nonce' => wp_create_nonce('validate-woo-wallet-withdrawal')));
    }

    /**
     * Add WooCommerce query vars.
     * @param type $query_vars
     * @return type
     */
    public function add_woocommerce_query_vars($query_vars) {
        $query_vars['woo-wallet-withdrawal'] = get_option('woocommerce_woo_wallet_withdrawal_endpoint', 'woo-wallet-withdrawal');
        return $query_vars;
    }

    /**
     * Change WooCommerce endpoint title for wallet pages.
     */
    public function woocommerce_endpoint_title() {
        return apply_filters('woo_wallet_withdrawal_account_menu_title', __('Retirada da Carteira Virtual', 'woo-wallet-withdrawal'));
    }

    public function woo_wallet_menu_items() {
        ?>
        <li class="card"><a href="<?php echo is_account_page() ? esc_url(wc_get_account_endpoint_url(get_option('woocommerce_woo_wallet_withdrawal_endpoint', 'woo-wallet-withdrawal'))) : add_query_arg('wallet_action', 'wallet_withdrawal', get_permalink()); ?>"><span class="dashicons dashicons-list-view"></span><p><?php echo apply_filters('woo_wallet_account_withdrawal_menu_title', __('Retirada', 'woo-wallet-withdrawal')); ?></p></a></li>
        <?php
    }

    public function woo_wallet_withdrawal_endpoint_content() {
        wp_enqueue_style('woo-wallet-jquery-ui-css');
        wp_enqueue_script('jquery-ui-tabs');
        wp_enqueue_script('woo-wallet-withdrawal');
        woo_wallet_withdrawal()->get_template('woo-wallet-withdrawal.php');
    }

    public function woo_wallet_shortcode_action($action) {
        if ('wallet_withdrawal' === $action) {
            $this->woo_wallet_withdrawal_endpoint_content();
        }
    }

    public function init_wp_loaded() {
        if (isset($_POST['woo_wallet_withdraw_submit']) && isset($_POST['woo_wallet_withdrawal'])) {
            $response = $this->validate_withdrawal_request();
            if (!$response['is_valid']) {
                wc_add_notice($response['message'], 'error');
            } else {
                $withdrawal_id = WOO_Wallet_Withdrawal_Post_Type::create_post();
                if (!is_wp_error($withdrawal_id)) {
                    $this->process_withdrawal($withdrawal_id);
                    wc_add_notice($response['message']);
                } else {
                    wc_add_notice(__('Algo deu errado. Por favor, tente mais tarde.', 'woo-wallet-withdrawal'), 'error');
                }
            }
        }
    }

    private function validate_withdrawal_request() {
        $response = array('is_valid' => true, 'message' => '');
        if (wp_verify_nonce($_POST['woo_wallet_withdrawal'], 'woo_wallet_withdrawal')) {
            $wallet_withdrawal_amount = floatval($_POST['wallet_withdrawal_amount']);
            $wallet_withdrawal_method = $_POST['wallet_withdrawal_method'];
            $transaction_charge = WOO_Wallet_Withdrawal_Payment_gateways::get_gateway_charge($wallet_withdrawal_amount, $wallet_withdrawal_method);
            if ($wallet_withdrawal_amount + $transaction_charge > woo_wallet()->wallet->get_wallet_balance(get_current_user_id(), 'edit')) {
                $response = array(
                    'is_valid' => false,
                    'message' => __('Você não tem saldo suficiente para essa solicitação', 'woo-wallet')
                );
            } else if (empty($wallet_withdrawal_method)) {
                $response = array(
                    'is_valid' => false,
                    'message' => __('Gateway de pagamento inválido', 'woo-wallet')
                );
            } else {
                $response = array(
                    'is_valid' => true,
                    'message' => __('Solicitação enviada com sucesso', 'woo-wallet')
                );
            }
        } else {
            $response = array(
                'is_valid' => false,
                'message' => __('Cheatin&#8217; huh?', 'woo-wallet')
            );
        }
        return $response;
    }

    private function process_withdrawal($withdrawal_id) {
        $wallet_withdrawal_amount = apply_filters('woo_wallet_withdrawal_requested_amount', floatval($_POST['wallet_withdrawal_amount']));
        $wallet_withdrawal_method = $_POST['wallet_withdrawal_method'];
        $transaction_charge = WOO_Wallet_Withdrawal_Payment_gateways::get_gateway_charge($wallet_withdrawal_amount, $wallet_withdrawal_method);
        update_post_meta($withdrawal_id, '_wallet_withdrawal_amount', $wallet_withdrawal_amount);
        update_post_meta($withdrawal_id, '_wallet_withdrawal_currency', get_woocommerce_currency());
        update_post_meta($withdrawal_id, '_wallet_withdrawal_transaction_charge', $transaction_charge);
        update_post_meta($withdrawal_id, '_wallet_withdrawal_method', $wallet_withdrawal_method);
        $withdrawal_transaction_id = woo_wallet()->wallet->debit(get_current_user_id(), ($wallet_withdrawal_amount + $transaction_charge), __('Wallet withdrawal request #', 'woo-wallet-withdrawal') . $withdrawal_id);
        update_wallet_transaction_meta($withdrawal_transaction_id, '_withdrawal_request_id', $withdrawal_id);
        update_post_meta($withdrawal_id, '_wallet_withdrawal_transaction_id', $withdrawal_transaction_id);
        do_action('woo_wallet_withdrawal_update_meta_data', $withdrawal_id);
    }

    public function woocommerce_edit_account_form_callback() {
        woo_wallet_withdrawal()->get_template('woo-wallet-withdrawal-settings.php');
    }

    public function woocommerce_save_account_details($user_id) {
        $bacs_account_name = !empty($_POST['bacs_account_name']) ? wc_clean($_POST['bacs_account_name']) : '';
        $bacs_account_number = !empty($_POST['bacs_account_number']) ? wc_clean($_POST['bacs_account_number']) : '';
        $bacs_bank_name = !empty($_POST['bacs_bank_name']) ? wc_clean($_POST['bacs_bank_name']) : '';
        $bacs_bank_address = !empty($_POST['bacs_bank_address']) ? wc_clean($_POST['bacs_bank_address']) : '';
        $bacs_bank_routing_number = !empty($_POST['bacs_bank_routing_number']) ? wc_clean($_POST['bacs_bank_routing_number']) : '';
        $bacs_bank_iban = !empty($_POST['bacs_bank_iban']) ? wc_clean($_POST['bacs_bank_iban']) : '';
        $bacs_bank_swift_code = !empty($_POST['bacs_bank_swift_code']) ? wc_clean($_POST['bacs_bank_swift_code']) : '';
        update_user_meta($user_id, '_bacs_account_name', $bacs_account_name);
        update_user_meta($user_id, '_bacs_account_number', $bacs_account_number);
        update_user_meta($user_id, '_bacs_bank_name', $bacs_bank_name);
        update_user_meta($user_id, '_bacs_bank_address', $bacs_bank_address);
        update_user_meta($user_id, '_bacs_bank_routing_number', $bacs_bank_routing_number);
        update_user_meta($user_id, '_bacs_bank_iban', $bacs_bank_iban);
        update_user_meta($user_id, '_bacs_bank_swift_code', $bacs_bank_swift_code);
        $woo_wallet_withdrawal_paypal_email = !empty($_POST['woo_wallet_withdrawal_paypal_email']) ? wc_clean($_POST['woo_wallet_withdrawal_paypal_email']) : '';
        update_user_meta($user_id, '_woo_wallet_withdrawal_paypal_email', $woo_wallet_withdrawal_paypal_email);
    }

}

new WOO_Wallet_Withdrawal_Frontend();
