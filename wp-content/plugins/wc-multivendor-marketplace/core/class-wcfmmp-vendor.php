<?php
/**
 * WCFMmp plugin core
 *
 * WCfMmp Vendor
 *
 * @author 		WC Lovers
 * @package 	wcfmmp/core
 * @version   1.0.0
 */
 
class WCFMmp_Vendor {
	
	public function __construct() {
		global $WCFM;
		
		if( !wcfm_is_vendor() ) {
			// Vendor Details Page
			add_action( 'begin_wcfm_vendors_new_form', array( &$this, 'wcfmmp_vendor_manage_marketplace_setting' ) );
			add_action( 'end_wcfm_vendors_manage_form', array( &$this, 'wcfmmp_vendor_manage_marketplace_setting' ) );
			
			// Bullk Store Assign
			add_action( 'woocommerce_product_bulk_edit_end', array( &$this, 'wcfmmp_bulk_store_edit' ) );
			add_action( 'wcfm_product_bulk_edit_end', array( &$this, 'wcfmmp_bulk_store_edit' ) );
			add_action( 'woocommerce_product_bulk_edit_save', array( &$this, 'wcfmmp_bulk_store_edit_save' ) );
			add_action( 'wcfm_product_bulk_edit_save', array( &$this, 'wcfmmpu_bulk_store_edit_save' ), 10, 2 );
		}
		
		// Vendor Profile Additional Info
		if( wcfm_is_vendor() ) {
			add_action( 'end_wcfm_user_profile', array( &$this, 'wcfmmp_profile_additional_info' ), 75 );
			add_action( 'wcfm_profile_update', array( &$this, 'wcfmmp_profile_additional_info_update' ), 75, 2 );
		}
		
		// Enable Vendor Order Email Notification
		//add_filter( 'wcfm_is_allow_order_notification_email', array( &$this, 'wcfmmp_is_allow_order_notification_email' ) );
		
		// Vendor Withdrawal Request Auto Apptove
		add_filter( 'wcfmmp_is_withdrawal_auto_approve', array( &$this, 'wcfmmp_is_vendor_withdrawal_auto_approve' ), 10, 2 );
		
		// Vendor Withdrawal Limit
		add_filter( 'wcfmmp_withdrawal_limit', array( &$this, 'wcfmmp_vendor_withdrawal_limit' ), 10, 2 );
		
		// Vendor Withdrawal Thresold
		add_filter( 'wcfmmp_withdrawal_thresold', array( &$this, 'wcfmmp_vendor_withdrawal_thresold' ), 10, 2 );
		
		// Vendor Withdrawal Charges
		add_filter( 'wcfmmp_withdrawal_charges', array( &$this, 'wcfmmp_charges_withdrawal_charges' ), 10, 3 );
		
		// Modify Vendor Order Status List
		add_filter( 'wcfm_allowed_order_status',  array( &$this, 'wcfmmp_allowed_order_status' ) );
		
		// Vendor Order Current Status
		add_filter( 'wcfm_current_order_status', array( &$this, 'wcfmmp_vendor_current_order_status' ), 10, 2 );
		
		// Modify Vendor Orders Menu
		add_filter( 'wcfmu_orders_menus',  array( &$this, 'wcfmmp_orders_menus' ) );
		
		// Vendor Ledger update on order process
		add_action( 'wcfmmp_order_item_processed', array( &$this, 'wcfmmp_order_item_processed_ledger_update' ), 10, 9 );
		
		// Vednor Ledger update on withdraw request process
		add_action( 'wcfmmp_withdraw_request_processed', array( &$this, 'wcfmmp_withdraw_request_processed_ledger_update' ), 10, 9 );
		
		// Vednor Ledger update on reverse withdraw request process
		add_action( 'wcfmmp_reverse_withdraw_request_processed', array( &$this, 'wcfmmp_reverse_withdraw_request_processed_ledger_update' ), 10, 10 );
		
		// Vednor Ledger update on refund request process
		add_action( 'wcfmmp_refund_request_processed', array( &$this, 'wcfmmp_refund_request_processed_ledger_update' ), 10, 6 );
		
		// Vendor Details In Order Eamail
		add_action( 'woocommerce_order_details_after_order_table', array( &$this, 'wcfmmp_vendor_details_in_order' ) );
		add_action( 'woocommerce_email_order_meta', array( &$this, 'wcfmmp_vendor_details_in_order' ) );
		
		// Store Info In Order Details Item
		add_action( 'woocommerce_display_item_meta', array( &$this, 'wcfmmp_order_item_meta_store' ), 10, 3 );
		
		// Store Off Line Store List Action
		add_filter( 'wcfm_vendors_actions', array( &$this, 'wcfmmp_vendors_actions' ), 50, 2 );
		
		// Store Purchase Disable if Store Offline
		add_filter( 'woocommerce_is_purchasable', array( &$this, 'wcfmmp_product_store_is_offline' ), 750, 2 );
		
		// Load Vendor Store Setup widget on first login
		add_action( 'template_redirect', array( &$this, 'wcfmmp_store_setup_on_first_login' ), 750 );
		
		// Vendor Profile complete percent
		add_action( 'before_wcfm_marketplace_settings', array( &$this, 'wcfmmp_vendor_profile_complete_percent' ) );
		
	}
	
	public function get_vendor_name_position( $vendor_id ) {
		global $WCFM, $WCFMmp;
		$vendor_data = get_user_meta( $vendor_id, 'wcfmmp_profile_settings', true );
		$global_store_name_position = isset( $WCFMmp->wcfmmp_marketplace_options['store_name_position'] ) ? $WCFMmp->wcfmmp_marketplace_options['store_name_position'] : 'on_banner';
		$store_name_position = isset( $vendor_data['store_name_position'] ) ? esc_attr( $vendor_data['store_name_position'] ) : $global_store_name_position;
		return $store_name_position;
	}
	
	/**
	 * Return is show store sidebar
	 * @return boolean
	 */
	public function is_store_sidebar() {
		global $WCFM, $WCFMmp;
		$store_sidebar = isset( $WCFMmp->wcfmmp_marketplace_options['store_sidebar'] ) ? $WCFMmp->wcfmmp_marketplace_options['store_sidebar'] : 'yes';
		if( $store_sidebar == 'yes' ) return true;
		return false;
	}
	
	/**
	 * Return is show sold by label
	 * @return boolean
	 */
	public function is_vendor_sold_by() {
		global $WCFM, $WCFMmp;
		$vendor_sold_by = isset( $WCFMmp->wcfmmp_marketplace_options['vendor_sold_by'] ) ? $WCFMmp->wcfmmp_marketplace_options['vendor_sold_by'] : 'yes';
		if( $vendor_sold_by == 'yes' ) return true;
		return false;
	}
	
	public function get_vendor_sold_by_template() {
		global $WCFM, $WCFMmp;
		$vendor_sold_by_template = isset( $WCFMmp->wcfmmp_marketplace_options['vendor_sold_by_template'] ) ? $WCFMmp->wcfmmp_marketplace_options['vendor_sold_by_template'] : 'advanced';
		return $vendor_sold_by_template;
	}
	
	public function sold_by_label( $sold_by_text = '' ) {
		return apply_filters( 'wcfmmp_sold_by_label', __('Store', 'wc-multivendor-marketplace') );
	}
	
	/**
	 * Return vendor's payment method
	 */
	public function get_vendor_payment_method( $vendor_id = 0 ) {
		global $WCFM, $WCFMmp;
		
		if( !$vendor_id ) {
			$vendor_id = $WCFMmp->vendor_id;
		}
		$vendor_data = get_user_meta( $vendor_id, 'wcfmmp_profile_settings', true );
		$payment_method = isset( $vendor_data['payment']['method'] ) ? esc_attr( $vendor_data['payment']['method'] ) : '' ;
		return $payment_method;
	}
	
	/**
	 * Return vendor's Payment Email
	 */
	public function get_vendor_payment_account( $vendor_id = 0, $account = 'paypal' ) {
		global $WCFM, $WCFMmp;
		
		if( !$vendor_id ) {
			$vendor_id = $WCFMmp->vendor_id;
		}
		$vendor_data = get_user_meta( $vendor_id, 'wcfmmp_profile_settings', true );
		$account_email = isset( $vendor_data['payment'][$account]['email'] ) ? esc_attr( $vendor_data['payment'][$account]['email'] ) : '' ;
		return $account_email;
	}
	
	/**
	 * Return vendor's Bank Detais
	 */
	public function get_vendor_bank_details( $vendor_id = 0 ) {
		global $WCFM, $WCFMmp;
		
		if( !$vendor_id ) {
			$vendor_id = $WCFMmp->vendor_id;
		}
		$vendor_data = get_user_meta( $vendor_id, 'wcfmmp_profile_settings', true );
		$bank_details = isset( $vendor_data['payment']['bank'] ) ? $vendor_data['payment']['bank'] : array();
		return $bank_details;
	}
	
	public function wcfmmp_get_vendor_billing_details( $vendor_id, $billing_option ) {
  	
  	if( !$vendor_id ) return;
  	if( !$billing_option ) return;
  	
  	if( $billing_option == 'bank_transfer' ) $billing_option = 'bank';
  	
  	$vendor_data = get_user_meta( $vendor_id, 'wcfmmp_profile_settings', true );
  	if( !$vendor_data ) $vendor_data = array();
  	
  	$billing_details  = isset( $vendor_data['payment'][$billing_option] ) ? $vendor_data['payment'][$billing_option] : array();
  	$billing_details  = implode( ", ", $billing_details );
  	
  	return $billing_details;
  }
	
	/**
	 * Enable New Order Email Notification to Vendors
	 */
	function wcfmmp_is_allow_order_notification_email( $is_allow ) {
		return true;
	}
	
	/**
	 * Vendor Withdrawal Request Auto Approve
	 */
	function wcfmmp_is_vendor_withdrawal_auto_approve( $is_auto_approve, $vendor_id ) {
		global $WCFM, $WCFMmp;
		
		if( $vendor_id ) {
			$vendor_data = get_user_meta( $vendor_id, 'wcfmmp_profile_settings', true );
			$vendor_withdrawal_mode = isset( $vendor_data['withdrawal']['withdrawal_mode'] ) ? $vendor_data['withdrawal']['withdrawal_mode'] : 'global';
			if( $vendor_withdrawal_mode != 'global' ) {
				$is_auto_approve = isset( $vendor_data['withdrawal']['request_auto_approve'] ) ? $vendor_data['withdrawal']['request_auto_approve'] : 'no';
				if( $is_auto_approve == 'yes' ) $is_auto_approve = true;
				else $is_auto_approve = false;
			}
		}
		return $is_auto_approve;
	}
	
	/**
	 * Vendor Withdrawal Limit
	 */
	function wcfmmp_vendor_withdrawal_limit( $withdrawal_limit, $vendor_id ) {
		global $WCFM, $WCFMmp;
		
		if( $vendor_id ) {
			$vendor_data = get_user_meta( $vendor_id, 'wcfmmp_profile_settings', true );
			$vendor_withdrawal_mode = isset( $vendor_data['withdrawal']['withdrawal_mode'] ) ? $vendor_data['withdrawal']['withdrawal_mode'] : 'global';
			if( $vendor_withdrawal_mode != 'global' ) {
				$withdrawal_limit = isset( $vendor_data['withdrawal']['withdrawal_limit'] ) ? $vendor_data['withdrawal']['withdrawal_limit'] : 0;
			}
		}
		return $withdrawal_limit;
	}
	
	/**
	 * Vendor Withdrawal Thresold
	 */
	function wcfmmp_vendor_withdrawal_thresold( $withdrawal_thresold, $vendor_id ) {
		global $WCFM, $WCFMmp;
		
		if( $vendor_id ) {
			$vendor_data = get_user_meta( $vendor_id, 'wcfmmp_profile_settings', true );
			$vendor_withdrawal_mode = isset( $vendor_data['withdrawal']['withdrawal_mode'] ) ? $vendor_data['withdrawal']['withdrawal_mode'] : 'global';
			if( $vendor_withdrawal_mode != 'global' ) {
				$withdrawal_thresold = isset( $vendor_data['withdrawal']['withdrawal_thresold'] ) ? $vendor_data['withdrawal']['withdrawal_thresold'] : '';
			}
		}
		return $withdrawal_thresold;
	}
	
	/**
	 * Vendor Withdrawal Charges
	 */
	function wcfmmp_charges_withdrawal_charges( $withdrawal_charges, $amount, $vendor_id ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		if( $vendor_id && $amount ) {
			$payment_method = $WCFMmp->wcfmmp_vendor->get_vendor_payment_method( $vendor_id );
			if( $payment_method ) {
				$vendor_data = get_user_meta( $vendor_id, 'wcfmmp_profile_settings', true );
				$vendor_withdrawal_mode = isset( $vendor_data['withdrawal']['withdrawal_mode'] ) ? $vendor_data['withdrawal']['withdrawal_mode'] : 'global';
				if( $vendor_withdrawal_mode != 'global' ) {
					
					$withdrawal_charge_type = isset( $vendor_data['withdrawal']['withdrawal_charge_type'] ) ? $vendor_data['withdrawal']['withdrawal_charge_type'] : 'no';
					
					$vendor_withdrawal_charge   = isset( $vendor_data['withdrawal']['withdrawal_charge'] ) ? $vendor_data['withdrawal']['withdrawal_charge'] : array();
					$withdrawal_charge_gateway  = isset( $vendor_withdrawal_charge[$payment_method] ) ? $vendor_withdrawal_charge[$payment_method][0] : array();
					$withdrawal_percent_charge  = isset( $withdrawal_charge_gateway['percent'] ) ? $withdrawal_charge_gateway['percent'] : 0;
					$withdrawal_fixed_charge    = isset( $withdrawal_charge_gateway['fixed'] ) ? $withdrawal_charge_gateway['fixed'] : 0;
					$withdrawal_charge_tax      = isset( $withdrawal_charge_gateway['tax'] ) ? $withdrawal_charge_gateway['tax'] : 0;
					
					switch( $withdrawal_charge_type ) {
						case 'no':
							$withdrawal_charges = 0;
						break;
						
						case 'fixed':
							$withdrawal_charges = (float) $withdrawal_fixed_charge;
						break;
						
						case 'percent':
							$withdrawal_charges = (float) $amount * ( (float) $withdrawal_percent_charge/100 );
						break;
						
						case 'percent_fixed':
							$withdrawal_charges  = (float) $amount * ( (float) $withdrawal_percent_charge/100 );
							$withdrawal_charges += (float) $withdrawal_fixed_charge;
						break;
						
						default:
							$withdrawal_charges = 0;
						break;
					}
					
					if( $withdrawal_charges && $withdrawal_charge_tax ) {
						$withdrawal_tax      = (float) $withdrawal_charges * ( (float) $withdrawal_charge_tax/100 );
						$withdrawal_charges += (float) $withdrawal_tax;
					}
					
					if( $withdrawal_charges ) {
						$withdrawal_charges = round( $withdrawal_charges, 2 );
					}
				}
			}
		}
		return $withdrawal_charges;
		
	}
	
	/**
	 * Modify Vendor's order status list
	 */
	function wcfmmp_allowed_order_status( $order_status ) {
		if( wcfm_is_vendor() ) {
			if( WCFMmp_Dependencies::wcfm_plugin_active_check() && WCFM_Dependencies::wcfmu_plugin_active_check() ) {
				$order_vendor_status = apply_filters( 'wcfmmp_vednor_order_status',
																							array(
																								'wc-shipped' => __( 'Shipped', 'wc-multivendor-marketplace' )
																								)
																						);
				$order_status = array_merge( $order_status, $order_vendor_status );
			}
			
			//if( isset( $order_status['wc-refunded'] ) ) unset( $order_status['wc-refunded'] );
			if( isset( $order_status['wc-cancelled'] ) ) unset( $order_status['wc-cancelled'] );
			if( isset( $order_status['wc-failed'] ) ) unset( $order_status['wc-failed'] );
		}
		return $order_status;
	}
	
	/**
	 * Return vendor order current status
	 */
	function wcfmmp_vendor_current_order_status( $order_status, $order_id ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		$vendor_id = $WCFMmp->vendor_id;
		if( wcfm_is_vendor() ) {
			$sql = 'SELECT commission_status  FROM ' . $wpdb->prefix . 'wcfm_marketplace_orders AS commission';
			$sql .= ' WHERE 1=1';
			$sql .= " AND `order_id` = " . $order_id;
			$sql .= " AND `vendor_id` = " . $vendor_id;
			$commissions = $wpdb->get_results( $sql );
			$product_id = 0;
			if( !empty( $commissions ) ) {
				foreach( $commissions as $commission ) {
					$order_status = $commission->commission_status;
				}
			}
		}
		return apply_filters( 'wcfmmp_vendor_current_order_status', $order_status, $order_id, $vendor_id );
	}
	
	/**
	 * Modify Vendor's orders menu
	 */
	function wcfmmp_orders_menus( $order_menus ) {
		if( wcfm_is_vendor() ) {
			$order_vendor_menus = apply_filters( 'wcfmmp_vednor_order_menus',
																						array(
																							'pending' => __( 'Pending', 'wc-multivendor-marketplace' ),
																							'shipped' => __( 'Shipped', 'wc-multivendor-marketplace' )
																							)
																					);
			$order_menus = array_merge( $order_menus, $order_vendor_menus );
			if( isset( $order_menus['cancelled'] ) ) unset( $order_menus['cancelled'] );
			if( isset( $order_menus['failed'] ) ) unset( $order_menus['failed'] );
		}
		return $order_menus;
	}
	
	public function wcfmmp_vendor_order_status_name( $order_ststus ) {
		$order_vendor_status = $this->wcfmmp_allowed_order_status( wc_get_order_statuses() );
		if( isset( $order_vendor_status[$order_ststus] ) ) return $order_vendor_status[$order_ststus];
		if( isset( $order_vendor_status['wc-'.$order_ststus] ) ) return $order_vendor_status['wc-'.$order_ststus];
		return ucfirst( $order_ststus );
	}
	
	/**
	 * Vednor Store Setting
	 */
	function wcfmmp_vendor_manage_marketplace_setting( $vendor_id ) {
		global $WCFM, $WCFMmp;
		
		if( !$vendor_id ) return;
		
		$disable_vendor = get_user_meta( $vendor_id, '_disable_vendor', true );
		if( $disable_vendor ) return;
		
		$vendor_data = get_user_meta( $vendor_id, 'wcfmmp_profile_settings', true );
		if( !$vendor_data ) $vendor_data = array();
		
		$the_vendor_user = get_user_by( 'id', $vendor_id );

		// Store Genral
		$gravatar          = isset( $vendor_data['gravatar'] ) ? absint( $vendor_data['gravatar'] ) : 0;
		$banner_type       = isset( $vendor_data['banner_type'] ) ? $vendor_data['banner_type'] : 'single_img';
		$banner            = isset( $vendor_data['banner'] ) ? absint( $vendor_data['banner'] ) : 0;
		$banner_video      = isset( $vendor_data['banner_video'] ) ? $vendor_data['banner_video'] : '';
		$banner_slider     = isset( $vendor_data['banner_slider'] ) ? $vendor_data['banner_slider'] : array();
		$list_banner_type  = isset( $vendor_data['list_banner_type'] ) ? $vendor_data['list_banner_type'] : 'single_img';
		$list_banner       = isset( $vendor_data['list_banner'] ) ? absint( $vendor_data['list_banner'] ) : 0;
		$list_banner_video = isset( $vendor_data['list_banner_video'] ) ? $vendor_data['list_banner_video'] : '';
		$mobile_banner     = isset( $vendor_data['mobile_banner'] ) ? $vendor_data['mobile_banner'] : '';
		
		$store_name     = isset( $vendor_data['store_name'] ) ? esc_attr( $vendor_data['store_name'] ) : '';
		$store_slug     = '';
		if( $vendor_id != 99999 ) {
			$store_name     = empty( $store_name ) ? $the_vendor_user->display_name : $store_name;
			$store_slug     = $the_vendor_user->user_nicename;
		}
		$phone          = isset( $vendor_data['phone'] ) ? esc_attr( $vendor_data['phone'] ) : '';
		
		// Address
		$address         = isset( $vendor_data['address'] ) ? $vendor_data['address'] : '';
		$street_1 = isset( $vendor_data['address']['street_1'] ) ? $vendor_data['address']['street_1'] : '';
		$street_2 = isset( $vendor_data['address']['street_2'] ) ? $vendor_data['address']['street_2'] : '';
		$city    = isset( $vendor_data['address']['city'] ) ? $vendor_data['address']['city'] : '';
		$zip     = isset( $vendor_data['address']['zip'] ) ? $vendor_data['address']['zip'] : '';
		$country = isset( $vendor_data['address']['country'] ) ? $vendor_data['address']['country'] : '';
		$state   = isset( $vendor_data['address']['state'] ) ? $vendor_data['address']['state'] : '';
		
		// Location
		$store_location   = isset( $vendor_data['store_location'] ) ? esc_attr( $vendor_data['store_location'] ) : '';
		$map_address    = isset( $vendor_data['find_address'] ) ? esc_attr( $vendor_data['find_address'] ) : '';
		$store_lat    = isset( $vendor_data['store_lat'] ) ? esc_attr( $vendor_data['store_lat'] ) : 0;
		$store_lng    = isset( $vendor_data['store_lng'] ) ? esc_attr( $vendor_data['store_lng'] ) : 0;
		
		// Country -> States
		$country_obj   = new WC_Countries();
		$countries     = $country_obj->countries;
		$states        = $country_obj->states;
		$state_options = array();
		if( $state && isset( $states[$country] ) && is_array( $states[$country] ) ) {
			$state_options = $states[$country];
		}
		if( $state ) $state_options[$state] = $state;
		
		// Gravatar image
		$gravatar_url = $gravatar ? wp_get_attachment_url( $gravatar ) : '';
		
		// List Banner URL
		$list_banner_url = $list_banner ? wp_get_attachment_url( $list_banner ) : '';
		
		// Banner URL
		$banner_url = $banner ? wp_get_attachment_url( $banner ) : '';
		
		// Mobile Banner URL
		$mobile_banner_url = $mobile_banner ? wp_get_attachment_url( $mobile_banner ) : '';
		
		// Visiblity
		$global_store_name_position = isset( $WCFMmp->wcfmmp_marketplace_options['store_name_position'] ) ? $WCFMmp->wcfmmp_marketplace_options['store_name_position'] : 'on_banner';
		$store_name_position = isset( $vendor_data['store_name_position'] ) ? esc_attr( $vendor_data['store_name_position'] ) : $global_store_name_position;
		$global_store_ppp = isset( $WCFMmp->wcfmmp_marketplace_options['store_ppp'] ) ? $WCFMmp->wcfmmp_marketplace_options['store_ppp'] : get_option('posts_per_page');
		$store_ppp = isset( $vendor_data['store_ppp'] ) ? absint( $vendor_data['store_ppp'] ) : $global_store_ppp;
		$store_hide_email = isset( $vendor_data['store_hide_email'] ) ? esc_attr( $vendor_data['store_hide_email'] ) : 'no';
		$store_hide_phone = isset( $vendor_data['store_hide_phone'] ) ? esc_attr( $vendor_data['store_hide_phone'] ) : 'no';
		$store_hide_address = isset( $vendor_data['store_hide_address'] ) ? esc_attr( $vendor_data['store_hide_address'] ) : 'no';
		
		// Payment
		$payment_mode = isset( $vendor_data['payment']['method'] ) ? esc_attr( $vendor_data['payment']['method'] ) : '' ;
		$paypal = isset( $vendor_data['payment']['paypal']['email'] ) ? esc_attr( $vendor_data['payment']['paypal']['email'] ) : '' ;
		$skrill = isset( $vendor_data['payment']['skrill']['email'] ) ? esc_attr( $vendor_data['payment']['skrill']['email'] ) : '' ;
		$ac_name   = isset( $vendor_data['payment']['bank']['ac_name'] ) ? esc_attr( $vendor_data['payment']['bank']['ac_name'] ) : '';
		$ac_number = isset( $vendor_data['payment']['bank']['ac_number'] ) ? esc_attr( $vendor_data['payment']['bank']['ac_number'] ) : '';
		$bank_name      = isset( $vendor_data['payment']['bank']['bank_name'] ) ? esc_attr( $vendor_data['payment']['bank']['bank_name'] ) : '';
		$bank_addr      = isset( $vendor_data['payment']['bank']['bank_addr'] ) ? esc_textarea( $vendor_data['payment']['bank']['bank_addr'] ) : '';
		$routing_number = isset( $vendor_data['payment']['bank']['routing_number'] ) ? esc_attr( $vendor_data['payment']['bank']['routing_number'] ) : '';
		$iban           = isset( $vendor_data['payment']['bank']['iban'] ) ? esc_attr( $vendor_data['payment']['bank']['iban'] ) : '';
		$swift     = isset( $vendor_data['payment']['bank']['swift'] ) ? esc_attr( $vendor_data['payment']['bank']['swift'] ) : '';
		$ifsc     = isset( $vendor_data['payment']['bank']['ifsc'] ) ? esc_attr( $vendor_data['payment']['bank']['ifsc'] ) : '';
		
		// Commission
		$wcfm_commission_types = get_wcfm_marketplace_commission_types();
		$wcfm_commission_types = array_merge( array( 'global' => __( 'By Global Rule', 'wc-multivendor-marketplace' ) ), $wcfm_commission_types );
		
		$vendor_commission_mode = isset( $vendor_data['commission']['commission_mode'] ) ? $vendor_data['commission']['commission_mode'] : 'global';
		$vendor_commission_fixed = isset( $vendor_data['commission']['commission_fixed'] ) ? $vendor_data['commission']['commission_fixed'] : '';
		$vendor_commission_percent = isset( $vendor_data['commission']['commission_percent'] ) ? $vendor_data['commission']['commission_percent'] : '90';
		$vendor_commission_by_sales = isset( $vendor_data['commission']['commission_by_sales'] ) ? $vendor_data['commission']['commission_by_sales'] : array();
		$vendor_commission_by_products = isset( $vendor_data['commission']['commission_by_products'] ) ? $vendor_data['commission']['commission_by_products'] : array();
		$vendor_get_shipping = isset( $vendor_data['commission']['get_shipping'] ) ? $vendor_data['commission']['get_shipping'] : '';
		$vendor_get_tax = isset( $vendor_data['commission']['get_tax'] ) ? $vendor_data['commission']['get_tax'] : '';
		$vendor_coupon_deduct = isset( $vendor_data['commission']['coupon_deduct'] ) ? $vendor_data['commission']['coupon_deduct'] : '';
		
		// Withdrawal
		$wcfm_withdrawal_options = array( 'global' => __( 'By Global Rule', 'wc-multivendor-marketplace' ), 'vendor' => __( 'Vendor Specific Rule', 'wc-multivendor-marketplace' ) );
		
		// Global Options
		$withdrawal_global_options       = get_option( 'wcfm_withdrawal_options', array() );
		$request_auto_approve            = isset( $withdrawal_global_options['request_auto_approve'] ) ? $withdrawal_global_options['request_auto_approve'] : 'no';
		$withdrawal_limit                = isset( $withdrawal_global_options['withdrawal_limit'] ) ? $withdrawal_global_options['withdrawal_limit'] : '';
		$withdrawal_thresold             = isset( $withdrawal_global_options['withdrawal_thresold'] ) ? $withdrawal_global_options['withdrawal_thresold'] : '';
		$withdrawal_charge_type          = isset( $withdrawal_global_options['withdrawal_charge_type'] ) ? $withdrawal_global_options['withdrawal_charge_type'] : 'no';
		$withdrawal_charge               = isset( $withdrawal_global_options['withdrawal_charge'] ) ? $withdrawal_global_options['withdrawal_charge'] : array();
		
		$vendor_withdrawal_mode          = isset( $vendor_data['withdrawal']['withdrawal_mode'] ) ? $vendor_data['withdrawal']['withdrawal_mode'] : 'global';
		$request_auto_approve            = isset( $vendor_data['withdrawal']['request_auto_approve'] ) ? $vendor_data['withdrawal']['request_auto_approve'] : $request_auto_approve;
		$withdrawal_limit                = isset( $vendor_data['withdrawal']['withdrawal_limit'] ) ? $vendor_data['withdrawal']['withdrawal_limit'] : $withdrawal_limit;
		$withdrawal_thresold             = isset( $vendor_data['withdrawal']['withdrawal_thresold'] ) ? $vendor_data['withdrawal']['withdrawal_thresold'] : $withdrawal_thresold;
		$withdrawal_charge_type          = isset( $vendor_data['withdrawal']['withdrawal_charge_type'] ) ? $vendor_data['withdrawal']['withdrawal_charge_type'] : $withdrawal_charge_type;
		
		$vendor_withdrawal_charge        = isset( $vendor_data['withdrawal']['withdrawal_charge'] ) ? $vendor_data['withdrawal']['withdrawal_charge'] : $withdrawal_charge;
		$withdrawal_charge_paypal        = isset( $vendor_withdrawal_charge['paypal'] ) ? $vendor_withdrawal_charge['paypal'] : array();
		$withdrawal_charge_stripe        = isset( $vendor_withdrawal_charge['stripe'] ) ? $vendor_withdrawal_charge['stripe'] : array();
		$withdrawal_charge_skrill        = isset( $vendor_withdrawal_charge['skrill'] ) ? $vendor_withdrawal_charge['skrill'] : array();
		$withdrawal_charge_bank_transfer = isset( $vendor_withdrawal_charge['bank_transfer'] ) ? $vendor_withdrawal_charge['bank_transfer'] : array();
		
		$store_banner_width = isset( $WCFMmp->wcfmmp_marketplace_options['store_banner_width'] ) ? $WCFMmp->wcfmmp_marketplace_options['store_banner_width'] : '1650';
		$store_banner_height = isset( $WCFMmp->wcfmmp_marketplace_options['store_banner_height'] ) ? $WCFMmp->wcfmmp_marketplace_options['store_banner_height'] : '350';
		$store_banner_mwidth = isset( $WCFMmp->wcfmmp_marketplace_options['store_banner_mwidth'] ) ? $WCFMmp->wcfmmp_marketplace_options['store_banner_mwidth'] : '520';
		$store_banner_mheight = isset( $WCFMmp->wcfmmp_marketplace_options['store_banner_mheight'] ) ? $WCFMmp->wcfmmp_marketplace_options['store_banner_mheight'] : '150';
		
		$banner_help_text = sprintf(
				__('Upload a banner for your store. Banner size is (%sx%s) pixels.', 'wc-frontend-manager' ),
				$store_banner_width, $store_banner_height
		);
		
		// Store Hours
		$wcfm_vendor_store_hours = (array) get_user_meta( $vendor_id, 'wcfm_vendor_store_hours', true );
		
		$wcfm_store_hours_enable = isset( $wcfm_vendor_store_hours['enable'] ) ? 'yes' : 'no';
		$wcfm_store_hours_disable_purchase = isset( $wcfm_vendor_store_hours['disable_purchase'] ) ? 'yes' : 'no';
		$wcfm_store_hours_off_days = isset( $wcfm_vendor_store_hours['off_days'] ) ? $wcfm_vendor_store_hours['off_days'] : array();
		$wcfm_store_hours_day_times = isset( $wcfm_vendor_store_hours['day_times'] ) ? $wcfm_vendor_store_hours['day_times'] : array();
		
		$wcfm_store_hours_mon_min_time  = isset( $wcfm_store_hours_day_times[0]['start'] ) ? $wcfm_store_hours_day_times[0]['start'] : '';
		$wcfm_store_hours_mon_max_time  = isset( $wcfm_store_hours_day_times[0]['end'] ) ? $wcfm_store_hours_day_times[0]['end'] : '';
		$wcfm_store_hours_thu_min_time  = isset( $wcfm_store_hours_day_times[1]['start'] ) ? $wcfm_store_hours_day_times[1]['start'] : '';
		$wcfm_store_hours_thu_max_time  = isset( $wcfm_store_hours_day_times[1]['end'] ) ? $wcfm_store_hours_day_times[1]['end'] : '';
		$wcfm_store_hours_wed_min_time  = isset( $wcfm_store_hours_day_times[2]['start'] ) ? $wcfm_store_hours_day_times[2]['start'] : '';
		$wcfm_store_hours_wed_max_time  = isset( $wcfm_store_hours_day_times[2]['end'] ) ? $wcfm_store_hours_day_times[2]['end'] : '';
		$wcfm_store_hours_thur_min_time = isset( $wcfm_store_hours_day_times[3]['start'] ) ? $wcfm_store_hours_day_times[3]['start'] : '';
		$wcfm_store_hours_thur_max_time = isset( $wcfm_store_hours_day_times[3]['end'] ) ? $wcfm_store_hours_day_times[3]['end'] : '';
		$wcfm_store_hours_fri_min_time  = isset( $wcfm_store_hours_day_times[4]['start'] ) ? $wcfm_store_hours_day_times[4]['start'] : '';
		$wcfm_store_hours_fri_max_time  = isset( $wcfm_store_hours_day_times[4]['end'] ) ? $wcfm_store_hours_day_times[4]['end'] : '';
		$wcfm_store_hours_sat_min_time  = isset( $wcfm_store_hours_day_times[5]['start'] ) ? $wcfm_store_hours_day_times[5]['start'] : '';
		$wcfm_store_hours_sat_max_time  = isset( $wcfm_store_hours_day_times[5]['end'] ) ? $wcfm_store_hours_day_times[5]['end'] : '';
		$wcfm_store_hours_sun_min_time  = isset( $wcfm_store_hours_day_times[6]['start'] ) ? $wcfm_store_hours_day_times[6]['start'] : '';
		$wcfm_store_hours_sun_max_time  = isset( $wcfm_store_hours_day_times[6]['end'] ) ? $wcfm_store_hours_day_times[6]['end'] : '';
		
		// Vacation Mode
		$wcfm_vacation_mode = isset( $vendor_data['wcfm_vacation_mode'] ) ? $vendor_data['wcfm_vacation_mode'] : 'no';
		$wcfm_disable_vacation_purchase = isset( $vendor_data['wcfm_disable_vacation_purchase'] ) ? $vendor_data['wcfm_disable_vacation_purchase'] : 'no';
		$wcfm_vacation_mode_type = isset( $vendor_data['wcfm_vacation_mode_type'] ) ? $vendor_data['wcfm_vacation_mode_type'] : 'instant';
		$wcfm_vacation_start_date = isset( $vendor_data['wcfm_vacation_start_date'] ) ? $vendor_data['wcfm_vacation_start_date'] : '';
		$wcfm_vacation_end_date = isset( $vendor_data['wcfm_vacation_end_date'] ) ? $vendor_data['wcfm_vacation_end_date'] : '';
		$wcfm_vacation_mode_msg = ! empty( $vendor_data['wcfm_vacation_mode_msg'] ) ? $vendor_data['wcfm_vacation_mode_msg'] : '';
		
		?>
		
		<!-- collapsible -->
		<div class="page_collapsible vendor_manage_store_setting" id="wcfm_vendor_manage_form_store_settings_head"><label class="fa fa-home"></label><?php _e( 'Store Settings', 'wc-frontend-manager' ); ?><span></span></div>
		<div class="wcfm-container">
			<div id="wcfm_vendor_manage_form_store_setting_expander" class="wcfm-content">
			  <?php if( $vendor_id != 99999 ) { ?>
				<form id="wcfm_vendor_manage_store_setting_form" class="wcfm">
				<?php } ?>
					<?php
					$store_banner_types = array( 'single_img' => __( 'Static Image', 'wc-frontend-manager' ), 'slider' => __( 'Slider', 'wc-frontend-manager' ), 'video' => __( 'Video', 'wc-frontend-manager' ) );
					$store_list_banner_types = array( 'single_img' => __( 'Static Image', 'wc-frontend-manager' ), 'video' => __( 'Video', 'wc-frontend-manager' ) );
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_marketplace_settings_fields_general', array(
																																																"gravatar" => array('label' => __('Profile Image', 'wc-frontend-manager') , 'type' => 'upload', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title', 'prwidth' => 150, 'value' => $gravatar_url ),
																																																"banner_type" => array('label' => __('Store Banner Type', 'wc-frontend-manager') , 'type' => 'select', 'options' => array( 'single_img' => __( 'Static Image', 'wc-frontend-manager' ), 'slider' => __( 'Slider', 'wc-frontend-manager' ), 'video' => __( 'Video', 'wc-frontend-manager' ) ), 'class' => 'wcfm-select wcfm_ele wcfm-banner-uploads', 'label_class' => 'wcfm_title', 'value' => $banner_type, ),
																																																"banner" => array('label' => __('Banner', 'wc-frontend-manager') , 'type' => 'upload', 'class' => 'wcfm-text wcfm_ele banner_type_upload banner_type_field banner_type_single_img wcfm-banner-uploads', 'label_class' => 'wcfm_title banner_type_field banner_type_single_img', 'prwidth' => 250, 'value' => $banner_url, 'hints' => $banner_help_text ),
																																																"banner_video" => array('label' => __('Video Banner', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele banner_type_field banner_type_video', 'label_class' => 'wcfm_title banner_type_field banner_type_video','value' => $banner_video, 'hints' => __( 'Insert YouTube video URL.', 'wc-frontend-manager' ) ),
																																																"banner_slider"  => array( 'label' => __('Slider', 'wc-frontend-manager'), 'type' => 'multiinput', 'class' => 'wcfm-text wcfm_ele banner_type_upload banner_type_field banner_type_slider wcfm_non_sortable', 'label_class' => 'wcfm_title banner_type_field banner_type_slider', 'value' => $banner_slider, 'hints' => $banner_help_text, 'options' => array(
																																																																									"image" => array( 'type' => 'upload', 'class' => 'wcfm_gallery_upload banner_type_upload wcfm-banner-uploads', 'prwidth' => 75),
																																																																									"link"  => array( 'type' => 'text', 'class' => 'wcfm-text banner_type_slilder_link', 'placeholder' => __( 'Slider Hyperlink', 'wc-frontend-manager' ) ),
																																																																								) ),
																																																"slider_break" => array( 'type' => 'html', 'value' => '<div class="wcfm_clearfix"></div>' ),
																																																
																																																"list_banner_type" => array('label' => __('Store List Banner Type', 'wc-frontend-manager') , 'type' => 'select', 'options' => $store_list_banner_types, 'class' => 'wcfm-select wcfm_ele wcfm-list-banner-uploads', 'label_class' => 'wcfm_title', 'value' => $list_banner_type ),
																																																"list_banner" => array('label' => __('Store List Banner', 'wc-frontend-manager') , 'type' => 'upload', 'class' => 'wcfm-text wcfm_ele wcfm-banner-uploads list_banner_type_upload list_banner_type_field list_banner_type_single_img', 'label_class' => 'wcfm_title list_banner_type_field list_banner_type_single_img', 'prwidth' => 250, 'value' => $list_banner_url, 'hints' => __( 'This Banner will be visible at Store List Page.', 'wc-frontend-manager' ) ),
																																																"list_banner_video" => array('label' => __('Store List Video Banner', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele list_banner_type_field list_banner_type_video', 'label_class' => 'wcfm_title list_banner_type_field list_banner_type_video','value' => $list_banner_video, 'hints' => __( 'Insert YouTube video URL.', 'wc-frontend-manager' ) ),
																																																
																																																"mobile_banner" => array('label' => __('Mobile Banner', 'wc-frontend-manager') , 'type' => 'upload', 'class' => 'wcfm-text wcfm_ele wcfm-banner-uploads', 'label_class' => 'wcfm_title', 'prwidth' => 250, 'value' => $mobile_banner_url, 'hints' => __( 'This Banner will be visible when someone browse store from Mobile.', 'wc-frontend-manager' ) ),
																																																
																																																"store_name" => array('label' => __('Shop Name', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'custom_attributes' => array( 'required' => true ), 'value' => $store_name ),
																																																"store_slug" => array('label' => __('Store Slug', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'custom_attributes' => array( 'required' => true ), 'value' => $store_slug ),
																																																"phone" => array('label' => __('Store Phone', 'wc-frontend-manager') , 'type' => 'text', 'placeholder' => '+123456..', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $phone ),
																																																"vendor_id" => array( 'type' => 'hidden', 'value' => $vendor_id )
																																																) ) );
					
					?>
					
					<div class="wcfm_clearfix"></div>
					<div class="wcfm_vendor_settings_heading"><h3><?php _e( 'Store Address', 'wc-frontend-manager' ); ?></h3></div>
					<div class="store_address store_address_wrap">
						<?php
							$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_marketplace_settings_fields_address', array(
																																																"street_1" => array('label' => __('Street', 'wc-frontend-manager'), 'placeholder' => __('Street adress', 'wc-frontend-manager'), 'name' => 'address[street_1]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $street_1 ),
																																																"street_2" => array('label' => __('Street 2', 'wc-frontend-manager'), 'placeholder' => __('Apartment, suit, unit etc. (optional)', 'wc-frontend-manager'), 'name' => 'address[street_2]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $street_2 ),
																																																"city" => array('label' => __('City/Town', 'wc-frontend-manager'), 'placeholder' => __('Town / City', 'wc-frontend-manager'), 'name' => 'address[city]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $city ),
																																																"zip" => array('label' => __('Postcode/Zip', 'wc-frontend-manager'), 'placeholder' => __('Postcode / Zip', 'wc-frontend-manager'), 'name' => 'address[zip]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $zip ),
																																																"country" => array('label' => __('Country', 'wc-frontend-manager'), 'name' => 'address[country]', 'type' => 'country', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $country ),
																																																"state" => array('label' => __('State/County', 'wc-frontend-manager'), 'name' => 'address[state]', 'type' => 'select', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'options' => $state_options, 'value' => $state ),
																																																) ) );
						?>
					</div>
					<script type="text/javascript">
						var selected_state = '<?php echo $state; ?>';
						var input_selected_state = '<?php echo $state; ?>';
					</script>
					
					<div class="wcfm_clearfix"></div>
					<div class="wcfm_vendor_settings_heading"><h3><?php _e( 'Visibility Setup', 'wc-frontend-manager' ); ?></h3></div>
					<div class="store_address store_visibility_wrap">
						<?php
							$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_marketplace_settings_fields_visibility', array(
								"store_name_position" => array( 'label' => __('Store Name Position', 'wc-multivendor-marketplace'), 'type' => 'select', 'options' => array( 'on_banner' => __( 'On Banner', 'wc-multivendor-marketplace' ), 'on_header' => __( 'At Header', 'wc-multivendor-marketplace' ) ), 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title', 'value' => $store_name_position, 'hints' => __( 'Store name position at you Store Page.', 'wc-frontend-manager' ) ),
								"store_ppp" => array( 'label' => __('Products per page', 'wc-multivendor-marketplace'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title', 'value' => $store_ppp, 'attributes' => array( 'min'=> 1, 'step' => 1 ), 'hints' => __( 'No of products at you Store Page.', 'wc-frontend-manager' ) ),
								"store_hide_email" => array('label' => __( 'Hide Email from Store', 'wc-frontend-manager') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title wcfm_ele', 'value' => 'yes', 'dfvalue' => $store_hide_email ),
								"store_hide_phone" => array('label' => __( 'Hide Phone from Store', 'wc-frontend-manager') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title wcfm_ele', 'value' => 'yes', 'dfvalue' => $store_hide_phone ),
								"store_hide_address" => array('label' => __( 'Hide Address from Store', 'wc-frontend-manager') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title wcfm_ele', 'value' => 'yes', 'dfvalue' => $store_hide_address ),
								) ) );
						?>
					</div>
					
					<div class="wcfm_clearfix"></div>
					<div class="wcfm_vendor_settings_heading"><h3><?php _e( 'Payment Setup', 'wc-frontend-manager' ); ?></h3></div>
					<div class="store_address">
						<?php
						$wcfm_marketplace_withdrwal_payment_methods = get_wcfm_marketplace_active_withdrwal_payment_methods();
						$wcfmmp_settings_fields_billing = apply_filters( 'wcfm_marketplace_settings_fields_billing', array(
																																														"payment_mode" => array('label' => __('Prefered Payment Method', 'wc-frontend-manager'), 'name' => 'payment[method]', 'type' => 'select', 'options' => $wcfm_marketplace_withdrwal_payment_methods, 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $payment_mode ),
																																														"paypal" => array('label' => __('PayPal Email', 'wc-frontend-manager'), 'name' => 'payment[paypal][email]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele paymode_field paymode_paypal', 'label_class' => 'wcfm_title wcfm_ele paymode_field paymode_paypal', 'value' => $paypal ),
																																														"skrill" => array('label' => __('Skrill Email', 'wc-frontend-manager'), 'name' => 'payment[skrill][email]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele paymode_field paymode_skrill', 'label_class' => 'wcfm_title wcfm_ele paymode_field paymode_skrill', 'value' => $skrill ),
																																														), $vendor_id );
						
						$WCFM->wcfm_fields->wcfm_generate_form_field( $wcfmmp_settings_fields_billing );
						?>
					</div>
					
					<?php if( in_array( 'bank_transfer', array_keys( $wcfm_marketplace_withdrwal_payment_methods ) ) ) { ?>
						<div class="wcfm_clearfix"></div>
						<div class="wcfm_vendor_settings_heading wcfm_marketplace_bank paymode_field paymode_bank_transfer"><h3><?php _e( 'Bank Details', 'wc-frontend-manager' ); ?></h3></div>
						<div class="store_address">
							<?php
								$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_marketplace_settings_fields_billing_bank', array(
																																		"ac_name" => array('label' => __('Account Name', 'wc-frontend-manager'), 'placeholder' => __('Your bank account name', 'wc-frontend-manager'), 'name' => 'payment[bank][ac_name]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele paymode_field paymode_bank_transfer', 'label_class' => 'wcfm_title wcfm_ele paymode_field paymode_bank_transfer', 'value' => $ac_name ),
																																		"ac_number" => array('label' => __('Account Number', 'wc-frontend-manager'), 'placeholder' => __('Your bank account number', 'wc-frontend-manager'), 'name' => 'payment[bank][ac_number]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele paymode_field paymode_bank_transfer', 'label_class' => 'wcfm_title wcfm_ele paymode_field paymode_bank_transfer', 'value' => $ac_number ),
																																		"bank_name" => array('label' => __('Bank Name', 'wc-frontend-manager'), 'placeholder' => __('Name of bank', 'wc-frontend-manager'), 'name' => 'payment[bank][bank_name]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele paymode_field paymode_bank_transfer', 'label_class' => 'wcfm_title wcfm_ele paymode_field paymode_bank_transfer', 'value' => $bank_name ),
																																		"bank_addr" => array('label' => __('Bank Address', 'wc-frontend-manager'), 'placeholder' => __('Address of your bank', 'wc-frontend-manager'), 'name' => 'payment[bank][bank_addr]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele paymode_field paymode_bank_transfer', 'label_class' => 'wcfm_title wcfm_ele paymode_field paymode_bank_transfer', 'value' => $bank_addr ),
																																		"routing_number" => array('label' => __('Routing Number', 'wc-frontend-manager'), 'placeholder' => __( 'Routing number', 'wc-frontend-manager' ), 'name' => 'payment[bank][routing_number]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele paymode_field paymode_bank_transfer', 'label_class' => 'wcfm_title wcfm_ele paymode_field paymode_bank_transfer', 'value' => $routing_number ),
																																		"iban" => array('label' => __('IBAN', 'wc-frontend-manager'), 'placeholder' => __('IBAN', 'wc-frontend-manager'), 'name' => 'payment[bank][iban]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele paymode_field paymode_bank_transfer', 'label_class' => 'wcfm_title wcfm_ele paymode_field paymode_bank_transfer', 'value' => $iban ),
																																		"swift" => array('label' => __('Swift Code', 'wc-frontend-manager'), 'placeholder' => __('Swift code', 'wc-frontend-manager'), 'name' => 'payment[bank][swift]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele paymode_field paymode_bank_transfer', 'label_class' => 'wcfm_title wcfm_ele paymode_field paymode_bank_transfer', 'value' => $swift ),
																																		"ifsc" => array('label' => __('IFSC Code', 'wc-frontend-manager'), 'placeholder' => __('Swift code', 'wc-frontend-manager'), 'name' => 'payment[bank][ifsc]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele paymode_field paymode_bank_transfer', 'label_class' => 'wcfm_title wcfm_ele paymode_field paymode_bank_transfer', 'value' => $ifsc ),
																																		) ) );
							?>
						</div>
					<?php } ?>
					
					<div class="wcfm_clearfix"></div>
					<div class="wcfm_vendor_settings_heading"><h3><?php _e( 'Commission Setup', 'wc-frontend-manager' ); ?></h3></div>
					<div class="store_address">
						<?php
						$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_marketplace_settings_fields_vendor_commission', array(
					                                                                        "vendor_commission_mode" => array('label' => __('Commission Mode', 'wc-multivendor-marketplace'), 'name' => 'commission[commission_mode]', 'type' => 'select', 'options' => $wcfm_commission_types, 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $vendor_commission_mode ),
					                                                                        "vendor_commission_percent" => array('label' => __('Commission Percent(%)', 'wc-multivendor-marketplace'), 'name' => 'commission[commission_percent]', 'type' => 'number', 'class' => 'wcfm-text wcfm_ele commission_mode_field commission_mode_percent commission_mode_percent_fixed', 'label_class' => 'wcfm_title wcfm_ele commission_mode_field commission_mode_percent commission_mode_percent_fixed', 'value' => $vendor_commission_percent, 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
					                                                                        "vendor_commission_fixed" => array('label' => __('Commission Fixed', 'wc-multivendor-marketplace') . '(' . get_woocommerce_currency_symbol() . ')', 'name' => 'commission[commission_fixed]', 'type' => 'number', 'class' => 'wcfm-text wcfm_ele commission_mode_field commission_mode_fixed commission_mode_percent_fixed', 'label_class' => 'wcfm_title wcfm_ele commission_mode_field commission_mode_fixed commission_mode_percent_fixed', 'value' => $vendor_commission_fixed, 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
					                                                                        "vendor_commission_by_sales" => array('label' => __('Commission By Sales Rule(s)', 'wc-multivendor-marketplace'), 'name' => 'commission[commission_by_sales]', 'type' => 'multiinput', 'class' => 'wcfm-text wcfm_ele commission_mode_field commission_mode_by_sales', 'label_class' => 'wcfm_title wcfm_ele wcfm_full_title commission_mode_field commission_mode_by_sales', 'desc_class' => 'commission_mode_field commission_mode_by_sales instructions', 'value' => $vendor_commission_by_sales, 'desc' => sprintf( __( 'Commission rules depending upon vendors total sales. e.g 50&#37; commission when sales < %s1000, 75&#37; commission when sales > %s1000 but < %s2000 and so on. You may define any number of such rules. Please be sure, do not set conflicting rules.', 'wc-multivendor-marketplace' ), get_woocommerce_currency_symbol(), get_woocommerce_currency_symbol(), get_woocommerce_currency_symbol() ),  'options' => array( 
					                                                                        																			"sales" => array('label' => __('Sales', 'wc-multivendor-marketplace'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'min' => '1', 'step' => '0.1') ),
					                                                                        																			"rule" => array('label' => __('Rule', 'wc-multivendor-marketplace'), 'type' => 'select', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'options' => array( 'upto' => __( 'Up to', 'wc-multivendor-marketplace' ), 'greater'   => __( 'More than', 'wc-multivendor-marketplace' ) ) ),
					                                                                        																			"type" => array('label' => __('Commission Type', 'wc-multivendor-marketplace'), 'type' => 'select', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'options' => array( 'percent' => __( 'Percent', 'wc-multivendor-marketplace' ), 'fixed'   => __( 'Fixed', 'wc-multivendor-marketplace' ) ) ),
					                                                                        																			"commission" => array('label' => __('Commission Amount', 'wc-multivendor-marketplace'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
					                                                                        																			) ),
					                                                                        "vendor_commission_by_products" => array('label' => __('Commission By Product Price', 'wc-multivendor-marketplace'), 'name' => 'commission[commission_by_products]', 'type' => 'multiinput', 'class' => 'wcfm-text wcfm_ele commission_mode_field commission_mode_by_products', 'label_class' => 'wcfm_title wcfm_ele wcfm_full_title commission_mode_field commission_mode_by_products', 'desc_class' => 'commission_mode_field commission_mode_by_products instructions', 'value' => $vendor_commission_by_products, 'desc' => sprintf( __( 'Commission rules depending upon product price. e.g 80&#37; commission when product cost < %s1000, %s100 fixed commission when product cost > %s1000 and so on. You may define any number of such rules. Please be sure, do not set conflicting rules.', 'wc-multivendor-marketplace' ), get_woocommerce_currency_symbol(), get_woocommerce_currency_symbol(), get_woocommerce_currency_symbol() ),  'options' => array( 
					                                                                        																			"cost" => array('label' => __('Product Cost', 'wc-multivendor-marketplace'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
					                                                                        																			"rule" => array('label' => __('Rule', 'wc-multivendor-marketplace'), 'type' => 'select', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'options' => array( 'upto' => __( 'Up to', 'wc-multivendor-marketplace' ), 'greater'   => __( 'More than', 'wc-multivendor-marketplace' ) ) ),
					                                                                        																			"type" => array('label' => __('Commission Type', 'wc-multivendor-marketplace'), 'type' => 'select', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'options' => array( 'percent' => __( 'Percent', 'wc-multivendor-marketplace' ), 'fixed'   => __( 'Fixed', 'wc-multivendor-marketplace' ) ) ),
					                                                                        																			"commission" => array('label' => __('Commission Amount', 'wc-multivendor-marketplace'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
					                                                                        																			) ),
																																									"vendor_get_shipping" => array('label' => __('Shipping cost goes to vendor?', 'wc-multivendor-marketplace'), 'name' => 'commission[get_shipping]', 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele commission_mode_field commission_mode_percent commission_mode_fixed commission_mode_percent_fixed commission_mode_by_sales commission_mode_by_products', 'label_class' => 'wcfm_title checkbox_title commission_mode_field commission_mode_percent commission_mode_fixed commission_mode_percent_fixed commission_mode_by_sales commission_mode_by_products', 'value' => 'yes', 'dfvalue' => $vendor_get_shipping ),
																																									"vendor_get_tax" => array('label' => __('Tax goes to vendor?', 'wc-multivendor-marketplace'), 'name' => 'commission[get_tax]', 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele commission_mode_field commission_mode_percent commission_mode_fixed commission_mode_percent_fixed commission_mode_by_sales commission_mode_by_products', 'label_class' => 'wcfm_title checkbox_title commission_mode_field commission_mode_percent commission_mode_fixed commission_mode_percent_fixed commission_mode_by_sales commission_mode_by_products', 'value' => 'yes', 'dfvalue' => $vendor_get_tax ),
																																									"vendor_coupon_deduct" => array('label' => __('Commission after deduct discounts?', 'wc-multivendor-marketplace'), 'name' => 'commission[coupon_deduct]', 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele commission_mode_field commission_mode_percent commission_mode_fixed commission_mode_percent_fixed commission_mode_by_sales commission_mode_by_products', 'label_class' => 'wcfm_title checkbox_title commission_mode_field commission_mode_percent commission_mode_fixed commission_mode_percent_fixed commission_mode_by_sales commission_mode_by_products', 'value' => 'yes', 'dfvalue' => $vendor_coupon_deduct, 'hints' => __( 'Generate vednor commission after deduct coupon or other discounts.', 'wc-multivendor-marketplace' ) ),
																																									) ) );
						?>
					</div>
					
					<div class="wcfm_clearfix"></div>
					<div class="wcfm_vendor_settings_heading"><h3><?php _e( 'Withdrawal Setup', 'wc-frontend-manager' ); ?></h3></div>
					<div class="store_address">
					  <?php
						$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_marketplace_settings_fields_vendor_withdrawal', array(
																																											"vendor_withdrawal_mode" => array('label' => __('Withdrawal Mode', 'wc-multivendor-marketplace'), 'name' => 'withdrawal[withdrawal_mode]', 'type' => 'select', 'options' => $wcfm_withdrawal_options, 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $vendor_withdrawal_mode ),
																																											"withdrawal_request_auto_approve" => array('label' => __('Request auto-approve?', 'wc-multivendor-marketplace'), 'type' => 'checkbox', 'name' => 'withdrawal[request_auto_approve]', 'class' => 'wcfm-checkbox wcfm_ele withdrawal_mode_field withdrawal_mode_vendor', 'label_class' => 'wcfm_title checkbox_title withdrawal_mode_field withdrawal_mode_vendor', 'value' => 'yes', 'dfvalue' => $request_auto_approve, 'desc_class' => 'wcfm_page_options_desc withdrawal_mode_field withdrawal_mode_vendor', 'desc' => __( 'Check this to automatically disburse payments to vendors on request, no admin approval required. Auto disbursement only works for auto-payment gateways, e.g. PayPal, Stripe etc. Bank Transfer or other non-autopay mode always requires approval, as these are manual transactions.', 'wc-multivendor-membership' ) ),
																																											"withdrawal_setting_break_1" => array( 'type' => 'html', 'value' => '<div style="height: 15px;"></div>' ),
																																											"withdrawal_limit" => array('label' => __('Minimum Withdraw Limit', 'wc-multivendor-marketplace'), 'name' => 'withdrawal[withdrawal_limit]', 'type' => 'number', 'class' => 'wcfm-text wcfm_ele withdrawal_mode_field withdrawal_mode_vendor', 'label_class' => 'wcfm_title withdrawal_mode_field withdrawal_mode_vendor', 'desc_class'=> 'wcfm_page_options_desc withdrawal_mode_field withdraw_charge_block withdrawal_mode_vendor', 'value' => $withdrawal_limit, 'attributes' => array( 'min' => '0.1', 'step' => '0.1'), 'desc' => __( 'Minimum balance required to make a withdraw request. Leave blank to set no minimum limits.', 'wc-multivendor-marketplace') ),
																																											"withdrawal_thresold" => array('label' => __('Withdraw Threshold', 'wc-multivendor-marketplace'), 'name' => 'withdrawal[withdrawal_thresold]', 'type' => 'number', 'class' => 'wcfm-text wcfm_ele withdrawal_mode_field withdrawal_mode_vendor', 'label_class' => 'wcfm_title wcfm_ele withdrawal_mode_field withdrawal_mode_vendor', 'desc_class' => 'wcfm_page_options_desc withdrawal_mode_field withdrawal_mode_vendor', 'value' => $withdrawal_thresold , 'attributes' => array( 'min' => '1', 'step' => '1'), 'desc' => __('Withdraw Threshold Days, (Make order matured to make a withdraw request). Leave empty to inactive this option.', 'wc-multivendor-marketplace') ),
																																											"withdrawal_charge_type" => array('label' => __('Withdrawal Charges', 'wc-multivendor-marketplace'), 'name' => 'withdrawal[withdrawal_charge_type]', 'type' => 'select', 'options' => array( 'no' => __( 'No Charge', 'wc-multivendor-marketplace' ), 'percent' => __( 'Percent', 'wc-multivendor-marketplace' ), 'fixed'   => __( 'Fixed', 'wc-multivendor-marketplace' ), 'percent_fixed' => __( 'Percent + Fixed', 'wc-multivendor-marketplace' ) ), 'class' => 'wcfm-select wcfm_ele withdrawal_mode_field withdrawal_mode_vendor', 'label_class' => 'wcfm_title wcfm_ele withdrawal_mode_field withdrawal_mode_vendor', 'desc_class' => 'wcfm_page_options_desc withdrawal_mode_field withdrawal_mode_vendor', 'value' => $withdrawal_charge_type , 'desc' => __('Charges applicable for each withdarwal.', 'wc-multivendor-marketplace') ),
																																											"withdrawal_setting_break_2" => array( 'type' => 'html', 'value' => '<div style="height: 15px;"></div>' ),
																																											
																																											
																																											"withdrawal_charge_paypal" => array( 'label' => __('PayPal Charge', 'wc-multivendor-marketplace'), 'type' => 'multiinput', 'name' => 'withdrawal[withdrawal_charge][paypal]', 'class' => 'withdrawal_mode_field withdrawal_mode_vendor withdraw_charge_block withdraw_charge_paypal', 'label_class' => 'wcfm_title wcfm_ele wcfm_fill_ele withdrawal_mode_field withdrawal_mode_vendor withdraw_charge_block withdraw_charge_paypal', 'value' => $withdrawal_charge_paypal, 'custom_attributes' => array( 'limit' => 1 ), 'options' => array(
																																																											"percent" => array('label' => __('Percent Charge(%)', 'wc-multivendor-marketplace'),  'type' => 'number', 'class' => 'wcfm-text wcfm_ele withdraw_charge_field withdraw_charge_percent withdraw_charge_percent_fixed', 'label_class' => 'wcfm_title wcfm_ele withdraw_charge_field withdraw_charge_percent withdraw_charge_percent_fixed', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
																																																											"fixed" => array('label' => __('Fixed Charge', 'wc-multivendor-marketplace'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele withdraw_charge_field withdraw_charge_fixed withdraw_charge_percent_fixed', 'label_class' => 'wcfm_title wcfm_ele withdraw_charge_field withdraw_charge_fixed withdraw_charge_percent_fixed', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
																																																											"tax" => array('label' => __('Charge Tax', 'wc-multivendor-marketplace'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'min' => '0.1', 'step' => '0.1'), 'hints' => __( 'Tax for withdrawal charge, calculate in percent.', 'wc-multivendor-marketplace' ) ),
																																																											) ),
																																											"withdrawal_charge_stripe" => array( 'label' => __('Stripe Charge', 'wc-multivendor-marketplace'), 'type' => 'multiinput', 'name' => 'withdrawal[withdrawal_charge][stripe]', 'class' => 'withdrawal_mode_field withdrawal_mode_vendor withdraw_charge_block withdraw_charge_stripe', 'label_class' => 'wcfm_title wcfm_ele wcfm_fill_ele withdrawal_mode_field withdrawal_mode_vendor withdraw_charge_block withdraw_charge_stripe', 'value' => $withdrawal_charge_stripe, 'custom_attributes' => array( 'limit' => 1 ), 'options' => array(
																																																													"percent" => array('label' => __('Percent Charge(%)', 'wc-multivendor-marketplace'),  'type' => 'number', 'class' => 'wcfm-text wcfm_ele withdraw_charge_field withdraw_charge_percent withdraw_charge_percent_fixed', 'label_class' => 'wcfm_title wcfm_ele withdraw_charge_field withdraw_charge_percent withdraw_charge_percent_fixed', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
																																																													"fixed" => array('label' => __('Fixed Charge', 'wc-multivendor-marketplace'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele withdraw_charge_field withdraw_charge_fixed withdraw_charge_percent_fixed', 'label_class' => 'wcfm_title wcfm_ele withdraw_charge_field withdraw_charge_fixed withdraw_charge_percent_fixed', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
																																																													"tax" => array('label' => __('Charge Tax', 'wc-multivendor-marketplace'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'min' => '0.1', 'step' => '0.1'), 'hints' => __( 'Tax for withdrawal charge, calculate in percent.', 'wc-multivendor-marketplace' ) ),
																																																													) ),
																																											"withdrawal_charge_skrill" => array( 'label' => __('Skrill Charge', 'wc-multivendor-marketplace'), 'type' => 'multiinput', 'name' => 'withdrawal[withdrawal_charge][skrill]', 'class' => 'withdrawal_mode_field withdrawal_mode_vendor withdraw_charge_block withdraw_charge_skrill', 'label_class' => 'wcfm_title wcfm_ele wcfm_fill_ele withdrawal_mode_field withdrawal_mode_vendor withdraw_charge_block withdraw_charge_skrill', 'value' => $withdrawal_charge_skrill, 'custom_attributes' => array( 'limit' => 1 ), 'options' => array(
																																																													"percent" => array('label' => __('Percent Charge(%)', 'wc-multivendor-marketplace'),  'type' => 'number', 'class' => 'wcfm-text wcfm_ele withdraw_charge_field withdraw_charge_percent withdraw_charge_percent_fixed', 'label_class' => 'wcfm_title wcfm_ele withdraw_charge_field withdraw_charge_percent withdraw_charge_percent_fixed', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
																																																													"fixed" => array('label' => __('Fixed Charge', 'wc-multivendor-marketplace'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele withdraw_charge_field withdraw_charge_fixed withdraw_charge_percent_fixed', 'label_class' => 'wcfm_title wcfm_ele withdraw_charge_field withdraw_charge_fixed withdraw_charge_percent_fixed', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
																																																													"tax" => array('label' => __('Charge Tax', 'wc-multivendor-marketplace'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'min' => '0.1', 'step' => '0.1'), 'hints' => __( 'Tax for withdrawal charge, calculate in percent.', 'wc-multivendor-marketplace' ) ),
																																																													) ),
																																											"withdrawal_charge_bank_transfer" => array( 'label' => __('Bank Transfer Charge', 'wc-multivendor-marketplace'), 'type' => 'multiinput', 'name' => 'withdrawal[withdrawal_charge][bank_transfer]', 'class' => 'withdrawal_mode_field withdrawal_mode_vendor withdraw_charge_block withdraw_charge_bank_transfer', 'label_class' => 'wcfm_title wcfm_ele wcfm_fill_ele withdrawal_mode_field withdrawal_mode_vendor withdraw_charge_block withdraw_charge_bank_transfer', 'value' => $withdrawal_charge_bank_transfer, 'custom_attributes' => array( 'limit' => 1 ), 'options' => array(
																																																													"percent" => array('label' => __('Percent Charge(%)', 'wc-multivendor-marketplace'),  'type' => 'number', 'class' => 'wcfm-text wcfm_ele withdraw_charge_field withdraw_charge_percent withdraw_charge_percent_fixed', 'label_class' => 'wcfm_title wcfm_ele withdraw_charge_field withdraw_charge_percent withdraw_charge_percent_fixed', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
																																																													"fixed" => array('label' => __('Fixed Charge', 'wc-multivendor-marketplace'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele withdraw_charge_field withdraw_charge_fixed withdraw_charge_percent_fixed', 'label_class' => 'wcfm_title wcfm_ele withdraw_charge_field withdraw_charge_fixed withdraw_charge_percent_fixed', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
																																																													"tax" => array('label' => __('Charge Tax', 'wc-multivendor-marketplace'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'min' => '0.1', 'step' => '0.1'), 'hints' => __( 'Tax for withdrawal charge, calculate in percent.', 'wc-multivendor-marketplace' ) ),
																																																													) )
																																											) ) );
						?>
					</div>
					
					<?php if( apply_filters( 'wcfm_is_allow_store_hours', true ) ) { ?>
						<div class="wcfm_vendor_settings_heading"><h3><?php _e('Store Hours Setting', 'wc-multivendor-marketplace'); ?></h3></div>
						<div class="store_address">
					
							<?php
								$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_vendors_settings_fields_store_hours', array(
																																																													"wcfm_store_hours" => array( 'label' => __( 'Enable Store Hours', 'wc-multivendor-marketplace'), 'name' => 'wcfm_store_hours[enable]', 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title wcfm_ele', 'value' => 'yes', 'dfvalue' => $wcfm_store_hours_enable ),
																																																													"wcfm_disable_purchase_off_time" => array( 'label' => __('Disable Purchase During OFF Time', 'wc-multivendor-marketplace'), 'name' => 'wcfm_store_hours[disable_purchase]', 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => 'yes', 'dfvalue' => $wcfm_store_hours_disable_purchase ),
																																																													"wcfm_store_hours_off_days" => array( 'label' => __( 'Set Week OFF', 'wc-multivendor-marketplace'), 'type' => 'select', 'name' => 'wcfm_store_hours[off_days]', 'attributes' => array( 'multiple' => 'multiple', 'style' => 'width: 60%;' ), 'options' => array( 0 => __( 'Monday', 'wc-multivendor-marketplace' ), 1 => __( 'Tuesday', 'wc-multivendor-marketplace' ), 2 => __( 'Wednesday', 'wc-multivendor-marketplace' ), 3 => __( 'Thrusday', 'wc-multivendor-marketplace' ), 4 => __( 'Friday', 'wc-multivendor-marketplace' ), 5 => __( 'Saturday', 'wc-multivendor-marketplace' ), 6 => __( 'Sunday', 'wc-multivendor-marketplace') ), 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title', 'value' => $wcfm_store_hours_off_days ),
																																																												 ) ) );
							?>
							
							<div class="wcfm_vendor_settings_heading"><h3><?php _e( 'Daily Basis Opening & Closing Hours', 'wc-multivendor-marketplace' ); ?></h3></div>
							
							<?php
							$WCFM->wcfm_fields->wcfm_generate_form_field( array( 
								"wcfm_store_hours_mon_min_time" => array( 'name' => 'wcfm_store_hours[day_times][0][start]', 'label' => __('Monday', 'wc-multivendor-marketplace'), 'placeholder' => __('Opening Hours', 'wc-multivendor-marketplace'), 'type' => 'time', 'class' => 'wcfm-text wcfm_ele wcfm_store_hours_field wcfm_store_hours_fields wcfm_store_hours_fields_0', 'label_class' => 'wcfm_title wcfm_store_hours_label  wcfm_store_hours_fields wcfm_store_hours_fields_0', 'value' => $wcfm_store_hours_mon_min_time ),
								"wcfm_store_hours_mon_max_time" => array( 'name' => 'wcfm_store_hours[day_times][0][end]', 'placeholder' => __('Closing Hours', 'wc-multivendor-marketplace'), 'type' => 'time', 'class' => 'wcfm-text wcfm_ele wcfm_store_hours_field  wcfm_store_hours_fields wcfm_store_hours_fields_0', 'value' => $wcfm_store_hours_mon_max_time ),
								"wcfm_store_hours_thu_min_time" => array( 'name' => 'wcfm_store_hours[day_times][1][start]', 'label' => __('Tuesday', 'wc-multivendor-marketplace'), 'placeholder' => __('Opening Hours', 'wc-multivendor-marketplace'), 'type' => 'time', 'class' => 'wcfm-text wcfm_ele wcfm_store_hours_field  wcfm_store_hours_fields wcfm_store_hours_fields_1', 'label_class' => 'wcfm_title wcfm_store_hours_label  wcfm_store_hours_fields wcfm_store_hours_fields_1', 'value' => $wcfm_store_hours_thu_min_time ),
								"wcfm_store_hours_thu_max_time" => array( 'name' => 'wcfm_store_hours[day_times][1][end]', 'placeholder' => __('Closing Hours', 'wc-multivendor-marketplace'), 'type' => 'time', 'class' => 'wcfm-text wcfm_ele wcfm_store_hours_field  wcfm_store_hours_fields wcfm_store_hours_fields_1', 'value' => $wcfm_store_hours_thu_max_time ),
								"wcfm_store_hours_wed_min_time" => array( 'name' => 'wcfm_store_hours[day_times][2][start]', 'label' => __('Wednesday', 'wc-multivendor-marketplace'), 'placeholder' => __('Opening Hours', 'wc-multivendor-marketplace'), 'type' => 'time', 'class' => 'wcfm-text wcfm_ele wcfm_store_hours_field  wcfm_store_hours_fields wcfm_store_hours_fields_2', 'label_class' => 'wcfm_title wcfm_store_hours_label  wcfm_store_hours_fields wcfm_store_hours_fields_2', 'value' => $wcfm_store_hours_wed_min_time ),
								"wcfm_store_hours_wed_max_time" => array( 'name' => 'wcfm_store_hours[day_times][2][end]', 'placeholder' => __('Closing Hours', 'wc-multivendor-marketplace'), 'type' => 'time', 'class' => 'wcfm-text wcfm_ele wcfm_store_hours_field  wcfm_store_hours_fields wcfm_store_hours_fields_2', 'value' => $wcfm_store_hours_wed_max_time ),
								"wcfm_store_hours_thur_min_time" => array( 'name' => 'wcfm_store_hours[day_times][3][start]', 'label' => __('Thursday', 'wc-multivendor-marketplace'), 'placeholder' => __('Opening Hours', 'wc-multivendor-marketplace'), 'type' => 'time', 'class' => 'wcfm-text wcfm_ele wcfm_store_hours_field  wcfm_store_hours_fields wcfm_store_hours_fields_3', 'label_class' => 'wcfm_title wcfm_store_hours_label  wcfm_store_hours_fields wcfm_store_hours_fields_3', 'value' => $wcfm_store_hours_thur_min_time ),
								"wcfm_store_hours_thur_max_time" => array( 'name' => 'wcfm_store_hours[day_times][3][end]', 'placeholder' => __('Closing Hours', 'wc-multivendor-marketplace'), 'type' => 'time', 'class' => 'wcfm-text wcfm_ele wcfm_store_hours_field  wcfm_store_hours_fields wcfm_store_hours_fields_3', 'value' => $wcfm_store_hours_thur_max_time ),
								"wcfm_store_hours_fri_min_time" => array( 'name' => 'wcfm_store_hours[day_times][4][start]', 'label' => __('Friday', 'wc-multivendor-marketplace'), 'placeholder' => __('Opening Hours', 'wc-multivendor-marketplace'), 'type' => 'time', 'class' => 'wcfm-text wcfm_ele wcfm_store_hours_field  wcfm_store_hours_fields wcfm_store_hours_fields_4', 'label_class' => 'wcfm_title wcfm_store_hours_label  wcfm_store_hours_fields wcfm_store_hours_fields_4', 'value' => $wcfm_store_hours_fri_min_time ),
								"wcfm_store_hours_fri_max_time" => array( 'name' => 'wcfm_store_hours[day_times][4][end]', 'placeholder' => __('Closing Hours', 'wc-multivendor-marketplace'), 'type' => 'time', 'class' => 'wcfm-text wcfm_ele wcfm_store_hours_field  wcfm_store_hours_fields wcfm_store_hours_fields_4', 'value' => $wcfm_store_hours_fri_max_time ),
								"wcfm_store_hours_sat_min_time" => array( 'name' => 'wcfm_store_hours[day_times][5][start]', 'label' => __('Saturday', 'wc-multivendor-marketplace'), 'placeholder' => __('Opening Hours', 'wc-multivendor-marketplace'), 'type' => 'time', 'class' => 'wcfm-text wcfm_ele wcfm_store_hours_field  wcfm_store_hours_fields wcfm_store_hours_fields_5', 'label_class' => 'wcfm_title wcfm_store_hours_label  wcfm_store_hours_fields wcfm_store_hours_fields_5', 'value' => $wcfm_store_hours_sat_min_time ),
								"wcfm_store_hours_sat_max_time" => array( 'name' => 'wcfm_store_hours[day_times][5][end]', 'placeholder' => __('Closing Hours', 'wc-multivendor-marketplace'), 'type' => 'time', 'class' => 'wcfm-text wcfm_ele wcfm_store_hours_field  wcfm_store_hours_fields wcfm_store_hours_fields_5', 'value' => $wcfm_store_hours_sat_max_time ),
								"wcfm_store_hours_sun_min_time" => array( 'name' => 'wcfm_store_hours[day_times][6][start]', 'label' => __('Sunday', 'wc-multivendor-marketplace'), 'placeholder' => __('Opening Hours', 'wc-multivendor-marketplace'), 'type' => 'time', 'class' => 'wcfm-text wcfm_ele wcfm_store_hours_field  wcfm_store_hours_fields wcfm_store_hours_fields_6', 'label_class' => 'wcfm_title wcfm_store_hours_label  wcfm_store_hours_fields wcfm_store_hours_fields_6', 'value' => $wcfm_store_hours_sun_min_time ),
								"wcfm_store_hours_sun_max_time" => array( 'name' => 'wcfm_store_hours[day_times][6][end]', 'placeholder' => __('Closing Hours', 'wc-multivendor-marketplace'), 'type' => 'time', 'class' => 'wcfm-text wcfm_ele wcfm_store_hours_field  wcfm_store_hours_fields wcfm_store_hours_fields_6', 'value' => $wcfm_store_hours_sun_max_time ),
								));
							?>
						</div>
					<?php } ?>
						
					<?php if( WCFMmp_Dependencies::wcfm_plugin_active_check() && WCFM_Dependencies::wcfmu_plugin_active_check() ) { ?>
						<div class="wcfm_clearfix"></div>
						<div class="wcfm_vendor_settings_heading"><h3><?php _e('Vacation Mode', 'wc-frontend-manager'); ?></h3></div>
						<div class="store_address">
							<?php
								$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_vendors_settings_fields_vacation', array(
																																																													"wcfm_vacation_mode" => array('label' => __('Enable Vacation Mode', 'wc-frontend-manager') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title wcfm_ele', 'value' => 'yes', 'dfvalue' => $wcfm_vacation_mode ),
																																																													"wcfm_disable_vacation_purchase" => array('label' => __('Disable Purchase During Vacation', 'wc-frontend-manager') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => 'yes', 'dfvalue' => $wcfm_disable_vacation_purchase ),
																																																													"wcfm_vacation_mode_type" => array('label' => __('Vacation Type', 'wc-frontend-manager') , 'type' => 'select', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'options' => array( 'instant' => __( 'Instantly Close', 'wc-frontend-manager' ), 'date_wise' => __( 'Date wise close', 'wc-frontend-manager' ) ), 'value' => $wcfm_vacation_mode_type ),
																																																													"wcfm_vacation_start_date" => array('label' => __('From', 'wc-frontend-manager'), 'type' => 'text', 'placeholder' => 'From... YYYY-MM-DD', 'class' => 'wcfm-text wcfm_ele date_wise_vacation_ele', 'label_class' => 'wcfm_title wcfm_ele date_wise_vacation_ele', 'value' => $wcfm_vacation_start_date),
																																																													"wcfm_vacation_end_date" => array('label' => __('Upto', 'wc-frontend-manager'), 'type' => 'text', 'placeholder' => 'To... YYYY-MM-DD', 'class' => 'wcfm-text wcfm_ele date_wise_vacation_ele', 'label_class' => 'wcfm_title wcfm_ele date_wise_vacation_ele', 'value' => $wcfm_vacation_end_date),
																																																													"wcfm_vacation_mode_msg" => array('label' => __('Vacation Message', 'wc-frontend-manager') , 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $wcfm_vacation_mode_msg )
																																																												 ) ) );
							?>
						</div>
					<?php } ?>
					
					<?php if( $vendor_id != 99999 ) { ?>
					<div class="wcfm-clearfix"></div>
					<div class="wcfm-message" tabindex="-1"></div>
					<div class="wcfm-clearfix"></div>
					<div id="wcfm_messages_submit">
						<input type="submit" name="save-data" value="<?php _e( 'Update', 'wc-frontend-manager' ); ?>" id="wcfm_store_setting_save_button" class="wcfm_submit_button" />
					</div>
					<div class="wcfm-clearfix"></div>
				</form>
				<?php } ?>
			</div>
		</div>
		<div class="wcfm_clearfix"></div>
		<?php if( $vendor_id != 99999 ) { ?>
			<br />
		<?php } ?>
		<!-- end collapsible -->
		
		<?php
	}
	
	/**
	 * Bulk Store Assign / Change
	 */
	function wcfmmp_bulk_store_edit() {
		global $WCFM, $WCFMmp, $wpdb;
		
		?>
		<label>
		  <span class="wcfm_popup_label title"><?php esc_html_e( 'Store', 'wc-multivendor-marketplace' ); ?></span>
				<span class="input-text-wrap">
					<select class="wcfmmp_store wcfm_popup_input" name="_wcfmmp_store">
					<?php
					$vendor_arr = $WCFM->wcfm_vendor_support->wcfm_get_vendor_list();
					unset($vendor_arr[0]);
					echo '<option value="">' . __( '— No change —', 'woocommerce' ) . '</option>';
					foreach ( $vendor_arr as $key => $value ) {
						echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
					}
					?>
				</select>
			</span>
		</label>
		<?php
	}
	
	/**
	 * Bulk Store Edit Save
	 */
	function wcfmmp_bulk_store_edit_save( $product ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		if ( isset( $_REQUEST['_wcfmmp_store'] ) && ! empty( $_REQUEST['_wcfmmp_store'] ) ) {
			$arg = array(
				'ID' => $product->get_id(),
				'post_author' => absint($_REQUEST['_wcfmmp_store']),
			);
			wp_update_post( $arg );
		}
	}
	
	/**
	 * Bulk Store Edit Save - Ultimate
	 */
	function wcfmmpu_bulk_store_edit_save( $product, $wcfm_bulk_edit_form_data ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		if ( isset( $wcfm_bulk_edit_form_data['_wcfmmp_store'] ) && ! empty( $wcfm_bulk_edit_form_data['_wcfmmp_store'] ) ) {
			$arg = array(
				'ID' => $product->get_id(),
				'post_author' => absint($wcfm_bulk_edit_form_data['_wcfmmp_store']),
			);
			wp_update_post( $arg );
		}
	}
	
	/**
	 * Vednor Profile Additional Info
	 */
	function wcfmmp_profile_additional_info( $vendor_id ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		$wcfmmp_addition_info_fields = get_option( 'wcfmvm_registration_custom_fields', array() );
		if( empty( $wcfmmp_addition_info_fields ) ) return;
		
		$has_addition_field = false;
		if( !empty( $wcfmmp_addition_info_fields ) ) {
			foreach( $wcfmmp_addition_info_fields as $wcfmvm_registration_custom_field ) {
				if( !isset( $wcfmvm_registration_custom_field['enable'] ) ) continue;
				if( !$wcfmvm_registration_custom_field['label'] ) continue;
				$has_addition_field = true;
				break;
			}
		}
		if( !$has_addition_field ) return;
		$wcfmvm_custom_infos = (array) get_user_meta( $vendor_id, 'wcfmvm_custom_infos', true );
		?>
		<!-- collapsible -->
		<div class="page_collapsible" id="wcfm_profile_form_additional_info_head">
			<label class="fa fa-star"></label>
			<?php _e('Additional Info', 'wc-multivendor-marketplace'); ?><span></span>
		</div>
		<div class="wcfm-container">
			<div id="wcfm_profile_form_additional_info_expander" class="wcfm-content">
			  <?php
			  if( !empty( $wcfmmp_addition_info_fields ) ) {
					foreach( $wcfmmp_addition_info_fields as $wcfmvm_registration_custom_field ) {
						if( !isset( $wcfmvm_registration_custom_field['enable'] ) ) continue;
						if( !$wcfmvm_registration_custom_field['label'] ) continue;
						$field_value = '';
						$wcfmvm_registration_custom_field['name'] = sanitize_title( $wcfmvm_registration_custom_field['label'] );
						$field_name = 'wcfmmp_additional_infos[' . $wcfmvm_registration_custom_field['name'] . ']';
					
						if( !empty( $wcfmvm_custom_infos ) ) {
							if( $wcfmvm_registration_custom_field['type'] == 'checkbox' ) {
								$field_value = isset( $wcfmvm_custom_infos[$wcfmvm_registration_custom_field['name']] ) ? $wcfmvm_custom_infos[$wcfmvm_registration_custom_field['name']] : 'no';
							} else {
								$field_value = isset( $wcfmvm_custom_infos[$wcfmvm_registration_custom_field['name']] ) ? $wcfmvm_custom_infos[$wcfmvm_registration_custom_field['name']] : '';
							}
						}
						
						// Is Required
						$custom_attributes = array();
						if( isset( $wcfmvm_registration_custom_field['required'] ) && $wcfmvm_registration_custom_field['required'] ) $custom_attributes = array( 'required' => 1 );
							
						switch( $wcfmvm_registration_custom_field['type'] ) {
							case 'text':
							case 'upload':
								$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $wcfmvm_registration_custom_field['name'] => array( 'label' => __($wcfmvm_registration_custom_field['label'], 'wc-multivendor-membership') , 'name' => $field_name, 'custom_attributes' => $custom_attributes, 'type' => 'text', 'class' => 'wcfm-text', 'label_class' => 'wcfm_title', 'value' => $field_value, 'hints' => __($wcfmvm_registration_custom_field['help_text'], 'wc-multivendor-membership') ) ) );
							break;
							
							case 'number':
								$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $wcfmvm_registration_custom_field['name'] => array( 'label' => __($wcfmvm_registration_custom_field['label'], 'wc-multivendor-membership') , 'name' => $field_name, 'custom_attributes' => $custom_attributes, 'type' => 'number', 'class' => 'wcfm-text', 'label_class' => 'wcfm_title', 'value' => $field_value, 'hints' => __($wcfmvm_registration_custom_field['help_text'], 'wc-multivendor-membership') ) ) );
							break;
							
							case 'textarea':
								$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $wcfmvm_registration_custom_field['name'] => array( 'label' => __($wcfmvm_registration_custom_field['label'], 'wc-multivendor-membership') , 'name' => $field_name, 'custom_attributes' => $custom_attributes, 'type' => 'textarea', 'class' => 'wcfm-textarea', 'label_class' => 'wcfm_title', 'value' => $field_value, 'hints' => __($wcfmvm_registration_custom_field['help_text'], 'wc-multivendor-membership') ) ) );
							break;
							
							case 'datepicker':
								$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $wcfmvm_registration_custom_field['name'] => array( 'label' => __($wcfmvm_registration_custom_field['label'], 'wc-multivendor-membership') , 'name' => $field_name, 'custom_attributes' => $custom_attributes, 'type' => 'text', 'placeholder' => 'YYYY-MM-DD', 'class' => 'wcfm-text', 'label_class' => 'wcfm_title', 'value' => $field_value, 'hints' => __($wcfmvm_registration_custom_field['help_text'], 'wc-multivendor-membership') ) ) );
							break;
							
							case 'timepicker':
								$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $wcfmvm_registration_custom_field['name'] => array( 'label' => __($wcfmvm_registration_custom_field['label'], 'wc-multivendor-membership') , 'name' => $field_name, 'custom_attributes' => $custom_attributes, 'type' => 'time', 'class' => 'wcfm-text', 'label_class' => 'wcfm_title', 'value' => $field_value, 'hints' => __($wcfmvm_registration_custom_field['help_text'], 'wc-multivendor-membership') ) ) );
							break;
							
							case 'checkbox':
								$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $wcfmvm_registration_custom_field['name'] => array( 'label' => __($wcfmvm_registration_custom_field['label'], 'wc-multivendor-membership') , 'name' => $field_name, 'custom_attributes' => $custom_attributes, 'type' => 'checkbox', 'class' => 'wcfm-checkbox', 'label_class' => 'wcfm_title checkbox-title', 'value' => 'yes', 'dfvalue' => $field_value, 'hints' => __($wcfmvm_registration_custom_field['help_text'], 'wc-multivendor-membership') ) ) );
							break;
							
							case 'upload':
								//$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $wcfmvm_registration_custom_field['name'] => array( 'label' => __($wcfmvm_registration_custom_field['label'], 'wc-multivendor-membership') , 'name' => $field_name, 'type' => 'upload', 'class' => 'wcfm_ele', 'label_class' => 'wcfm_title', 'value' => $field_value, 'hints' => $wcfmvm_registration_custom_field['help_text'] ) ) );
							break;
							
							case 'select':
								$select_opt_vals = array();
								$select_options = explode( '|', $wcfmvm_registration_custom_field['options'] );
								if( !empty ( $select_options ) ) {
									foreach( $select_options as $select_option ) {
										if( $select_option ) {
											$select_opt_vals[$select_option] = __(ucfirst( str_replace( "-", " " , $select_option ) ), 'wc-multivendor-membership');
										}
									}
								}
								$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $wcfmvm_registration_custom_field['name'] => array( 'label' => __($wcfmvm_registration_custom_field['label'], 'wc-multivendor-membership') , 'name' => $field_name, 'custom_attributes' => $custom_attributes, 'type' => 'select', 'class' => 'wcfm-select', 'label_class' => 'wcfm_title', 'options' => $select_opt_vals, 'value' => $field_value, 'hints' => __($wcfmvm_registration_custom_field['help_text'], 'wc-multivendor-membership') ) ) );
							break;
						}
					}
				}
				?>
			</div>
		</div>
		<?php
	}
	
	/**
	 * Vendor Profile Additional Info Update
	 */
	function wcfmmp_profile_additional_info_update( $vendor_id, $wcfm_profile_form ){
		global $WCFM, $WCFMmp, $wpdb;
		
		if( isset( $wcfm_profile_form['wcfmmp_additional_infos'] ) ) {
			update_user_meta( $vendor_id, 'wcfmvm_custom_infos', $wcfm_profile_form['wcfmmp_additional_infos'] );
			
			// Toolset User Fields Compatibility added
			$wcfmmp_addition_info_fields = get_option( 'wcfmvm_registration_custom_fields', array() );
			$wcfmvm_custom_infos = (array) $wcfm_profile_form['wcfmmp_additional_infos'];
			
			if( !empty( $wcfmmp_addition_info_fields ) ) {
				foreach( $wcfmmp_addition_info_fields as $wcfmvm_registration_custom_field ) {
					if( !isset( $wcfmvm_registration_custom_field['enable'] ) ) continue;
					if( !$wcfmvm_registration_custom_field['label'] ) continue;
					$field_value = '';
					$wcfmvm_registration_custom_field['name'] = sanitize_title( $wcfmvm_registration_custom_field['label'] );
				
					if( !empty( $wcfmvm_custom_infos ) ) {
						if( $wcfmvm_registration_custom_field['type'] == 'checkbox' ) {
							$field_value = isset( $wcfmvm_custom_infos[$wcfmvm_registration_custom_field['name']] ) ? $wcfmvm_custom_infos[$wcfmvm_registration_custom_field['name']] : 'no';
						} else {
							$field_value = isset( $wcfmvm_custom_infos[$wcfmvm_registration_custom_field['name']] ) ? $wcfmvm_custom_infos[$wcfmvm_registration_custom_field['name']] : '';
						}
					}
					if( !$field_value ) $field_value = '';
					update_user_meta( $vendor_id, $wcfmvm_registration_custom_field['name'], $field_value );
				}
			}
		}
	}
	
	/**
	 * Vednor Ledger update on new commission processed
	 */
	function wcfmmp_order_item_processed_ledger_update( $commission_id, $order_id, $order, $vendor_id, $product_id, $order_item_id, $grosse_total, $total_commission, $is_auto_withdrawal ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		$reference_details = sprintf( __( 'Commission for %s order.', 'wc-multivendor-marketplace' ), '<b>' . get_the_title( $product_id ) . '</b>' );
		$this->wcfmmp_ledger_update( $vendor_id, $commission_id, $total_commission, 0, 'order', $reference_details );
	}
	
	/**
	 * Vednor Ledger update on new withdrawal request processed
	 */
	function wcfmmp_withdraw_request_processed_ledger_update( $withdraw_request_id, $vendor_id, $order_ids, $commission_ids, $withdraw_amount, $withdraw_charges, $withdraw_status, $withdraw_mode, $is_auto_withdrawal ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		// Withdrawal Charges Ledger Entry
		if( $withdraw_charges ) {
			$reference_details = __( 'Withdrawal Charges.', 'wc-multivendor-marketplace' );
			$this->wcfmmp_ledger_update( $vendor_id, $withdraw_request_id, 0, $withdraw_charges, 'withdraw-charges', $reference_details );
			$withdraw_amount = (float)$withdraw_amount - (float)$withdraw_charges;
		}  
		
		if( $is_auto_withdrawal ) {
			$reference_details = __( 'Auto withdrawal by paymode.', 'wc-multivendor-marketplace' );
		} elseif( $withdraw_mode == 'by_split_pay' ) {
			$reference_details = __( 'Withdrawal by Stripe Split Pay.', 'wc-multivendor-marketplace' );
		} else {
			$reference_details = __( 'Withdrawal by request.', 'wc-multivendor-marketplace' );
		}
		$this->wcfmmp_ledger_update( $vendor_id, $withdraw_request_id, 0, $withdraw_amount, 'withdraw', $reference_details );
	}
	
	/**
	 * Vednor Ledger update on new reverse withdrawal request processed
	 */
	function wcfmmp_reverse_withdraw_request_processed_ledger_update( $reverse_withdraw_request_id, $vendor_id, $order_id, $commission_id, $grosse_total, $withdraw_amount, $balance, $withdraw_status, $withdraw_mode, $is_auto_withdrawal ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		$reference_details = __( 'Reverse pay for auto withdrawal.', 'wc-multivendor-marketplace' );
		$this->wcfmmp_ledger_update( $vendor_id, $reverse_withdraw_request_id, 0, $balance, 'reverse-withdraw', $reference_details );
	}
	
	/**
	 * Vednor Ledger update on new refund request processed
	 */
	function wcfmmp_refund_request_processed_ledger_update( $refund_request_id, $vendor_id, $order_id, $commission_id, $refunded_amount, $refund_type ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		if( !$order_id ) return;
		if( !$vendor_id ) return;
		if( !$commission_id ) return;
		
		if( wcfm_is_vendor() ) {
			$reference_details = __( 'Request by Vendor.', 'wc-multivendor-marketplace' );
		} elseif( current_user_can('administrator') ) {
			$reference_details = __( 'Request by Admin.', 'wc-multivendor-marketplace' );
		} else {
			$reference_details = __( 'Request by Customer.', 'wc-multivendor-marketplace' );
		}
		$this->wcfmmp_ledger_update( $vendor_id, $refund_request_id, 0, $refunded_amount, $refund_type, $reference_details );
	}
	
	/**
	 * Vendor Ledger Update
	 */
	public function wcfmmp_ledger_update( $vendor_id, $reference_id, $credit = 0, $debit = 0, $reference = 'order', $reference_details = '', $reference_status = 'pending' ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		$wpdb->query(
						$wpdb->prepare(
							"INSERT INTO `{$wpdb->prefix}wcfm_marketplace_vendor_ledger` 
									( vendor_id
									, credit
									, debit
									, reference_id
									, reference
									, reference_details
									, reference_status
									) VALUES ( %d
									, %s
									, %s
									, %d
									, %s
									, %s
									, %s 
									) ON DUPLICATE KEY UPDATE `created` = now()"
							, $vendor_id
							, $credit
							, $debit
							, $reference_id
							, $reference
							, $reference_details
							, $reference_status
			)
		);
		$ledger_id = $wpdb->insert_id;
		do_action( 'after_wcfmmp_ledger_update', $ledger_id, $reference_id, $reference, $credit, $debit );
	}
	
	/**
	 * Vendor Ledger Entry Status Update
	 */
	public function wcfmmp_ledger_status_update( $reference_id, $reference_status  = 'completed', $reference = 'order' ) {
		global $WCFM, $WCFMmp, $wpdb;
		if( !$reference_id ) return;
		$wpdb->update("{$wpdb->prefix}wcfm_marketplace_vendor_ledger", array('reference_status' => $reference_status, 'reference_update_date' => date('Y-m-d H:i:s', time())), array('reference_id' => $reference_id, 'reference' => $reference), array('%s', '%s'), array('%d', '%s'));
		do_action( 'wcfmmp_ledger_status_updated', $reference_id, $reference_status );
	}
	
	/**
	 * Return whether vendor get shipping or not
	 */
	public function is_vendor_get_shipping( $vendor_id ) {
		global $WCFM, $WCFMmp, $wpdb;
		if( !$vendor_id ) return false;
		
		$vendor_get_shipping = true;
		
		$vendor_data = get_user_meta( $vendor_id, 'wcfmmp_profile_settings', true );
		$vendor_commission_mode = isset( $vendor_data['commission']['commission_mode'] ) ? $vendor_data['commission']['commission_mode'] : 'global';
		$vendor_get_shipping = isset( $vendor_data['commission']['get_shipping'] ) ? true : false;
		
		if( $vendor_commission_mode == 'global' ) {
			$vendor_get_shipping = isset( $WCFMmp->wcfmmp_commission_options['get_shipping'] ) ? $WCFMmp->wcfmmp_commission_options['get_shipping'] : 'yes';
			if( $vendor_get_shipping != 'yes' ) $vendor_get_shipping = false;
		}
		
		return apply_filters( 'wcfmmp_vendor_get_shipping', $vendor_get_shipping, $vendor_id );
	}
	
	/**
	 * Return whether vendor get tax or not
	 */
	public function is_vendor_get_tax( $vendor_id ) {
		global $WCFM, $WCFMmp, $wpdb;
		if( !$vendor_id ) return false;
		
		$vendor_get_tax = true;
		
		$vendor_data = get_user_meta( $vendor_id, 'wcfmmp_profile_settings', true );
		$vendor_commission_mode = isset( $vendor_data['commission']['commission_mode'] ) ? $vendor_data['commission']['commission_mode'] : 'global';
		$vendor_get_tax = isset( $vendor_data['commission']['get_tax'] ) ? true : false;
		
		if( $vendor_commission_mode == 'global' ) {
			$vendor_get_tax = isset( $WCFMmp->wcfmmp_commission_options['get_tax'] ) ? $WCFMmp->wcfmmp_commission_options['get_tax'] : 'yes';
			if( $vendor_get_tax != 'yes' ) $vendor_get_tax = false;
		}
		
		return apply_filters( 'wcfmmp_vendor_get_tax', $vendor_get_tax, $vendor_id );
	}
	
	/**
	 * Return whether vendor get tax or not
	 */
	public function is_vendor_deduct_discount( $vendor_id ) {
		global $WCFM, $WCFMmp, $wpdb;
		if( !$vendor_id ) return false;
		
		$vendor_coupon_deduct = true;
		
		$vendor_data = get_user_meta( $vendor_id, 'wcfmmp_profile_settings', true );
		$vendor_commission_mode = isset( $vendor_data['commission']['commission_mode'] ) ? $vendor_data['commission']['commission_mode'] : 'global';
		$vendor_coupon_deduct = isset( $vendor_data['commission']['vendor_coupon_deduct'] ) ? true : false;
		
		if( $vendor_commission_mode == 'global' ) {
			$vendor_coupon_deduct = isset( $WCFMmp->wcfmmp_commission_options['coupon_deduct'] ) ? $WCFMmp->wcfmmp_commission_options['coupon_deduct'] : 'yes';
			if( $vendor_coupon_deduct != 'yes' ) $vendor_coupon_deduct = false;
		}
		
		return apply_filters( 'wcfmmp_vendor_coupon_deduct', $vendor_coupon_deduct, $vendor_id );
	}
	
	/**
	 * Vendor Details in Order Details
	 * Policies
	 * Customer Support Info
	 */
	function wcfmmp_vendor_details_in_order( $order ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		if( is_wcfm_page() ) return;
		
		$wcfm_vendor_invoice_options  = get_option( 'wcfm_vendor_invoice_options', array() );
		$wcfm_vendor_invoice_policies = isset( $wcfm_vendor_invoice_options['policies'] ) ? 'yes' : '';
		$order_items                  = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
		
		if( apply_filters( 'wcfm_is_pref_policies', true ) && $wcfm_vendor_invoice_policies ) {
			foreach ( $order_items as $item_id => $item ) {
				$product_id          = $item->get_product_id();
				$vendor_id           = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $product_id );
				if( !$vendor_id || !wcfm_is_vendor( $vendor_id ) ) continue;
				if( $WCFM->wcfm_vendor_support->wcfm_vendor_has_capability( $vendor_id, 'policy' ) && $WCFM->wcfm_vendor_support->wcfm_vendor_has_capability( $vendor_id, 'vendor_policy' ) ) {
					$store_name          = $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_name_by_vendor( $vendor_id );
					$shipping_policy     = $WCFM->wcfm_policy->get_shipping_policy( $product_id );
					$refund_policy       = $WCFM->wcfm_policy->get_refund_policy( $product_id );
					$cancellation_policy = $WCFM->wcfm_policy->get_cancellation_policy( $product_id );
					?>
					<br/>
					<h2 style="font-size: 18px; color: #00798b; line-height: 20px;margin-top: 6px;margin-bottom: 10px;padding: 0px;text-decoration: underline;"><?php echo get_the_title( $product_id ) . ' ('. $store_name .') ' . __( 'Policies', 'wc-multivendor-marketplace' ); ?>:</h2>
					<table width="100%" style="width:100%;">
						<tbody>
							<?php if( !wcfm_empty($shipping_policy) ) { ?>
								<tr>
									<th colspan="3" style="background-color: #eeeeee;padding: 1em 1.41575em;line-height: 1.5;"><strong><?php echo apply_filters('wcfm_shipping_policies_heading', __('Shipping Policy', 'wc-frontend-manager')); ?></strong></th>
									<td colspan="5" style="background-color: #f8f8f8;padding: 1em;"><?php echo $shipping_policy; ?></td>
								</tr>
							<?php } ?>
							<?php if( !wcfm_empty($refund_policy) ) { ?>
								<tr>
									<th colspan="3" style="background-color: #eeeeee;padding: 1em 1.41575em;line-height: 1.5;"><strong><?php echo apply_filters('wcfm_refund_policies_heading', __('Refund Policy', 'wc-frontend-manager')); ?></strong></th>
									<td colspan="5" style="background-color: #f8f8f8;padding: 1em;"><?php echo $refund_policy; ?></td>
								</tr>
							<?php } ?>
							<?php if( !wcfm_empty($cancellation_policy) ) { ?>
								<tr>
									<th colspan="3" style="background-color: #eeeeee;padding: 1em 1.41575em;line-height: 1.5;"><strong><?php echo apply_filters('wcfm_cancellation_policies_heading', __('Cancellation / Return / Exchange Policy', 'wc-frontend-manager')); ?></strong></th>
									<td colspan="5" style="background-color: #f8f8f8;padding: 1em;"><?php echo $cancellation_policy; ?></td>
								</tr>
							<?php } ?>
							
							<?php if( $WCFM->wcfm_vendor_support->wcfm_vendor_has_capability( $vendor_id, 'customer_support' ) ) { ?>
								<tr>
									<th colspan="3" style="background-color: #eeeeee;padding: 1em 1.41575em;line-height: 1.5;"><strong><?php echo apply_filters('wcfm_customer_support_heading', __('Customer Support', 'wc-frontend-manager')); ?></strong></th>
									<td colspan="5" style="background-color: #f8f8f8;padding: 1em;"><?php echo wcfmmp_get_store( $vendor_id )->get_customer_support_details(); ?></td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
					<br/>
					<?php
				}
			}
		}
	}
	
	/**
	 * Store Info with Order Details Item
	 */
	function wcfmmp_order_item_meta_store( $html, $item, $args ) {
		global $WCFM, $WCFMmp;
		
		if( $WCFMmp->wcfmmp_vendor->is_vendor_sold_by() ) {
			$meta_data         = $item->get_meta_data();
			foreach ( $meta_data as $meta ) {
				$meta->key     = rawurldecode( (string) $meta->key );
				$meta->value   = rawurldecode( (string) $meta->value );
				
				if( $meta->key == '_vendor_id' ) {
					$html = '<ul class="wc-item-meta"><li><strong class="wc-item-meta-label">' . __( 'Store', 'wc-frontend-manager' ) . ':</strong> ' . $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_by_vendor( absint($meta->value) ) . '</li></ul>' . $html;
				}
			}
		}
		
		return $html;
	}
	
	/**
	 * Store Off line - Vendors Dashbaord Action 
	 */
	function wcfmmp_vendors_actions( $actions, $wcfm_vendors_id ) {
		
		if( apply_filters( 'wcfm_is_allow_store_off_line', true ) ) {
			$disable_vendor = get_user_meta( $wcfm_vendors_id, '_disable_vendor', true );
			if( !$disable_vendor ) {
				$is_store_offline = get_user_meta( $wcfm_vendors_id, '_wcfm_store_offline', true );
				if( !$is_store_offline ) {
					$actions .= '<a href="#" data-memberid="'.$wcfm_vendors_id.'" class="wcfm_vendor_store_offline_button wcfm-action-icon"><span class="fa fa-power-off text_tip" data-tip="' . __( 'Off-line Vendor Store', 'wc-multivendor-marketplace' ) . '"></span></a>';
				} else {
					$actions .= '<a href="#" data-memberid="'.$wcfm_vendors_id.'" class="wcfm_vendor_store_online_button wcfm-action-icon"><span class="fa fa-globe text_tip" data-tip="' . __( 'On-line Vendor Store', 'wc-multivendor-marketplace' ) . '"></span></a>';
				}
			}
		}
		
		return $actions;
	}
	
	/**
	 * Disable Product purchase for Offline Store
	 */
	function wcfmmp_product_store_is_offline( $is_purchasable, $product ) {
		global $WCFM, $WCFMmp;
		
		$product_id = $product->get_id();
		if( $product_id ) {
			$vendor_id = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $product_id );
			if( $vendor_id ) {
				$is_store_offline = get_user_meta( $vendor_id, '_wcfm_store_offline', true );
				$is_store_offline = apply_filters( 'wcfmmp_is_store_offline', $is_store_offline, $vendor_id );
				if( $is_store_offline ) $is_purchasable = false;
			}
		}
		
		return $is_purchasable;
	}
	
	/**
	 * Load Store setup on first login
	 */
	function wcfmmp_store_setup_on_first_login() {
		global $WCFM, $WCFMmp;
		
		$user_id = $WCFMmp->vendor_id;
		
		if( is_user_logged_in() && wcfm_is_vendor() && $user_id ) {
			$store_setup = get_user_meta( $user_id, '_store_setup', true );
			
			if( !$store_setup ) {
				$redirect_to = admin_url( 'index.php?page=store-setup' );
				if ( function_exists('icl_object_id') ) {
					global $sitepress;
					$redirect_to = add_query_arg( array( 'lang' => $sitepress->get_current_language() ), $redirect_to );
				}
				update_user_meta( $user_id, '_store_setup', 'yes' );
				if( apply_filters( 'wcfm_is_allow_store_setup', true ) ) {
					wp_safe_redirect( $redirect_to );
				}
			}
		}
	}
	
	/**
	 * Vendor Profile complete percent
	 */
	function wcfmmp_vendor_profile_complete_percent( $user_id ) {
		global $WCFM, $WCFMmp;
		
		if( !apply_filters( 'wcfm_is_allow_profile_complete_bar', true ) ) return;
		
		$vendor_data = get_user_meta( $user_id, 'wcfmmp_profile_settings', true );
		
		$profile_complete_components = apply_filters( 'profile_complete_components', array(
																																											 'banner',
																																											 'gravatar',
																																											 'store_name',
																																											 'phone',
																																											 'about',
																																											 'address',
																																											 'location',
																																											 'payment',
																																											 'policy',
																																											 'support',
																																											 'seo',
																																											 'shipping'
																																											) );
		if( !apply_filters( 'wcfm_is_allow_store_logo', true ) ) {
			unset( $profile_complete_components['gravatar'] );
		}
		
		if( !apply_filters( 'wcfm_is_allow_store_name', true ) ) {
			unset( $profile_complete_components['store_name'] );
		}
		
		if( !apply_filters( 'wcfm_is_allow_store_banner', true ) ) {
			unset( $profile_complete_components['banner'] );
		}
		
		if( !apply_filters( 'wcfm_is_allow_store_phone', true ) ) {
			unset( $profile_complete_components['phone'] );
		}
		
		if( !apply_filters( 'wcfm_is_allow_store_description', true ) ) {
			unset( $profile_complete_components['about'] );
		}
		
		if( !apply_filters( 'wcfm_is_allow_store_address', true ) ) {
			unset( $profile_complete_components['address'] );
		}
		
		$api_key = isset( $WCFMmp->wcfmmp_marketplace_options['wcfm_google_map_api'] ) ? $WCFMmp->wcfmmp_marketplace_options['wcfm_google_map_api'] : '';
		if ( !$api_key ) {
			unset( $profile_complete_components['location'] );
		}
		
		if( !apply_filters( 'wcfm_is_pref_policies', true ) || !apply_filters( 'wcfm_is_allow_policy_settings', true ) ) {
			unset( $profile_complete_components['policy'] );
		}
		
		if( !apply_filters( 'wcfm_is_allow_customer_support_settings', true ) || !apply_filters( 'wcfm_is_allow_customer_support', true ) ) {
			unset( $profile_complete_components['support'] );
		}
		
		if( !apply_filters( 'wcfm_is_allow_vseo_settings', true ) ) {
			unset( $profile_complete_components['seo'] );
		}
		
		if( !apply_filters( 'wcfm_is_allow_shipping', true ) || !apply_filters( 'wcfm_is_allow_vshipping_settings', true ) ) {
			unset( $profile_complete_components['shipping'] );
		}
		
		$component_percent = 100/count($profile_complete_components);
		
		// Store Genral
		$gravatar       = isset( $vendor_data['gravatar'] ) ? absint( $vendor_data['gravatar'] ) : 0;
		$banner         = isset( $vendor_data['banner'] ) ? absint( $vendor_data['banner'] ) : 0;
		$store_name     = isset( $vendor_data['store_name'] ) ? esc_attr( $vendor_data['store_name'] ) : '';
		$phone          = isset( $vendor_data['phone'] ) ? esc_attr( $vendor_data['phone'] ) : '';
		
		// Store Description
		$shop_description = get_user_meta( $user_id, '_store_description', true );
		
		// Address
		$street_1 = isset( $vendor_data['address']['street_1'] ) ? $vendor_data['address']['street_1'] : '';
		$street_2 = isset( $vendor_data['address']['street_2'] ) ? $vendor_data['address']['street_2'] : '';
		$city    = isset( $vendor_data['address']['city'] ) ? $vendor_data['address']['city'] : '';
		$zip     = isset( $vendor_data['address']['zip'] ) ? $vendor_data['address']['zip'] : '';
		$country = isset( $vendor_data['address']['country'] ) ? $vendor_data['address']['country'] : '';
		$state   = isset( $vendor_data['address']['state'] ) ? $vendor_data['address']['state'] : '';
		
		// Location
		$store_location   = isset( $vendor_data['store_location'] ) ? esc_attr( $vendor_data['store_location'] ) : '';
		
		// Payment
		$payment_mode = isset( $vendor_data['payment']['method'] ) ? esc_attr( $vendor_data['payment']['method'] ) : '' ;
		$paypal = isset( $vendor_data['payment']['paypal']['email'] ) ? esc_attr( $vendor_data['payment']['paypal']['email'] ) : '' ;
		$skrill = isset( $vendor_data['payment']['skrill']['email'] ) ? esc_attr( $vendor_data['payment']['skrill']['email'] ) : '' ;
		$ac_number = isset( $vendor_data['payment']['bank']['ac_number'] ) ? esc_attr( $vendor_data['payment']['bank']['ac_number'] ) : '';
		
		// Policy
		$wcfm_policy_vendor_options = (array) get_user_meta( $user_id, 'wcfm_policy_vendor_options', true );
		$_wcfm_vendor_policy_tab_title = isset( $wcfm_policy_vendor_options['policy_tab_title'] ) ? $wcfm_policy_vendor_options['policy_tab_title'] : '';
		$_wcfm_vendor_shipping_policy = isset( $wcfm_policy_vendor_options['shipping_policy'] ) ? $wcfm_policy_vendor_options['shipping_policy'] : '';
		$_wcfm_vendor_refund_policy = isset( $wcfm_policy_vendor_options['refund_policy'] ) ? $wcfm_policy_vendor_options['refund_policy'] : '';
		$_wcfm_vendor_cancellation_policy = isset( $wcfm_policy_vendor_options['cancellation_policy'] ) ? $wcfm_policy_vendor_options['cancellation_policy'] : '';
		
		// SEO
		$wcfmmp_seo_meta_title = isset( $vendor_data['store_seo']['wcfmmp-seo-meta-title'] ) ? $vendor_data['store_seo']['wcfmmp-seo-meta-title'] : '';
		$wcfmmp_seo_meta_desc = isset( $vendor_data['store_seo']['wcfmmp-seo-meta-desc'] ) ? $vendor_data['store_seo']['wcfmmp-seo-meta-desc'] : '';
		$wcfmmp_seo_meta_keywords    = isset( $vendor_data['store_seo']['wcfmmp-seo-meta-keywords'] ) ? $vendor_data['store_seo']['wcfmmp-seo-meta-keywords'] : '';
		$wcfmmp_seo_og_title     = isset( $vendor_data['store_seo']['wcfmmp-seo-og-title'] ) ? $vendor_data['store_seo']['wcfmmp-seo-og-title'] : '';
		$wcfmmp_seo_og_desc = isset( $vendor_data['store_seo']['wcfmmp-seo-og-desc'] ) ? $vendor_data['store_seo']['wcfmmp-seo-og-desc'] : '';
		$wcfmmp_seo_og_image   = isset( $vendor_data['store_seo']['wcfmmp-seo-og-image'] ) ? $vendor_data['store_seo']['wcfmmp-seo-og-image'] : 0;
		$wcfmmp_seo_twitter_title     = isset( $vendor_data['store_seo']['wcfmmp-seo-twitter-title'] ) ? $vendor_data['store_seo']['wcfmmp-seo-twitter-title'] : '';
		$wcfmmp_seo_twitter_desc = isset( $vendor_data['store_seo']['wcfmmp-seo-twitter-desc'] ) ? $vendor_data['store_seo']['wcfmmp-seo-twitter-desc'] : '';
		$wcfmmp_seo_twitter_image   = isset( $vendor_data['store_seo']['wcfmmp-seo-twitter-image'] ) ? $vendor_data['store_seo']['wcfmmp-seo-twitter-image'] : 0;
		
		// Customer Support
		$vendor_customer_phone = isset( $vendor_data['customer_support']['phone'] ) ? $vendor_data['customer_support']['phone'] : '';
		$vendor_customer_email = isset( $vendor_data['customer_support']['email'] ) ? $vendor_data['customer_support']['email'] : '';
		$vendor_csd_return_address1 = isset( $vendor_data['customer_support']['address1'] ) ? $vendor_data['customer_support']['address1'] : '';
		$vendor_csd_return_address2 = isset( $vendor_data['customer_support']['address2'] ) ? $vendor_data['customer_support']['address2'] : '';
		$vendor_csd_return_country = isset( $vendor_data['customer_support']['country'] ) ? $vendor_data['customer_support']['country'] : '';
		$vendor_csd_return_city = isset( $vendor_data['customer_support']['city'] ) ? $vendor_data['customer_support']['city'] : '';
		$vendor_csd_return_state = isset( $vendor_data['customer_support']['state'] ) ? $vendor_data['customer_support']['state'] : '';
		$vendor_csd_return_zip = isset( $vendor_data['customer_support']['zip'] ) ? $vendor_data['customer_support']['zip'] : '';
		
		$profile_complete_percent = 0;
		$profile_remaining_items = array();
		
		
		if( apply_filters( 'wcfm_is_allow_store_logo', true ) ) {
			if( $gravatar ) {
				$profile_complete_percent += $component_percent;
			} else {
				$profile_remaining_items['gravatar'] = __( 'Add Store Logo', 'wc-multivendor-marketplace' );
			}
		}
		
		if( apply_filters( 'wcfm_is_allow_store_name', true ) ) {
			if( $store_name ) {
				$profile_complete_percent += $component_percent;
			} else {
				$profile_remaining_items['store_name'] = __( 'Add Store Logo', 'wc-multivendor-marketplace' );
			}
		}
		
		if( apply_filters( 'wcfm_is_allow_store_banner', true ) ) {
			if( $banner  ) {
				$profile_complete_percent += $component_percent;
			} else {
				$profile_remaining_items['banner'] = __( 'Add Store Banner', 'wc-multivendor-marketplace' );
			}
		}
		
		if( apply_filters( 'wcfm_is_allow_store_phone', true ) ) {
			if(  $phone ) {
				$profile_complete_percent += $component_percent;
			} else {
				$profile_remaining_items['phone'] = __( 'Add Store Phone', 'wc-multivendor-marketplace' );
			}
		}
		
		if( apply_filters( 'wcfm_is_allow_store_description', true ) && $shop_description ) {
			$profile_complete_percent += $component_percent;
		} else {
			$profile_remaining_items['phone'] = __( 'Add Store Description', 'wc-multivendor-marketplace' );
		}
		
		if( apply_filters( 'wcfm_is_allow_store_address', true ) ) {
			if( $street_1 && $country ) {
				$profile_complete_percent += $component_percent;
			} else {
				$profile_remaining_items['address'] = __( 'Add Store Address', 'wc-multivendor-marketplace' );
			}
		}
		
		$api_key = isset( $WCFMmp->wcfmmp_marketplace_options['wcfm_google_map_api'] ) ? $WCFMmp->wcfmmp_marketplace_options['wcfm_google_map_api'] : '';
		if ( $api_key && $store_location ) {
			$profile_complete_percent += $component_percent;
		} else {
			$profile_remaining_items['location'] = __( 'Add Store Location', 'wc-multivendor-marketplace' );
		}
		
		if( $payment_mode && ( $paypal || $skrill || $ac_number ) ) {
			$profile_complete_percent += $component_percent;
		} else {
			$profile_remaining_items['payment'] = __( 'Set your payment method', 'wc-multivendor-marketplace' );
		}
		
		if( apply_filters( 'wcfm_is_pref_policies', true ) && apply_filters( 'wcfm_is_allow_policy_settings', true ) ) {
			if( $_wcfm_vendor_policy_tab_title && $_wcfm_vendor_shipping_policy && $_wcfm_vendor_refund_policy && $_wcfm_vendor_cancellation_policy ) {
				$profile_complete_percent += $component_percent;
			} else {
				$profile_remaining_items['policies'] = __( 'Setup Store Policies', 'wc-multivendor-marketplace' );
			}
		}
		
		if( apply_filters( 'wcfm_is_allow_customer_support_settings', true ) && apply_filters( 'wcfm_is_allow_customer_support', true ) ) {
			if( $vendor_customer_phone && $vendor_customer_email && $vendor_csd_return_address1 && $vendor_csd_return_country ) {
				$profile_complete_percent += $component_percent;
			} else {
				$profile_remaining_items['support'] = __( 'Setup Store Customer Supprt', 'wc-multivendor-marketplace' );
			}
		}
		
		if( apply_filters( 'wcfm_is_allow_vseo_settings', true ) ) {
			if( $wcfmmp_seo_meta_title && $wcfmmp_seo_meta_desc && $wcfmmp_seo_meta_keywords  ) {
				$profile_complete_percent += $component_percent;
			} else {
				$profile_remaining_items['seo'] = __( 'Setup Store SEO', 'wc-multivendor-marketplace' );
			}
		}
		
		if( apply_filters( 'wcfm_is_allow_shipping', true ) && apply_filters( 'wcfm_is_allow_vshipping_settings', true ) ) {
			$profile_complete_percent += $component_percent;
		} else {
			//$profile_remaining_items['shipping'] = __( 'Setup Store Shipping', 'wc-multivendor-marketplace' );
		}
		
		//echo round( $profile_complete_percent, 2 );
		//print_r($profile_remaining_items);
		?>
		<script>
		var $profile_complete_percent = <?php echo round( $profile_complete_percent, 0 ); ?>;
		var $complete = '<?php _e( 'Complete!', 'wc-multivendor-marketplace' ); ?>'; 
		</script>
		<div class="wcfm-clearfix"></div>
		<div id="wcfmmp_profile_complete_progressbar"><div class="wcfmmp_profile_complete_progress_label"><?php _e( 'Loading', 'wc-multivendor-marketplace' ); ?>...</div></div>
		<?php
		if( !empty( $profile_remaining_items ) ) {
			echo '<p class="wcfmmp_profile_complete_help description">' . __( 'Suggestion(s)', 'wc-multivendor-marketplace' ) . ': ' . implode( ", ", $profile_remaining_items ) . '</p>' ;
		}
		?>
		<div class="wcfm-clearfix"></div><br />
		<?php
	}
	
	function wcfmmp_search_vendor_list( $all = false, $offset = '', $number = '', $search = '', $category = '', $country = '', $state = '', $allow_vendors_list = '' ) {
		return $this->wcfmmp_get_vendor_list( $all, $offset, $number, $search, $allow_vendors_list, 'ASC', 'login', $country, $state, $category );
	}
	
	function wcfmmp_get_vendor_list( $all = false, $offset = '', $number = '', $search = '', $allow_vendors_list = '', $order = 'ASC', $orderby = 'login', $country = '', $state = '', $category = '' ) {
  	global $WCFM, $WCFMmp, $wpdb;
  	
  	$is_marketplace = wcfm_is_marketplace();
  	$vendor_arr = array();
		$wcfm_allow_vendors_list = apply_filters( 'wcfm_allow_vendors_list', $allow_vendors_list, $is_marketplace );
		$exclude_vendor_list     = apply_filters( 'wcfm_exclude_vendors_list', array() );
		
		if( $category ) {
			$category_vendors = $wpdb->get_results( "SELECT vendor_id FROM {$wpdb->prefix}wcfm_marketplace_store_taxonomies WHERE term = " . absint($category) );
			if( !empty( $category_vendors ) ) {
				foreach( $category_vendors as $category_vendor ) {
					$wcfm_allow_vendors_list[] = $category_vendor->vendor_id;
				}
			} else {
				$wcfm_allow_vendors_list = array(0);
			}
		}
		
		$offstore_args = array(
														'role__in'     => array( 'wcfm_vendor' ),
														'meta_key'     => '_wcfm_store_offline',
														'meta_value'   => 'yes',
														'fields'       => array( 'ID', 'display_name' )
													);
		$offline_users = get_users( $offstore_args );
		if( !empty( $offline_users ) ) {
			foreach( $offline_users as $offline_user ) {
				$exclude_vendor_list[] = $offline_user->ID;
			}
		}
		

		$args = array(
			'role__in'     => apply_filters( 'wcfmmp_allwoed_vendor_user_roles', array( 'wcfm_vendor' ) ),
			'orderby'      => $orderby,
			'order'        => $order,
			'include'      => $wcfm_allow_vendors_list,
			'exclude'      => $exclude_vendor_list,
			'count_total'  => false,
			'fields'       => array( 'ID', 'display_name' ),
		 ); 
		if( $orderby == 'avg_review_rating' ) {
			$args['meta_key'] = '_wcfmmp_avg_review_rating';
			$args['orderby']  = 'meta_value';
		}
		if( $number ) {
			$args['offset'] = $offset;
			$args['number'] = $number;
		}
		if( $search ) {
			//$args['search'] = $search;
			$args['meta_query'] = array( 
																	array(
																			 'relation' => 'OR',
																				array(
																						'key'     => 'first_name',
																						'value'   => $search,
																						'compare' => 'LIKE'
																				),
																				array(
																						'key'     => 'last_name',
																						'value'   => $search,
																						'compare' => 'LIKE'
																				),
																				array(
																						'key'     => 'nickname',
																						'value'   => $search,
																						'compare' => 'LIKE'
																				),
																				array(
																						'key'     => 'wcfmmp_store_name',
																						'value'   => $search,
																						'compare' => 'LIKE'
																				),
																				array(
																						'key'     => 'store_name',
																						'value'   => $search,
																						'compare' => 'LIKE'
																				),
																		),
																);
		}
		
		// Seach By Country
		if( $country ) {
			if( $search ) $args['meta_query']['relation'] = 'AND';
			$args['meta_query'][] = array(
																	 'relation' => 'OR',
																	 array(
																				'key'     => '_wcfm_country',
																				'value'   => $country,
																				'compare' => '='
																		),
																	 array(
																				'key'     => 'billing_country',
																				'value'   => $country,
																				'compare' => '='
																		),
																	 array(
																				'key'     => 'shipping_country',
																				'value'   => $country,
																				'compare' => '='
																		)
																 );
		}
		
		// Seach By State
		if( $state ) {
			if( $search || $country ) $args['meta_query']['relation'] = 'AND';
			$args['meta_query'][] = array(
																		 'relation' => 'OR',
																		 array(
																				'key'     => '_wcfm_state',
																				'value'   => $state,
																				'compare' => '='
																		),
																		 array(
																				'key'     => 'billing_state',
																				'value'   => $state,
																				'compare' => '='
																		),
																		 array(
																				'key'     => 'shipping_state',
																				'value'   => $state,
																				'compare' => '='
																		)
																	);
		}
		
		$all_users = get_users( $args );
		if( !empty( $all_users ) ) {
			foreach( $all_users as $all_user ) {
				$vendor_arr[$all_user->ID] = $all_user->display_name;
			}
		}
		
		return $vendor_arr;
	}

	public function wcfmmp_best_selling_vendors( $limit = 2 ) {
    global $WCFMmp, $wpdb, $WCFM;
    $commission_table = 'wcfm_marketplace_orders'; 
    $item_total = 'item_total';
    $time = 'created';
    $vendor_handler = 'vendor_id';
    $table_handler = 'commission';
    $func = 'SUM';
    $sql = "SELECT {$func}( commission.{$item_total} ) AS item_total, commission.{$vendor_handler} AS vendor_id FROM {$wpdb->prefix}{$commission_table} AS commission";
		$sql .= " WHERE 1=1";
    $status = get_wcfm_marketplace_active_withdrwal_order_status_in_comma();
    $sql .= " AND commission.order_status IN ({$status})";
    $sql .= " AND `is_refunded` != 1 AND `is_trashed` != 1";
    $sql .= " GROUP BY {$vendor_handler}";
    $sql .= " ORDER BY item_total DESC LIMIT {$limit}";
    $vendorwise_sales = $wpdb->get_results( $sql, ARRAY_A );
    return $vendorwise_sales;
  }
  
  public function wcfmmp_save_vendor_taxonomy( $vendor_id, $product_id = 0, $term, $taxonomy = 'product_cat' ) {
  	global $WCFMmp, $wpdb, $WCFM;
  	
  	if( !$vendor_id ) return;
  	if( !$product_id ) return;
  	if( !$term ) return;
  	
  	$term       = absint($term);
  	$product_id = absint($product_id);
  	
  	$product_term = get_term( $term );
					
		$lang = '';
		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			$lang = ICL_LANGUAGE_CODE;
		}
		
		$wpdb->query(
			$wpdb->prepare(
				"INSERT INTO `{$wpdb->prefix}wcfm_marketplace_store_taxonomies` 
						( vendor_id
						, product_id
						, term
						, parent
						, taxonomy
						, lang
						) VALUES ( %d
						, %d
						, %d
						, %d
						, %s
						, %s
						)"
				, $vendor_id
				, $product_id
				, $product_term->term_id
				, $product_term->parent
				, $taxonomy
				, $lang
			)
		);
  }
  
  public function wcfmmp_get_vendor_taxonomy( $vendor_id, $taxonomy = 'product_cat' ) {
  	global $WCFMmp, $wpdb, $WCFM;
  	
  	if( !$vendor_id ) return;
  	
  	$sql  = "SELECT * FROM `{$wpdb->prefix}wcfm_marketplace_store_taxonomies`";
  	$sql .= " WHERE 1=1";
  	$sql .= " AND `vendor_id` = {$vendor_id}";
  	
  	if( $taxonomy ) 
  		$sql .= " AND `taxonomy` = '{$taxonomy}'";
  	
  	$lang = '';
		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			$lang = ICL_LANGUAGE_CODE;
			$sql .= " AND `lang` = '{$lang}'";
		}
  	
  	$taxonomies = $wpdb->get_results($sql);
  	
  	$vendor_taxonomies = array();
  	if( !empty( $taxonomies ) ) {
  		foreach( $taxonomies as $taxonomy ) {
  			
				$product_cat_parents = get_ancestors( absint( $taxonomy->term ), 'product_cat' );
				if( !empty( $product_cat_parents ) ) {
					$product_ancestors_taxonomies = array( $taxonomy->term => $taxonomy->term );
					foreach( $product_cat_parents as $product_cat_parent ) {
						$product_ancestors_taxonomies_copy = $product_ancestors_taxonomies;
						$product_ancestors_taxonomies = array();
						$product_ancestors_taxonomies[$product_cat_parent] = $product_ancestors_taxonomies_copy;
						if( !isset($vendor_taxonomies[$product_cat_parent]) ) $vendor_taxonomies[$product_cat_parent] = $product_ancestors_taxonomies[$product_cat_parent];
						if( isset($vendor_taxonomies[$product_cat_parent]) && !is_array($vendor_taxonomies[$product_cat_parent]) ) $vendor_taxonomies[$product_cat_parent] = $product_ancestors_taxonomies[$product_cat_parent];
						if( isset($vendor_taxonomies[$product_cat_parent]) && is_array($vendor_taxonomies[$product_cat_parent]) ) $vendor_taxonomies[$product_cat_parent] = array_replace( $vendor_taxonomies[$product_cat_parent], $product_ancestors_taxonomies[$product_cat_parent] );
					}
				} elseif( !isset( $product_taxonomies[$taxonomy->term] ) ) {
					$vendor_taxonomies[$taxonomy->term] = $taxonomy->term;
				}
  			
  			/*$vendor_term = get_term( absint( $taxonomy->term ) );
  			if( $vendor_term->parent ) {
  				$vendor_parent_term = get_term( absint( $vendor_term->parent ) );
  				if( $vendor_parent_term->parent ) {
  					if( !isset($vendor_taxonomies[$vendor_parent_term->parent]) ) $vendor_taxonomies[$vendor_parent_term->parent] = array();
  					if( isset($vendor_taxonomies[$vendor_parent_term->parent]) && !is_array($vendor_taxonomies[$vendor_parent_term->parent]) ) $vendor_taxonomies[$vendor_parent_term->parent] = array( $vendor_taxonomies[$vendor_parent_term->parent] => $vendor_taxonomies[$vendor_parent_term->parent] );
  					
  					if( !isset($vendor_taxonomies[$vendor_parent_term->parent][$vendor_term->parent]) ) $vendor_taxonomies[$vendor_parent_term->parent][$vendor_term->parent] = array();
						if( isset($vendor_taxonomies[$vendor_parent_term->parent][$vendor_term->parent]) && !is_array($vendor_taxonomies[$vendor_parent_term->parent][$vendor_term->parent]) ) $vendor_taxonomies[$vendor_parent_term->parent][$vendor_term->parent] = array( $vendor_taxonomies[$vendor_parent_term->parent][$vendor_term->parent] => $vendor_taxonomies[$vendor_parent_term->parent][$vendor_term->parent] );
						if( !isset( $vendor_taxonomies[$vendor_parent_term->parent][$vendor_term->parent][$taxonomy->term] ) ) $vendor_taxonomies[$vendor_parent_term->parent][$vendor_term->parent][$taxonomy->term] = $taxonomy->term;
  				} else {
						if( !isset($vendor_taxonomies[$vendor_term->parent]) ) $vendor_taxonomies[$vendor_term->parent] = array();
						if( isset($vendor_taxonomies[$vendor_term->parent]) && !is_array($vendor_taxonomies[$vendor_term->parent]) ) $vendor_taxonomies[$vendor_term->parent] = array( $vendor_taxonomies[$vendor_term->parent] => $vendor_taxonomies[$vendor_term->parent] );
						if( !isset( $vendor_taxonomies[$vendor_term->parent][$taxonomy->term] ) ) $vendor_taxonomies[$vendor_term->parent][$taxonomy->term] = $taxonomy->term;
					}
  			} elseif( !isset( $vendor_taxonomies[$taxonomy->term] ) ) {
  				$vendor_taxonomies[$taxonomy->term] = $taxonomy->term;
  			}*/
  		}
  	}
  	
  	for( $i = 0; $i < 10; $i++ ) {
			$vendor_taxonomy_rearrange = array();
			if( !empty( $vendor_taxonomies ) ) {
				krsort($vendor_taxonomies);
				foreach( $vendor_taxonomies as $vendor_taxonomy_id => $vendor_taxonomy ) {
					if( !is_array( $vendor_taxonomy ) ) {
						$vendor_taxonomy_rearrange[$vendor_taxonomy_id] = $vendor_taxonomy_id;
					} else {
						foreach( $vendor_taxonomies as $vendor_taxonomy_in_id => $vendor_in_taxonomy ) {
						  if( is_array( $vendor_in_taxonomy ) && array_key_exists( $vendor_taxonomy_in_id, $vendor_taxonomy ) ) {
								if( isset( $vendor_taxonomy[$vendor_taxonomy_in_id] ) && is_array( $vendor_taxonomy[$vendor_taxonomy_in_id] ) ) $vendor_taxonomy[$vendor_taxonomy_in_id] = array_replace( $vendor_taxonomy[$vendor_taxonomy_in_id], $vendor_in_taxonomy );
								else $vendor_taxonomy[$vendor_taxonomy_in_id] = $vendor_in_taxonomy;
								
								if( isset( $vendor_taxonomy_rearrange[$vendor_taxonomy_id] ) && is_array( $vendor_taxonomy_rearrange[$vendor_taxonomy_id] ) ) $vendor_taxonomy_rearrange[$vendor_taxonomy_id] = array_replace( $vendor_taxonomy_rearrange[$vendor_taxonomy_id], $vendor_taxonomy );
								else $vendor_taxonomy_rearrange[$vendor_taxonomy_id] = $vendor_taxonomy;
							} else {
								if( isset( $vendor_taxonomy_rearrange[$vendor_taxonomy_id] ) && is_array( $vendor_taxonomy_rearrange[$vendor_taxonomy_id] ) ) $vendor_taxonomy_rearrange[$vendor_taxonomy_id] = array_replace( $vendor_taxonomy_rearrange[$vendor_taxonomy_id], $vendor_taxonomy );
								else $vendor_taxonomy_rearrange[$vendor_taxonomy_id] = $vendor_taxonomy;
							}
						}
					}
				}
			}
			$vendor_taxonomies = $vendor_taxonomy_rearrange;
		}
		
		$vendor_store_taxonomies = $vendor_taxonomies;
		if( !empty( $vendor_taxonomies ) ) {
			foreach( $vendor_taxonomies as $vendor_taxonomy_id => $vendor_taxonomy ) {
				if( is_array( $vendor_taxonomy ) ) {
					foreach( $vendor_taxonomy as $vendor_taxonomy_1_id => $vendor_1_taxonomy ) {
						if( array_key_exists( $vendor_taxonomy_1_id, $vendor_taxonomies ) ) {
							unset( $vendor_store_taxonomies[$vendor_taxonomy_1_id] );
						}
					}
				}
			}
		}
		ksort($vendor_store_taxonomies);
  	
  	return $vendor_store_taxonomies; 
  }
  
  public function wcfmmp_reset_vendor_taxonomy( $vendor_id, $product_id = 0, $taxonomy = 'product_cat' ) {
  	global $WCFMmp, $wpdb, $WCFM;
  	
  	if( !$vendor_id ) return;
  	
  	$sql  = "DELETE FROM `{$wpdb->prefix}wcfm_marketplace_store_taxonomies`";
  	$sql .= " WHERE 1=1";
  	$sql .= " AND `vendor_id` = {$vendor_id}";
  	
  	if( $product_id )
  		$sql .= " AND `product_id` = {$product_id}";
  	
  	if( $taxonomy ) 
  		$sql .= " AND `taxonomy` = '{$taxonomy}'";
  	
  	$wpdb->query($sql);
  }
  
}