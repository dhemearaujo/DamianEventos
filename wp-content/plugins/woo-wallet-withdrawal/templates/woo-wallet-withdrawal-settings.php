<?php
/**
 * The Template for displaying wallet recharge form
 *
 * This template can be overridden by copying it to yourtheme/woo-wallet-withdrawal/woo-wallet-withdrawal-settings.php.
 *
 * HOWEVER, on occasion we will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @author 	Subrata Mal
 * @version     1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
$user_id = get_current_user_id();
if (empty(woo_wallet_withdrawal()->gateways->get_available_gateways())) {
    return;
}
?>
<fieldset>
    <legend><?php esc_html_e('Retirada da Carteira', 'woo-wallet-withdrawal'); ?></legend>
    <?php if ('on' === woo_wallet()->settings_api->get_option('bacs', '_wallet_settings_withdrawal', 'on')) : ?>
        <fieldset>
            <legend><?php esc_html_e('Transferência de Banco', 'woo-wallet-withdrawal'); ?></legend>
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="bacs_account_name" id="bacs_account_name" value="<?php echo get_user_meta($user_id, '_bacs_account_name', true); ?>" placeholder="<?php _e('Nome de Conta do Banco', 'woo-wallet-withdrawal') ?>" />
            </p>
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="bacs_account_number" id="bacs_account_number" value="<?php echo get_user_meta($user_id, '_bacs_account_number', true); ?>" placeholder="<?php _e('Número da Conta do Banco', 'woo-wallet-withdrawal') ?>" />
            </p>
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="bacs_bank_name" id="bacs_bank_name" value="<?php echo get_user_meta($user_id, '_bacs_bank_name', true); ?>" placeholder="<?php _e('Nome do Banco', 'woo-wallet-withdrawal'); ?>" />
            </p>
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <textarea class="woocommerce-Input woocommerce-Input--textarea input-text" name="bacs_bank_address" id="bacs_bank_address" placeholder="<?php _e('Endereço do Banco', 'woo-wallet-withdrawal'); ?>" ><?php echo get_user_meta($user_id, '_bacs_bank_address', true); ?></textarea>
            </p>
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="bacs_bank_routing_number" id="bacs_bank_routing_number" value="<?php echo get_user_meta($user_id, '_bacs_bank_routing_number', true); ?>" placeholder="<?php _e('Número de Roteamento', 'woo-wallet-withdrawal'); ?>" />
            </p>
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="bacs_bank_iban" id="bacs_bank_iban" value="<?php echo get_user_meta($user_id, '_bacs_bank_iban', true); ?>" placeholder="<?php _e('IBAN', 'woo-wallet-withdrawal'); ?>" />
            </p>
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="bacs_bank_swift_code" id="bacs_bank_swift_code" value="<?php echo get_user_meta($user_id, '_bacs_bank_swift_code', true); ?>" placeholder="<?php _e('Código Swift', 'woo-wallet-withdrawal'); ?>" />
            </p>
        </fieldset>
        <div class="clear"></div>
    <?php endif; ?>
    <?php if ('on' === woo_wallet()->settings_api->get_option('paypal', '_wallet_settings_withdrawal', 'on')) : ?>
        <fieldset>
            <legend><?php esc_html_e('Transferência via PayPal', 'woo-wallet-withdrawal'); ?></legend>
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <input type="email" class="woocommerce-Input woocommerce-Input--email input-email" name="woo_wallet_withdrawal_paypal_email" id="woo_wallet_withdrawal_paypal_email" value="<?php echo get_user_meta($user_id, '_woo_wallet_withdrawal_paypal_email', true); ?>" placeholder="<?php _e('E-mail do PayPal', 'woo-wallet-withdrawal') ?>" />
            </p>
        </fieldset>
        <div class="clear"></div>
    <?php endif; ?>
    <?php do_action('woo_wallet_withdrawal_payment_gateway_settings'); ?>
</fieldset>
<div class="clear"></div>