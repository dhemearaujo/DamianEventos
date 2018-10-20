<?php
/**
 *  Plugin Name: Boleto Facil - WooCommerce Gateway
 *  Description: Adiciona o Boleto Fácil como meio de pagamento ao WooCommerce.
 *  Version: 1.2.1
 *  Author: BoletoBancario.com
 *  Author URI: https://boletobancario.com/
 *	Copyright: © 2000-2018 BoletoBancario.com.
 *	License: GNU General Public License v3.0
 *	License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *  WC requires at least: 3.0.0
 *  WC tested up to: 3.2.0
 */

class BoletoFacil {

    const VERSION = '1.2.1';

 	/**
 	* Get BoletoFacil templates path.
 	*
 	* @return string
 	*/
	public static function getTemplatePath() {
		return plugin_dir_path( __FILE__ ) . 'templates/';
	}
}

function wc_boletofacil_woocommerce_is_missing() {
	echo '<div class="error"><p>Boleto Facil para WooCommerce depende da última versão do WooCommerce para funcionar! <a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a></p></div>';
}

function wc_extra_checkout_br_woocommerce_is_missing() {
	echo '<div class="error"><p>Boleto Facil para WooCommerce depende do plugin WooCommerce Extra Checkout Fields for Brazil para funcionar! <a href="https://wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/">WooCommerce Extra Checkout Fields for Brazil</a></p></div>';
}

function wc_boletofacil_gateway_init() {
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		add_action( 'admin_notices', 'wc_boletofacil_woocommerce_is_missing' );
		return;
	}

	if ( ! class_exists( 'Extra_Checkout_Fields_For_Brazil' ) ) {
		add_action( 'admin_notices', 'wc_extra_checkout_br_woocommerce_is_missing' );
		return;
	}

	function wc_boletofacil_add_gateway( $methods ) {
		$methods[] = 'WC_BoletoFacil_Gateway';
		return $methods;
	}
	add_filter( 'woocommerce_payment_gateways', 'wc_boletofacil_add_gateway' );
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'plugin_action_links_' );
	add_filter( 'plugin_row_meta', 'bf_plugin_row_meta', 10, 2 );
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-boletofacil-gateway.php';
}

function plugin_action_links_( $links ) {
	$plugin_links   = array();
	$plugin_links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=boletofacil' ) ) . '"> Configurações </a>';
	return array_merge( $plugin_links, $links );
}

function bf_plugin_row_meta( $links, $file ) {
	if ( plugin_basename( __FILE__ ) === $file ) {
		$row_meta = array(
			'account' => '<a href="' . esc_url('https://www.boletobancario.com/boletofacil/user/signup.html') . '" aria-label="Crie sua conta no Boleto Fácil">Criar conta</a>',
			'faq'     => '<a href="' . esc_url('https://www.boletobancario.com/boletofacil/faq/faq.html') . '" aria-label="Ajuda: Descubra sobre taxas, transferência e outras dúvidas!">Ajuda</a>',
		);
		return array_merge( $links, $row_meta );
	}

	return (array) $links;
}



add_action( 'plugins_loaded', 'wc_boletofacil_gateway_init', 0 );

function wc_boletofacil_hides_when_is_outside_brazil( $available_gateways ) {
//	if ( isset( $_REQUEST['country'] ) && 'BR' != $_REQUEST['country'] ) {
//		unset( $available_gateways['boletofacil'] );
//	}
	return $available_gateways;
}
add_filter( 'woocommerce_available_payment_gateways', 'wc_boletofacil_hides_when_is_outside_brazil' );

function wc_boletofacil_pending_payment_instructions( $order_id ) {
	$order = new WC_Order( $order_id );
	if ( 'on-hold' == $order->get_status() && 'boletofacil' == $order->get_payment_method() ) {
		$html = '<div class="woocommerce-info">';
		$message = "";

		$is_credit_card = get_post_meta($order->get_id(), 'boletofacil_is_creditcard', true);
		if (!$is_credit_card) {
	        $message .= '<strong> Atenção! </strong> Você não receberá o boleto pelos Correios. <br />';
			$message .= 'Por favor clique no link "Imprimir boleto" a seguir e pague o boleto em seu Internet Banking. <br />';
			$message .= 'Se preferir, imprima e pague em uma agência bancária ou casa lotérica. <br />';
			$message .= 'Ignore esta mensagem se o pagamento já foi realizado.<br />';
		} else {
			$message .= 'Seu pagamento ainda está sendo processado, por favor, aguarde!. <br />';
		}
		$html .= apply_filters( 'woocommerce_boletofacil_pending_payment_instructions', $message, $order );
		if (!$is_credit_card) {
			$html .= sprintf( '<br /><a class="button" href="%s" target="_blank">%s</a>', get_post_meta( $order->get_id(), 'boletofacil_url', true ), 'Imprimir boleto' );
		}
		$html .= '</div>';
		echo $html;
	}
}
add_action( 'woocommerce_view_order', 'wc_boletofacil_pending_payment_instructions' );
