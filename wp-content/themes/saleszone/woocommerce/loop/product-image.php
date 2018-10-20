<?php
if (!defined('ABSPATH')) {
	exit;
}

global $product;

$image_size = apply_filters('single_product_archive_thumbnail_size', 'shop_catalog');

if ( has_post_thumbnail( $product->get_id() ) ) {
    $main_image_src = wp_get_attachment_image_url($variation->get_image_id(), $image_size);
} elseif ( ( $parent_id = wp_get_post_parent_id( $product->get_id() ) ) && has_post_thumbnail( $parent_id ) ){
    $main_image_src =  wp_get_attachment_image_url($variation->get_image_id(), $image_size);
} else {
    $main_image_src = wc_placeholder_img_src();
}

?>

<div class="product-photo">
    <a class="product-photo__item"
		<?php if( saleszone_option('category-product-lazyload') && saleszone_option('category-product-lazyload-spinner') ):?>
            data-loader-frame-permanent
		<?php endif; ?>
       data-product-photo-permalink
       href="<?php echo esc_url(get_the_permalink()); ?>">
        <?php if( !$flip_image_src ):?>
            <img class="product-photo__img"
                <?php if( saleszone_option('category-product-lazyload') ):?>
                    data-lazy-load
                    src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
                    data-srcset="<?php echo esc_attr(wp_get_attachment_image_srcset( $variation->get_image_id(), apply_filters('single_product_archive_thumbnail_size', 'shop_catalog') )); ?>"
                    data-original="<?php echo esc_url($main_image_src); ?>"
                <?php else : ?>
                    src="<?php echo esc_url($main_image_src) ;?>"
                    srcset="<?php echo esc_attr(wp_get_attachment_image_srcset( $variation->get_image_id(), apply_filters('single_product_archive_thumbnail_size', 'shop_catalog') )); ?>"
                <?php endif; ?>
                 data-product-medium-photo
                 data-src-placeholder="<?php echo esc_url(wc_placeholder_img_src()); ?>"
                 alt="<?php echo esc_attr(saleszone_get_img_alt($variation->get_image_id(), $variation->get_name())); ?>"
                 title="<?php echo esc_attr($variation->get_name()); ?>"
                 sizes="<?php echo esc_attr(wp_get_attachment_image_sizes( $variation->get_image_id(), apply_filters('single_product_archive_thumbnail_size', 'shop_catalog') )); ?>"
            >
            <?php if( saleszone_option('category-product-lazyload') && saleszone_option('category-product-lazyload-spinner') ):?>
                <i class="spinner-circle"></i>
            <?php endif; ?>
        <?php else: ?>
            <img class="product-photo__img"
                 data-lazy-load
                 src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
                 data-original="<?php echo esc_url($flip_image_src); ?>"
                 alt="<?php echo esc_attr(saleszone_get_img_alt($variation->get_image_id(), $variation->get_name())); ?>"
                 title="<?php echo esc_attr($variation->get_name()); ?>"

                 <?php
                 $gallery_images_ids = $variation->get_gallery_image_ids();
                 $gallery_first_image = $gallery_images_ids[0];
                 ?>
                 data-srcset="<?php echo esc_attr(wp_get_attachment_image_srcset( $gallery_first_image, apply_filters('single_product_archive_thumbnail_size', 'shop_catalog') )); ?>"
                 sizes="<?php echo esc_attr(wp_get_attachment_image_sizes( $gallery_first_image, apply_filters('single_product_archive_thumbnail_size', 'shop_catalog') )); ?>"
            >
	    <?php endif; ?>
    </a>
</div>