
    var checkExist = setInterval(function () {
        if (jQuery(int_phone_input_element).length) {
            // let's wait until input appear
            jQuery(function () {
                setTimeout('validatePhone(int_phone_input_element)', 2500);
                var telInput = jQuery(int_phone_input_element);
                telInput.after('<span class="int-phone-input-valid-msg hide" style="position: absolute;" >✓ Valid</span>');
                telInput.after('<span class="int-phone-input-error-msg hide" style="position: absolute;" >Invalid number</span>');


                // on blur: validate
                telInput.blur(function () {
                    validatePhone(this);
                });
                // on keydown: reset
                telInput.keydown(function () {
                    var errorMsg = jQuery(this).parent().find(".int-phone-input-error-msg"),
                            validMsg = jQuery(this).parent().find(".int-phone-input-valid-msg");
                    jQuery(this).removeClass("error");
                    errorMsg.addClass("hide");
                    validMsg.addClass("hide");
                });

            });


            clearInterval(checkExist);
        }
    }, 100); // check every 100ms        



function validatePhone(phone_el) {
    var telInput = jQuery(phone_el);
    var errorMsg = jQuery(phone_el).parent().find(".int-phone-input-error-msg"),
            validMsg = jQuery(phone_el).parent().find(".int-phone-input-valid-msg");
    if (jQuery.trim(telInput.val())) {
        if (telInput.intlTelInput("isValidNumber")) {
            validMsg.removeClass("hide");
            errorMsg.addClass("hide");
            var nationalPhone = telInput.intlTelInput("getNumber");
            telInput.val(nationalPhone);
        } else {
            telInput.addClass("error");
            errorMsg.removeClass("hide");
            validMsg.addClass("hide");
        }
    }
}