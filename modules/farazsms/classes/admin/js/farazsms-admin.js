(function ($) {
    'use strict';
    $.updateQueryStringParameter = function (uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        if (uri.match(re)) {
            return uri.replace(re, '$1' + key + "=" + value + '$2');
        }
        else {
            return uri + separator + key + "=" + value;
        }
    }
    $.urlParam = function (name) {
        var results = new RegExp('[\?&]' + name + '=([^&#]*)')
            .exec(window.location.search);

        return (results !== null) ? results[1] || 0 : false;
    }
    var tab_selected = $.urlParam('tab');
    if(tab_selected){
        $("#" + tab_selected).click();
    }

    $( ".tabs .tab .tab-switch" ).on( "click", function() {
        var tab_clicked = $(this).attr('id');
        var new_url = $.updateQueryStringParameter(window.location.href, 'tab', tab_clicked);
        window.history.replaceState(null, null, new_url);
    });

    $("#fsms_sendwm_with_pattern").click(function (){
        if($(this).is(":checked")){
            $("#fsms_welcomep").parent().show()
            $("#fsms_welcome_message").parent().hide()
        }else {
            $("#fsms_welcomep").parent().hide()
            $("#fsms_welcome_message").parent().show()
        }
    })
    if($("#fsms_sendwm_with_pattern").is(":checked")){
        $("#fsms_welcomep").parent().show()
        $("#fsms_welcome_message").parent().hide()
    }else {
        $("#fsms_welcomep").parent().hide()
        $("#fsms_welcome_message").parent().show()
    }

    var save_button = $('#fsms_save_button');
    var response_message = $('#fsms-response-message');

    $('#fsms_credentials').validate({
        rules: {
            fsms_apikey: "required",
            fsms_uname: "required",
            fsms_password: "required",
            fsms_admin_notify_number: "required",
            fsms_fromnum: "required",
            fsms_fromnum_adver: "required",
            fsms_welcome_message: {
                required:function(element) {
                    return ($('#fsms_sendwm').is(":checked")) ? true : false
                }
            },
            fsms_welcomep: {
                required:function(element) {
                    return ($('#fsms_sendwm_with_pattern').is(":checked")) ? true : false
                }
            },
            fsms_admin_login_noti_p: {
                required:function(element) {
                    return ($('#fsms_admin_login_noti').is(":checked")) ? true : false
                }
            },
            fsms_woo_poll_time: {
                required:function(element) {
                    return ($('#fsms_woo_poll').is(":checked")) ? true : false
                },
                number: true
            },
            fsms_woo_poll_message: {
                required:function(element) {
                    return ($('#fsms_woo_poll').is(":checked")) ? true : false
                }
            },
        },
        messages: {
            fsms_apikey: "کلید دسترسی اجباری می باشد",
            fsms_uname: "نام کاربری اجباری می باشد",
            fsms_password: "رمز عبور اجباری می باشد",
            fsms_admin_notify_number: "شماره موبایل مدیر اجباری می باشد",
            fsms_fromnum: "شماره ارسال کننده خط خدماتی اجباری می باشد",
            fsms_fromnum_adver: "شماره ارسال کننده خط تبلیغاتی اجباری می باشد",
            fsms_welcome_message: "متن پیامک اجباری می باشد",
            fsms_welcomep: "کد پترن اجباری می باشد",
            fsms_admin_login_noti_p: "کد پترن اجباری می باشد",
            fsms_woo_poll_time: "زمان باید به روز باشد",
            fsms_woo_poll_message: "متن پیامک اجباری است"
    },
        submitHandler: function(form) {
            var data = {
                'action': 'fsms_save_settings',
                'username': $('#fsms_uname').val(),
                'password': $('#fsms_password').val(),
                'admin_notify_number': $('#fsms_admin_notify_number').val(),
                'fromnum': $('#fsms_fromnum').val(),
                'fromnum_adver': $('#fsms_fromnum_adver').val(),
                'apikey': $('#fsms_apikey').val(),
                'sendwm': $('#fsms_sendwm').is(":checked"),
                'sendwm_with_pattern': $('#fsms_sendwm_with_pattern').is(":checked"),
                'welcome_message': $('#fsms_welcome_message').val(),
                'welcomep': $('#fsms_welcomep').val(),
                'admin_login_noti': $('#fsms_admin_login_noti').is(":checked"),
                'admin_login_noti_roles': $('#fsms_admin_login_noti_roles').val(),
                'admin_login_noti_p': $('#fsms_admin_login_noti_p').val(),
            };
            response_message.hide();
            response_message.empty();
            response_message.removeClass();
            save_button.addClass("fsms_button--loading");
            $.post(ajaxurl, data, function (response) {
                save_button.removeClass("fsms_button--loading");
                if(!response.success){
                    $('<span>'+response.data+'</span>').appendTo(response_message);
                    response_message.addClass("fsms-error-message");
                    response_message.show();
                }else {
                    $('<span>اطلاعات با موفقیت ذخیره شد</span>').appendTo(response_message);
                    response_message.addClass("fsms-success-message");
                    response_message.show();
                    setTimeout(function(){ location.reload();; }, 500);
                }
            });
        }
    });

    var phone_book_save_button = $('#fsms_phone_book_save_button');
    var phone_book_response_message = $('#fsms-phone-book-response-message');

    phone_book_save_button.click(function(){
        var data = {
            'action': 'fsms_save_phone_book_settings',
            'custom_phone_book': $('#custom_phone_book').val(),
            'custom_phone_meta_keys': $('#custom_phone_meta_keys').val(),
            'digits_phone_book': $('#digits_phone_book').val(),
            'woo_phone_book': $('#woo_phone_book').val(),
            'bookly_phone_book': $('#bookly_phone_book').val(),
            'gf_phone_book': $('#gf_phone_book').val(),
            'GF_selected_field': $('#fsc-gravity-forms-fields').val(),
        };
        phone_book_response_message.hide();
        phone_book_response_message.empty();
        phone_book_response_message.removeClass();
        phone_book_save_button.addClass("fsms_button--loading");
        $.post(ajaxurl, data, function (response) {
            phone_book_save_button.removeClass("fsms_button--loading");
            if(response.success){
                $('<span>اطلاعات با موفقیت ذخیره شد</span>').appendTo(phone_book_response_message);
                phone_book_response_message.addClass("fsms-success-message");
                phone_book_response_message.show();
            }
        });
    });

    var send_message_to_phonebooks_button = $('#send_message_to_phonebooks_button');
    var send_message_response_message = $('#fsms-send-message-response-message');
    $('#send_message_to_phonebooks_form').validate({
        ignore: [],
        rules: {
            fsms_to_phonebooks_message: "required",
            fsms_phones_choice: {
                required:function(element) {
                    return ($('#fsms_send_formnum_choice').find(':selected').val() === '2') ? true : false
                },
                minlength: 23
            },
        },
        messages: {
            fsms_to_phonebooks_message: "متن پیام نباید خالی باشد",
            fsms_phones_choice: "برای ارسال پیامک تبلیغاتی وارد کردن حداقل 2 شماره موبایل اجباری می باشد",
        },
        submitHandler: function(form) {
            var data = {
                'action': 'fsms_send_message_to_phonebooks',
                'message': $('#fsms_to_phonebooks_message').val(),
                'phonebooks': $('#fsms_phonebooks_choice').val(),
                'send_to_subscribers': $('#fsms_subscribers').is(":checked"),
                'phones': $('#fsms_phones_choice').val(),
                'send_formnum_choice': $('#fsms_send_formnum_choice').val(),
            };
            send_message_response_message.hide();
            send_message_response_message.empty();
            send_message_response_message.removeClass();
            send_message_to_phonebooks_button.addClass("fsms_button--loading");
            $.post(ajaxurl, data, function (response) {
                send_message_to_phonebooks_button.removeClass("fsms_button--loading");
                if(!response.success){
                    $('<span>'+response.data+'</span>').appendTo(send_message_response_message);
                    send_message_response_message.addClass("fsms-error-message");
                    send_message_response_message.show();
                }else {
                    $('<span>پیامک با موفقیت ارسال شد</span>').appendTo(send_message_response_message);
                    send_message_response_message.addClass("fsms-success-message");
                    send_message_response_message.show();
                }
            });
        }
    });

    var sync_buttons = $('.fsms-sync-buttons button');
    var fsms_sync_response = $('#fsms-sync-response');

    sync_buttons.click(function (){
        var button = $(this);
        var data = {
            'action': 'fsms_sync_operation',
            'sync_operation': button.attr('id'),
        };
        fsms_sync_response.empty().removeClass().hide();
        button.addClass("fsms_button--loading");
        $.post(ajaxurl, data, function (response) {
            button.removeClass("fsms_button--loading");
            if(response.success){
                $('<span>هماهنگ سازی با موفقیت انجام شد</span>').appendTo(fsms_sync_response);
                fsms_sync_response.addClass("fsms-success-message").show();
            }else {
                $('<span>'+ response.data +'</span>').appendTo(fsms_sync_response);
                fsms_sync_response.addClass("fsms-error-message").show();
            }
        });
    });

    var comment_settings_save_button = $('#fsms_comment_settings_save_button');
    var comment_settings_response_message = $('#fsms-comment-settings-response-message');

    comment_settings_save_button.click(function(){
        var data = {
            'action': 'fsms_save_comment_settings',
            'add_mobile_field': $('#fsms_add_mobile_field').is(":checked"),
            'required_mobile_field':$('#fsms_required_mobile_field').is(":checked"),
            'approved_comment_pattern': $('#fsms_approved_commentp').val(),
            'comment_pattern': $('#fsms_commentp').val(),
            'comment_phone_book': $('#comment_phone_book').val(),
            'notify_admin':$('#fsms_notify_admin').is(":checked"),
            'fsms_admin_notify_pcode': $('#fsms_admin_notify_pcode').val(),
        };
        comment_settings_response_message.hide();
        comment_settings_response_message.empty();
        comment_settings_response_message.removeClass();
        comment_settings_save_button.addClass("fsms_button--loading");
        $.post(ajaxurl, data, function (response) {
            comment_settings_save_button.removeClass("fsms_button--loading");
            if(response.success){
                $('<span>اطلاعات با موفقیت ذخیره شد</span>').appendTo(comment_settings_response_message);
                comment_settings_response_message.addClass("fsms-success-message");
                comment_settings_response_message.show();
            }else {
                $('<span>'+response.data+'</span>').appendTo(comment_settings_response_message);
                comment_settings_response_message.addClass("fsms-error-message");
                comment_settings_response_message.show();
            }
        });
    });

    var edd_settings_save_button = $('#fsms_edd_settings_save_button');
    var edd_settings_response_message = $('#fsms-edd-settings-response-message');

    edd_settings_save_button.click(function() {
        var data = {
            'action': 'fsms_save_edd_settings',
            'edd_phonebooks_choice': $('#fsms_edd_phonebooks_choice').val(),
            'edd_send_to_user': $('#fsms_edd_send_to_user').is(":checked"),
            'edd_user_pcode': $('#fsms_edd_user_pcode').val(),
            'edd_send_to_admin':$('#fsms_edd_send_to_admin').is(":checked"),
            'edd_admin_pcode': $('#fsms_edd_admin_pcode').val(),
        };
        edd_settings_response_message.hide();
        edd_settings_response_message.empty();
        edd_settings_response_message.removeClass();
        edd_settings_save_button.addClass("fsms_button--loading");
        $.post(ajaxurl, data, function (response) {
            edd_settings_save_button.removeClass("fsms_button--loading");
            if(response.success){
                $('<span>اطلاعات با موفقیت ذخیره شد</span>').appendTo(edd_settings_response_message);
                edd_settings_response_message.addClass("fsms-success-message");
                edd_settings_response_message.show();
            }else {
                $('<span>'+response.data+'</span>').appendTo(edd_settings_response_message);
                edd_settings_response_message.addClass("fsms-error-message");
                edd_settings_response_message.show();
            }
        });
    });

    var woo_settings_save_button = $('#fsms_woo_settings_save_button');
    var woo_settings_response_message = $('#fsms-woo-settings-response-message');
    $('#woo_setting_form').validate({
        rules: {
            fsms_woo_checkout_otp_pattern: {
                required:function(element) {
                    return ($('#fsms_woo_checkout_otp').is(":checked")) ? true : false
                },
            },
            fsms_woo_poll_time: {
                required:function(element) {
                    return ($('#fsms_woo_poll').is(":checked")) ? true : false
                },
                number: true
            },
            fsms_woo_poll_message: {
                required:function(element) {
                    return ($('#fsms_woo_poll').is(":checked")) ? true : false
                }
            },
        },
        messages: {
            fsms_woo_checkout_otp_pattern: "لطفا کد پترن را وارد کنید",
            fsms_woo_poll_time: "زمان باید به روز باشد",
            fsms_woo_poll_message: "متن پیامک اجباری است"
        },
        submitHandler: function(form) {
            var data = {
                'action': 'fsms_save_woo_settings',
                'woo_checkout_otp': $('#fsms_woo_checkout_otp').is(":checked"),
                'woo_checkout_otp_pattern': $('#fsms_woo_checkout_otp_pattern').val(),
                'woo_poll': $('#fsms_woo_poll').is(":checked"),
                'woo_poll_time': $('#fsms_woo_poll_time').val(),
                'woo_poll_message': $('#fsms_woo_poll_message').val(),
                'woo_tracking_code_pattern': $('#fsms_woo_tracking_code_pattern').val(),
                'woo_retention_order_no': $('#fsms_woo_retention_order_no').val(),
                'woo_retention_order_month': $('#fsms_woo_retention_order_month').val(),
                'woo_retention_message': $('#fsms_woo_retention_message').val(),
            };
            woo_settings_response_message.hide();
            woo_settings_response_message.empty();
            woo_settings_response_message.removeClass();
            woo_settings_save_button.addClass("fsms_button--loading");
            $.post(ajaxurl, data, function (response) {
                woo_settings_save_button.removeClass("fsms_button--loading");
                if(!response.success){
                    $('<span>'+response.data+'</span>').appendTo(woo_settings_response_message);
                    woo_settings_response_message.addClass("fsms-error-message");
                    woo_settings_response_message.show();
                }else {
                    $('<span>اطلاعات با موفقیت ذخیره شد</span>').appendTo(woo_settings_response_message);
                    woo_settings_response_message.addClass("fsms-success-message");
                    woo_settings_response_message.show();
                }
            });
        }
    });

    $('.fsms_form_element select,#comment_phone_book,#fsms_phonebooks_choice,#fsms_send_formnum_choice,#fsms_edd_phonebooks_choice,#fsms_newsletter_phonebooks,#fsms_admin_login_noti_roles').select2({
        theme: "classic",
        "language": {
            "noResults": function(){
                return "نتیجه ای پیدا نشد!";
            }
        }
    });

    $("#custom_phone_meta_keys").select2({
        tags: true
    });

    var fscgf = $('#fsc-gravity-forms');
    fscgf.on('change', function() {
        var form_id = this.value;
        $("#fsc-gravity-forms-fields > option").each(function () {
            if (this.value.substring(0, form_id.length) === form_id) {
                //$(this).removeAttr("disabled");
                $(this).show();
            }else {
                //$(this).attr("disabled", "disabled");
                $(this).hide();
            }
        });
    });

    var newsletter_settings_save_button = $('#fsms_newsletter_settings_save_button');
    var newsletter_settings_response_message = $('#fsms-newsletter-settings-response-message');

    newsletter_settings_save_button.click(function() {
        var data = {
            'action': 'fsms_save_newsletter_settings',
            'newsletter_phonebooks': $('#fsms_newsletter_phonebooks').val(),
            'newsletter_send_ver_code': $('#fsms_newsletter_send_ver_code').is(":checked"),
            'newsletter_pcode': $('#fsms_newsletter_pcode').val(),
            'newsletter_welcome': $('#fsms_newsletter_welcome').is(":checked"),
            'newsletter_welcomep': $('#fsms_newsletter_welcomep').val(),
            'newsletter_new_post_notification': $('#fsms_newsletter_new_post_notification').is(":checked"),
            'newsletter_post_notification_message': $('#fsms_newsletter_post_notification_message').val(),
            'newsletter_new_product_notification': $('#fsms_newsletter_new_product_notification').is(":checked"),
            'newsletter_product_notification_message': $('#fsms_newsletter_product_notification_message').val(),
        };
        newsletter_settings_response_message.hide();
        newsletter_settings_response_message.empty();
        newsletter_settings_response_message.removeClass();
        newsletter_settings_save_button.addClass("fsms_button--loading");
        $.post(ajaxurl, data, function (response) {
            newsletter_settings_save_button.removeClass("fsms_button--loading");
            if(response.success){
                $('<span>اطلاعات با موفقیت ذخیره شد</span>').appendTo(newsletter_settings_response_message);
                newsletter_settings_response_message.addClass("fsms-success-message");
                newsletter_settings_response_message.show();
            }else {
                $('<span>'+response.data+'</span>').appendTo(newsletter_settings_response_message);
                newsletter_settings_response_message.addClass("fsms-error-message");
                newsletter_settings_response_message.show();
            }
        });
    });

    var delete_subscriber = $(".delete_subscriber");
    delete_subscriber.click(function (){
        var but = $(this);
        if (confirm('آیا مطمئن هستید که میخواهید این کاربر را از خبرنامه حذف کنید؟')) {
            var data = {
                'action': 'fsms_delete_user_from_subscribers',
                'subscriber_id': $(this).val(),
            };
            newsletter_settings_response_message.hide();
            newsletter_settings_response_message.empty();
            newsletter_settings_response_message.removeClass();
            but.addClass("fsms_button--loading");
            $.post(ajaxurl, data, function (response) {
                but.removeClass("fsms_button--loading");
                if(response.success){
                    $('<span>کاربر با موفقیت از خبرنامه حذف شد</span>').appendTo(newsletter_settings_response_message);
                    newsletter_settings_response_message.addClass("fsms-success-message");
                    newsletter_settings_response_message.show();
                    setTimeout(function(){ location.reload(); }, 500);
                }
            });
        } else {return;}
    });

    var send_message_to_subscribers = $('#send_message_to_subscribers_button');
    var send_message_to_subscribers_response = $('#send_message_to_subscribers_response');
    $('#send_message_to_subscribers').validate({
        ignore: [],
        rules: {
            fsms_to_subscribers_message: "required",
        },
        messages: {
            fsms_to_subscribers_message: "متن پیام نباید خالی باشد",
        },
        submitHandler: function(form) {
            var data = {
                'action': 'fsms_send_message_to_subscribers',
                'message': $('#fsms_to_subscribers_message').val(),
            };
            send_message_to_subscribers_response.hide();
            send_message_to_subscribers_response.empty();
            send_message_to_subscribers_response.removeClass();
            send_message_to_subscribers.addClass("fsms_button--loading");
            $.post(ajaxurl, data, function (response) {
                send_message_to_subscribers.removeClass("fsms_button--loading");
                if(!response.success){
                    $('<span>'+response.data+'</span>').appendTo(send_message_to_subscribers_response);
                    send_message_to_subscribers_response.addClass("fsms-error-message");
                    send_message_to_subscribers_response.show();
                }else {
                    $('<span>پیامک با موفقیت ارسال شد</span>').appendTo(send_message_to_subscribers_response);
                    send_message_to_subscribers_response.addClass("fsms-success-message");
                    send_message_to_subscribers_response.show();
                }
            });
        }
    });

    var others_settings_save_button = $('#fsms_others_settings_save_button');
    var others_settings_response_message = $('#fsms-others-settings-response-message');

    $('#fsms_others_settings_save_form').validate({
        rules: {
            fsms_ihc_first_noti_sms_message: {
                required:function(element) {
                    return ($('#fsms_ihc_send_first_noti_sms,#fsms_ihc_send_second_noti_sms,#fsms_ihc_send_third_noti_sms').is(":checked")) ? true : false
                }
            },
            fsms_pmp_expire_noti_sms_message: {
                required:function(element) {
                    return ($('#fsms_pmp_send_expire_noti_sms').is(":checked")) ? true : false
                }
            },
            fsms_aff_user_register_message: {
                required:function(element) {
                    return ($('#fsms_aff_user_register').is(":checked")) ? true : false
                }
            },
            fsms_aff_user_new_ref_message: {
                required:function(element) {
                    return ($('#fsms_aff_user_new_ref').is(":checked")) ? true : false
                }
            },
            fsms_aff_user_on_approval_message: {
                required:function(element) {
                    return ($('#fsms_aff_user_on_approval').is(":checked")) ? true : false
                }
            },
            fsms_aff_admin_user_register_message: {
                required:function(element) {
                    return ($('#fsms_aff_admin_user_register').is(":checked")) ? true : false
                }
            },
            fsms_aff_admin_user_new_ref_message: {
                required:function(element) {
                    return ($('#fsms_aff_admin_user_new_ref').is(":checked")) ? true : false
                }
            },
            fsms_aff_admin_user_on_approval_message: {
                required:function(element) {
                    return ($('#fsms_aff_admin_user_on_approval').is(":checked")) ? true : false
                }
            },
        },
        messages: {
            fsms_ihc_first_noti_sms_message: "متن پیام نمی تواند خالی باشد",
            fsms_pmp_expire_noti_sms_message: "متن پیام نمی تواند خالی باشد",
            fsms_aff_user_register_message: "کد پترن نمی تواند خالی باشد",
            fsms_aff_user_new_ref_message: "کد پترن نمی تواند خالی باشد",
            fsms_aff_user_on_approval_message: "کد پترن نمی تواند خالی باشد",
            fsms_aff_admin_user_register_message: "کد پترن نمی تواند خالی باشد",
            fsms_aff_admin_user_new_ref_message: "کد پترن نمی تواند خالی باشد",
            fsms_aff_admin_user_on_approval_message: "کد پترن نمی تواند خالی باشد",
        },
        submitHandler: function(form) {
            var data = {
                'action': 'fsms_save_other_settings',
                'ihc_send_first_noti_sms': $('#fsms_ihc_send_first_noti_sms').is(":checked"),
                'ihc_send_second_noti_sms': $('#fsms_ihc_send_second_noti_sms').is(":checked"),
                'ihc_send_third_noti_sms': $('#fsms_ihc_send_third_noti_sms').is(":checked"),
                'ihc_first_noti_sms_message': $('#fsms_ihc_first_noti_sms_message').val(),
                'pmp_send_expire_noti_sms': $('#fsms_pmp_send_expire_noti_sms').is(":checked"),
                'pmp_expire_noti_sms_message': $('#fsms_pmp_expire_noti_sms_message').val(),
                'aff_user_mobile_field': $('#fsms_aff_user_mobile_field').val(),
                'aff_user_register': $('#fsms_aff_user_register').is(":checked"),
                'aff_user_register_message': $('#fsms_aff_user_register_message').val(),
                'aff_user_new_ref': $('#fsms_aff_user_new_ref').is(":checked"),
                'aff_user_new_ref_message': $('#fsms_aff_user_new_ref_message').val(),
                'aff_user_on_approval': $('#fsms_aff_user_on_approval').is(":checked"),
                'aff_user_on_approval_message': $('#fsms_aff_user_on_approval_message').val(),
                'aff_admin_user_register': $('#fsms_aff_admin_user_register').is(":checked"),
                'aff_admin_user_register_message': $('#fsms_aff_admin_user_register_message').val(),
                'aff_admin_user_new_ref': $('#fsms_aff_admin_user_new_ref').is(":checked"),
                'aff_admin_user_new_ref_message': $('#fsms_aff_admin_user_new_ref_message').val(),
                'aff_admin_user_on_approval': $('#fsms_aff_admin_user_on_approval').is(":checked"),
                'aff_admin_user_on_approval_message': $('#fsms_aff_admin_user_on_approval_message').val(),
            };
            others_settings_response_message.removeClass().empty().hide();
            others_settings_save_button.addClass("fsms_button--loading");
            $.post(ajaxurl, data, function (response) {
                others_settings_save_button.removeClass("fsms_button--loading");
                if(!response.success){
                    $('<span>'+response.data+'</span>').appendTo(others_settings_response_message);
                    others_settings_response_message.addClass("fsms-error-message").show();
                }else {
                    $('<span>اطلاعات با موفقیت ذخیره شد</span>').appendTo(others_settings_response_message);
                    others_settings_response_message.addClass("fsms-success-message").show();
                }
            });
        }
    });


    var send_feedback_button = $('#fsms_send_feedback');
    var feedback_response_message = $('#fsms-feedback-response-message');
    $('#fsms_send_feedback_form').validate({
        rules: {
            fsms_feedback_message: "required"
        },
        messages: {
            fsms_feedback_message: "متن پیام اجباری است"
        },
        submitHandler: function(form) {
            var data = {
                'action': 'fsms_send_feedback',
                'feedback_message': $('#fsms_feedback_message').val(),
            };
            feedback_response_message.removeClass().empty().hide();
            send_feedback_button.addClass("fsms_button--loading");
            $.post(ajaxurl, data, function (response) {
                send_feedback_button.removeClass("fsms_button--loading");
                if(!response.success){
                    $('<span>'+response.data+'</span>').appendTo(feedback_response_message);
                    feedback_response_message.addClass("fsms-error-message").show();
                }else {
                    $('<span>پیام با موفقیت ارسال شد</span>').appendTo(feedback_response_message);
                    feedback_response_message.addClass("fsms-success-message").show();
                }
            });
        }
    });
})(jQuery);
