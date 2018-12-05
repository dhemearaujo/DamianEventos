<?php
/**
 * Approved Withdraw request Email.
 *
 * An email sent to the user when a withdraw request is approved by admin.
 *
 * @version     1.0.1
 * 
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
echo "= " . $email_heading . " =\n\n";
?>

<?php _e( Oi, '. $data['username'], 'woo-wallet-withdrawal' ); echo " \n";?>

<?php _e( 'Sua solicitação de retirada foi aprovada, parabéns!', 'woo-wallet-withdrawal' ); echo " \n";?>

<?php _e( 'Você enviou um pedido de retirada de:', 'woo-wallet-withdrawal' );  echo " \n";?>

<?php _e( 'Montante: '.$data['amount'], 'woo-wallet-withdrawal' ); echo " \n";?>
<?php _e( 'Método: '.$data['method'], 'woo-wallet-withdrawal' ); echo " \n";?>

<?php _e( 'Vamos transferir esse valor para o seu destino preferido em breve.', 'woo-wallet-withdrawal' ); echo " \n";?>

<?php _e( 'Obrigado por estar conosco.', 'woo-wallet-withdrawal' );  echo " \n";?>

<?php
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
