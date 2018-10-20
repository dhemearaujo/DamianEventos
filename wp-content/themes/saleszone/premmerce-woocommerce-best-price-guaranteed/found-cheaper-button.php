<?php

if ( ! defined( 'WPINC' ) ) die;

?>

<div class="pc-product-action pc-product-action--found-cheaper">
    <div class="pc-product-action__ico pc-product-action__ico--discount">
        <?php saleszone_the_svg('percentage-discount'); ?>
    </div>
    <button class="pc-product-action__link"
            data-found-less-expensive-button="<?php echo esc_url(add_query_arg(array('wc-ajax' => 'found_cheaper_popup'), get_the_permalink())); ?>">
        <?php esc_html_e('Found cheaper?', 'saleszone' ); ?>
    </button>
    <input type="hidden"
           name="product_id"
           value="<?php echo esc_attr($product->get_id()); ?>"
           form="data-found-less-expensive-form">
</div>