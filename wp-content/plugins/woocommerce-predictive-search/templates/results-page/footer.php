<?php
/**
 * The Template for Predictive Search plugin
 *
 * Override this template by copying it to yourtheme/woocommerce/results-page/footer.php
 *
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<script type="text/template" id="wc_psearch_result_footerTpl"><div style="clear:both"></div>
	{{ if ( next_page_number > 1 ) { }}
	<div id="ps_more_check"></div>
	{{ } else if ( total_items == 0 && first_load ) { }}
	<p style="text-align:center"><?php wc_ps_ict_t_e( 'Sem retorno.', __('Nenhum resultado encontrado! Por favor, tente novamente.', 'woocommerce-predictive-search' ) ); ?></p>
	{{ } }}
</script>