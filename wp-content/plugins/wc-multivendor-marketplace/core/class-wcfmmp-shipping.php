<?php
/**
 * WCFMmp plugin core
 *
 * WCfMmp Shipping
 *
 * @author 		WC Lovers
 * @package 	wcfmmp/core
 * @version   1.0.0
 */
 
class WCFMmp_Shipping {
	
	public function __construct() {
		global $WCFM, $WCFMmp;
    //Load Vendor Shipping Settings
		add_action( 'wcfm_marketplace_shipping', array( &$this, 'wcfmmp_load_shipping_view' ) );
    
    //Save Vendor Shipping Settings
    add_action('wcfm_vendor_settings_update' , array( &$this,'wcfmmp_vendor_shipping_settings_update' ), 10, 2 );
    
    // split woocommerce shipping packages
    add_filter('woocommerce_cart_shipping_packages', array(&$this, 'wcfmmp_split_shipping_packages'), 0);

    // Add extra vendor_id to shipping packages
    add_action('woocommerce_checkout_create_order_shipping_item', array(&$this, 'wcfmmp_add_meta_date_in_shipping_package'), 10, 3);

    // Rename woocommerce shipping packages
    add_filter('woocommerce_shipping_package_name', array(&$this, 'wcfmmp_shipping_package_name'), 10, 3);

    //Hide Admin Shipping If vendor Shipping is available
    add_filter( 'woocommerce_package_rates', array(&$this, 'wcfmmp_hide_admin_shipping' ), 100 );
    
	}
	
  public function wcfmmp_load_shipping_view() {
    global $WCFM, $WCFMmp;
    $WCFMmp->template->get_template( 'shipping/wcfmmp-view-shipping-settings.php' );
  }
  
  public function wcfmmp_vendor_shipping_settings_update( $user_id, $wcfm_settings_form ) {
    
    if(!isset($wcfm_settings_form['wcfmmp_shipping']['_wcfmmp_user_shipping_enable'])) {
      $wcfm_settings_form['wcfmmp_shipping']['_wcfmmp_user_shipping_enable'] = 'no';
    }

    update_user_meta($user_id, '_wcfmmp_shipping', $wcfm_settings_form['wcfmmp_shipping']);
    
    if(!empty( $wcfm_settings_form['wcfmmp_shipping']['_wcfmmp_user_shipping_type']) && $wcfm_settings_form['wcfmmp_shipping']['_wcfmmp_user_shipping_type'] == 'by_country') {
      //print_r($wcfm_settings_form);
      if(isset($wcfm_settings_form['wcfmmp_shipping_by_country'])) {
        update_user_meta($user_id, '_wcfmmp_shipping_by_country', $wcfm_settings_form['wcfmmp_shipping_by_country']);
      }
      // Shipping Rates
      if(isset($wcfm_settings_form['wcfmmp_shipping_rates']) && !empty($wcfm_settings_form['wcfmmp_shipping_rates'])) {
        $wcfmmp_country_rates = array();
        $wcfmmp_state_rates   = array(); 
        foreach( $wcfm_settings_form['wcfmmp_shipping_rates'] as $wcfmmp_shipping_rates ) {
          if( $wcfmmp_shipping_rates['wcfmmp_country_to'] ) {
            if( $wcfmmp_shipping_rates['wcfmmp_shipping_state_rates'] && !empty( $wcfmmp_shipping_rates['wcfmmp_shipping_state_rates'] ) ) {
              foreach( $wcfmmp_shipping_rates['wcfmmp_shipping_state_rates'] as $wcfmmp_shipping_state_rates ) {
                if( $wcfmmp_shipping_state_rates['wcfmmp_state_to'] ) {
                  $wcfmmp_state_rates[$wcfmmp_shipping_rates['wcfmmp_country_to']][$wcfmmp_shipping_state_rates['wcfmmp_state_to']] = $wcfmmp_shipping_state_rates['wcfmmp_state_to_price'];
                }
              }
            }
            $wcfmmp_country_rates[$wcfmmp_shipping_rates['wcfmmp_country_to']] = $wcfmmp_shipping_rates['wcfmmp_country_to_price'];
          }
        }
        update_user_meta( $user_id, '_wcfmmp_country_rates', $wcfmmp_country_rates );
        update_user_meta( $user_id, '_wcfmmp_state_rates', $wcfmmp_state_rates );
      }
    }
    
    if( !empty( $wcfm_settings_form['wcfmmp_shipping']['_wcfmmp_user_shipping_type'] ) && $wcfm_settings_form['wcfmmp_shipping']['_wcfmmp_user_shipping_type'] == 'by_zone') {

      //print_r($wcfm_settings_form);
      $all_allowed_countries = WC()->countries->get_allowed_countries();
      $location = array();
      $zone_id = 0;
      //print_r($all_allowed_countries);
      if(!empty($wcfm_settings_form['wcfmmp_shipping_zone'])) {
        foreach( $wcfm_settings_form['wcfmmp_shipping_zone'] as $shipping_zone ) {
          if( isset($shipping_zone['_zone_id'] ) && $shipping_zone['_zone_id'] != 0 ) {
            $zone_id = $shipping_zone['_zone_id'];
            if(isset( $shipping_zone['_limit_zone_location'] ) && $shipping_zone['_limit_zone_location'] ) {
              if(!empty($shipping_zone['_select_zone_country'])) {
                $country_array = array();
                foreach ( $shipping_zone['_select_zone_country'] as $zone_country ) {
                  $country_array[] = array(
                    'code' => $zone_country,
                    'type'  => 'country'
                  );
                }
                $location = array_merge( $location, $country_array );
              }
              if(!empty($shipping_zone['_select_zone_states'])) {
                $state_array = array();
                foreach ( $shipping_zone['_select_zone_states'] as $zone_state ) {
                  $state_array[] = array(
                    'code' => $zone_state,
                    'type'  => 'state'
                  );
                }
                $location = array_merge( $location, $state_array );
              }
                            
              if(!empty($shipping_zone['_select_zone_city'])) {
                $city_array = array();
                foreach ( $shipping_zone['_select_zone_city'] as $zone_city ) {
                  $city_array[] = array(
                    'code' => $zone_city,
                    'type'  => 'city'
                  );
                }
                $location = array_merge( $location, $city_array );
              }
              
              if(!empty($shipping_zone['_select_zone_postcodes'])) {
                $postcode_array = array();
                $zone_postcodes = array_map('trim', explode(',', $shipping_zone['_select_zone_postcodes'] ));
                foreach ( $zone_postcodes as $zone_postcode ) {
                  $postcode_array[] = array(
                    'code' => $zone_postcode,
                    'type'  => 'postcode'
                  );
                }
                $location = array_merge( $location, $postcode_array );
              }
              
            }
          }
        }
        //print_r($location);
      }
      WCFMmp_Shipping_Zone::save_location( $location, $zone_id );
    }
    
  }
  
  /**
    * split woocommerce shipping packages 
    * @since 1.0.0
    * @param array $packages
    * @return array
    */
  public function wcfmmp_split_shipping_packages($packages) {
    // Reset all packages
    global $WCFM;
    $packages = array();
    $split_packages = array();
    foreach (WC()->cart->get_cart() as $item) {
        if ($item['data']->needs_shipping()) {
            $product_id = $item['product_id'];

            $vendor_id = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $product_id );
            if ($vendor_id && wcfmmp_is_shipping_enabled($vendor_id)) {
                $split_packages[$vendor_id][] = $item;
            } else {
                $split_packages[0][] = $item;
            }
        }
    }

    foreach ($split_packages as $vendor_id => $split_package) {
			$packages[$vendor_id] = array(
				'contents' => $split_package,
				'contents_cost' => array_sum(wp_list_pluck($split_package, 'line_total')),
				'applied_coupons' => WC()->cart->get_applied_coupons(),
				'user' => array(
						'ID' => apply_filters( 'wcfm_current_vendor_id', get_current_user_id() ),
				),
				'destination' => array(
						'country' => WC()->customer->get_shipping_country(),
						'state' => WC()->customer->get_shipping_state(),
						'postcode' => WC()->customer->get_shipping_postcode(),
						'city' => WC()->customer->get_shipping_city(),
						'address' => WC()->customer->get_shipping_address(),
						'address_2' => WC()->customer->get_shipping_address_2()
				),
				'vendor_id' => $vendor_id
			);
    }
    return apply_filters('wcfmmp_split_shipping_packages', $packages);
  }
  
  /**
   * 
   * @param object $item
   * @param sting $package_key as $vendor_id
   */
  public function wcfmmp_add_meta_date_in_shipping_package($item, $package_key, $package) {
		$item->add_meta_data('vendor_id', $package_key, true);
		$package_qty = array_sum(wp_list_pluck($package['contents'], 'quantity'));
		$item->add_meta_data('package_qty', $package_qty, true);
		do_action('wcfmmp_add_shipping_package_meta_data');
  }
  
  /**
     * Rename shipping packages 
     * @since 1.0.0
     * @param string $package_name
     * @param string $vendor_id
     * @param array $package
     * @return string
     */
  public function wcfmmp_shipping_package_name($package_name, $vendor_id, $package) {
  	global $WCFM, $WCFMmp;
  	
  	if( $WCFMmp->wcfmmp_vendor->is_vendor_sold_by() ) {
			$zone = WC_Shipping_Zones::get_zone_matching_package( $package );
			$shipping_methods = WCFMmp_Shipping_Zone::get_shipping_methods( $zone->get_id(), $vendor_id );
			$is_available = false;
			$vendor_shipping_details = get_user_meta( $vendor_id, '_wcfmmp_shipping', true );
			$type = $vendor_shipping_details['_wcfmmp_user_shipping_type'];
			foreach ( $shipping_methods as $key => $method ) {
				if ( 'free_shipping' == $method['id'] && 'yes' == $method['enabled'] ) {
					$is_available = true;
					$min_amount = (isset( $method['settings']['min_amount'] ) ) ? $method['settings']['min_amount'] : 0;
				}
			}
			if ($vendor_id && $vendor_id != 0) {
				$Store_name = $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_name_by_vendor( $vendor_id );
				$more_to_free_shipping_text_enable = apply_filters('more_to_free_shipping_text_enable', true);
				if( ( !empty($type) && $type != 'by_country' ) && $more_to_free_shipping_text_enable && $is_available && ( ( $min_amount > 0 ) && ($package['contents_cost'] < $min_amount ) ) ) {
					return sprintf( __ ('%s Shipping <br />( Shop for %s%d more to get free shipping. )', 'wc-multivendor-marketplace'), $Store_name , get_woocommerce_currency_symbol(), ( $min_amount - $package['contents_cost'] ) );
				}
				return $Store_name . ' ' . __('Shipping', 'wc-multivendor-marketplace');
			}
		}
		return $package_name;
	}
	
	/**
	 * Rename vendor shipping for an order 
	 * @since 1.0.0
	 * @param object $order
	 * @return array
	 */
	public function get_order_vendor_shipping( $order ) {
		global $WCFM, $WCFMmp;
		
		$vendor_shipping = array();
		
		if( !$order ) return $vendor_shipping;
		
		if( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}
		
		$shipping_items = $order->get_items('shipping');
		foreach ($shipping_items as $shipping_item_id => $shipping_item) {
			$order_item_shipping = new WC_Order_Item_Shipping($shipping_item_id);
			$shipping_vendor_id = $order_item_shipping->get_meta('vendor_id', true);
			if( !$shipping_vendor_id ) $shipping_vendor_id = 0;
			$vendor_shipping[$shipping_vendor_id] = array(
					  'shipping'            => $order_item_shipping->get_total()
					, 'shipping_tax'        => $order_item_shipping->get_total_tax()
					, 'package_qty'         => $order_item_shipping->get_meta('package_qty', true)
					, 'shipping_item_id'    => $shipping_item_id
			);
		}
		return $vendor_shipping;
	}

  /**
   * Hide Admin Shipping If vendor Shipping is available callback
   * @since 1.0.2
   * @param array $rates
   * @return array
   */
  public function wcfmmp_hide_admin_shipping( $rates ) {
    //print_r($rates); die;
    $free_shipping_available = false;
    $wcfmmp_shipping = array();
    foreach ( $rates as $rate_id => $rate ) {
      if ( 'wcfmmp_product_shipping_by_country' === $rate->method_id || 'wcfmmp_product_shipping_by_zone' === $rate->method_id ) {
        $id = explode(":", $rate_id, 2);
        $id = $id[0];
        if($id === 'free_shipping') {
          $free_shipping_available = apply_filters('hide_other_shipping_if_free', false );
        }
        $wcfmmp_shipping[ $rate_id ] = $rate;  
      }
    }
    if($free_shipping_available) {
      foreach ( $wcfmmp_shipping as $rate_id => $rate ) { 
        $id = explode(":", $rate_id, 2);
        $id = $id[0];
        if($id !== 'free_shipping') {
          unset($wcfmmp_shipping[$rate_id]);
        }
      }
    }
    return ! empty( $wcfmmp_shipping ) ? $wcfmmp_shipping : $rates;
  }
  
}