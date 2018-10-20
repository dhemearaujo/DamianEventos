<?php get_header(); ?>
    <div class="content">
        <div class="content__container">
            <?php if (have_posts()): the_post(); ?>
                <div class="content__header">
                    <h1 class="content__title"><?php the_title(); ?></h1>
                </div>
                <div class="content__row">
                    <div class="typo"><?php the_content(); ?></div>
                </div>
                <?php if(comments_open()) :?>
                <div class="content__row">
                    <?php comments_template(); ?>
                </div>
                <?php endif; ?>
            <?php endif ?>
        </div>
    </div>
<?php get_footer(); ?>