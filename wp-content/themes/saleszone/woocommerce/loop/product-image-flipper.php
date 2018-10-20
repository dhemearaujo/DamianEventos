<?php
if (!defined('ABSPATH')) {
	exit;
}

?>

<div class="flipper
        <?php if(saleszone_option('category-product-flipper-animation') != 'none'):?>
            flipper--<?php echo esc_attr(saleszone_option('category-product-flipper-animation')); ?>
        <?php endif; ?>
    ">
	<div class="flipper__body">
		<div class="flipper__front">
			<?php wc_get_template('loop/product-image.php', array(
				'variation' => $variation,
				'flip_image_src' => false
            )); ?>
		</div>
		<div class="flipper__back">
			<div class="product-photo">
				<?php wc_get_template('loop/product-image.php', array(
					'variation' => $variation,
					'flip_image_src' => $flip_image_src
                )); ?>
			</div>
		</div>
	</div>
</div>
