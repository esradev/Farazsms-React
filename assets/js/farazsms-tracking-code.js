(function ($) {
  "use strict";
  let send_tracking_code_button = $("#send_tracking_code_button");
  let fsms_tracking_code = $("#fsms_tracking_code");
  let fsms_post_service_provider = $("#fsms_post_service_provider");
  let fsms_post_service_custom = $("#fsms_custom_provider");
  let fsms_post_date = $("#fsms_post_date");
  let send_tracking_code_response = $("#send_tracking_code_response");
  let fsms_tracking_code_order_id = $("#fsms-tracking-code-order_id");

  send_tracking_code_button.click(function (event) {
    let post_service_provider_value;
    send_tracking_code_response.removeClass().empty().hide();
    let tracking_code_value = fsms_tracking_code.val();
    if (fsms_post_service_provider.val() === "custom_provider") {
      post_service_provider_value = fsms_post_service_custom.val();
    } else {
      post_service_provider_value = fsms_post_service_provider
        .find(":selected")
        .text();
    }
    let post_date_value = fsms_post_date.val();
    let data = {
      action: "fsms_send_tracking_code_sms",
      tracking_code: tracking_code_value,
      post_service_provider: post_service_provider_value,
      post_date: post_date_value,
      order_id: fsms_tracking_code_order_id.val(),
    };
    send_tracking_code_response.removeClass().empty().hide();
    send_tracking_code_button.addClass("fsms_button--loading");
    $.post(ajaxurl, data, function (response) {
      send_tracking_code_button.removeClass("fsms_button--loading");
      if (!response.success) {
        $("<span>" + response.data + "</span>").appendTo(
          send_tracking_code_response
        );
        send_tracking_code_response.addClass("fsms-error-message").show();
      } else {
        $("<span>کد رهگیری با موفقیت ارسال شد</span>").appendTo(
          send_tracking_code_response
        );
        send_tracking_code_response.addClass("fsms-success-message").show();
      }
    });
  });
})(jQuery);
