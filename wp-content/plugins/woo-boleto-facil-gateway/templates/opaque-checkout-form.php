<?php
/**
 *
 * BoletoFacil - Opaque Checkout template.
 * order_total, paymentTypes and max_installments are from WC_BoletoFacil_Gateway
 *
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
$boletoEnabled = TRUE;
$creditCardEnabled = TRUE;

if ('CREDIT_CARD' == $paymentTypes) {
    $boletoEnabled = FALSE;
} else if ('BOLETO' == $paymentTypes) {
    $creditCardEnabled = FALSE;
}
?>

<fieldset id="boletofacil-credit-card-fields-opaque">

    <?php if ($boletoEnabled && $creditCardEnabled) : ?>
        <p>
            <label for="boletofacil-boleto" class="form-row form-row-first">
                <input id="boletofacil-boleto" type="radio" name="pmethod" value="BOLETO">
                Boleto Bancário
            </label>
            <label for="boletofacil-credit-card" class="form-row form-row-last">
                <input id="boletofacil-credit-card" class="" type="radio" name="pmethod" value="CREDIT_CARD" checked="checked">
                Cartão de crédito
            </label>
        </p>
    <?php endif; ?>
    <?php
    if ($boletoEnabled) {
        require_once('installments-bankslip.php');
    }

    if($creditCardEnabled) {
        require_once('installments-creditcard.php');
    }
    ?>
</fieldset>