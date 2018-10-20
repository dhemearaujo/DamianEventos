<?php
/**
 *
 * BoletoFacil - Transparent Checkout template.
 * order_total, paymentTypes and max_installments_bank are from WC_BoletoFacil_Gateway
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

<fieldset id="boletofacil-credit-card-fields">
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
<?php else: ?>
    <input type="hidden" name="pmethod" value="<?php echo $paymentTypes; ?>">
<?php endif; ?>

<?php if ($creditCardEnabled) : ?>
    <div id="creditCard">
        <div id="boleto_facil_errors"></div>
        <?php require_once('installments-creditcard.php'); ?>
        <div>
            <p class="form-row form-row-wide">
                <label for="card_number">Número do cartão<span class="required">*</span></label>
                <input id="card_number" class="input-text" type="tel" maxlength="20" autocomplete="off"
                       placeholder="•••• •••• •••• ••••" name="card_number">
            </p>
            <p class="form-row form-row-wide">
                <label for="card_name">Nome no cartão<span class="required">*</span></label>
                <input id="card_name" class="input-text" type="text" maxlength="255" autocomplete="off"
                       placeholder="Nome completo" name="card_name">
            </p>
            <p>
                <label>Mês e ano de vencimento do cartão<span class="required">*</span></label>
            <p class="form-row form-row-first">
                <input id="card_expiration_month" class="input-text" type="text" maxlength="2" autocomplete="off"
                       placeholder="MM" name="card_expiration_month">
            </p>
            <p class="form-row form-row-last">
                <input id="card_expiration_year" class="input-text" type="text" maxlength="4" autocomplete="off"
                       placeholder="YYYY" name="card_expiration_year">
            </p>
            </p>
            <p class="form-row form-row-wide">
                <label for="card_security_code">Código de segurança<span class="required">*</span></label>
                <input id="card_security_code" class="input-text" type="password" maxlength="4" autocomplete="off"
                       name="card_security_code">
            </p>

        </div>
    </div>
<?php endif; ?>
<?php if ($boletoEnabled): require_once('installments-bankslip.php'); endif; ?>
</fieldset>
