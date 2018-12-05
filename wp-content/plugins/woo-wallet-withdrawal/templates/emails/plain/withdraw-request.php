<?php
/**
 * New Withdraw request Email.
 *
 * An email sent to the admin when a new withdraw request is created by vendor.
 *
 * @version     1.0.1
 * 
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
echo "= " . $email_heading . " =\n\n";
?>
<?php _e( 'Oi,', 'woo-wallet-withdrawal' );  echo " \n";?>

<?php _e( 'Uma nova solicitação de retirada foi feito por - '.$data['username'], 'woo-wallet-withdrawal' );  echo " \n";?>

<?php _e( 'Montante da solicitação: '.$data['amount'], 'woo-wallet-withdrawal' );  echo " \n";?>
<?php _e( 'Método de Pagamento: '.$data['method'], 'woo-wallet-withdrawal' );  echo " \n";?>

<?php _e( 'Usuário: '.$data['username'], 'woo-wallet-withdrawal' );  echo " \n";?>
<?php _e( 'Perfil: '.$data['profile_url'], 'woo-wallet-withdrawal' );  echo " \n";?>

<?php _e( 'Você pode aprovar ou negar isso aqui: '.$data['withdraw_page'], 'woo-wallet-withdrawal' );  echo " \n";?>

<?php
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
