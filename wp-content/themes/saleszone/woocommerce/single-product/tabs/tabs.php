<?php
/**
 * Single Product tabs
 *
 * @see 	https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Filter tabs and allow third parties to add their own.
 *
 * Each tab is an array containing title, callback and priority.
 * @see woocommerce_default_product_tabs()
 */
$tabs = apply_filters('woocommerce_product_tabs', array());

if (empty($tabs)) {
    return;
}
?>
<?php if(saleszone_option('product-tabs-type') == 'no-tabs'): ?>
    <div class="product-fullinfo woocommerce-tabs">
        <?php foreach ($tabs as $key => $tab) : ?>
            <div class="product-fullinfo__item">
                <?php if ($key == 'reviews'): ?> <div class="product-fullinfo__reviews-id" id="product-reviews"></div> <?php endif; ?>
                <div class="product-fullinfo__header">
                    <div class="product-fullinfo__title">
                        <?php echo esc_html(apply_filters('woocommerce_product_' . $key . '_tab_title', $tab['title'], $key)); ?>
                    </div>
                </div>
                <div class="product-fullinfo__inner">
                    <?php call_user_func($tab['callback'], $key, $tab); ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php elseif (saleszone_option('product-tabs-type') == 'tabs'): ?>
    <ul class="accordion-tabs woocommerce-tabs" data-accordion-tabs>
        <?php foreach ($tabs as $key => $tab) : ?>
            <li class="accordion-tabs__item" data-accordion-tabs-item>

                <a href="#" class="accordion-tabs__link js-init-active" data-accordion-tabs-link <?php if ($key == 'reviews'): ?> id="product-reviews" <?php endif; ?>>
                    <?php echo esc_html(apply_filters('woocommerce_product_' . $key . '_tab_title', $tab['title'], $key)); ?>
                </a>

                <div class="accordion-tabs__content" data-accordion-tabs-content>
                    <?php call_user_func($tab['callback'], $key, $tab); ?>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
