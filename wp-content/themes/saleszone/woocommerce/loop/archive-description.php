<?php if ($description = saleszone_archive_description()): ?>
    <div class="content__row content__row--top-md">
        <div class="typo">
            <?php echo wp_kses_post($description); ?>
        </div>
    </div>
<?php endif; ?>