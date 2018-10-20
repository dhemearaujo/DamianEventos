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

$allowInstallmentsBankSlip = $max_installments_bank_slip > 1;
if ($allowInstallmentsBankSlip) : ?>
	<div id="installments_bank_slip" class="form-row form-row-wide">
		<p>Parcelar compra no boleto</p>
        <p class="form-row form-row-wide">
            <select id="max_installments_bank_slip" name="max_installments_bank_slip">
                <?php
                    $selects = '<option value="1">À vista  R$ '.number_format($order_total, 2, '.', '' ).'</option>';
                    for($i = 2; $i <= $max_installments_bank_slip; $i++) {
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
<?php endif;?>