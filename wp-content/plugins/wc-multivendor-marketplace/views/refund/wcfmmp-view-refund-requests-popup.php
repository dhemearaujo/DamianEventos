<?php
/**
 * WCFM plugin view
 *
 * WCfM Refund popup View
 *
 * @author 		WC Lovers
 * @package 	wcfmmp/views/refund
 * @version   1.0.0
 */
 
global $wp, $WCFM, $WCFMmp, $_POST, $wpdb;

$order_id      = sanitize_text_field( $_POST['order_id'] );
$order_id = str_replace( '#', '', $order_id );

if( !$order_id ) return;

$item_id = 0;
if( isset( $_POST['item_id'] ) ) {
	$item_id       = sanitize_text_field( $_POST['item_id'] );
}

$commission_id = 0;
if( isset( $_POST['commission_id'] ) ) {
	$commission_id = sanitize_text_field( $_POST['commission_id'] );
}


$order                  = wc_get_order( $order_id );
$line_items             = $order->get_items( apply_filters( 'woocommerce_admin_order_item_types', 'line_item' ) );
$product_items          = array();
foreach ( $line_items as $order_item_id => $item ) {
	$product_items[$order_item_id] = $item->get_name() . ' (' . $item->get_total() . ' ' . $order->get_currency() . ')';
}

$wcfm_refund_product_attr = array( 'style' => 'width: 95%;' );
if( $item_id ) {
	$wcfm_refund_product_attr = array( 'style' => 'width: 95%;', 'readonly' => true );
}

$wcfm_refund_request_class = '';
if( !wcfm_is_vendor() ) {
	$wcfm_refund_request_class = ' wcfm_custom_hide';
}
?>

<div class="wcfm-clearfix"></div><br />
<div id="wcfm_refund_form_wrapper">
	<form action="" method="post" id="wcfm_refund_requests_form" class="refund-form wcfm_popup_wrapper" novalidate="">
		<div style="margin-bottom: 15px;"><h2 style="float: none;"><?php _e( 'Refund Request', 'wc-multivendor-marketplace' ); ?></h2></div>
		<p class="wcfm-refund-form-product wcfm_popup_label">
			<label for="wcfm_refund_product"><?php _e( 'Product', 'wc-multivendor-marketplace' ); ?> <span class="required">*</span></label> 
		</p>
		<?php $WCFM->wcfm_fields->wcfm_generate_form_field( array( "wcfm_refund_product" => array( 'type' => 'select', 'class' => 'wcfm-select wcfm_ele wcfm_popup_input', 'label_class' => 'wcfm_title', 'options' => $product_items , 'value' => $item_id ) ) ); ?>
		
		<p class="wcfm-refund-form-request wcfm_popup_label <?php echo $wcfm_refund_request_class; ?>">
			<label for="wcfm_refund_request"><?php _e( 'Refund Requests', 'wc-multivendor-marketplace' ); ?> <span class="required">*</span></label> 
		</p>
		<?php $WCFM->wcfm_fields->wcfm_generate_form_field( array( "wcfm_refund_request" => array( 'type' => 'select', 'class' => 'wcfm-select wcfm_ele wcfm_popup_input'.$wcfm_refund_request_class, 'label_class' => 'wcfm_title', 'options' => array( 'full' => __( 'Full Refund', 'wc-multivendor-marketplace' ), 'partial' => __( 'Partial Refund', 'wc-multivendor-marketplace' ) ) ) ) ); ?>
		
		<p class="wcfm-refund-form-request-amount wcfm_popup_label">
			<label for="wcfm_refund_request_amount"><?php _e( 'Refund Amount', 'wc-multivendor-marketplace' ); ?> <span class="required">*</span></label> 
		</p>
		<?php $WCFM->wcfm_fields->wcfm_generate_form_field( array( "wcfm_refund_request_amount" => array( 'type' => 'number', 'attributes' => array( 'min' => '1', 'step' => '1' ), 'class' => 'wcfm-text wcfm_ele wcfm-refund-form-request-amount wcfm_popup_input', 'label_class' => 'wcfm_title', 'value' => '1' ) ) ); ?>
	
		<p class="wcfm-refund-form-reason wcfm_popup_label">
			<label for="comment"><?php _e( 'Refund Requests Reason', 'wc-multivendor-marketplace' ); ?> <span class="required">*</span></label>
		</p>
		<textarea id="wcfm_refund_reason" name="wcfm_refund_reason" class="wcfm_popup_input wcfm_popup_textarea"></textarea>
		
		<?php if ( function_exists( 'gglcptch_init' ) ) { ?>
		<div class="wcfm_clearfix"></div>
		<div class="wcfm_gglcptch_wrapper" style="float:right;">
			<?php echo apply_filters( 'gglcptch_display_recaptcha', '', 'wcfm_refund_request_form' ); ?>
		</div>
	<?php } elseif ( class_exists( 'anr_captcha_class' ) && function_exists( 'anr_captcha_form_field' ) ) { ?>
		<div class="wcfm_clearfix"></div>
		<div class="wcfm_gglcptch_wrapper" style="float:right;">
			<?php do_action( 'anr_captcha_form_field' ); ?>
		</div>
	<?php } ?>
		<div class="wcfm_clearfix"></div>
		<div class="wcfm-message" tabindex="-1"></div>
		<div class="wcfm_clearfix"></div><br />
		
		<p class="form-submit">
			<input name="submit" type="submit" id="wcfm_refund_requests_submit_button" class="submit wcfm_popup_button" value="<?php _e( 'Submit', 'wc-multivendor-marketplace' ); ?>"> 
			<input type="hidden" name="wcfm_refund_order_id" value="<?php echo $order_id; ?>" id="wcfm_refund_order_id">
			<input type="hidden" name="wcfm_refund_commission_id" value="<?php echo $commission_id; ?>" id="wcfm_refund_commission_id">
		</p>	
	</form>
</div>
<div class="wcfm-clearfix"></div>