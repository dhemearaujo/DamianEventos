<?php

if(!function_exists('saleszone_tgmpa_config')){
    function saleszone_tgmpa_config(){
        $plugins = array(
            array(
                'name'     				=> 'WooCommerce',
                'slug'     				=> 'woocommerce',
                'version' 				=> '3.3.1 ',
                'required'              => false
            ),
            array(
                'name'     				=> 'Kirki Toolkit',
                'slug'     				=> 'kirki',
                'version' 				=> '3.0.25 ',
                'required'              => false
            ),
            array(
                'name'     				=> 'MetaSlider',
                'slug'     				=> 'ml-slider',
                'version' 				=> '3.7.2',
                'required'              => false
            ),
        );

        if(!saleszone_is_plugin_active('premmerce-premium/premmerce.php')){
            $plugins[] = array(
                'name'     				=> 'Premmerce',
                'slug'     				=> 'premmerce',
                'version' 				=> '1.3',
                'required'              => false
            );
        }

        $config = array(
            'is_automatic' => true,
            'parent_slug' => 'saleszone'
        );

        tgmpa( $plugins, $config );
    }
}
add_action('tgmpa_register', 'saleszone_tgmpa_config');