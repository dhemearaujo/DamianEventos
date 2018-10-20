<?php

/**
 * Assign textdomain for theme localization
 */
load_theme_textdomain( 'saleszone', get_template_directory() . '/languages' );
if ( !function_exists( 'is_plugin_active' ) ) {
    include ABSPATH . 'wp-admin/includes/plugin.php';
}
/**
 * Insert custom and third party styles and scripts into index page
 */
$theme_functions_dir = get_template_directory() . "/functions/";
require $theme_functions_dir . "assets.php";
require $theme_functions_dir . "support.php";
require $theme_functions_dir . "utilities.php";
require $theme_functions_dir . "register-sidebar.php";
require $theme_functions_dir . "hooks.php";
require $theme_functions_dir . "hooks-functions.php";
require $theme_functions_dir . "filters.php";
require $theme_functions_dir . "filters-functions.php";
require $theme_functions_dir . "compatibility.php";
require $theme_functions_dir . "navs/WalkerHeaderStatic.php";
require $theme_functions_dir . "navs/WalkerFooterStatic.php";
require $theme_functions_dir . "navs/WalkerMobileStatic.php";
require $theme_functions_dir . "navs/WalkerCatalogMain.php";
require $theme_functions_dir . "navs/WalkerCatalogMega.php";
require $theme_functions_dir . "navs/WalkerCatalogVertical.php";
require $theme_functions_dir . "navs/WalkerCatalogMegaVertical.php";
require $theme_functions_dir . "navs/WalkerHeaderNavbar.php";
require $theme_functions_dir . "navs/WalkerCatalogCards.php";
require $theme_functions_dir . "navs/WalkerCatalogCarousel.php";
require $theme_functions_dir . "admin/default-options/default-options.php";
require $theme_functions_dir . "admin/default-options/default-options-background.php";
require $theme_functions_dir . "admin/default-options/default-options-css-variables.php";
require $theme_functions_dir . "admin/default-options/default-options-social-follow.php";
require $theme_functions_dir . "admin/default-options/default-options-social-share.php";
require $theme_functions_dir . "helpers.php";
require $theme_functions_dir . "ajax.php";
/**
 * Customizer
 */
require $theme_functions_dir . "custom-css.php";
/**
 * Widgets
 */
require $theme_functions_dir . "widgets/premmerce-recent-post.php";
require $theme_functions_dir . "widgets/premmerce-user-menu.php";
/**
 * Theme Admin
 */
if ( current_user_can( 'manage_options' ) ) {
    require $theme_functions_dir . '/admin/admin.php';
}