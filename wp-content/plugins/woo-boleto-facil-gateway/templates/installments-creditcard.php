<?php
/**
 *
 * BoletoFacil - Opaque Checkout template.
 * order_total, paymentTypes and max_installments are from WC_BoletoFacil_Gateway
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$allowInstallmentsCreditCard = $max_installments_credit_card > 1;
if ($allowInstallmentsCreditCard) : ?>
    <div id="installments_credit_card" class="form-row form-row-wide">
        <label for="max_installments_credit_card">Parcelar compra no cartão</label>
        <p class="form-row form-row-wide">
            <select id="max_installments_credit_card" name="max_installments_credit_card">
                <?php
                $selects = '<option value="1">À vista  R$ '.number_format($order_total, 2, '.', '' ).'</option>';
                for($i = 2; $i <= $max_installments_credit_card; $i++) {
                    $partial_order_value =  number_format(($order_total/$i), 2, '.', '' );
                    if ($partial_order_value > 5) {
                        $selects .= "<option value=".$i.">".$i."x de R$ ".($partial_order_value)." </option>";
                    } else {
                        break;
                    }
                }
                echo $selects;
                ?>
            </select>
        </p>
    </div>
<?php endif; ?>