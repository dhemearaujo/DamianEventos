<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Plugin admin class
 *
 * @author subrata
 */
class WOO_Wallet_Withdrawal_Admin {

    /**
     * Class Constructor
     */
    public function __construct() {
        add_filter('woo_wallet_settings_sections', array($this, 'woo_wallet_settings_sections'));
        add_filter('woo_wallet_settings_filds', array($this, 'woo_wallet_settings_filds'));
        add_filter('woo_wallet_extensions_settings_sections', array($this, 'woo_wallet_extensions_settings_sections'));
        add_filter('woo_wallet_extensions_settings_filds', array($this, 'woo_wallet_extensions_settings_filds'));
        add_action('admin_menu', array($this, 'admin_menu'), 55);

        add_filter('woo_wallet_endpoint_settings_fields', array($this, 'woo_wallet_endpoint_settings_fields'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));

        add_action('update_option__wallet_settings_extensions_withdrawal_license', array($this, 'extensions_withdrawal_license_check'), 10, 3);
    }

    public function woo_wallet_settings_sections($sections) {
        $sections[] = array(
            'id' => '_wallet_settings_withdrawal',
            'title' => __('Opções de Retirada', 'woo-wallet-withdrawal'),
            'icon' => 'dashicons-tickets-alt'
        );
        return $sections;
    }

    public function woo_wallet_extensions_settings_sections($sections) {
        $sections[] = array(
            'id' => '_wallet_settings_extensions_withdrawal_license',
            'title' => __('Licença WooWallet Withdrawal', 'woo-wallet-withdrawal'),
            'icon' => 'dashicons-tickets-alt'
        );
        return $sections;
    }

    public function woo_wallet_settings_filds($settings_fields) {
        $settings_fields['_wallet_settings_withdrawal'] = array_merge(
                $this->get_withdrawal_methods(), array(
            array(
                'name' => '_is_enable_gateway_charge',
                'label' => __('Taxa do Gateway de Pagamento', 'woo-wallet-withdrawal'),
                'desc' => __('Se marcado, você pode definir a cobrança do gateway de pagamento ao usuário para retirada da carteira.', 'woo-wallet-withdrawal'),
                'type' => 'checkbox'),
            array(
                'name' => '_withdrawal_gateway_charge_type',
                'label' => __('Tipo de Cobrança do Gateway', 'woo-wallet'),
                'desc' => __('Selecione porcentagem do tipo de cobrança do gateway ou fixa', 'woo-wallet'),
                'type' => 'select',
                'options' => array('percent' => __('Porcentagem', 'woo-wallet'), 'fixed' => __('Quantia fixa', 'woo-wallet')),
                'size' => 'regular-text'
            )
                ), $this->get_withdrawal_methods('charge')
        );
        return $settings_fields;
    }

    public function woo_wallet_extensions_settings_filds($settings_fields) {
        $settings_fields['_wallet_settings_extensions_withdrawal_license'] = array(
            array(
                'name' => 'licence_key',
                'label' => __('Chave de Licença da API', 'woo-wallet-withdrawal'),
                'desc' => __('Digite a Chave de Licença', 'woo-wallet-withdrawal'),
                'type' => 'text',
                'default' => ''
            ),
            array(
                'name' => 'license_email',
                'label' => __('E-mail da Licença da API', 'woo-wallet-withdrawal'),
                'desc' => __('Digite o e-mail da Licença', 'woo-wallet-withdrawal'),
                'type' => 'text',
                'default' => ''
            ),
            array(
                'name' => 'is_activate',
                'label' => __('Desativar a chave de licença da API', 'woo-wallet'),
                'desc' => __('Desativa uma chave de licença de API para que possa ser usada em outra plataforma.', 'woo-wallet'),
                'type' => 'checkbox',
            )
        );
        return $settings_fields;
    }

    /**
     * init admin menu
     */
    public function admin_menu() {
        add_submenu_page('woo-wallet', __('Retirada', 'woo-wallet'), __('Retirada', 'woo-wallet'), 'manage_woocommerce', 'edit.php?post_type=wallet_withdrawal');
    }

    public function get_withdrawal_methods($for = 'checkbox') {
        $withdrawal_methods = array();
        $gateways = woo_wallet_withdrawal()->gateways->payment_gateways;
        if ($gateways) {
            foreach ($gateways as $id => $gateway) {
                if ('checkbox' === $for) {
                    $withdrawal_methods[] = array(
                        'name' => $id,
                        'label' => (current($gateways)->get_method_id() == $gateway->get_method_id()) ? __('Métodos de Retirada', 'woo-wallet-withdrawal') : '',
                        'desc' => $gateway->get_method_title(),
                        'type' => 'checkbox',
                        'default' => 'on'
                    );
                } else if ('charge' === $for) {
                    $withdrawal_methods[] = array(
                        'name' => '_charge_' . $id,
                        'label' => __('', 'woo-wallet'),
                        'desc' => __('Insira o valor da cobrança do gateway para ', 'woo-wallet') . $gateway->get_method_title(),
                        'type' => 'number'
                    );
                } else {
                    $withdrawal_methods[] = $id;
                }
            }
        }
        return $withdrawal_methods;
    }

    public function woo_wallet_endpoint_settings_fields($settings_fields) {
        $settings_fields[] = array(
            'title' => __('Retirada da Carteira', 'woo-wallet-withdrawal'),
            'desc' => __('Endpoint for the "My account &rarr; Wallet withdrawal" page.', 'woo-wallet-withdrawal'),
            'id' => 'woocommerce_woo_wallet_withdrawal_endpoint',
            'type' => 'text',
            'default' => 'woo-wallet-withdrawal',
            'desc_tip' => true,
        );
        return $settings_fields;
    }

    public function admin_enqueue_scripts() {
        $screen = get_current_screen();
        $screen_id = $screen ? $screen->id : '';
        wp_register_script('woo_wallet_withdrawal_post_type', woo_wallet_withdrawal()->plugin_url() . '/assets/js/admin-post-type.js', array('jquery'), WOO_WALLET_WITHDRAWAL_VERSION);
        wp_register_script('woo_wallet_withdrawal_admin_settings', woo_wallet_withdrawal()->plugin_url() . '/assets/js/admin-settings.js', array('jquery'), WOO_WALLET_WITHDRAWAL_VERSION);
        $withdrawal_post_type_param = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'security_nonce' => wp_create_nonce('woo-wallet-withdrawal-post-type-action'),
            'confirmation_message' => __('Tem certeza de que deseja excluir esta solicitação??', 'woo-wallet-withdrawal')
        );
        $woo_wallet_withdrawal_admin_settings_param = array(
            'gateways' => $this->get_withdrawal_methods('id')
        );
        wp_localize_script('woo_wallet_withdrawal_post_type', 'withdrawal_post_type_param', $withdrawal_post_type_param);
        wp_localize_script('woo_wallet_withdrawal_admin_settings', 'woo_wallet_withdrawal_admin_settings_param', $woo_wallet_withdrawal_admin_settings_param);
        if (in_array($screen_id, array('edit-wallet_withdrawal'))) {
            wp_enqueue_script('woo_wallet_withdrawal_post_type');
        }
        if (in_array($screen_id, array('woowallet_page_woo-wallet-settings'))) {
            wp_enqueue_script('woo_wallet_withdrawal_admin_settings');
        }
        if(in_array($screen_id, array('edit-wallet_withdrawal'))){
            add_thickbox();
        }
    }

    public function extensions_withdrawal_license_check($old_value, $value, $option) {
        $args = array(
            'email' => $value['license_email'],
            'licence_key' => $value['licence_key'],
            'activation_status' => $value['is_activate'] ? $value['is_activate'] : 'off' 
        );
        Woo_Wallet_Withdrawal_License::manage_api_license($args);
    }

}

new WOO_Wallet_Withdrawal_Admin();
