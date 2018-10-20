<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
get_header();
?>
    <div class="content">
        <div class="content__container">
            <?php if (have_posts()): the_post(); ?>
                <div class="content__header">
                    <h1 class="content__title"><?php the_title(); ?></h1>
                </div>
                <div class="content__row">
                    <?php the_content(); ?>
                </div>
            <?php endif ?>
        </div>
    </div>
<?php get_footer(); ?>