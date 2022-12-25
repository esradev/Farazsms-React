(function( $ ) {
    'use strict';
    var send_tracking_code_button = $("#send_tracking_code_button");
    var fsms_racking_code = $("#fsms_racking_code");
    var send_tracking_code_response = $("#send_tracking_code_response");
    var fsms_tracking_code_order_id = $("#fsms-tracking-code-order_id");

    send_tracking_code_button.click(function (event){
        send_tracking_code_response.removeClass().empty().hide();
        var tacking_code_value = fsms_racking_code.val().trim();
        // if(tacking_code_value.length < 20 || tacking_code_value.length > 24){
        //     send_tracking_code_response.addClass("fsms-error-message").text("کد رهگیری بین 20 تا 24 کاراکتر است").show();
        //     return;
        // }
        var data = {
            'action': 'fsms_send_tracking_code_sms',
            'tacking_code': tacking_code_value,
            'order_id': fsms_tracking_code_order_id.val(),
        };
        send_tracking_code_response.removeClass().empty().hide();
        send_tracking_code_button.addClass("fsms_button--loading");
        $.post(ajaxurl, data, function (response) {
            send_tracking_code_button.removeClass("fsms_button--loading");
            if(!response.success){
                $('<span>'+response.data+'</span>').appendTo(send_tracking_code_response);
                send_tracking_code_response.addClass("fsms-error-message").show();
            }else {
                $('<span>کد رهگیری با موفقیت ارسال شد</span>').appendTo(send_tracking_code_response);
                send_tracking_code_response.addClass("fsms-success-message").show();
            }
        });
    });
})( jQuery );
