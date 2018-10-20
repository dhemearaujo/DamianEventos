<?php

global $product;
global $premmerce_wishlist_frontend;

$productId = isset($productId) ? $productId : $product->get_ID();
$addRoute = home_url('wp-json/premmerce/wishlist/add/popup');
$addUrl = wp_nonce_url($addRoute . '?wishlist_product_id=' . $productId ,'wp_rest');
if(!isset($type)){
    $type = false;
}

?>

<?php if($type == 'button') :?>
    <button class="btn btn-default" data-modal="<?php echo esc_url($addUrl); ?>">
        <i class="btn-default__ico btn-default__ico--wishlist"><?php saleszone_the_svg('heart'); ?></i>
    </button>
<?php elseif ($type == 'link') : ?>
    <div class="pc-product-action__ico pc-product-action__ico--wishlist">
        <?php saleszone_the_svg('heart'); ?>
    </div>
    <button class="pc-product-action__link" data-modal="<?php echo esc_url($addUrl); ?>">
        <?php esc_html_e('Add to Wishlist','saleszone'); ?>
    </button>
<?php endif; ?>