<?php
/**
 * ed_woo_loader Class
 *
 * @package Woo_Table_Product_View
 * @author Saiful ( edatastyle )
 * @since 1.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

class woo_product_table_view_loader {
	 /**
	 * Instance of the Action Hook.
	 *
	 * @since 1.0.0
	 * @var array
	 */
    protected $actions;
 	 /**
	 * Instance of the filter Hook.
	 *
	 * @since 1.0.0
	 * @var array
	 */
    protected $filters;
 
    public function __construct() {
 
        $this->actions = array();
        $this->filters = array();
     
    }
 
    public function add_action( $hook, $component, $callback ) {
        $this->actions = $this->add( $this->actions, $hook, $component, $callback );
    }
 
    public function add_filter( $hook, $component, $callback ) {
        $this->filters = $this->add( $this->filters, $hook, $component, $callback );
    }
 
    private function add( $hooks, $hook, $component, $callback ) {
 
        $hooks[] = array(
            'hook'      => $hook,
            'component' => $component,
            'callback'  => $callback
        );
 
        return $hooks;
 
    }
 
    public function run() {
 
        foreach ( $this->filters as $hook ) {
            add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ) );
        }
 
        foreach ( $this->actions as $hook ) {
            add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ) );
        }
 
    }
 
}
