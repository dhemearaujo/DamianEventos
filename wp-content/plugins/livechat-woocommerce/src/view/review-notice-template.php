<?php
/**
 * Review notice template.
 *
 * @category Admin pages
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="notice notice-info is-dismissible" id="wc-lc-review-notice">
	<p><?php _e('Do you like LiveChat? Leave us a review and join our LiveChat Knowledge Journey!', 'livechat-woocommerce'); ?></p>
	<p><?php _e('We do our best to create the best live chat software available out there. Leave us a review, help us grow LiveChat and join our exclusive Knowledge Journey community. Learn how to:', 'livechat-woocommerce'); ?></p>
	<ul style="list-style-type:disc;list-style-position:inside;margin:0;padding:3px;">
		<li><?php _e('Bring people to your website', 'livechat-woocommerce'); ?></li>
		<li><?php _e('Be the hero of your customers\' needs', 'livechat-woocommerce'); ?></li>
		<li><?php _e('Sell like a boss with LiveChat', 'livechat-woocommerce'); ?></li>
		<li><?php _e('Succeed in customer success', 'livechat-woocommerce'); ?></li>
	</ul>
	<p>
		<a href="https://wordpress.org/support/plugin/livechat-woocommerce/reviews/#new-post" target="_blank" style="text-decoration: none" id="wc-lc-review-now">
            <span class="dashicons dashicons-thumbs-up"></span> <?php _e('Leave a review and join LiveChat Knowledge Journey!', 'livechat-woocommerce'); ?>
        </a> |
		<a href="#" style="text-decoration: none" id="wc-lc-review-postpone">
            <span class="dashicons dashicons-clock"></span> <?php _e('Maybe later', 'livechat-woocommerce'); ?>
        </a> |
		<a href="#" style="text-decoration: none" id="wc-lc-review-dismiss">
            <span class="dashicons dashicons-no-alt"></span><?php _e('I don’t use this app anymore', 'livechat-woocommerce'); ?>
        </a>
	</p>
</div>
