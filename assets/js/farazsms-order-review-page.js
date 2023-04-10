(function ($) {
  "use strict";

  $(".review-submit").click(function () {
    let form = $(this).closest(".review-form");
    let product_id = form.find(".fsms_order_review_product_id");
    let user_name = form.find(".fsms_order_review_user_name");
    let user_email = form.find(".fsms_order_review_user_email");
    let rating = form.find(".fsms_order_review_rating");
    let review = form.find(".fsms_order_review_review");
    let response_message = form.find(".fsms_order_review_message");

    console.log();

    let data = {
      action: "farazsms_submit_order_review",
      product_id: product_id.val(),
      user_name: user_name.val(),
      user_email: user_email.val(),
      rating: rating.val(),
      review: review.val(),
    };

    $(this).prop("disabled", true);

    $.post(fsms_ajax_object.ajax_url, data, function (response) {
      if (response.success) {
        response_message.removeClass("success error");
        response_message.hide();
        response_message.empty();
        response_message.addClass("success");
        response_message.append("نظر شما با موفقیت ثبت شد.");
        response_message.show();
      } else {
        response_message.addClass("error");
        response_message.append("خطایی رخ داده است.");
        response_message.show();
      }
    });
  });
})(jQuery);
