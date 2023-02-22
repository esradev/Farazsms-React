(function ($) {
  "use strict";
  let otp_div = $("#billing_phone_otp_button_field");
  let send_otp_div = $("#billing_phone_send_otp_field");
  otp_div.empty();
  send_otp_div.empty();

  let otp_send_button = "";
  otp_send_button += '<div class="button alt" id="otp_send_button">';
  otp_send_button += '<span class="button__text">ارسال کد تایید</span>';
  otp_send_button += "</div>";
  otp_send_button += '<div style="display: none;" id="response_message">';
  otp_send_button += "</div>";
  send_otp_div.append(otp_send_button);

  let otp_verify_button = "";
  otp_verify_button +=
    '<div id="fsms_woo_second_buttons"><div class="button alt" id="otp_resend">';
  otp_verify_button += '<span class="button__text">ارسال مجدد</span>';
  otp_verify_button += "</div>";
  otp_verify_button +=
    '<div style="display: none;" id="response_message_resend">';
  otp_verify_button += "</div>";
  otp_div.append(otp_verify_button);

  $("#billing_phone").keyup(function () {
    let mobile = $(this).val();
    if (mobile.length < 11) {
      $(this).parents("#billing_phone_field").addClass("woocommerce-invalid");
    }
    if (mobile.length > 11) {
      $(this).val(mobile.substr(0, 11));
    }
  });

  $("#billing_phone_send_otp_field").on(
    "click",
    "#otp_send_button",
    function () {
      let mobile = $("#billing_phone").val();
      $("#response_message").removeClass().hide();
      if (mobile.length < 11 || mobile.length > 11) {
        $("#billing_phone_field").addClass("woocommerce-invalid");
        $("#response_message")
          .text("لطفا شماره موبایل خود را وارد کنید")
          .addClass("error")
          .show();
        return;
      }
      let otp_send_button = $(this);
      otp_send_button.addClass("fsms_button--loading").prop("disabled", true);
      let data = {
        action: "fsms_send_otp_code",
        mobile: mobile,
      };
      $.post(fsms_ajax_url.ajax_url, data, function (response) {
        if (response.success) {
          otp_send_button
            .removeClass("fsms_button--loading")
            .prop("disabled", false);
          otp_send_button.parent().hide();
          $("#billing_phone_otp_field,#billing_phone_otp_button_field").show();
          let seconds = 90;
          let interval;
          let resend_code = $("#otp_resend");
          resend_code.addClass("disabled");
          interval = setInterval(function () {
            resend_code.find("span").html("ارسال مجدد" + " (" + seconds + ")");
            if (seconds == 0) {
              resend_code.find("span").html("ارسال مجدد کد");
              resend_code.removeClass("disabled");
              clearInterval(interval);
            }
            seconds--;
          }, 1000);
        } else {
          otp_send_button.removeClass("fsms_button--loading");
          $("#response_message")
            .text("خطایی رخ داد لطفا با مدیریت تماس بگیرید")
            .addClass("error")
            .show();
        }
      });
    }
  );

  $("#billing_phone_otp").keyup(function () {
    let mobile = $(this).val();
    if (mobile.length < 4) {
      $(this).parents(".fsms_otp_field").addClass("woocommerce-invalid");
    }
    if (mobile.length > 4) {
      $(this).val(mobile.substr(0, 4));
    }
  });

  $("#billing_phone_otp_button_field").on("click", "#otp_resend", function () {
    let otp_resend_button = $(this);
    if (otp_resend_button.hasClass("disabled")) {
      return;
    }
    let mobile = $("#billing_phone").val();
    $("#response_message_resend").removeClass().hide();
    if (mobile.length < 11 || mobile.length > 11) {
      $("#billing_phone_field").addClass("woocommerce-invalid");
      $("#response_message_resend")
        .text("لطفا شماره موبایل خود را وارد کنید")
        .addClass("error")
        .show();
      return;
    }
    otp_resend_button.addClass("fsms_button--loading").prop("disabled", true);
    let data = {
      action: "fsms_send_otp_code",
      mobile: mobile,
    };
    $.post(fsms_ajax_url.ajax_url, data, function (response) {
      if (response.success) {
        otp_resend_button
          .removeClass("fsms_button--loading")
          .prop("disabled", false);
        let seconds = 90;
        let interval;
        otp_resend_button.addClass("disabled");
        interval = setInterval(function () {
          otp_resend_button
            .find("span")
            .html("ارسال مجدد" + " (" + seconds + ")");
          if (seconds == 0) {
            otp_resend_button.find("span").html("ارسال مجدد کد");
            otp_resend_button.removeClass("disabled");
            clearInterval(interval);
          }
          seconds--;
        }, 1000);
      } else {
        otp_resend_button.removeClass("fsms_button--loading");
        $("#response_message")
          .text("خطایی رخ داد لطفا با مدیریت تماس بگیرید")
          .addClass("error")
          .show();
      }
    });
  });
})(jQuery);
