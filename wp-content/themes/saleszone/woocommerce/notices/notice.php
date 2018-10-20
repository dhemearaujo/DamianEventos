<?php
/**
 * Show messages
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if (!$messages) {
    return;
}

?>


<div class="message message--info">
    <?php if (count($messages) == 1): ?>
        <?php echo wp_kses_post($messages[0]); ?>
    <?php else: ?>
        <ul class="message__list">
            <?php foreach ($messages as $message) : ?>
                <li class="message__item"><?php echo wp_kses_post($message); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
