<?php

if ( class_exists( 'WP_Customize_Section' ) ) {
    class Saleszone_Customizer_Pro_Section extends WP_Customize_Section
    {
        function __construct( $manager, $id, $args = array() )
        {
            $manager->register_section_type( 'Saleszone_Customizer_Pro_Section' );
            parent::__construct( $manager, $id, $args );
        }
        
        /**
         * The type of customize section being rendered.
         *
         * @since  1.0.0
         * @access public
         * @var    string
         */
        public  $type = 'premmerce-support-section' ;
        /**
         * Add custom parameters to pass to the JS via JSON.
         *
         * @since  1.0.0
         */
        public function json()
        {
            $json = parent::json();
            $json['theme_title'] = wp_get_theme()->get( 'Name' );
            $json['go_premium_url'] = __( 'https://premmerce.com/saleszone/', 'saleszone' );
            return $json;
        }
        
        /**
         * Outputs the Underscore.js template.
         *
         * @since  1.0.0
         */
        protected function render_template()
        {
            ?>
            <li id="fs_customizer_support"
                class="accordion-section control-section control-section-{{ data.type }} cannot-expand">
                <h3 class="accordion-section-title">
                    <span>
                        {{ data.theme_title }}
                    </span>
                    <a href="{{ data.go_premium_url }}" class="button button-secondary" target="_blank">
                        <?php 
            esc_html_e( 'Upgrade to Premium', 'saleszone' );
            ?>
                    </a>
                </h3>
            </li>
            <?php 
        }
    
    }
    /**
     * Add go premium section
     */
    add_action( 'customize_register', 'saleszone_pro_section_customize_register' );
    if ( !function_exists( 'saleszone_pro_section_customize_register' ) ) {
        function saleszone_pro_section_customize_register( $customizer )
        {
            $customizer->add_section( new Saleszone_Customizer_Pro_Section( $customizer, 'saleszone_pro', array(
                'priority' => 1,
            ) ) );
        }
    
    }
}
