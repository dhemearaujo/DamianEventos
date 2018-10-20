// WooCommerce LiveChat
//
// @category Admin pages
(function ($) {
    var WooCommerceLiveChat = {
        init: function () {
            this.signInWithLiveChat();
            this.bindDisconnect();
            this.alreadyHaveAccountForm();
            this.settingsForm();
        },
        bindEvent: function(element, eventName, eventHandler) {
            if (element.addEventListener){
                element.addEventListener(eventName, eventHandler, false);
            } else if (element.attachEvent) {
                element.attachEvent('on' + eventName, eventHandler);
            }
        },
        signInWithLiveChat: function () {
            var logoutButton = document.getElementById('resetAccount'),
                iframeEl = document.getElementById('login-with-livechat');

            WooCommerceLiveChat.bindEvent(window, 'message', function (e) {
                if(e.data.startsWith('{')) {
                    var lcDetails = JSON.parse(e.data);
                    switch (lcDetails.type) {
                        case 'logged-in':
                            var licenseForm = $('div#useExistingAccount form#licenseForm');
                            if(licenseForm.length) {
                                licenseForm.find('input#licenseEmail').val(lcDetails.email);
                                licenseForm.find('input#licenseNumber').val(lcDetails.license);
                                WooCommerceLiveChat.sendEvent(
                                    'Integrations: User authorized the app',
                                    lcDetails.license,
                                    lcDetails.email,
                                    function () {
                                        licenseForm.submit();
                                    }
                                );
                            }
                            break;
                        case 'signed-out':
                            $('#login-with-livechat').css('display', 'block');
                            $('#logout').css('display', 'none');
                            break;
                    }
                }
            });

            if(logoutButton) {
                WooCommerceLiveChat.bindEvent(logoutButton, 'click', function (e) {
                    sendMessage('logout');
                });
            }

            var sendMessage = function(msg) {
                iframeEl.contentWindow.postMessage(msg, '*');
            };
        },

        bindDisconnect: function() {
            $('#resetAccount').click(function (e) {
                e.preventDefault();
                WooCommerceLiveChat.sendEvent(
                    'Integrations: User unauthorized the app',
                    lcDetails.license,
                    lcDetails.email,
                    function () {
                        location.href = $('#resetAccount').attr('href');
                    }
                );
            });
        },

        sendEvent: function(eventName, license, email, callback) {
            var amplitudeURL = 'https://queue.livechatinc.com/app_event/';
            var data = {
                "e" : JSON.stringify(
                    [{
                        "event_type": eventName,
                        "user_id": email,
                        "user_properties": {
                            "license": license
                        },
                        "product_name": "livechat",
                        "event_properties": {
                            "integration name": "livechat-woocommerce"
                        }
                    }]
                )
            };
            $.ajax({
                url: amplitudeURL,
                type: 'GET',
                crossOrigin: true,
                data: data
            }).always(function () {
                if(callback) callback();
            });
        },

        alreadyHaveAccountForm: function () {
            var licenseForm = $('div#useExistingAccount form#licenseForm');
            licenseForm.submit(function () {

                var licenseEmail = licenseForm.find('input#licenseEmail').val();
                var licenseNumber = licenseForm.find('input#licenseNumber').val();

                WooCommerceLiveChat.setSettings('licenseEmail', licenseEmail);
                WooCommerceLiveChat.setSettings('licenseId', licenseNumber, 1);

                return false;
            });
        },
        setSettings: function(key, value, reload) {
            var data = {action: 'wc-livechat-update-settings'};
            data[key] = value;

            $.ajax({
                url: WcLcUrls.setSettings,
                type: "POST",
                data: data,
                dataType: 'json',
                cache: false,
                async: false,
                success: function (data, status, error) {
                    if (data == 'ok') {
                        if (reload == 1) {
                            location.reload();
                        }
                    }
                },
                error: function (data, status, error) {
                    alert('Something went wrong. Please try again or contact our support team.');
                }
            });

        },
        settingsForm: function() {
            $('#resetAccount').click(function() {
                return confirm('This will reset your LiveChat plugin settings. Do you want to continue?');
            });
            $('.settings .title').click(function() {
                $(this).next('.onoffswitch').children('label').click();
            });
            $('.onoffswitch-checkbox').change(function() {
                var paramName = $(this).attr('id');
                if ($('#' + paramName).is(':checked')) {
                    WooCommerceLiveChat.setSettings('customDataSettings', paramName + ':' + 1);
                } else {
                    WooCommerceLiveChat.setSettings('customDataSettings', paramName + ':' + 0);
                }
            });
        }
    };

    $(document).ready(function ()
    {
        WooCommerceLiveChat.init();
    });
})(jQuery);