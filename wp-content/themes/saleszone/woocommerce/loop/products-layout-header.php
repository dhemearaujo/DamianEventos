<div class="content__header">
    <h1 class="content__title">
        <?php woocommerce_page_title(); ?>
    </h1>
    <?php woocommerce_result_count(); ?>
</div>

<?php
/* Include woocommerce/content-product_cat.php template */
if (woocommerce_get_loop_display_mode() == 'both') {
    woocommerce_output_product_categories(array(
        'before' => '<div class="content__row"><div class="row row--ib row--ib row--vindent-m">',
        'after' => '</div></div>',
        'parent_id' => is_product_category() ? get_queried_object_id() : 0,
    ));
}
?>

<?php wc_get_template('loop/toolbar.php'); ?>