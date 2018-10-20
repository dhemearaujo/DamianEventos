<!-- Autocomplet -->
<div class="site-search__autocomplete autocomplete" >

    <div class="autocomplete__frame hidden" data-autocomplete-frame>

        <?php for ($i = 0; $i < 5; $i++) : ?>
            <a class="autocomplete__item hidden" href="#" data-autocomplete-product="<?php echo esc_attr($i); ?>">
                <div class="autocomplete__product">
                    <!-- Photo  -->
                    <div class="autocomplete__product-photo">
                        <div class="product-photo">
                          <span class="product-photo__item product-photo__item--xs">
                            <img class="product-photo__img" src="#" alt="No photo"
                                 data-autocomplete-product-img>
                          </span>
                        </div>
                    </div>

                    <div class="autocomplete__product-info">
                        <!-- Title -->
                        <div class="autocomplete__product-title" data-autocomplete-product-name></div>
                        <!-- Price -->
                        <div class="autocomplete__product-price">
                            <div class="product-price product-price--sm product-price--bold" data-autocomplete-product-price>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        <?php endfor; ?>

    </div>

    <div class="autocomplete__frame hidden" data-autocomplete-noitems>
        <!-- Message if no items found after search request -->
        <div class="autocomplete__message autocomplete__message--noitems" >
            <?php esc_html_e('No products were found matching your selection.', 'saleszone'); ?>
        </div>

    </div>
</div>