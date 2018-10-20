<?php
/**
 * Loop Rating
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' || get_option( 'woocommerce_enable_reviews' ) === 'no') {
	return;
}

wc_get_template('single-product/rating.php');
