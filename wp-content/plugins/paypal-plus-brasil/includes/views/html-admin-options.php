<?php /** @var WC_PPP_Brasil_Gateway $this */ ?>
<?php if ( 'yes' == $this->wrong_credentials ): ?>
    <div id="message-invalid-credentials" class="error inline">
        <p><strong>Suas credenciais estão inválidas. Verifique os dados informados e salve as configurações
                novamente.</strong></p>
    </div>
<?php endif; ?>
<?php if ( pppbr_needs_cpf() && ! class_exists( 'Extra_Checkout_Fields_For_Brazil' ) ): ?>
    <div id="message-wecfb" class="error inline">
        <p>
            <strong><?php echo sprintf( __( 'O PayPal Plus não está ativo. Não foi possível encontrar nenhum plugin com o suporte de CPF/CNPJ, por favor visite a <a href="%s" target="_blank">página oficial</a> do plugin para mais informações.', 'paypal-plus-brasil' ), 'https://br.wordpress.org/plugins/paypal-plus-brasil/' ); ?></strong>
        </p>
    </div>
<?php endif; ?>
<?php if ( ! pppbr_needs_cpf() ): ?>
    <div id="message-alert-usd" class="error inline">
        <p>
            <strong><?php _e( 'Você está utilizando USD em sua loja. Desta forma você só poderá receber pagamento de contas não-brasileiras.', 'ppp-brasil' ); ?></strong>
        </p>
    </div>
<?php endif; ?>
<img class="ppp-brasil-banner"
     src="<?php echo $this->plugin_url( 'assets/images/banner.png' ); ?>"
     title="PayPal Plus Brasil"
     alt="PayPal Plus Brasil">
<?php echo wp_kses_post( wpautop( $this->get_method_description() ) ); ?>

<table class="form-table">
	<?php echo $this->generate_settings_html( $this->get_form_fields(), false ); ?>
</table>