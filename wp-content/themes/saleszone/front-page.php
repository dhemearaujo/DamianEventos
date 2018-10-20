<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
get_header(); ?>

<?php if(saleszone_is_woocommerce_activated()): ?>
    <div class="start-page">
        <?php if (have_posts()): the_post(); ?>
            <?php the_content(); ?>
        <?php endif; ?>
        <?php if (is_active_sidebar('homepage_widgets')) : ?>
            <div class="start-page__row-widgets">
                <div class="content__container">
                    <div class="row row--ib row--vindent-m">
                        <?php dynamic_sidebar('homepage_widgets'); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php else: ?>
    <?php get_template_part('index'); ?>
<?php endif; ?>

<?php get_footer(); ?>

