<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class="content__header">
    <h1 class="content__title">
        <?php woocommerce_page_title(); ?>
    </h1>
</div>

<div class="content__row">
    <div class="row">
        <div class="col-md-9">
            <?php woocommerce_output_product_categories(array(
                'before' => '<div class="row row--ib row--ib row--vindent-m">',
                'after' => '</div>',
                'parent_id' => is_product_category() ? get_queried_object_id() : 0,
            )); ?>

            <?php do_action('saleszone-category-category-layout-body-end'); ?>
        </div>
        <?php if (is_active_sidebar('subcategories_sidebar')): ?>
            <div class="col-md-3 col--spacer-sm hidden-xs hidden-sm">
                <div class="content__sidebar content__sidebar--right">
                    <?php
                    /**
                     * @hooked saleszone_dynamic_subcategories_sidebar
                     */
                    do_action('saleszone_subcategories_sidebar');
                    do_action('woocommerce_sidebar');
                    ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>