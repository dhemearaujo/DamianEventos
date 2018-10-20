<?php

// Exit if not in WordPress.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Check if class already exists before create.
if ( ! class_exists( 'WC_PPP_Brasil_Gateway' ) ) {

	/**
	 * Class WC_PPP_Brasil_Gateway.
	 * @property string client_id
	 * @property string client_secret
	 * @property string $mode
	 * @property string webhook_id
	 * @property string debug
	 * @property WC_Logger log
	 * @property string wrong_credentials
	 * @property string form_height
	 * @property string invoice_id_prefix
	 * @property string js_debug
	 */
	class WC_PPP_Brasil_Gateway extends WC_Payment_Gateway {

		/**
		 * WC_PPP_Brasil_Gateway constructor.
		 */
		public function __construct() {
			// Set default settings.
			$this->id                 = 'wc-ppp-brasil-gateway';
			$this->has_fields         = true;
			$this->method_title       = __( 'PayPal Plus Brasil', 'ppp-brasil' );
			$this->method_description = __( 'Solução PayPal para pagamentos transparentes aonde utiliza-se apenas o Cartão de Crédito.', 'ppp-brasil' );
			$this->supports           = array( 'products', 'refunds' );

			// Load settings fields.
			$this->init_form_fields();
			$this->init_settings();

			// Get options in variable.
			$this->title             = $this->get_option( 'title' );
			$this->client_id         = $this->get_option( 'client_id' );
			$this->client_secret     = $this->get_option( 'client_secret' );
			$this->webhook_id        = $this->get_option( 'webhook_id' );
			$this->mode              = $this->get_option( 'mode' );
			$this->debug             = $this->get_option( 'debug' );
			$this->js_debug          = $this->get_option( 'js_debug' );
			$this->wrong_credentials = $this->get_option( 'wrong_credentials' );
			$this->form_height       = $this->get_option( 'form_height' );
			$this->invoice_id_prefix = $this->get_option( 'invoice_id_prefix', '' );

			// Active logs.
			if ( 'yes' == $this->debug ) {
				$this->log = new WC_Logger();
			}

			// Handler for IPN.
			add_action( 'woocommerce_api_' . $this->id, array( $this, 'webhook_handler' ) );

			// Update web experience profile id before actually saving.
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array(
				$this,
				'before_process_admin_options'
			), 1 );

			// Now save with the save hook.
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array(
				$this,
				'process_admin_options'
			), 10 );

			// Filter the save data to add a custom experience profile id.
			add_filter( 'woocommerce_settings_api_sanitized_fields_' . $this->id, array( $this, 'filter_save_data' ) );

			// Enqueue scripts.
			add_action( 'wp_enqueue_scripts', array( $this, 'checkout_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		}

		/**
		 * Check if the gateway is available for use.
		 *
		 * @return bool
		 */
		public function is_available() {
			$is_available = ( 'yes' === $this->enabled );

			if ( WC()->cart && 0 < $this->get_order_total() && 0 < $this->max_amount && $this->max_amount < $this->get_order_total() ) {
				$is_available = false;
			}

			if ( ! $this->client_id || ! $this->client_secret || ! $this->webhook_id || $this->wrong_credentials === 'yes' ) {
				$is_available = false;
			}

			return $is_available;
		}

		/**
		 * Set some settings before save the options.
		 */
		public function before_process_admin_options() {
			$client_id_key     = $this->get_field_key( 'client_id' );
			$client_secret_key = $this->get_field_key( 'client_secret' );
			$mode_key          = $this->get_field_key( 'mode' );
			// Update the client_id and client_secret with the posted data.
			$this->client_id     = isset( $_POST[ $client_id_key ] ) ? sanitize_text_field( trim( $_POST[ $client_id_key ] ) ) : '';
			$this->client_secret = isset( $_POST[ $client_secret_key ] ) ? sanitize_text_field( trim( $_POST[ $client_secret_key ] ) ) : '';
			$this->mode          = isset( $_POST[ $mode_key ] ) ? sanitize_text_field( $_POST[ $mode_key ] ) : '';
			// Get the API context.
			$api_context = $this->get_api_context();
			// Update things.
			$this->update_webhooks( $api_context );
		}

		/**
		 * Update the webhooks.
		 *
		 * @param $api_context
		 */
		public function update_webhooks( $api_context ) {
			// Set by default as not found.
			$webhook = null;
			// Check if has client_id and client_secret to connect and get webhooks.
			$this->log( 'Updating the webhooks' );
			try {
				$webhook_url = $this->get_webhook_url();
				// Get a list of webhooks
				$registered_webhooks = \PayPal\Api\Webhook::getAllWithParams( array(), $api_context );
				$this->log( 'Webhooks list: ' . $this->print_r( $registered_webhooks->toArray(), true ) );
				/** @var \PayPal\Api\Webhook $registered_webhook */
				foreach ( $registered_webhooks->getWebhooks() as $registered_webhook ) {
					if ( $registered_webhook->getUrl() === $webhook_url ) {
						$this->log( 'Match webhook: ' . $this->print_r( $registered_webhook->toArray(), true ) );
						$webhook = $registered_webhook;
						break;
					}
				}
				// If no webhook matched, create a new one.
				if ( ! $webhook ) {
					$this->log( 'No webhook matched. Creating one.' );
					$webhook = $this->create_webhook( $api_context );
				}
				// Set the webhook ID
				$this->log( 'Set webhook ID to: ' . $webhook->getId() );
				$this->webhook_id        = $webhook->getId();
				$this->wrong_credentials = 'no';
			} catch ( \PayPal\Exception\PayPalConnectionException $ex ) { // If we get here probably was not possible to get the profile ID
				$uid_error = $this->unique_id();
				$this->log( 'Error #' . $uid_error );
				$this->log( 'Code: ' . $ex->getCode() );
				$this->log( $ex->getMessage() );
				$this->log( 'PayPalConnectionException: ' . $this->print_r( json_decode( $ex->getData(), true ), true ) );
				// If is invalid credentials
				if ( $ex->getCode() == 401 ) {
					$this->wrong_credentials = 'yes';
				}
			} catch ( Exception $ex ) {
				$uid_error = $this->unique_id();
				$this->log( 'Error #' . $uid_error );
				$this->log( $ex->getMessage() );
				$this->log( 'PHP Error: ' . $this->print_r( json_decode( $ex->getMessage(), true ), true ) );
			}
			// If we don't have a webhook, set as empty.ˆ
			if ( ! $webhook ) {
				$this->webhook_id = '';
			}
		}

		/**
		 * Add the experience profile ID to save data.
		 *
		 * @param $settings
		 *
		 * @return mixed
		 */
		public function filter_save_data( $settings ) {
			if ( $this->wrong_credentials === 'yes' ) {
				$this->client_id           = '';
				$settings['client_id']     = $this->client_id;
				$this->client_secret       = '';
				$settings['client_secret'] = $this->client_secret;
				$this->webhook_id          = '';
				$settings['webhook_id']    = $this->webhook_id;
			}
			$settings['webhook_id']        = $this->webhook_id ? $this->webhook_id : '';
			$settings['wrong_credentials'] = $this->wrong_credentials ? $this->wrong_credentials : 'no';

			return $settings;
		}

		private function create_webhook( $api_context ) {
			$webhook_url = $this->get_webhook_url();

			$webhook = new \PayPal\Api\Webhook();
			$webhook->setUrl( $webhook_url );

			$events_types         = array(
				'PAYMENT.SALE.COMPLETED',
				'PAYMENT.SALE.DENIED',
				'PAYMENT.SALE.PENDING',
				'PAYMENT.SALE.REFUNDED',
				'PAYMENT.SALE.REVERSED',
			);
			$webhook_events_types = array();
			foreach ( $events_types as $event_type ) {
				$arg                    = json_encode( array( 'name' => $event_type ) );
				$webhook_events_types[] = new \PayPal\Api\WebhookEventType( $arg );
			}

			$webhook->setEventTypes( $webhook_events_types );

			$this->log( 'Request to webhook: ' . $this->print_r( $webhook->toArray(), true ) );

			return $webhook->create( $api_context );
		}

		private function get_webhook_url() {
			$base_url = site_url();
			if ( $_SERVER['HTTP_HOST'] === 'localhost' ) {
				$base_url = 'https://example.com/';
			}

			return str_replace( 'http:', 'https:', add_query_arg( 'wc-api', $this->id, $base_url ) );
		}

		/**
		 * Init the admin form fields.
		 */
		public function init_form_fields() {
			$this->form_fields = array(
				'enabled'           => array(
					'title'   => __( 'Habilitar/Desabilitar', 'ppp-brasil' ),
					'type'    => 'checkbox',
					'label'   => __( 'Habilitar', 'ppp-brasil' ),
					'default' => 'yes',
				),
				'title'             => array(
					'title'       => __( 'Nome de exibição', 'ppp-brasil' ),
					'type'        => 'text',
					'default'     => '',
					'placeholder' => __( 'Exemplo: (Parcelado em até 12x)', 'ppp-brasil' ),
					'description' => __( 'Será exibido no checkout: Cartão de Crédito (Parcelado em até 12x)', 'ppp-brasil' ),
					'desc_tip'    => __( 'Por padrão a solução do PayPal Plus é exibida como “Cartão de Crédito”, utilize esta opção para definir um texto adicional como parcelamento ou descontos.', 'ppp-brasil' ),
				),
				'mode'              => array(
					'title'       => __( 'Modo', 'ppp-brasil' ),
					'type'        => 'select',
					'options'     => array(
						'live'    => __( 'Produção', 'ppp-brasil' ),
						'sandbox' => __( 'Sandbox', 'ppp-brasil' ),
					),
					'description' => __( 'Utilize esta opção para alternar entre os modos Sandbox e Produção. Sandbox é utilizado para testes e Produção para compras reais.', 'ppp-brasil' ),
				),
				'client_id'         => array(
					'title'       => __( 'Client ID', 'ppp-brasil' ),
					'type'        => 'text',
					'default'     => '',
					'description' => sprintf( __( 'Para gerar o Client ID acesse <a href="%s" target="_blank">aqui</a> e procure pela seção “REST API apps”.', 'ppp-brasil' ), 'https://developer.paypal.com/docs/classic/lifecycle/sb_credentials/' ),

				),
				'client_secret'     => array(
					'title'       => __( 'Secret ID', 'ppp-brasil' ),
					'type'        => 'text',
					'default'     => '',
					'description' => sprintf( __( 'Para gerar o Secret ID acesse <a href="%s" target="_blank">aqui</a> e procure pela seção “REST API apps”.', 'ppp-brasil' ), 'https://developer.paypal.com/docs/classic/lifecycle/sb_credentials/' ),
				),
				'debug'             => array(
					'title'       => __( 'Modo depuração', 'ppp-brasil' ),
					'type'        => 'checkbox',
					'label'       => __( 'Habilitar', 'ppp-brasil' ),
					'desc_tip'    => __( 'Habilite este modo para depurar a aplicação em caso de homologação ou erros.', 'ppp-brasil' ),
					'description' => sprintf( __( 'Os logs serão salvos no caminho: %s.', 'woo-paypal-plus-brazil' ), $this->get_log_view() ),
				),
				'js_debug'          => array(
					'title'       => __( 'Modo depuração JS', 'ppp-brasil' ),
					'type'        => 'checkbox',
					'label'       => __( 'Habilitar', 'ppp-brasil' ),
					'desc_tip'    => __( 'Habilite este modo para depurar o Javascript da aplicação.', 'ppp-brasil' ),
					'description' => __( 'Somente ative esta opção caso seja necessário avaliar os dados no checkout.', 'ppp-brasil' ),
				),
				'advanced_settings' => array(
					'title'       => __( 'Configurações avançadas', 'ppp-brasil' ),
					'type'        => 'title',
					'description' => __( 'Utilize estas opções para customizar a experiência da solução.', 'ppp-brasil' ),
				),
				'form_height'       => array(
					'title'       => __( 'Altura do formulário', 'ppp-brasil' ),
					'type'        => 'text',
					'default'     => '',
					'placeholder' => __( 'px', 'ppp-brasil' ),
					'description' => __( 'Utilize esta opção para definir uma altura máxima do formulário de cartão de crédito (será considerado um valor em pixels). Será aceito um valor em pixels entre 400 - 550.', 'ppp-brasil' ),
				),
				'invoice_id_prefix' => array(
					'title'       => __( 'Prefixo de Invoice ID', 'ppp-brasil' ),
					'type'        => 'text',
					'default'     => '',
					'description' => __( 'Adicione um prefixo ao Invoice ID das compras feitas com PayPal Plus na sua loja. Isso pode auxiliar em problemas de Invoice duplicado caso trabalhe com a mesma conta PayPal em mais de um site.', 'ppp-brasil' ),
				),
			);
		}

		/**
		 * Get log.
		 *
		 * @return string
		 */
		protected function get_log_view() {
			return '<a target="_blank" href="' . esc_url( admin_url( 'admin.php?page=wc-status&tab=logs&log_file=' . esc_attr( $this->id ) . '-' . sanitize_file_name( wp_hash( $this->id ) ) . '.log' ) ) . '">' . __( 'Status do Sistema &gt; Logs', 'ppp-brasil' ) . '</a>';
		}

		/**
		 * Process the payment.
		 *
		 * @param int $order_id
		 *
		 * @param bool $force
		 *
		 * @return null|array
		 */
		public function process_payment( $order_id, $force = false ) {
			$this->log( 'Processing payment for order #' . $order_id );
			$order      = wc_get_order( $order_id );
			$payment_id = WC()->session->get( 'wc-ppp-brasil-payment-id' );

			// Check if is a iframe error
			if ( isset( $_POST['wc-ppp-brasil-error'] ) && ! empty( $_POST['wc-ppp-brasil-error'] ) ) {
				switch ( $_POST['wc-ppp-brasil-error'] ) {
					case 'CARD_ATTEMPT_INVALID':
						wc_add_notice( __( 'Número de tentativas excedidas, por favor tente novamente. Se o erro persistir entre em contato.', 'ppp-brasil' ), 'error' );
						break;
					case 'INTERNAL_SERVICE_ERROR':
					case 'SOCKET_HANG_UP':
					case 'socket hang up':
					case 'connect ECONNREFUSED':
					case 'connect ETIMEDOUT':
					case 'UNKNOWN_INTERNAL_ERROR':
					case 'fiWalletLifecycle_unknown_error':
					case 'Failed to decrypt term info':
						wc_add_notice( __( 'Ocorreu um erro inesperado, por favor tente novamente. Se o erro persistir entre em contato.', 'ppp-brasil' ), 'error' );
						break;
					case 'RISK_N_DECLINE':
					case 'NO_VALID_FUNDING_SOURCE_OR_RISK_REFUSED':
					case 'TRY_ANOTHER_CARD':
					case 'NO_VALID_FUNDING_INSTRUMENT':
						wc_add_notice( __( 'Não foi possível processar o seu pagamento, tente novamente ou entre em contato contato com o PayPal (0800-047-4482).', 'ppp-brasil' ), 'error' );
						break;
					default:
						wc_add_notice( __( 'Por favor revise as informações inseridas do cartão de crédito.', 'ppp-brasil' ), 'error' );
						break;
				}

				WC()->session->set( 'refresh_totals', true );

				$uid_error = $this->unique_id();
				$this->log( 'Error #' . $uid_error );
				$this->log( 'Payment failed because an iframe error: ' . sanitize_text_field( $_POST['wc-ppp-brasil-error'] ) );

				return null;
			}

			// Prevent submit any dummy data.
			if ( WC()->session->get( 'wc-ppp-brasil-dummy-data' ) === true ) {
				wc_add_notice( __( 'You are not allowed to do that.', 'ppp-brasil' ), 'error' );
				$this->log( 'Payment failed because was trying to pay with dummy data.' );

				return null;
			}

			// Check the payment id
			if ( ! $payment_id ) {
				wc_add_notice( __( 'Invalid payment ID.', 'ppp-brasil' ), 'error' );
				$this->log( 'Payment failed because was trying to pay with invalid payment ID' );

				return null;
			}

			try {
				$iframe_data = isset( $_POST['wc-ppp-brasil-data'] ) ? json_decode( wp_unslash( $_POST['wc-ppp-brasil-data'] ), true ) : null;
				$this->log( 'Iframe init data: ' . $this->print_r( $iframe_data, true ) );
				$response_data = isset( $_POST['wc-ppp-brasil-response'] ) ? json_decode( wp_unslash( $_POST['wc-ppp-brasil-response'] ), true ) : null;
				$this->log( 'Iframe response data: ' . $this->print_r( $response_data, true ) );
				$payer_id       = $response_data['result']['payer']['payer_info']['payer_id'];
				$remember_cards = $response_data['result']['rememberedCards'];

				// Check if the payment id
				if ( empty( $payer_id ) ) {
					wc_add_notice( __( 'Ocorreu um erro inesperado, por favor tente novamente. Se o erro persistir entre em contato.', 'ppp-brasil' ), 'error' );
					$this->log( 'Empty payer ID' );

					return null;
				}

				// Check if the payment id equal to stored
				if ( $payment_id !== $iframe_data['payment_id'] ) {
					wc_add_notice( __( 'Ocorreu um erro inesperado, por favor tente novamente. Se o erro persistir entre em contato.', 'ppp-brasil' ), 'error' );
					$this->log( 'Payment failed because was trying to change the iframe response data with a new payment ID' );

					return null;
				}

				// execute the order here.
				$execution = $this->execute_payment( $order, $payment_id, $payer_id );
				$this->log( 'Execute payment response: ' . $this->print_r( $execution->toArray(), true ) );
				$transactions      = $execution->getTransactions();
				$related_resources = $transactions[0]->getRelatedResources();
				$sale              = $related_resources[0]->getSale();
				update_post_meta( $order_id, 'wc_ppp_brasil_sale_id', $sale->getId() );
				update_post_meta( $order_id, 'wc_ppp_brasil_sale', $sale->toArray() );
				$installments = 1;
				if ( $response_data['result'] && $response_data['result']['term'] && $response_data['result']['term']['term'] && is_numeric( $response_data['result']['term']['term'] ) ) {
					$installments = $response_data['result']['term']['term'];
				}
				update_post_meta( $order_id, 'wc_ppp_brasil_installments', $installments );
				update_post_meta( $order_id, 'wc_ppp_brasil_sandbox', $this->mode );
				$result_success = false;
				switch ( $sale->getState() ) {
					case 'completed';
						$order->payment_complete();
						$result_success = true;
						break;
					case 'pending':
						wc_reduce_stock_levels( $order_id );
						$order->update_status( 'on-hold', __( 'O pagamento está em revisão pelo PayPal.', 'ppp-brasil' ) );
						$result_success = true;
						break;
				}

				if ( $result_success ) {
					// Remember user cards
					if ( is_user_logged_in() ) {
						update_user_meta( get_current_user_id(), 'wc_ppp_brasil_remembered_cards', $remember_cards );
					}

					// Return the success URL.s
					return array(
						'result'   => 'success',
						'redirect' => $this->get_return_url( $order ),
					);
				}
			} catch ( \PayPal\Exception\PayPalConnectionException $ex ) {
				$data      = json_decode( $ex->getData(), true );
				$uid_error = $this->unique_id();

				switch ( $data['name'] ) {
					// Repeat the execution
					case 'INTERNAL_SERVICE_ERROR':
						if ( $force ) {
							$this->log( 'Error #' . $uid_error );
							wc_add_notice( sprintf( __( 'Ocorreu um erro inesperado, por favor tente novamente. Se o erro persistir entre em contato. Código: %s.', 'ppp-brasil' ), $uid_error ), 'error' );
						} else {
							$this->process_payment( $order_id, true );
						}
						break;
					case 'VALIDATION_ERROR':
						wc_add_notice( sprintf( __( 'Ocorreu um erro inesperado, por favor tente novamente. Se o erro persistir entre em contato. Código: %s.', 'ppp-brasil' ), $uid_error ), 'error' );
						$this->log( 'Error #' . $uid_error );
						break;
					case 'PAYMENT_ALREADY_DONE':
						wc_add_notice( __( 'Já existe um pagamento para este pedido.', 'ppp-brasil' ), 'error' );
						break;
					default:
						wc_add_notice( __( 'O seu pagamento não foi aprovado, por favor tente novamente.', 'ppp-brasil' ), 'error' );
						break;
				}

				// Log anyway
				$this->log( 'PayPalConnectionException: ' . $this->print_r( $ex->getMessage(), true ) );

				return null;
			} catch ( Exception $ex ) {
				$uid_error = $this->unique_id();
				wc_add_notice( sprintf( __( 'Ocorreu um erro inesperado, por favor tente novamente. Se o erro persistir entre em contato. Código: %s.', 'ppp-brasil' ), $uid_error ), 'error' );
				$this->log( 'Error #' . $uid_error );
				$this->log( 'PHP Error: ' . $this->print_r( $ex->getMessage(), true ) );

				return null;
			}

			return null;
		}

		/**
		 * Process the refund for an order.
		 *
		 * @param int $order_id
		 * @param null $amount
		 * @param string $reason
		 *
		 * @return WP_Error|bool
		 */
		public function process_refund( $order_id, $amount = null, $reason = '' ) {

			$amount  = floatval( $amount );
			$sale_id = get_post_meta( $order_id, 'wc_ppp_brasil_sale_id', true );

			// Check if the amount is bigger than zero
			if ( $amount <= 0 ) {
				return new WP_Error( 'error', sprintf( __( 'O reembolso não pode ser menor que %s.', 'ppp-brasil' ), wc_price( 0 ) ) );
			}

			// Get the API context
			$api_context = $this->get_api_context();

			// Check if we got the sale ID
			if ( $sale_id ) {
				try {

					// Refund amount.
					$refund_amount = new \PayPal\Api\Amount();
					$refund_amount->setCurrency( get_woocommerce_currency() )
					              ->setTotal( $amount );

					// Refund request.
					$refund_request = new \PayPal\Api\RefundRequest();
					$refund_request->setAmount( $refund_amount );

					// Prepare the sale.
					$sale = new \PayPal\Api\Sale();
					$sale->setId( $sale_id );

					$this->log( 'Doing refund: ' . $this->print_r( $refund_request->toArray(), true ) );

					// Try to refund.
					$refund_sale = $sale->refundSale( $refund_request, $api_context );

					$this->log( 'Refund response: ' . $this->print_r( $refund_sale->toArray(), true ) );

					// Check the refuld success.
					if ( $refund_sale->getState() === 'completed' ) {
						return true;
					} else {
						return new WP_Error( 'error', $refund_sale->getReason() );
					}

				} catch ( \PayPal\Exception\PayPalConnectionException $ex ) { // Catch any PayPal error.
					$uid_error = $this->unique_id();
					$data      = json_decode( $ex->getData(), true );
					$this->log( 'Error #' . $uid_error );
					$this->log( 'Code: ' . $ex->getCode() );
					$this->log( $ex->getMessage() );
					$this->log( 'PayPalConnectionException: ' . $this->print_r( $data, true ) );

					return new WP_Error( 'error', $data['message'] . ' -  Code: #' . $uid_error );
				} catch ( Exception $ex ) { // Catch any other error.
					$uid_error = $this->unique_id();
					$this->log( 'Error #' . $uid_error );
					$this->log( 'Code: ' . $ex->getCode() );
					$this->log( $ex->getMessage() );

					return new WP_Error( 'error', $ex->getMessage() . ' -  Code: #' . $uid_error );
				}
			} else { // If we don't have the PayPal sale ID.
				$uid_error = $this->unique_id();
				$this->log( 'Error #' . $uid_error );
				$this->log( 'Trying to refund a PayPal payment without the sale ID' );

				return new WP_Error( 'error', sprintf( __( 'Parece que você não tem um pedido para realizar o reembolso. Código: #%s', 'ppp-brasil' ), $uid_error ) );
			}

		}

		/** @noinspection PhpDocRedundantThrowsInspection */

		/**
		 * Execute a payment.
		 *
		 * @param $order WC_Order
		 * @param $payment_id
		 * @param $payer_id
		 *
		 * @throws \PayPal\Exception\PayPalConnectionException
		 *
		 * @return \PayPal\Api\Payment
		 */
		public function execute_payment( $order, $payment_id, $payer_id ) {
			// Get API context.
			$api_context = $this->get_api_context();
			$payment     = \PayPal\Api\Payment::get( $payment_id, $api_context );

			// Create invoice number with order ID.
			$patchAddInvoiceNumber = new \PayPal\Api\Patch();
			$patchAddInvoiceNumber->setOp( 'add' )
			                      ->setPath( '/transactions/0/invoice_number' )
			                      ->setValue( $this->invoice_id_prefix . $order->get_id() );

			// Add the description with order ID.
			$patchAddDescription = new \PayPal\Api\Patch();
			$patchAddDescription->setOp( 'add' )
			                    ->setPath( '/transactions/0/description' )
			                    ->setValue( sprintf( __( 'Pedido #%s realizado na loja %s', 'ppp-brasil' ), $order->get_id(), get_bloginfo( 'name' ) ) );

			// Create patch request.
			$patchRequest = new \PayPal\Api\PatchRequest();
			$patchRequest->setPatches( array( $patchAddInvoiceNumber, $patchAddDescription ) );

			// Update with patch
			$payment->update( $patchRequest, $api_context );

			// Payment execute
			$execution = new \PayPal\Api\PaymentExecution();
			$execution->setPayerId( $payer_id );

			return $payment->execute( $execution, $api_context );
		}

		/**
		 * Render the payment fields in checkout.
		 */
		public function payment_fields() {
			include dirname( __FILE__ ) . '/libs/PayPal-PHP-SDK/autoload.php';
			include dirname( __FILE__ ) . '/views/html-payment-fields.php';
		}

		/**
		 * Render HTML in admin options.
		 */
		public function admin_options() {
			include dirname( __FILE__ ) . '/views/html-admin-options.php';
		}

		/**
		 * Get the posted data in the checkout.
		 *
		 * @return array
		 * @throws Exception
		 */
		public function get_posted_data() {
			$order_id = get_query_var( 'order-pay' );
			$order    = $order_id ? new WC_Order( $order_id ) : null;
			$data     = array();
			$defaults = array(
				'first_name'       => '',
				'last_name'        => '',
				'person_type'      => '',
				'cpf'              => '',
				'cnpj'             => '',
				'phone'            => '',
				'email'            => '',
				'postcode'         => '',
				'address'          => '',
				'number'           => '',
				'address_2'        => '',
				'neighborhood'     => '',
				'city'             => '',
				'state'            => '',
				'country'          => '',
				'approval_url'     => '',
				'payment_id'       => '',
				'dummy'            => false,
				'invalid'          => array(),
				'remembered_cards' => '',
			);

			if ( $order ) {
				$billing_cellphone = get_post_meta( $order->get_id(), '_billing_cellphone', true );
				$data['postcode']  = $order->get_shipping_postcode();
				$data['address']   = $order->get_shipping_address_1();
				$data['address_2'] = $order->get_shipping_address_2();
				$data['city']      = $order->get_shipping_city();
				$data['state']     = $order->get_shipping_state();
				$data['country']   = $order->get_shipping_country();

				$data['neighborhood'] = get_post_meta( $order->get_id(), '_billing_neighborhood', true );
				$data['number']       = get_post_meta( $order->get_id(), '_billing_number', true );
				$data['first_name']   = $order->get_billing_first_name();
				$data['last_name']    = $order->get_billing_last_name();
				$data['person_type']  = get_post_meta( $order->get_id(), '_billing_persontype', true );
				$data['cpf']          = get_post_meta( $order->get_id(), '_billing_cpf', true );
				$data['cnpj']         = get_post_meta( $order->get_id(), '_billing_cnpj', true );
				$data['phone']        = $billing_cellphone ? $billing_cellphone : $order->get_billing_phone();
				$data['email']        = $order->get_billing_email();
			} else if ( $_POST ) {
				$data['postcode']  = isset( $_POST['s_postcode'] ) ? preg_replace( '/[^0-9]/', '', $_POST['s_postcode'] ) : '';
				$data['address']   = isset( $_POST['s_address'] ) ? sanitize_text_field( $_POST['s_address'] ) : '';
				$data['address_2'] = isset( $_POST['s_address_2'] ) ? sanitize_text_field( $_POST['s_address_2'] ) : '';
				$data['city']      = isset( $_POST['s_city'] ) ? sanitize_text_field( $_POST['s_city'] ) : '';
				$data['state']     = isset( $_POST['s_state'] ) ? sanitize_text_field( $_POST['s_state'] ) : '';
				$data['country']   = isset( $_POST['s_country'] ) ? sanitize_text_field( $_POST['s_country'] ) : '';
				// Now get other post data that other fields can send.
				$post_data = array();
				if ( isset( $_POST['post_data'] ) ) {
					parse_str( $_POST['post_data'], $post_data );
				}
				$billing_cellphone    = isset( $post_data['billing_cellphone'] ) ? sanitize_text_field( $post_data['billing_cellphone'] ) : '';
				$data['neighborhood'] = isset( $post_data['billing_neighborhood'] ) ? sanitize_text_field( $post_data['billing_neighborhood'] ) : '';
				$data['number']       = isset( $post_data['billing_number'] ) ? sanitize_text_field( $post_data['billing_number'] ) : '';
				$data['first_name']   = isset( $post_data['billing_first_name'] ) ? sanitize_text_field( $post_data['billing_first_name'] ) : '';
				$data['last_name']    = isset( $post_data['billing_last_name'] ) ? sanitize_text_field( $post_data['billing_last_name'] ) : '';
				$data['person_type']  = isset( $post_data['billing_persontype'] ) ? sanitize_text_field( $post_data['billing_persontype'] ) : '';
				$data['cpf']          = isset( $post_data['billing_cpf'] ) ? sanitize_text_field( $post_data['billing_cpf'] ) : '';
				$data['cnpj']         = isset( $post_data['billing_cnpj'] ) ? sanitize_text_field( $post_data['billing_cnpj'] ) : '';
				$data['phone']        = $billing_cellphone ? $billing_cellphone : ( isset( $post_data['billing_phone'] ) ? sanitize_text_field( $post_data['billing_phone'] ) : '' );
				$data['email']        = isset( $post_data['billing_email'] ) ? sanitize_text_field( $post_data['billing_email'] ) : '';
			}

			if ( pppbr_needs_cpf() ) {
				// Get wcbcf settings
				$wcbcf_settings = get_option( 'wcbcf_settings' );
				// Set the person type default if we don't have any person type defined
				if ( $wcbcf_settings && ! $data['person_type'] && ( $wcbcf_settings['person_type'] == '2' || $wcbcf_settings['person_type'] == '3' ) ) {
					// The value 2 from person_type in settings is CPF (1) and 3 is CNPJ (2), and 1 is both, that won't reach here.
					$data['person_type']         = $wcbcf_settings['person_type'] == '2' ? '1' : '2';
					$data['person_type_default'] = true;
				}
			}

			// Now set the invalid.
			$data    = wp_parse_args( $data, $defaults );
			$invalid = $this->validate_data( $data );

			// if its invalid, return demo data.
			if ( $invalid ) {
				$data = array(
					'first_name'   => 'PayPal',
					'last_name'    => 'Brasil',
					'person_type'  => '2',
					'cpf'          => '',
					'cnpj'         => '10.878.448/0001-66',
					'phone'        => '(21) 99999-99999',
					'email'        => 'contato@paypal.com.br',
					'postcode'     => '01310-100',
					'address'      => 'Av. Paulista',
					'number'       => '1048',
					'address_2'    => '',
					'neighborhood' => 'Bela Vista',
					'city'         => 'São Paulo',
					'state'        => 'SP',
					'country'      => 'BR',
					'dummy'        => true,
					'invalid'      => $invalid,
				);
			}

			// Add session if is dummy data to check it later.
			WC()->session->set( 'wc-ppp-brasil-dummy-data', $data['dummy'] );

			// Return the data if is dummy. We don't need to process this.
			if ( $invalid ) {
				return $data;
			}

			// Create the payment.
			$payment = $this->create_payment( $data, $data['dummy'] );

			// Add session with payment ID to check it later.
			WC()->session->set( 'wc-ppp-brasil-payment-id', $payment->getId() );

			// Add the saved remember card, approval link and the payment URL.
			$data['remembered_cards'] = is_user_logged_in() ? get_user_meta( get_current_user_id(), 'wc_ppp_brasil_remembered_cards', true ) : '';
			$data['approval_url']     = $payment->getApprovalLink();
			$data['payment_id']       = $payment->getId();

			return $data;
		}

		/**
		 * Create the PayPal payment.
		 *
		 * @param $data
		 * @param bool $dummy
		 *
		 * @return \PayPal\Api\Payment
		 * @throws Exception
		 */
		public function create_payment( $data, $dummy = false ) {
			// Don' log if is dummy data.
			if ( $dummy ) {
				$this->debug = false;
			}
			// Check if is order pay
			$order_id = get_query_var( 'order-pay' );
			$order    = $order_id ? wc_get_order( $order_id ) : false;
			$cart     = WC()->cart;
			$this->log( 'Creating payment' );
			$exception_data = array();
			try {

				// Store the amount_total, so this will always have the correct total.
				$amount_total = 0;

				// Create the details.
				$details      = new  \PayPal\Api\Details();
				$amount_total += $order ? $order->get_shipping_total() : $cart->shipping_total;
				$details->setShipping( $order ? $order->get_shipping_total() : $cart->shipping_total )
				        ->setSubtotal( $order ? $order->get_subtotal() - $order->get_discount_total() : $cart->subtotal - $cart->discount_cart );

				// Create payment options.
				$payment_options = new PayPal\Api\PaymentOptions();
				$payment_options->setAllowedPaymentMethod( 'IMMEDIATE_PAY' );

				// Add the items.
				$items      = array();
				$cart_items = $order ? $order->get_items() : $cart->get_cart();
				foreach ( $cart_items as $item_data ) {
					/** @var WC_Product $product */
					$product       = wc_get_product( $item_data['variation_id'] ? $item_data['variation_id'] : $item_data['product_id'] );
					$item          = new PayPal\Api\Item();
					$items[]       = $item;
					$product_price = $order ? $item_data['line_subtotal'] / $item_data['qty'] : $item_data['line_subtotal'] / $item_data['quantity'];
					$product_price += $order ? $item_data['line_tax'] / $item_data['qty'] : $item_data['line_tax'] / $item_data['quantity'];
					$product_title = isset( $item_data['variation_id'] ) && $item_data['variation_id'] ? $product->get_title() . ' - ' . implode( ', ', $item_data['variation'] ) : $product->get_title();
					$item->setName( $product_title )
					     ->setCurrency( get_woocommerce_currency() )
					     ->setQuantity( $order ? $item_data['qty'] : $item_data['quantity'] )
					     ->setPrice( $product_price )
					     ->setSku( $product->get_sku() ? $product->get_sku() : $product->get_id() )
					     ->setUrl( $product->get_permalink() );

					$amount_total += $product_price * ( $order ? $item_data['qty'] : $item_data['quantity'] );
				}

				// If order has discount, add this as a item
				$has_discount = $order ? $order->get_total_discount() : $cart->has_discount();
				if ( $has_discount ) {
					$item     = new PayPal\Api\Item();
					$items[]  = $item;
					$discount = $order ? $order->get_total_discount() : $cart->discount_cart;
					$item->setSku( 'discount' )
					     ->setName( __( 'Desconto', 'ppp-brasil' ) )
					     ->setQuantity( 1 )
					     ->setPrice( $discount * - 1 )
					     ->setCurrency( get_woocommerce_currency() );

					$amount_total += $discount * - 1;
				}

				// If order has fees, add this as a item
				$fees = $order ? $order->get_fees() : $cart->get_fees();
				if ( $fees ) {
					$total_fees = 0;
					foreach ( $fees as $fee ) {
						$total_fees += $fee->amount;
						$item       = new PayPal\Api\Item();
						$items[]    = $item;
						$item->setSku( sanitize_title( $fee->name ) )
						     ->setName( $fee->name )
						     ->setQuantity( 1 )
						     ->setPrice( $fee->amount )
						     ->setCurrency( get_woocommerce_currency() );
					}
					if ( $total_fees ) {
						$details->setSubtotal( (float) $details->getSubtotal() + $total_fees );
						$amount_total += $total_fees;
					}
				}

				// Create the amount.
				$amount = new \PayPal\Api\Amount();
				$amount->setCurrency( get_woocommerce_currency() )
				       ->setTotal( $amount_total )
				       ->setDetails( $details );

				// Create the item list.
				$item_list = new \PayPal\Api\ItemList();
				$item_list->setItems( $items );

				// Create the address.
				if ( ! $dummy ) {

					if ( $data['address_2'] ) {
						if ( $data['number'] ) {
							$address_line_1 = sprintf( '%s, %s, %s', $data['address'], $data['number'], $data['address_2'] );
						} else {
							$address_line_1 = sprintf( '%s, %s', $data['address'], $data['address_2'] );
						}
					} else {
						if ( $data['number'] ) {
							$address_line_1 = sprintf( '%s, %s', $data['address'], $data['number'] );
						} else {
							$address_line_1 = sprintf( '%s', $data['address'] );
						}
					}

					$address_line_2 = $data['neighborhood'];

					$shipping_address = new \PayPal\Api\ShippingAddress();
					$shipping_address->setRecipientName( $data['first_name'] . ' ' . $data['last_name'] )
					                 ->setCountryCode( $data['country'] )
					                 ->setPostalCode( $data['postcode'] )
					                 ->setLine1( $address_line_1 )
					                 ->setCity( $data['city'] )
					                 ->setState( $data['state'] )
					                 ->setPhone( $data['phone'] );

					if ( $address_line_2 ) {
						$shipping_address->setLine2( $address_line_2 );
					}

					$item_list->setShippingAddress( $shipping_address );
				}

				// Create the payer.
				$payer = new \PayPal\Api\Payer();
				$payer->setPaymentMethod( 'paypal' );

				// Create the transaction.
				$transaction = new \PayPal\Api\Transaction();
				$transaction->setPaymentOptions( $payment_options )
				            ->setItemList( $item_list )
				            ->setAmount( $amount );

				// Create thhe redirect urls.
				$redirect_urls = new \PayPal\Api\RedirectUrls();
				$redirect_urls->setReturnUrl( home_url() )
				              ->setCancelUrl( home_url() );

				// Create the payment.
				$payment = new \PayPal\Api\Payment();
				$payment->setIntent( 'sale' )
				        ->setPayer( $payer )
				        ->setTransactions( array( $transaction ) )
				        ->setRedirectUrls( $redirect_urls );

				$this->log( 'Sending create payment request: ' . $this->print_r( $payment->toArray(), true ) );

				// Get API Context.
				$api_context = $this->get_api_context();

				// Set the application context
				$application_context = new \PayPal\Api\ApplicationContext();
				$application_context->setBrandName( get_bloginfo( 'name' ) );
				$application_context->setShippingPreference();
				$payment->setApplicationContext( $application_context );

				// Create the payment.
				$payment->create( $api_context );

				$this->log( 'Payment created: ' . $this->print_r( $payment->toArray(), true ) );

				return $payment;
			} catch ( \PayPal\Exception\PayPalConnectionException $ex ) { // Catch any PayPal error.
				$this->log( 'Code: ' . $ex->getCode() );
				$this->log( $ex->getMessage() );
				$error_data = json_decode( $ex->getData(), true );
				$this->log( 'PayPalConnectionException: ' . $this->print_r( $error_data, true ) );
				if ( $error_data['name'] === 'VALIDATION_ERROR' ) {
					$exception_data = $error_data['details'];
				}
			} catch ( Exception $ex ) { // Catch any other error.
				$this->log( 'PHP Error: ' . $this->print_r( $ex->getMessage(), true ) );
			}

			$exception       = new Exception( __( 'Ocorreu um erro inesperado, por favor tente novamente. Se o erro persistir entre em contato.', 'ppp-brasil' ) );
			$exception->data = $exception_data;

			throw $exception;
		}

		/**
		 * Validate data if contain any invalid field.
		 *
		 * @param $data
		 *
		 * @return array
		 */
		private function validate_data( $data ) {
			$errors = array();

			// Check first name.
			if ( empty( $data['first_name'] ) ) {
				$errors['first_name'] = __( 'Nome inválido', 'ppp-brasil' );
			}

			// Check last name.
			if ( empty( $data['last_name'] ) ) {
				$errors['last_name'] = __( 'Sobrenome inválido', 'ppp-brasil' );
			}

			// Check phone.
			if ( empty( $data['phone'] ) ) {
				$errors['phone'] = __( 'Telefone inválido', 'ppp-brasil' );
			}

			if ( empty( $data['address'] ) ) {
				$errors['address'] = __( 'Endereço inválido', 'ppp-brasil' );
			}

			if ( empty( $data['city'] ) ) {
				$errors['city'] = __( 'Cidade inválida', 'ppp-brasil' );
			}

			if ( empty( $data['state'] ) ) {
				$errors['state'] = __( 'Estado inválido', 'ppp-brasil' );
			}

			if ( empty( $data['country'] ) ) {
				$errors['country'] = __( 'País inválido', 'ppp-brasil' );
			}

			if ( empty( $data['postcode'] ) ) {
				$errors['postcode'] = __( 'CEP inválido', 'ppp-brasil' );
			}

			// Check email.
			if ( ! is_email( $data['email'] ) ) {
				$errors['email'] = __( 'Email inválido', 'ppp-brasil' );
			}

			// Only if require CPF/CNPJ
			if ( pppbr_needs_cpf() ) {

				// Check address number (only with CPF/CPNJ)
				if ( empty( $data['number'] ) ) {
					$errors['number'] = __( 'Número inválido', 'ppp-brasil' );
				}

				// Check person type.
				if ( $data['person_type'] !== '1' && $data['person_type'] !== '2' ) {
					$errors['person_type'] = __( 'Tipo de pessoa inválido', 'ppp-brasil' );
				}

				// Check the CPF
				if ( $data['person_type'] == '1' && ! $this->is_cpf( $data['cpf'] ) ) {
					$errors['cpf'] = __( 'CPF inválido', 'ppp-brasil' );
				}

				// Check the CNPJ
				if ( $data['person_type'] == '2' && ! $this->is_cnpj( $data['cnpj'] ) ) {
					$errors['cnpj'] = __( 'CNPJ inválido', 'ppp-brasil' );
				}

			}

			return $errors;
		}

		/**
		 * Enqueue scripts in checkout.
		 */
		public function checkout_scripts() {
			// Just load this script in checkout and if isn't in order-receive.
			if ( is_checkout() && ! get_query_var( 'order-received' ) ) {
				if ( 'yes' === $this->js_debug ) {
					wp_enqueue_script( 'pretty-web-console', plugins_url( 'assets/js/pretty-web-console.lib.js', __DIR__ ), array(), '0.10.1', true );
				}
				wp_enqueue_script( 'ppp-script', '//www.paypalobjects.com/webstatic/ppplusdcc/ppplusdcc.min.js', array(), WC_PPP_Brasil::$VERSION, true );
				wp_localize_script( 'ppp-script', 'wc_ppp_brasil_data', array(
					'id'                => $this->id,
					'order_pay'         => ! ! get_query_var( 'order-pay' ),
					'mode'              => $this->mode === 'sandbox' ? 'sandbox' : 'live',
					'form_height'       => $this->get_form_height(),
					'show_payer_tax_id' => pppbr_needs_cpf(),
					'language'          => get_woocommerce_currency() === 'BRL' ? 'pt_BR' : 'en_US',
					'country'           => $this->get_woocommerce_country(),
					'messages'          => array(
						'check_entry' => __( 'Verifique os dados informados e tente novamente', 'ppp-brasil' ),
					),
					'debug_mode'        => 'yes' === $this->js_debug,
				) );
				wp_enqueue_script( 'wc-ppp-brasil-script', plugins_url( 'assets/js/frontend.js', __DIR__ ), array( 'jquery' ), WC_PPP_Brasil::$VERSION, true );
				wp_enqueue_style( 'wc-ppp-brasil-style', plugins_url( 'assets/css/frontend.css', __DIR__ ), array(), WC_PPP_Brasil::$VERSION, 'all' );
			}
		}

		/**
		 * Get the WooCommerce country.
		 *
		 * @return string
		 */
		private function get_woocommerce_country() {
			return get_woocommerce_currency() === 'BRL' ? 'BR' : 'US';
		}

		/**
		 * Get form height.
		 */
		private function get_form_height() {
			$height    = trim( $this->form_height );
			$min_value = 400;
			$max_value = 550;
			$test      = preg_match( '/[0-9]+/', $height, $matches );
			if ( $test && $matches[0] === $height && $height >= $min_value && $height <= $max_value ) {
				return $height;
			}

			return null;
		}

		/**
		 * Enqueue admin scripts.
		 */
		public function admin_scripts() {
			$screen         = get_current_screen();
			$screen_id      = $screen ? $screen->id : '';
			$wc_screen_id   = sanitize_title( __( 'WooCommerce', 'woocommerce' ) );
			$wc_settings_id = $wc_screen_id . '_page_wc-settings';
			if ( $wc_settings_id === $screen_id && isset( $_GET['section'] ) && $_GET['section'] === $this->id ) {
				wp_enqueue_style( 'wc-ppp-brasil-admin-style', plugins_url( 'assets/css/backend.css', __DIR__ ), array(), WC_PPP_Brasil::$VERSION, 'all' );
			}
		}

		/**
		 * Get the PayPal API Context.
		 *
		 * @return \PayPal\Rest\ApiContext
		 */
		public function get_api_context( $client_id = null, $client_secret = null, $mode = null ) {
			// Autoload the SDK.
			include_once dirname( __FILE__ ) . '/libs/PayPal-PHP-SDK/autoload.php';

			// Set the instance client_id if not given.
			if ( $client_id === null ) {
				$client_id = $this->client_id;
			}

			// Set the instance client_secret if not given.
			if ( $client_secret === null ) {
				$client_secret = $this->client_secret;
			}

			// Set the instance sandbox if not given.
			if ( $mode === null ) {
				$mode = $this->mode;
			}

			// Create the credentials.
			$credential         = new \PayPal\Auth\OAuthTokenCredential( $client_id, $client_secret );
			$api_context        = new \PayPal\Rest\ApiContext( $credential );
			$api_context_config = array(
				'mode' => $mode === 'sandbox' ? 'sandbox' : 'live',
			);
			$api_context->setConfig( $api_context_config );

			// Add an ID to track this extension.
			$api_context->addRequestHeader( "PayPal-Partner-Attribution-Id", 'WooCommerceBR_Ecom_PPPlus' );

			return $api_context;
		}

		public function get_api_context_negative( $client_id = null, $client_secret = null, $mode = null ) {
			// Autoload the SDK.
			include_once dirname( __FILE__ ) . '/libs/PayPal-PHP-SDK/autoload.php';

			// Set the instance client_id if not given.
			if ( $client_id === null ) {
				$client_id = $this->client_id;
			}

			// Set the instance client_secret if not given.
			if ( $client_secret === null ) {
				$client_secret = $this->client_secret;
			}

			// Set the instance sandbox if not given.
			if ( $mode === null ) {
				$mode = $this->mode;
			}

			// Create the credentials.
			$credential         = new \PayPal\Auth\OAuthTokenCredential( $client_id, $client_secret );
			$api_context        = new \PayPal\Rest\ApiContext( $credential );
			$api_context_config = array(
				'mode' => $mode === 'sandbox' ? 'sandbox' : 'live',
			);
			$api_context->setConfig( $api_context_config );

			// Add an ID to track this extension.
			$api_context->addRequestHeader( "PayPal-Partner-Attribution-Id", 'WooCommerceBR_Ecom_PPPlus' );
			$api_context->addRequestHeader( "PayPal-Mock-Response", '{"mock_application_codes":"INSUFFICIENT_FUNDS"}' );

			return $api_context;
		}

		/**
		 * Handle webhooks events.
		 */
		public function webhook_handler() {
			// Include the handler.
			include_once dirname( __FILE__ ) . '/class-wc-ppp-brasil-webhooks-handler.php';

			try {
				$this->log( 'Checking webhook' );

				// Get the data.
				$headers     = array_change_key_case( getallheaders(), CASE_UPPER );
				$body        = $this->get_raw_data();
				$api_context = $this->get_api_context();

				// Instance the handler.
				$handler = new WC_PPP_Brasil_Webhooks_Handler( $api_context );

				// Prepare the signature verification.
				$signature_verification = new \PayPal\Api\VerifyWebhookSignature();
				$signature_verification->setAuthAlgo( $headers['PAYPAL-AUTH-ALGO'] );
				$signature_verification->setTransmissionId( $headers['PAYPAL-TRANSMISSION-ID'] );
				$signature_verification->setCertUrl( $headers['PAYPAL-CERT-URL'] );
				$signature_verification->setWebhookId( $this->webhook_id );
				$signature_verification->setTransmissionSig( $headers['PAYPAL-TRANSMISSION-SIG'] );
				$signature_verification->setTransmissionTime( $headers['PAYPAL-TRANSMISSION-TIME'] );

				// Create a webhook event from JSON.
				$webhook_event = new \PayPal\Api\WebhookEvent();
				$webhook_event->fromJson( $body );

				$signature_verification->setWebhookEvent( $webhook_event );

				// Verify the webhook.
				$output = $signature_verification->post( $api_context );

				// If verification success, handle the event.
				if ( $output->getVerificationStatus() === 'SUCCESS' ) {
					$handler->handle( $webhook_event->toArray() );
				}
			} catch ( \PayPal\Exception\PayPalConnectionException $ex ) { // Catch any PayPal error.
				$this->log( 'Code: ' . $ex->getCode() );
				$this->log( $ex->getMessage() );
				$this->log( 'PayPalConnectionException: ' . $this->print_r( json_decode( $ex->getData(), true ), true ) );
			} catch ( Exception $ex ) { // Catch any other error.
				$this->log( 'PHP Error: ' . $this->print_r( $ex->getMessage(), true ) );
			}

		}

		/**
		 * Retrieve the raw request entity (body).
		 *
		 * @return string
		 */
		public function get_raw_data() {
			// $HTTP_RAW_POST_DATA is deprecated on PHP 5.6
			if ( function_exists( 'phpversion' ) && version_compare( phpversion(), '5.6', '>=' ) ) {
				return file_get_contents( 'php://input' );
			}
			global $HTTP_RAW_POST_DATA;
			// A bug in PHP < 5.2.2 makes $HTTP_RAW_POST_DATA not set by default,
			// but we can do it ourself.
			if ( ! isset( $HTTP_RAW_POST_DATA ) ) {
				$HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
			}

			return $HTTP_RAW_POST_DATA;
		}

		/**
		 * Format money.
		 *
		 * @param $value
		 *
		 * @return string
		 */
		protected function money_format( $value ) {
			return number_format( $value, 2, '.', '' );
		}

		/**
		 * Add log to the system.
		 *
		 * @param $log
		 */
		private function log( $log ) {
			// Check if is in debug mode.
			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, $log );
			}
		}

		/**
		 * Gerneate ID for logs.
		 *
		 * @return int
		 */
		private function unique_id() {
			return rand( 1, 10000 );
		}

		/**
		 * Checks if the CPF is valid.
		 *
		 * @param  string $cpf CPF to validate.
		 *
		 * @return bool
		 */
		public function is_cpf( $cpf ) {
			$cpf = preg_replace( '/[^0-9]/', '', $cpf );

			if ( 11 !== strlen( $cpf ) || preg_match( '/^([0-9])\1+$/', $cpf ) ) {
				return false;
			}

			$digit = substr( $cpf, 0, 9 );

			for ( $j = 10; $j <= 11; $j ++ ) {
				$sum = 0;

				for ( $i = 0; $i < $j - 1; $i ++ ) {
					$sum += ( $j - $i ) * intval( $digit[ $i ] );
				}

				$summod11        = $sum % 11;
				$digit[ $j - 1 ] = $summod11 < 2 ? 0 : 11 - $summod11;
			}

			return intval( $digit[9] ) === intval( $cpf[9] ) && intval( $digit[10] ) === intval( $cpf[10] );
		}

		/**
		 * Checks if the CNPJ is valid.
		 *
		 * @param  string $cnpj CNPJ to validate.
		 *
		 * @return bool
		 */
		public function is_cnpj( $cnpj ) {
			$cnpj = sprintf( '%014s', preg_replace( '{\D}', '', $cnpj ) );

			if ( 14 !== strlen( $cnpj ) || 0 === intval( substr( $cnpj, - 4 ) ) ) {
				return false;
			}

			for ( $t = 11; $t < 13; ) {
				for ( $d = 0, $p = 2, $c = $t; $c >= 0; $c --, ( $p < 9 ) ? $p ++ : $p = 2 ) {
					$d += $cnpj[ $c ] * $p;
				}

				if ( intval( $cnpj[ ++ $t ] ) !== ( $d = ( ( 10 * $d ) % 11 ) % 10 ) ) {
					return false;
				}
			}

			return true;
		}

		/**
		 * Get assets file in the plugin.
		 */
		public function plugin_url( $file ) {
			return plugins_url( $file, __DIR__ );
		}

		/**
		 * Return the gateway's title.
		 *
		 * @return string
		 */
		public function get_title() {
			$title = __( 'Cartão de Crédito', 'ppp-brasil' );
			if ( ! empty( $this->title ) ) {
				$title .= ' ' . $this->title;
			}

			return apply_filters( 'woocommerce_gateway_title', $title, $this->id );
		}

		protected function print_r( $expression, $return = false ) {
			if ( ! function_exists( 'wc_print_r' ) ) {
				return print_r( $expression, $return );
			} else {
				return wc_print_r( $expression, $return );
			}
		}

	}

}