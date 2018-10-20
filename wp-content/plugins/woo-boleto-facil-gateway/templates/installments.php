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



$allowInstallments = $max_installments > 1;

if ($allowInstallments) : ?>
	<div id="installments">
		<p>Parcelar compra</p>
		<div>
			<p>
				<select id="max_installments" class="input-text" name="max_installments">
					<?php
						$selects = '<option value="1">Sem parcelamento</option>';
						for($i = 1; $i <= $max_installments && $i <= 24; $i++) {
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
		<br>
	</div>
<?php endif; ?>