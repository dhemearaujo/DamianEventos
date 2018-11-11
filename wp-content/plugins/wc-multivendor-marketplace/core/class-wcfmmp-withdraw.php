<?php
/**
 * WCFMmp plugin core
 *
 * WCfMmp Withdraw
 *
 * @author 		WC Lovers
 * @package 	wcfmmp/core
 * @version   1.0.0
 */
 
class WCFMmp_Withdraw {

	public function __construct() {
		global $WCFM, $WCFMmp;
		
		add_action( 'wcfmmp_order_item_processed', array( &$this, 'wcfmmp_order_item_auto_withdrawal_processed' ), 30, 9 ); 
	}
	
	/**
	 * Return Withdrawal request auto approve or not
	 * @return boolean
	 */
	function is_withdrawal_auto_approve( $vendor_id = 0 ) {
		global $WCFM, $WCFMmp;
		$request_auto_approve = isset( $WCFMmp->wcfmmp_withdrawal_options['request_auto_approve'] ) ? $WCFMmp->wcfmmp_withdrawal_options['request_auto_approve'] : 'no';
		if( $request_auto_approve == 'yes' ) return apply_filters( 'wcfmmp_is_withdrawal_auto_approve', true, $vendor_id );
		return apply_filters( 'wcfmmp_is_withdrawal_auto_approve', false, $vendor_id );
	}
	
	/**
	 * Return Withdrawal Limit
	 * @return boolean
	 */
	function get_withdrawal_limit( $vendor_id = 0 ) {
		global $WCFM, $WCFMmp;
		$withdrawal_limit = isset( $WCFMmp->wcfmmp_withdrawal_options['withdrawal_limit'] ) ? $WCFMmp->wcfmmp_withdrawal_options['withdrawal_limit'] : '';
		return apply_filters( 'wcfmmp_withdrawal_limit', $withdrawal_limit, $vendor_id );
	}
	
	/**
	 * Return Withdrawal Thresold
	 * @return boolean
	 */
	function get_withdrawal_thresold( $vendor_id = 0 ) {
		global $WCFM, $WCFMmp;
		$withdrawal_thresold = isset( $WCFMmp->wcfmmp_withdrawal_options['withdrawal_thresold'] ) ? $WCFMmp->wcfmmp_withdrawal_options['withdrawal_thresold'] : '';
		return apply_filters( 'wcfmmp_withdrawal_thresold', $withdrawal_thresold, $vendor_id );
	}
	
	/**
	 * Auto withdrawal Order item process as withdrawal request
	 */
	function wcfmmp_order_item_auto_withdrawal_processed( $commission_id, $order_id, $order, $vendor_id, $product_id, $order_item_id, $grosse_total, $total_commission, $is_auto_withdrawal ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		if( !$order_id ) return;
		if( !$vendor_id ) return;
		if( !$commission_id ) return;
		if( !$is_auto_withdrawal ) return;
		
		$payment_method = ! empty( $order->get_payment_method() ) ? $order->get_payment_method() : '';
		$withdraw_mode = 'by_paymode';
		$withdraw_status = 'pending';
		$this->wcfmmp_withdrawal_processed( $vendor_id, $order_id, $commission_id, $payment_method, $grosse_total, $total_commission, 0, $withdraw_status, $withdraw_mode, $is_auto_withdrawal );
		
		// Reverse Withdrwal Process
		$withdrawal_reverse = isset( $WCFMmp->wcfmmp_withdrawal_options['withdrawal_reverse'] ) ? 'yes' : '';
		if( $withdrawal_reverse ) {
			$this->wcfmmp_reverse_withdrawal_processed( $vendor_id, $order_id, $commission_id, $payment_method, $grosse_total, $total_commission, $withdraw_status, $withdraw_mode, $is_auto_withdrawal );	
		}
	}
	
	public function wcfmmp_withdrawal_processed( $vendor_id, $order_ids, $commission_ids, $payment_method, $grosse_total, $withdraw_amount, $withdraw_charges = 0, $withdraw_status = 'pending', $withdraw_mode = 'by_request', $is_auto_withdrawal = 0 ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		$wpdb->query(
						$wpdb->prepare(
							"INSERT INTO `{$wpdb->prefix}wcfm_marketplace_withdraw_request` 
									( vendor_id
									, order_ids
									, commission_ids
									, payment_method
									, withdraw_amount
									, withdraw_charges
									, withdraw_status
									, withdraw_mode
									, is_auto_withdrawal
									) VALUES ( %d
									, %s
									, %s
									, %s
									, %s
									, %s
									, %s 
									, %s
									, %d
									) ON DUPLICATE KEY UPDATE `created` = now()"
							, $vendor_id
							, $order_ids
							, $commission_ids
							, $payment_method
							, $withdraw_amount
							, $withdraw_charges
							, $withdraw_status
							, $withdraw_mode
							, $is_auto_withdrawal
			)
		);
		$withdraw_request_id = $wpdb->insert_id;
		do_action( 'wcfmmp_withdraw_request_processed', $withdraw_request_id, $vendor_id, $order_ids, $commission_ids, $withdraw_amount, $withdraw_charges, $withdraw_status, $withdraw_mode, $is_auto_withdrawal );
		return $withdraw_request_id;
	}
	
	public function wcfmmp_reverse_withdrawal_processed( $vendor_id, $order_id, $commission_id, $payment_method, $grosse_total, $withdraw_amount, $withdraw_status = 'pending', $withdraw_mode = 'by_request', $is_auto_withdrawal = 0 ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		$withdrawal_reverse = isset( $WCFMmp->wcfmmp_withdrawal_options['withdrawal_reverse'] ) ? 'yes' : '';
		if( !$withdrawal_reverse ) return;
		
		$balance = (float) $grosse_total - (float) $withdraw_amount;
		$balance = round($balance, 2);
		
		$withdraw_note = ''; //__( 'Reverse pay for auto withdrawal.', 'wc-multivendor-marketplace' );
		
		$wpdb->query(
						$wpdb->prepare(
							"INSERT INTO `{$wpdb->prefix}wcfm_marketplace_reverse_withdrawal` 
									( vendor_id
									, order_id
									, commission_id
									, payment_method
									, gross_total
									, commission
									, balance
									, withdraw_status
									, withdraw_note
									) VALUES ( %d
									, %d
									, %d
									, %s
									, %s
									, %s
									, %s 
									, %s
									, %s
									) ON DUPLICATE KEY UPDATE `created` = now()"
							, $vendor_id
							, $order_id
							, $commission_id
							, $payment_method
							, $grosse_total
							, $withdraw_amount
							, $balance
							, $withdraw_status
							, $withdraw_note
			)
		);
		$reverse_withdraw_request_id = $wpdb->insert_id;
		do_action( 'wcfmmp_reverse_withdraw_request_processed', $reverse_withdraw_request_id, $vendor_id, $order_id, $commission_id, $grosse_total, $withdraw_amount, $balance, $withdraw_status, $withdraw_mode, $is_auto_withdrawal );
		return $reverse_withdraw_request_id;
	}
	
	/**
	 * Reverse Withdrawal amount for a vendor
	 */
	function wcfm_get_pending_reverse_withdrawal_by_vendor( $vendor_id ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		$reverse_balance = 0;
		
		$withdrawal_reverse = isset( $WCFMmp->wcfmmp_withdrawal_options['withdrawal_reverse'] ) ? 'yes' : '';
		if( !$withdrawal_reverse ) return 0;
		
		$sql = "SELECT SUM( commission.balance ) AS total_due FROM {$wpdb->prefix}wcfm_marketplace_reverse_withdrawal AS commission";
		$sql .= " WHERE 1=1";
		$sql .= " AND commission.withdraw_status = 'pending'";
		$sql .= " AND commission.vendor_id = {$vendor_id}";
		$reverse_withdrawal = $wpdb->get_results( $sql );
		if( !empty( $reverse_withdrawal ) ) {
			foreach( $reverse_withdrawal as $reverse_withdrawa ) {
				$reverse_balance = $reverse_withdrawa->total_due;
			}
		}
		
		return $reverse_balance;
	}
	
	public function wcfmmp_update_withdrawal_meta( $withdrawal_id, $key, $value ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		$wpdb->query(
						$wpdb->prepare(
							"INSERT INTO `{$wpdb->prefix}wcfm_marketplace_withdraw_request_meta` 
									( withdraw_id
									, `key`
									, `value`
									) VALUES ( %d
									, %s
									, %s
									)"
							, $withdrawal_id
							, $key
							, $value
			)
		);
		$withdraw_meta_id = $wpdb->insert_id;
		return $withdraw_meta_id;
	}
	
	/**
	 * Withdrawal Payment Processing
	 */
	public function wcfmmp_withdrawal_payment_processesing( $withdrawal_id, $vendor_id, $payment_method, $withdraw_amount, $withdraw_charges = 0, $withdraw_note = '' ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		if( !$withdraw_note ) {
			$withdraw_note = apply_filters( 'wcfmmp_auto_withdrawal_note', __( 'Payment Processed', 'wc-multivendor-marketplace' ) );
		}
		
		$payment_processesing_status = true;
		
		if( $vendor_id ) {
			if ( array_key_exists( $payment_method, $WCFMmp->wcfmmp_gateways->payment_gateways ) ) {
				if( $withdraw_charges ) {
					$withdraw_amount = (float)$withdraw_amount - (float)$withdraw_charges;
				}
				
				$response = $WCFMmp->wcfmmp_gateways->payment_gateways[$payment_method]->process_payment( $withdrawal_id, $vendor_id, $withdraw_amount, $withdraw_charges, 'manual' );
				if ($response) {
					if( isset( $response['status'] ) && $response['status'] ) {
						
						// Update withdrawal status
						$this->wcfmmp_withdraw_status_update_by_withdrawal( $withdrawal_id, 'completed', $withdraw_note );
						
						do_action( 'wcfmmp_withdrawal_request_approved', $withdrawal_id );
						
						wcfmmp_log( sprintf( '#%s - payment processing complete via %s. Amount: %s', sprintf( '%06u', $withdrawal_id ), ucfirst( $payment_method ), $withdraw_amount . ' ' . get_woocommerce_currency() ), 'info' );
						
					} else {
						foreach ($response as $message) {
							wcfmmp_log( sprintf( '#%s - payment processing failed: %s', sprintf( '%06u', $withdrawal_id ), $message['message'] ), 'error' );
						}
						$payment_processesing_status = false;
					}
				} else {
					wcfmmp_log( sprintf( '#%s - payment processing failed: %s', sprintf( '%06u', $withdrawal_id ), __('Something went wrong please try again later.', 'wc-multivendor-marketplace') ), 'error' );
					$payment_processesing_status = false;
				}
			} else {
				wcfmmp_log( sprintf( '#%s - payment processing failed: %s', sprintf( '%06u', $withdrawal_id ), __('Invalid payment method.', 'wc-multivendor-marketplace') ), 'error' );
				$payment_processesing_status = false;
			}
		} else {
			wcfmmp_log( sprintf( '#%s - payment processing failed: %s', sprintf( '%06u', $withdrawal_id ), __('No vendor for payment processing.', 'wc-multivendor-marketplace') ), 'error' );
			$payment_processesing_status = false;
		}
		return $payment_processesing_status;
	}
	
	/**
	 * Withdraw status update by Withdrawal ID
	 */
	public function wcfmmp_withdraw_status_update_by_withdrawal( $withdrawal_id, $status = 'completed', $note = '' ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		if( !$withdrawal_id ) return;
		
		$wpdb->update("{$wpdb->prefix}wcfm_marketplace_withdraw_request", array('withdraw_status' => $status, 'withdraw_note' => $note, 'withdraw_paid_date' => date('Y-m-d H:i:s', time())), array('ID' => $withdrawal_id), array('%s', '%s', '%s'), array('%d'));
		
		
		$vendor_id = 0;
			
		// Commission table update
		$sql = 'SELECT commission_ids, vendor_id FROM ' . $wpdb->prefix . 'wcfm_marketplace_withdraw_request';
		$sql .= ' WHERE 1=1';
		$sql .= " AND ID = " . $withdrawal_id;
		$withdrawal_infos = $wpdb->get_results( $sql );
		if( !empty( $withdrawal_infos ) ) {
			foreach( $withdrawal_infos as $withdrawal_info ) {
				$vendor_id = $withdrawal_info->vendor_id;
				$commission_ids = explode(",", $withdrawal_info->commission_ids );
				if( !empty( $commission_ids ) ) {
					foreach( $commission_ids as $commission_id ) {
						$wpdb->update("{$wpdb->prefix}wcfm_marketplace_orders", array('withdraw_status' => $status, 'commission_paid_date' => date('Y-m-d H:i:s', time())), array('ID' => $commission_id), array('%s', '%s'), array('%d'));
						
						// Update commission ledger status
						$WCFMmp->wcfmmp_vendor->wcfmmp_ledger_status_update( $commission_id, $status );
						
						do_action( 'wcfmmp_withdraw_status_'.$status.'_by_commission', $withdrawal_id, $commission_id );
					}
				}
			}
			
			// Vendor Notification
			if( $vendor_id ) {
				$wcfm_messages = sprintf( __( 'Your withdrawal request #%s %s.', 'wc-multivendor-marketplace' ), '<a target="_blank" class="wcfm_dashboard_item_title" href="' . wcfm_transaction_details_url( $withdrawal_id ) . '">' . sprintf( '%06u', $withdrawal_id ) . '</a>', __( $status, 'wc-multivendor-marketplace' ) );
				$WCFM->wcfm_notification->wcfm_send_direct_message( -1, $vendor_id, 1, 0, $wcfm_messages, 'withdraw-request' );
			}
			
			// On withdrawal update ledge entry status update
			$WCFMmp->wcfmmp_vendor->wcfmmp_ledger_status_update( $withdrawal_id, $status, 'withdraw' );
			$WCFMmp->wcfmmp_vendor->wcfmmp_ledger_status_update( $withdrawal_id, $status, 'withdraw-charges' );
			
			do_action( 'wcfmmp_withdraw_status_'.$status, $withdrawal_id );
		}
	}
	
	/**
	 * Withdraw status update by commission ID
	 */
	public function wcfmmp_withdraw_status_update_by_commission( $commission_id, $status = 'completed', $note = '' ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		if( !$commission_id ) return;
		
		$wpdb->update("{$wpdb->prefix}wcfm_marketplace_withdraw_request", array('withdraw_status' => $status, 'withdraw_note' => $note, 'withdraw_paid_date' => date('Y-m-d H:i:s', time())), array('commission_ids' => $commission_id), array('%s', '%s', '%s'), array('%d'));
		
		// ledge entry status update
		$sql = 'SELECT ID, vendor_id  FROM ' . $wpdb->prefix . 'wcfm_marketplace_withdraw_request AS withdraw';
		$sql .= ' WHERE 1=1';
		$sql .= " AND `commission_ids` = '" . $commission_id . "'";
		$withdrawals = $wpdb->get_results( $sql );
		
		if( !empty( $withdrawals ) ) {
			foreach( $withdrawals as $withdrawal ) {
				
				// Ledger Status Update
				$WCFMmp->wcfmmp_vendor->wcfmmp_ledger_status_update( $withdrawal->ID, $status, 'withdraw' );
				$WCFMmp->wcfmmp_vendor->wcfmmp_ledger_status_update( $withdrawal->ID, $status, 'withdraw-charges' );
				
				// Vendor Notification
				$wcfm_messages = sprintf( __( 'Your withdrawal request #%s %s.', 'wc-multivendor-marketplace' ), '<a target="_blank" class="wcfm_dashboard_item_title" href="' . wcfm_transaction_details_url( $withdrawal->ID ) . '">' . sprintf( '%06u', $withdrawal->ID ) . '</a>', __( $status, 'wc-multivendor-marketplace' ) );
				$WCFM->wcfm_notification->wcfm_send_direct_message( -1, $withdrawal->vendor_id, 1, 0, $wcfm_messages, 'withdraw-request' );
				
				do_action( 'wcfmmp_withdraw_status_'.$status, $withdrawal->ID );
				do_action( 'wcfmmp_withdraw_status_'.$status.'_by_commission', $withdrawal->ID, $commission_id );
			}
		}
	}
	
	/**
	 * Reverse Withdraw status update by Withdrawal ID
	 */
	public function wcfmmp_reverse_withdraw_status_update( $reverse_withdrawal_id, $status = 'completed', $note = '' ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		if( !$reverse_withdrawal_id ) return;
		
		$withdrawal_reverse = isset( $WCFMmp->wcfmmp_withdrawal_options['withdrawal_reverse'] ) ? 'yes' : '';
		if( !$withdrawal_reverse ) return;
		
		$wpdb->update("{$wpdb->prefix}wcfm_marketplace_reverse_withdrawal", array('withdraw_status' => $status, 'withdraw_note' => $note, 'withdraw_paid_date' => date('Y-m-d H:i:s', time())), array('ID' => $reverse_withdrawal_id), array('%s', '%s', '%s'), array('%d'));
		
		
		$vendor_id = 0;
			
		// Commission table update
		$sql = 'SELECT order_id, commission_id, vendor_id FROM ' . $wpdb->prefix . 'wcfm_marketplace_reverse_withdrawal';
		$sql .= ' WHERE 1=1';
		$sql .= " AND ID = " . $reverse_withdrawal_id;
		$withdrawal_infos = $wpdb->get_results( $sql );
		if( !empty( $withdrawal_infos ) ) {
			foreach( $withdrawal_infos as $withdrawal_info ) {
				$vendor_id = $withdrawal_info->vendor_id;
				$order_id = $withdrawal_info->order_id;
				$commission_id = $withdrawal_info->commission_id;
				if( $commission_id ) {
					// Vendor Notification
					if( $vendor_id ) {
						$wcfm_messages = sprintf( __( 'Reverse withdrawal for order #%s %s.', 'wc-multivendor-marketplace' ), $order_id, $status );
						$WCFM->wcfm_notification->wcfm_send_direct_message( -1, $vendor_id, 1, 0, $wcfm_messages, 'withdraw-request' );
					}
					
					// On withdrawal update ledge entry status update
					$WCFMmp->wcfmmp_vendor->wcfmmp_ledger_status_update( $reverse_withdrawal_id, $status, 'reverse-withdraw' );
					
					do_action( 'wcfmmp_reverse_withdraw_status_'.$status, $reverse_withdrawal_id );
				}
			}
		}
	}
	
	/**
	 * Reverse Withdraw status update by Withdrawal ID
	 */
	public function wcfmmp_reverse_withdraw_status_update_by_commission( $commission_id, $status = 'completed', $note = '' ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		if( !$commission_id ) return;
		
		$withdrawal_reverse = isset( $WCFMmp->wcfmmp_withdrawal_options['withdrawal_reverse'] ) ? 'yes' : '';
		if( !$withdrawal_reverse ) return;
		
		$wpdb->update("{$wpdb->prefix}wcfm_marketplace_reverse_withdrawal", array('withdraw_status' => $status, 'withdraw_note' => $note, 'withdraw_paid_date' => date('Y-m-d H:i:s', time())), array('commission_id' => $commission_id), array('%s', '%s', '%s'), array('%d'));
		
		
		$vendor_id = 0;
			
		// Commission table update
		$sql = 'SELECT ID, order_id, vendor_id FROM ' . $wpdb->prefix . 'wcfm_marketplace_reverse_withdrawal';
		$sql .= ' WHERE 1=1';
		$sql .= " AND commission_id = " . $commission_id;
		$withdrawal_infos = $wpdb->get_results( $sql );
		if( !empty( $withdrawal_infos ) ) {
			foreach( $withdrawal_infos as $withdrawal_info ) {
				$vendor_id             = $withdrawal_info->vendor_id;
				$order_id              = $withdrawal_info->order_id;
				$reverse_withdrawal_id = $withdrawal_info->ID;
				if( $commission_id ) {
					// Vendor Notification
					if( $vendor_id ) {
						$wcfm_messages = sprintf( __( 'Reverse withdrawal for order #%s %s.', 'wc-multivendor-marketplace' ), $order_id, $status );
						$WCFM->wcfm_notification->wcfm_send_direct_message( -1, $vendor_id, 1, 0, $wcfm_messages, 'withdraw-request' );
					}
					
					// On withdrawal update ledge entry status update
					$WCFMmp->wcfmmp_vendor->wcfmmp_ledger_status_update( $reverse_withdrawal_id, $status, 'reverse-withdraw' );
					
					do_action( 'wcfmmp_reverse_withdraw_status_'.$status, $reverse_withdrawal_id );
				}
			}
		}
	}
	
	/**
	 * Calculate and Reture Withdrawal charges
	 */
	public function calculate_withdrawal_charges( $amount, $vendor_id = 0 ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		$withdrawal_charges = 0;
		
		$payment_method = $WCFMmp->wcfmmp_vendor->get_vendor_payment_method( $vendor_id );
		if( $payment_method && $amount ) {
			$withdrawal_charge_type = isset( $WCFMmp->wcfmmp_withdrawal_options['withdrawal_charge_type'] ) ? $WCFMmp->wcfmmp_withdrawal_options['withdrawal_charge_type'] : 'no';
			$withdrawal_charge          = isset( $WCFMmp->wcfmmp_withdrawal_options['withdrawal_charge'] ) ? $WCFMmp->wcfmmp_withdrawal_options['withdrawal_charge'] : array();
			
			$withdrawal_charge_gateway  = isset( $withdrawal_charge[$payment_method] ) ? $withdrawal_charge[$payment_method][0] : array();
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
					$withdrawal_charges = (float) $amount * ( (float)$withdrawal_percent_charge/100 );
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
		}
		
		if( $withdrawal_charges ) {
			$withdrawal_charges = round( $withdrawal_charges, 2 );
		}
		
		return apply_filters( 'wcfmmp_withdrawal_charges', $withdrawal_charges, $amount, $vendor_id );
	}
}