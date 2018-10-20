/* global wc_boletofacil_params, wc_checkout_params, DirectCheckout */
/*jshint devel: true */
(function ($) {
    $('form.checkout').on('click', 'input[name="payment_method"]', function (e) {
        var target_payment_box = $('div.payment_box.' + $(this).attr('ID'));
        if ($(this).is(':checked') && !target_payment_box.is(':visible')) {
            $('div.payment_box').filter(':visible').slideUp(250);
            if ($(this).is(':checked')) {
                if ($('div.payment_box.' + $(this).attr('ID')).text().trim() == '') {
                    wc_boletofacil_params.hasIncompatiblePlugin ? '' : e.stopImmediatePropagation();
                } else {
                    $('div.payment_box.' + $(this).attr('ID')).slideDown(250);
                }
            }
        }
        changePaymentMethod();
    });

    function changePaymentMethod() {

        if ($("#boletofacil-boleto").is(":checked")) {
            $("#creditCard").hide();
            $("#installments_credit_card").hide();
            $("#boleto").show();
            $("#installments_bank_slip").show();
            $("#max_installments_bank_slip").val('1');
        }

        if($("#boletofacil-credit-card").is(":checked")) {
            $("#boleto").hide();
            $("#installments_bank_slip").hide();
            $("#creditCard").show();
            $("#installments_credit_card").show();
            $("#max_installments_credit_card").val('1');
        }

    }

    $(function () {

        var boletofacil_submit = false;
        const checkout = new DirectCheckout(wc_boletofacil_params.public_token, wc_boletofacil_params.testmode === 'yes' ? false : true);

        $('form.checkout').on('checkout_place_order', function () {
            return formHandler(this);
        });

        $('form#order_review').submit(function () {
            return formHandler(this);
        });

        $('form.checkout').on('click', 'input[name="pmethod"]', function () {
            changePaymentMethod();
        });

        $('body').on('checkout_error', function () {
            $('#credit_card_hash').remove();
        });

        $('form.checkout, form#order_review').on('change', '#boletofacil-credit-card-fields input', function () {
            $('#credit_card_hash').remove();
        });

        $('form.checkout').on('keyup', 'input[name="card_number"]', validateOnlyNumbers);
        $('form.checkout').on('keyup', 'input[name="card_expiration_month"]', validateOnlyNumbers);
        $('form.checkout').on('keyup', 'input[name="card_expiration_year"]', validateOnlyNumbers);
        $('form.checkout').on('keyup', 'input[name="card_security_code"]', validateOnlyNumbers);

        /**
         * Form Handler.
         *
         * @param  {object} form
         *
         * @return {bool}
         */
        function formHandler(form) {

            if (boletofacil_submit) {
                boletofacil_submit = false;
                return true;
            }

            if (!$('#payment_method_boletofacil').is(':checked')) {
                return true;
            }

            if ($('#creditCard').is(':visible')) {
                var errors = "";
                var $form = $(form),
                    creditCardForm = $('#boletofacil-credit-card-fields', $form),
                    errorHtml = '';

                if (!$('#card_number', creditCardForm).val()) {
                    errors += '<li>Número do cartão é obrigatório!</li>';
                }

                if (!$('#card_name', creditCardForm).val()) {
                    errors += '<li>Nome do cartão é obrigatório!</li>';
                }

                if (!$('#card_expiration_month', creditCardForm).val()) {
                    errors += '<li>Mês de expiração do cartão é obrigatório!</li>';
                }
                if (!$('#card_expiration_year', creditCardForm).val()) {
                    errors += '<li>Ano de expiração do cartão é obrigatório!</li>';
                }

                if (!$('#card_security_code', creditCardForm).val()) {
                    errors += '<li>Código de segurança do cartão é obrigatório!</li>';
                }

                var cardData = {
                    cardNumber: $('#card_number', creditCardForm).val(),
                    holderName: $('#card_name', creditCardForm).val(),
                    securityCode: $('#card_security_code', creditCardForm).val(),
                    expirationMonth: $('#card_expiration_month', creditCardForm).val(),
                    expirationYear: $('#card_expiration_year', creditCardForm).val()
                };

                if (errors === '') {
                    if (!checkout.isValidCardNumber(cardData.cardNumber)) {
                        errors += '<li>Número do cartão é inválido!</li>';
                    }

                    if (!checkout.isValidSecurityCode(cardData.cardNumber, cardData.securityCode)) {
                        errors += '<li>Código de segurança do cartão é inválido!</li>';
                    }

                    if (!checkout.isValidExpireDate(cardData.expirationMonth, cardData.expirationYear)) {
                        errors += '<li>Data de expiração do cartão é inválida</li>';
                    }
                }

                if (errors != '') {
                    $('#boleto_facil_errors').addClass('woocommerce-error').html('<ul>' + errors + '</ul>');
                    $('#boleto_facil_errors').show();

                    return false;
                } else {

                    $('#boleto_facil_errors').removeClass('woocommerce-error').html('');
                    $('#boleto_facil_errors').hide();

                    checkout.getCardHash(cardData, function (hash) {
                        // Submit the form.
                        if (hash) {
                            // Remove any old hash input.
                            $('#credit_card_hash').remove();

                            // Add new HASH
                            $form.append($('<input class="credit_card_hash" name="credit_card_hash" type="hidden" />').val(hash));
                            boletofacil_submit = true;
                            $form.submit();
                        } else {
                            return false;
                        }

                    });

                    return false;
                }
            } else {
                return true;
            }

            return false;
        }

        function validateOnlyNumbers() {
            if (/\D/g.test(this.value)) {
                this.value = this.value.replace(/\D/g, '');
            }
        }

    });
})
(jQuery);
