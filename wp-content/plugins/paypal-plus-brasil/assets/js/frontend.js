var WC_PPP_Brasil_Checkout = (function () {
    function WC_PPP_Brasil_Checkout() {
        var _this = this;
        /**
         * Run after form submit to submit the iframe and after submit the form again.
         * @param event
         */
        this.onSubmitForm = function (event) {
            var checked = jQuery('#payment_method_' + wc_ppp_brasil_data.id + ':checked');
            _this.log('info', 'Checking if PayPal Payment method is checked...');
            _this.log('data', !!checked.length);
            if (!jQuery("#payment_method_" + wc_ppp_brasil_data.id).length) {
                _this.log('error', "PayPal Plus check button wasn't detected. Should have an element #payment_method_" + wc_ppp_brasil_data.id);
            }
            // Block the form in order pay, as it isn't default.
            if (wc_ppp_brasil_data['order_pay']) {
                _this.$form.block({
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                });
            }
            // Check if is not forced submit and prevent submit before submit PayPal iframe or isn't the payment selected.
            if (_this.forceSubmit && checked.length) {
                _this.log('info', 'Form will be forced to submit.');
            }
            else if (checked.length) {
                _this.log('info', "Form won't be forced to submit, will try to contact PayPal iframe first.");
            }
            // Submit the iframe
            if (!_this.forceSubmit && checked.length) {
                event.preventDefault();
                event.stopImmediatePropagation();
                // Check if we have any instance
                if (_this.instance) {
                    _this.instance.doContinue();
                }
                else {
                    _this.log('error', "We don't have the iframe instance, something wrong may have occurred. May be the fields isn't fulfilled.");
                }
            }
        };
        /**
         * Trigger the update checkout to reload the checkout.
         */
        this.updateCheckout = function (event) {
            if (event === void 0) { event = null; }
            if (event) {
                event.preventDefault();
            }
            _this.triggerUpdateCheckout();
        };
        /**
         * Debounce the trigger checkout.
         *
         * @type {()=>any}
         */
        this.triggerUpdateCheckout = this.debounce(function () {
            _this.log('info', 'Updating checkout...');
            _this.$body.trigger('update_checkout');
        }, 500);
        /**
         * Create the iframe after update the checkout.
         */
        this.onUpdatedCheckout = function () {
            _this.$inputData = jQuery('#wc-ppp-brasil-data');
            _this.$inputResponse = jQuery('#wc-ppp-brasil-response');
            _this.$inputError = jQuery('#wc-ppp-brasil-error');
            _this.$inputSubmit = jQuery('#place_order');
            if (!_this.$inputSubmit.length) {
                _this.log('error', "Input submit wasn't found. Should have the #place_order element in the form.");
            }
            _this.$overlay = jQuery('#wc-ppb-brasil-container-overlay');
            _this.$loading = jQuery('#wc-ppp-brasil-container-loading');
            _this.$containerDummy = jQuery('#wc-ppp-brasil-container-dummy');
            _this.$overlay.on('click', '[data-action=update-checkout]', _this.updateCheckout);
            _this.showOverlay();
            var inputData = _this.$inputData.val();
            var phpErrorData = jQuery('#wc-ppp-brasil-api-error-data').val();
            if (phpErrorData) {
                _this.log('error', 'There was an error with following data:');
                _this.log('data', JSON.parse(phpErrorData));
            }
            try {
                if (inputData) {
                    var data = JSON.parse(inputData);
                    _this.log('info', 'Creating iframe with data:');
                    _this.log('data', data);
                    if (data.invalid.length !== 0) {
                        _this.log('error', "There's some invalid data. Iframe will render dummy version:");
                        _this.log('data', data.invalid);
                    }
                    _this.createIframe(data);
                }
            }
            catch (error) {
                _this.log('error', 'There was some error creating the iframe.');
                _this.log('info', 'Data received:');
                _this.log('data', inputData);
                _this.log('info', 'Error:');
                _this.log('data', error);
            }
        };
        /**
         * Listen for messages in the page.ˆ
         * @param event
         */
        this.messageListener = function (event) {
            try {
                var message = JSON.parse(event.data);
                _this.log('info', 'Received a message:');
                _this.log('data', message);
                // Check if is iframe error handling or is just an action.
                if (typeof message['cause'] !== 'undefined') {
                    _this.log('error', 'This message is an iframe error!');
                    _this.treatIframeError(message);
                }
                else {
                    _this.treatIframeAction(message);
                }
            }
            catch (err) {
            }
        };
        this.log('heading', 'PayPal Plus logging enabled\n');
        this.log('info', 'Backend data:');
        this.log('data', wc_ppp_brasil_data);
        // Set the body element.
        this.$body = jQuery(document.body);
        // Log document.body detection.
        if (this.$body.length) {
            this.log('info', 'HTML body detected.');
        }
        else {
            this.log('error', "HTML body didn't detected.");
        }
        // Set the form element
        this.$form = wc_ppp_brasil_data['order_pay'] ? jQuery('form#order_review') : jQuery('form.checkout.woocommerce-checkout');
        // Log form element
        if (wc_ppp_brasil_data['order_pay']) {
            this.log('info', 'Running script as order pay.');
        }
        else {
            this.log('info', 'Running script as order review.');
        }
        if (this.$form.length) {
            this.log('info', 'Detected form.checkout.woocommerce-checkout element.');
            this.log('data', this.$form);
        }
        else {
            this.log('error', "Didn't detect form.checkout.woocommerce-checkout element.");
        }
        // Listen for input/select changes.
        this.listenInputChanges();
        // Listen for updated checkout.
        this.$body.on('updated_checkout', this.onUpdatedCheckout);
        // Listen for the form submit.
        this.$form.on('submit', this.onSubmitForm);
        // Listen for window messages
        window.addEventListener('message', this.messageListener, false);
        // Trigger update checkout on order pay page
        if (wc_ppp_brasil_data['order_pay']) {
            jQuery(function ($) {
                jQuery('body').trigger('updated_checkout');
            });
        }
    }
    /**
     * Add event listener for input/select changes and trigger the update checkout.
     */
    WC_PPP_Brasil_Checkout.prototype.listenInputChanges = function () {
        var _this = this;
        var keySelectors = [
            '[name=billing_first_name]',
            '[name=billing_last_name]',
            '[name=billing_cpf]',
            '[name=billing_cnpj]',
            '[name=billing_phone]',
            '[name=billing_address_1]',
            '[name=billing_number]',
            '[name=billing_address_2]',
            '[name=billing_neighborhood]',
            '[name=billing_city]',
            '[name=billing_state]',
            '[name=billing_country]',
            '[name=billing_email]',
        ];
        var changeSelectors = [
            '[name=billing_persontype]',
        ];
        jQuery(keySelectors.join(',')).on('keyup', function () { return _this.updateCheckout(); });
        this.log('info', 'Listening for keyup to following elements:');
        this.log('data', keySelectors);
        jQuery(changeSelectors.join(',')).on('change', function () { return _this.updateCheckout(); });
        this.log('info', 'Listening for change to following elements:');
        this.log('data', changeSelectors);
    };
    /**
     * Create the iframe with the data.
     * @param data
     */
    WC_PPP_Brasil_Checkout.prototype.createIframe = function (data) {
        // If it's not a dummy data, remove the overlay.
        if (!data.dummy) {
            this.hideOverlay();
            // Show loading.
            this.showLoading();
            // Settings
            var settings = {
                'approvalUrl': data.approval_url,
                'placeholder': 'wc-ppp-brasil-container',
                'mode': wc_ppp_brasil_data['mode'],
                'payerFirstName': data.first_name,
                'payerLastName': data.last_name,
                'payerPhone': data.phone,
                'language': wc_ppp_brasil_data.language,
                'country': wc_ppp_brasil_data.country,
                'payerEmail': data.email,
                'rememberedCards': data.remembered_cards,
            };
            if (wc_ppp_brasil_data['form_height']) {
                settings['iframeHeight'] = wc_ppp_brasil_data['form_height'];
            }
            // Fill conditional data
            if (wc_ppp_brasil_data.show_payer_tax_id) {
                settings['payerTaxId'] = data.person_type === '1' ? data.cpf : data.cnpj;
                settings['payerTaxIdType'] = data.person_type === '1' ? 'BR_CPF' : 'BR_CNPJ';
            }
            else {
                settings['payerTaxId'] = '';
            }
            this.log('info', 'Settings for iframe:');
            this.log('data', settings);
            // Instance the PPP.
            this.instance = PAYPAL.apps.PPP(settings);
        }
        else {
            this.$containerDummy.removeClass('hidden');
        }
    };
    /**
     * Hide the overlay in container.
     */
    WC_PPP_Brasil_Checkout.prototype.hideOverlay = function () {
        this.$overlay.addClass('hidden');
    };
    WC_PPP_Brasil_Checkout.prototype.showOverlay = function () {
        this.$overlay.removeClass('hidden');
    };
    WC_PPP_Brasil_Checkout.prototype.hideLoading = function () {
        this.$loading.addClass('hidden');
    };
    WC_PPP_Brasil_Checkout.prototype.showLoading = function () {
        this.$loading.removeClass('hidden');
    };
    /**
     * Treat the iframe errors.
     * @param message
     */
    WC_PPP_Brasil_Checkout.prototype.treatIframeError = function (message) {
        var cause = message['cause'].replace(/['"]+/g, "");
        switch (cause) {
            case 'CHECK_ENTRY':
                this.showMessage('<div class="woocommerce-error">' + wc_ppp_brasil_data['messages']['check_entry'] + '</div>');
                break;
            default:
                this.log("This message won't be treated, so form will be submitted.");
                this.$inputError.val(message['cause']);
                this.forceSubmitForm();
                break;
        }
    };
    /**
     * Treat the iframe actions.
     * @param message
     */
    WC_PPP_Brasil_Checkout.prototype.treatIframeAction = function (message) {
        switch (message['action']) {
            // When call to enable the continue button.
            case 'enableContinueButton':
                this.enableSubmitButton();
                break;
            // When call to disable continue button.
            case 'disableContinueButton':
                this.disableSubmitButton();
                break;
            // When the iframe was submitted and we have the payment info.
            case 'checkout':
                var data = JSON.stringify(message);
                this.log('info', 'Success message received from iframe:');
                this.log('data', data);
                // Add the data in the inputs
                this.$inputResponse.val(data);
                // Submit the form
                this.forceSubmitForm();
                break;
            // In case we get some error.
            case 'onError':
                this.$inputResponse.val('');
                break;
            case 'loaded':
                this.hideLoading();
                break;
        }
    };
    /**
     * Disable the submit button.
     */
    WC_PPP_Brasil_Checkout.prototype.disableSubmitButton = function () {
        this.$inputSubmit.prop('disabled', true);
    };
    /**
     * Enable the submit button.
     */
    WC_PPP_Brasil_Checkout.prototype.enableSubmitButton = function () {
        this.$inputSubmit.prop('disabled', false);
    };
    /**
     * Force the form submit.
     */
    WC_PPP_Brasil_Checkout.prototype.forceSubmitForm = function () {
        this.forceSubmit = true;
        this.$form.submit();
    };
    WC_PPP_Brasil_Checkout.prototype.showMessage = function (messages) {
        var $form = jQuery('form.checkout');
        if (!$form.length) {
            this.log('error', "Isn't possible to find the form.checkout element.");
        }
        // Remove notices from all sources
        jQuery('.woocommerce-error, .woocommerce-message').remove();
        // Add new errors
        if (messages) {
            $form.prepend('<div class="woocommerce-NoticeGroup woocommerce-NoticeGroup-updateOrderReview">' + messages + '</div>');
            // Lose focus for all fields
            $form.find('.input-text, select, input:checkbox').blur();
            // Scroll to top
            jQuery('html, body').animate({
                scrollTop: ($form.offset().top - 100)
            }, 1000);
        }
    };
    WC_PPP_Brasil_Checkout.prototype.debounce = function (func, wait, immediate) {
        if (immediate === void 0) { immediate = false; }
        var timeout;
        return function () {
            var context = this;
            var args = arguments;
            var later = function () {
                timeout = null;
                if (!immediate)
                    func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow)
                func.apply(context, args);
        };
    };
    ;
    WC_PPP_Brasil_Checkout.prototype.log = function (type) {
        var data = [];
        for (var _i = 1; _i < arguments.length; _i++) {
            data[_i - 1] = arguments[_i];
        }
        // Only log when debug_mode is enabled.
        if (!wc_ppp_brasil_data.debug_mode) {
            return;
        }
        // Log each type.
        switch (type) {
            case 'heading':
                pwc().color("#003087").size(25).bold().log(data);
                break;
            case 'log':
                pwc().log(data);
                break;
            case 'info':
                pwc().bold().italic().color('#009cde').info(data);
                break;
            case 'warn':
                pwc().warn(data);
                break;
            case 'error':
                pwc().error(data);
                break;
            case 'data':
                data.forEach(function (item) { return console.log(item); });
                break;
            case 'custom-message':
                pwc().color('#012169').bold().italic().log(data);
                break;
        }
    };
    return WC_PPP_Brasil_Checkout;
})();
new WC_PPP_Brasil_Checkout();
