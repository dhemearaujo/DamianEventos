<?php

$panel = 'shop';
$section = 'shop-product';
Saleszone_Kirki::add_section( $section, array(
    'title' => esc_html__( 'Product', 'saleszone' ),
    'panel' => $panel,
) );
Saleszone_Kirki::add_field( 'option', array(
    'type'     => 'checkbox',
    'settings' => 'product-sidebar',
    'label'    => esc_html__( 'Product sidebar', 'saleszone' ),
    'section'  => $section,
    'default'  => saleszone_default_options( 'product-sidebar' ),
) );
Saleszone_Kirki::add_field( 'option', array(
    'type'     => 'checkbox',
    'settings' => 'product-main-attributes',
    'label'    => esc_html__( 'Product main attributes', 'saleszone' ),
    'section'  => $section,
    'default'  => saleszone_default_options( 'product-main-attributes' ),
) );
Saleszone_Kirki::add_field( 'option', array(
    'type'     => 'checkbox',
    'settings' => 'product-btn-add-to-cart-icon',
    'label'    => esc_html__( 'Button add to cart show icon', 'saleszone' ),
    'section'  => $section,
    'default'  => saleszone_default_options( 'product-btn-add-to-cart-icon' ),
) );