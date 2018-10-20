<?php
/**
 * Shipping Methods Display
 *
 * In 2.1 we show methods per package. This allows for multiple methods per order if so desired.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.2.0
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="cart-totals__label">
    <?php echo wp_kses_post($package_name); ?>
</div>
<div class="cart-totals__value">
    <tr class="shipping">
        <div class="shipping-methods <?php echo is_checkout() ? 'shipping-methods--checkout':'' ?>" data-title="<?php echo esc_attr($package_name); ?>">

            <!-- shipping-methods if $available_methods > 1 -->
            <?php if (1 < count($available_methods)) : ?>
                <ul class="shipping-methods__list" id="shipping_method">
                    <?php foreach ($available_methods as $method) : ?>
                        <li class="shipping-methods__item">
                            <div class="shipping-methods__field">
                                <input class="shipping-methods__control shipping_method"
                                       type="radio"
                                       name="shipping_method[<?php echo esc_attr($index); ?>]"
                                       data-index="<?php echo esc_attr($index); ?>"
                                       id="shipping_method_<?php echo esc_attr($index); ?>_<?php echo esc_attr(sanitize_title($method->id)); ?>"
                                       value="<?php echo esc_attr($method->id); ?>"
                                    <?php echo checked($method->id, $chosen_method, false); ?> />
                                <label class="shipping-methods__title"
                                       for="shipping_method_<?php echo esc_attr($index); ?>_<?php echo esc_attr(sanitize_title($method->id)); ?>">
                                    <?php echo wp_kses_post(wc_cart_totals_shipping_method_label($method)); ?>
                                </label>
                            </div>
                            <?php
                            if (has_action('woocommerce_after_shipping_rate')): ?>
                                <div class="shipping-methods__after-field">
                                    <?php do_action('woocommerce_after_shipping_rate', $method, $index); ?>
                                </div>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <!-- shipping-methods if $available_methods == 1 -->
            <?php elseif (1 === count($available_methods)) : ?>
                <?php $method = current($available_methods); ?>
                <?php printf('<input type="hidden" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d" value="%2$s" class="shipping_method" />', esc_html($index), esc_attr($method->id)); ?>
                <?php echo wp_kses_post(wc_cart_totals_shipping_method_label($method)); ?>
                <?php do_action('woocommerce_after_shipping_rate', $method, $index); ?>
            <?php elseif (WC()->customer->has_calculated_shipping()) : ?>
                <?php echo wp_kses_post(apply_filters(is_cart() ? 'woocommerce_cart_no_shipping_available_html' : 'woocommerce_no_shipping_available_html', wpautop(__('There are no shipping methods available. Please ensure that your address has been entered correctly, or contact us if you need any help.', 'saleszone')))); ?>
            <?php elseif (!is_cart()) : ?>
                <?php echo wp_kses_post(wpautop(__('Enter your full address to see shipping costs.', 'saleszone'))); ?>
            <?php endif; ?>

            <?php if ($show_package_details) : ?>
                <?php echo '<p class="woocommerce-shipping-contents"><small>' . esc_html($package_details) . '</small></p>'; ?>
            <?php endif; ?>

        </div>
    </tr>
</div>
