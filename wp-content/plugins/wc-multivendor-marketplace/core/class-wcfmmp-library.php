<?php

/**
 * WCFMmp plugin library
 *
 * Plugin intiate library
 *
 * @author 		WC Lovers
 * @package 	wcfmmp/core
 * @version   1.0.0
 */
 
class WCFMmp_Library {
	
	public $lib_path;
  
  public $lib_url;
  
  public $php_lib_path;
  
  public $php_lib_url;
  
  public $js_lib_path;
  
  public $js_lib_url;
  
  public $css_lib_path;
  
  public $css_lib_url;
  
  public $views_path;
	
	public function __construct() {
    global $WCFMmp;
		
	  $this->lib_path = $WCFMmp->plugin_path . 'assets/';

    $this->lib_url = $WCFMmp->plugin_url . 'assets/';
    
    $this->php_lib_path = $this->lib_path . 'php/';
    
    $this->php_lib_url = $this->lib_url . 'php/';
    
    $this->js_lib_path = $this->lib_path . 'js/';
    
    $this->js_lib_url = $this->lib_url . 'js/';
    
    $this->js_lib_url_min = $this->js_lib_url . 'min/';
    
    $this->css_lib_path = $this->lib_path . 'css/';
    
    $this->css_lib_url = $this->lib_url . 'css/';
    
    $this->css_lib_url_min = $this->css_lib_url . 'min/';
    
    $this->views_path = $WCFMmp->plugin_path . 'views/';
    
    // Load wcfmmp Scripts
    add_action( 'wcfm_load_scripts', array( &$this, 'load_scripts' ) );
    add_action( 'after_wcfm_load_scripts', array( &$this, 'load_scripts' ) );
    
    // Load wcfmmp Styles
    add_action( 'wcfm_load_styles', array( &$this, 'load_styles' ) );
    
    // Load wcfmmp views
    add_action( 'wcfm_load_views', array( &$this, 'load_views' ) );
  }
  
  public function load_scripts( $end_point ) {
	  global $WCFM, $WCFMmp;
    
	  switch( $end_point ) {
	  	
	    case 'wcfm-dashboard':
	    	
	  	break;
	  	
	  	case 'wcfm-products-manage':
	  		if( !wcfm_is_vendor() ) {
	  			wp_enqueue_script( 'wcfmmp_settings_js', $this->js_lib_url . 'wcfmmp-script-settings.js', array('jquery'), $WCFMmp->version, true );
	  		}
      break;
	  	
	  	case 'wcfm-settings':
	  		if( !wcfm_is_vendor() ) {
	  			wp_enqueue_script( 'wcfmmp_settings_js', $this->js_lib_url . 'wcfmmp-script-settings.js', array('jquery'), $WCFMmp->version, true );
	  			
	  			$wcfm_store_color_setting_options = $WCFMmp->wcfmmp_settings->wcfmmp_store_color_setting_options();
					wp_localize_script( 'wcfmmp_settings_js', 'wcfm_store_color_setting_options', $wcfm_store_color_setting_options );
	  		}
      break;
      
      case 'wcfm-memberships-manage':
      case 'wcfm-vendors-manage':     
      case 'wcfm-vendors-new': 
	  		if( !wcfm_is_vendor() ) {
	  			$WCFM->library->load_multiinput_lib();
	  			wp_enqueue_script( 'wcfmmp_settings_js', $this->js_lib_url . 'wcfmmp-script-settings.js', array('jquery'), $WCFMmp->version, true );
	  		}
      break;
	  	
    }
  }
  
  public function load_styles( $end_point ) {
	  global $WCFM, $WCFMmp;
		
	  switch( $end_point ) {
	  	
	  	case 'wcfm-memberships-manage':
	    	// wp_enqueue_style( 'wcfm_settings_css',  $WCFM->library->css_lib_url . 'settings/wcfm-style-settings.css', array(), $WCFM->version );
		  break;
		}
	}
	
	public function load_views( $end_point ) {
	  global $WCFM, $WCFMmp;
	  
	  switch( $end_point ) {
	  	
	  	case 'wcfm-analytics':
        //$WCFMmp->template->get_template( 'wcfmmp-view-analytics.php' );
      break;
    }
  }
}