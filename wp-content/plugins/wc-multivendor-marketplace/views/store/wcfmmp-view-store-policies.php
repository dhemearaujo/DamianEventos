<?php
/**
 * The Template for displaying all store policies.
 *
 * @package WCfM Markeplace Views Policies
 *
 * For edit coping this to yourtheme/wcfm/store 
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $WCFM, $WCFMmp;

$wcfm_policy_vendor_options = $store_user->get_store_policies();

$shipping_policy = isset( $wcfm_policy_vendor_options['shipping_policy'] ) ? $wcfm_policy_vendor_options['shipping_policy'] : '';
$refund_policy = isset( $wcfm_policy_vendor_options['refund_policy'] ) ? $wcfm_policy_vendor_options['refund_policy'] : '';
$cancellation_policy = isset( $wcfm_policy_vendor_options['cancellation_policy'] ) ? $wcfm_policy_vendor_options['cancellation_policy'] : '';
?>

<div class="_area" id="policy">
	<div class="wcfmmp-store-policies">
	 
	  <?php do_action( 'wcfmmp_store_before_policies', $store_user->get_id() ); ?>
	
		<?php if( !wcfm_empty($shipping_policy) ) { ?>
			<div class="wcfm-shipping-policies">
				<h2 class="wcfm_policies_heading"><?php echo apply_filters('wcfm_shipping_policies_heading', __('Shipping Policy', 'wc-frontend-manager')); ?></h2>
				<div class="wcfm_policies_description" ><?php echo $shipping_policy; ?></div>
			</div>
		<?php } if( !wcfm_empty( $refund_policy ) ){ ?>
			<div class="wcfm-refund-policies">
				<h2 class="wcfm_policies_heading"><?php echo apply_filters('wcfm_refund_policies_heading', __('Refund Policy', 'wc-frontend-manager')); ?></h2>
				<div class="wcfm_policies_description" ><?php echo $refund_policy; ?></div>
			</div>
		<?php } if( !wcfm_empty( $cancellation_policy ) ){ ?>
			<div class="wcfm-cancellation-policies">
				<h2 class="wcfm_policies_heading"><?php echo apply_filters('wcfm_cancellation_policies_heading', __('Cancellation / Return / Exchange Policy', 'wc-frontend-manager')); ?></h2>
				<div class="wcfm_policies_description" ><?php echo $cancellation_policy; ?></div>
			</div>
		<?php } ?>
		
		<?php do_action( 'wcfmmp_store_after_policies', $store_user->get_id() ); ?>
		
	</div>
</div>