<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
$withdrawal = get_post($withdraw_id);
$amount = get_post_meta($withdraw_id, '_wallet_withdrawal_amount', true);
$currency = get_post_meta($withdraw_id, '_wallet_withdrawal_currency', true);
$charge = get_post_meta($withdraw_id, '_wallet_withdrawal_transaction_charge', true);
$gateways = woo_wallet_withdrawal()->gateways->payment_gateways;
$request_method = get_post_meta($withdrawal->ID, '_wallet_withdrawal_method', true);
?>
<table id="woo_wallet_withdraw_details">
    <tr>
        <td><?php _e('Montante', 'woo-wallet-withdrawal'); ?></td>
        <td><?php echo wc_price($amount, array('currency' => $currency)); ?></td>
    </tr>
    <tr>
        <td><?php _e('Taxa de Transferência', 'woo-wallet-withdrawal'); ?></td>
        <td><?php echo wc_price($charge, array('currency' => $currency)); ?></td>
    </tr>
    <tr>
        <td><?php _e('Método de Pagamento', 'woo-wallet-withdrawal'); ?></td>
        <td><?php echo isset($gateways[$request_method]) ? $gateways[$request_method]->get_method_title() : ''; ?></td>
    </tr>
    <?php if('paypal' === $request_method) : ?>
    <tr>
        <td><?php _e('E-mail do PayPal', 'woo-wallet-withdrawal'); ?></td>
        <td><?php echo get_user_meta($withdrawal->post_author, '_woo_wallet_withdrawal_paypal_email', true); ?></td>
    </tr>
    <?php endif; ?>
</table>
<?php if('bacs' === $request_method) : ?>
<h4><?php _e('Detalhes da Conta Bancária', 'woo-wallet-withdrawal'); ?></h4>
<table id="woo_wallet_withdraw_details">
    <tr>
        <td><?php _e('Nome da Conta Bancária', 'woo-wallet-withdrawal') ?></td>
        <td><?php echo get_user_meta($withdrawal->post_author, '_bacs_account_name', true); ?></td>
    </tr>
    <tr>
        <td><?php _e('Número da Conta Bancária', 'woo-wallet-withdrawal'); ?></td>
        <td><?php echo get_user_meta($withdrawal->post_author, '_bacs_account_number', true); ?></td>
    </tr>
    <tr>
        <td><?php _e('Nome do Banco', 'woo-wallet-withdrawal'); ?></td>
        <td><?php echo get_user_meta($withdrawal->post_author, '_bacs_bank_name', true); ?></td>
    </tr>
    <tr>
        <td><?php _e('Endereço do Banco', 'woo-wallet-withdrawal'); ?></td>
        <td><?php echo get_user_meta($withdrawal->post_author, '_bacs_bank_address', true); ?></td>
    </tr>
    <tr>
        <td><?php _e('Número de Roteamento', 'woo-wallet-withdrawal') ?></td>
        <td><?php echo get_user_meta($withdrawal->post_author, '_bacs_bank_routing_number', true); ?></td>
    </tr>
    <tr>
        <td><?php _e('IBAN', 'woo-wallet-withdrawal'); ?></td>
        <td><?php echo get_user_meta($withdrawal->post_author, '_bacs_bank_iban', true); ?></td>
    </tr>
    <tr>
        <td><?php _e('Código Swift', 'woo-wallet-withdrawal'); ?></td>
        <td><?php echo get_user_meta($withdrawal->post_author, '_bacs_bank_swift_code', true); ?></td>
    </tr>
</table>
<?php endif; ?>
<style>
    #woo_wallet_withdraw_details {
        border-collapse: collapse;
        width: 100%;
    }

    #woo_wallet_withdraw_details td, #woo_wallet_withdraw_details th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    #woo_wallet_withdraw_details tr:nth-child(even){background-color: #f2f2f2;}

    #woo_wallet_withdraw_details tr:hover {background-color: #ddd;}

    #woo_wallet_withdraw_details th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #4CAF50;
        color: white;
    }
</style>