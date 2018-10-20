<?php $end_date = get_post_meta( $id, '_sale_price_dates_to', true ); ?>

<?php if($end_date && saleszone_option('category-product-action-counter')): ?>
    <?php
        $date = saleszone_get_countdown($end_date);
        $days = intval($date['dd']);
        $hours = intval($date['hh']);
        $minutes = intval($date['mm']);
    ?>
    <div class="product-cut__action-counter" data-product-loop-action-counter>

            <?php esc_html_e('On sale ', 'saleszone'); ?>

            <?php
            if ($days) {
                /* translators: %s: days count */
                echo esc_html(sprintf( _n( '%s day', '%s days', $days, 'saleszone' ), $days ));
            }?>

            <?php
            if ($hours) {
                /* translators: %s: hours count */
                echo esc_html(sprintf( _n( '%s hour', '%s hours', $hours, 'saleszone' ), $hours ));
            }?>

            <?php
            if ($minutes) {
                /* translators: %s: minutes count */
                echo esc_html(sprintf( _n( '%s minute', '%s minutes', $minutes, 'saleszone' ), $minutes ));
            }?>
    </div>
<?php endif; ?>