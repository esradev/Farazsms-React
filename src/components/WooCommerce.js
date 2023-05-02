/**
 * Import remote dependencies.
 */
import React, { useState, useEffect, useContext } from "react";
import { useImmerReducer } from "use-immer";
import { __ } from "@wordpress/i18n";

/**
 * Import local dependencies
 */
import DispatchContext from "../DispatchContext";
import SettingsForm from "../views/SettingsForm";
import SectionHeader from "../views/SectionHeader";
import SectionError from "../views/SectionError";
import LoadingSpinner from "../views/LoadingSpinner";
import useFetchOptions from "../hooks/useFetchOptions";
import useSaveOptions from "../hooks/useSaveOptions";

function Woocommerce(props) {
  const appDispatch = useContext(DispatchContext);
  // Init States
  const originalState = {
    inputs: {
      woo_checkout_otp: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "woo_checkout_otpChange",
        name: "woo_checkout_otp",
        type: "checkbox",
        label: __(
          "Mobile number verification on the account checkout page?",
          "farazsms"
        ),
      },
      woo_checkout_otp_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "woo_checkout_otp_patternChange",
        name: "woo_checkout_otp_pattern",
        type: "text",
        label: __("Mobile number verification pattern code:", "farazsms"),
        infoTitle: __("Usable variables:", "farazsms"),
        infoBody: __("The verification code variable is %code%", "farazsms"),
        isDependencyUsed: false,
      },
      woo_poll: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "woo_pollChange",
        name: "woo_poll",
        type: "checkbox",
        label: __("Sending a timed survey SMS for WooCommerce?", "farazsms"),
      },
      woo_poll_time: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "woo_poll_timeChange",
        name: "woo_poll_time",
        type: "text",
        label: __("Days of sending SMS after placing the order:", "farazsms"),
        isDependencyUsed: false,
      },
      woo_poll_msg: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "woo_poll_msgChange",
        name: "woo_poll_msg",
        type: "textarea",
        label: __("Message content:", "farazsms"),
        infoTitle: __("Usable variables:", "farazsms"),
        infoBody: __(
          "time %time% | store name %sitename% | reciew page link %review_link%",
          "farazsms"
        ),
        isDependencyUsed: false,
      },
      woo_tracking_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "woo_tracking_patternChange",
        name: "woo_tracking_pattern",
        type: "text",
        label: __("Pattern code to send tracking code:", "farazsms"),
        infoTitle: __("Usable variables:", "farazsms"),
        infoBody: __(
          "tracking code %tracking_code% (required) | order number %order_id% | order status %order_status% | full name in billing address %billing_full_name% | full name in shipping address %shipping_full_name% | post service provider %post_service_provider% | post date %post_date%",
          "farazsms"
        ),
      },
    },
    isFetching: true,
    isSaving: false,
    sendCount: 0,
    sectionName: __("Woocommerce", "farazsms"),
  };

  function ourReduser(draft, action) {
    switch (action.type) {
      case "fetchComplete":
        //Init state values by action.value
        draft.inputs.woo_checkout_otp.value = action.value.woo_checkout_otp;
        if (action.value.woo_checkout_otp === true) {
          draft.inputs.woo_checkout_otp_pattern.isDependencyUsed = true;
        } else {
          draft.inputs.woo_checkout_otp_pattern.isDependencyUsed = false;
        }
        draft.inputs.woo_checkout_otp_pattern.value =
          action.value.woo_checkout_otp_pattern;
        draft.inputs.woo_poll.value = action.value.woo_poll;
        if (action.value.woo_poll === true) {
          draft.inputs.woo_poll_time.isDependencyUsed = true;
          draft.inputs.woo_poll_msg.isDependencyUsed = true;
        } else {
          draft.inputs.woo_poll_time.isDependencyUsed = false;
          draft.inputs.woo_poll_msg.isDependencyUsed = false;
        }
        draft.inputs.woo_poll_time.value = action.value.woo_poll_time;
        draft.inputs.woo_poll_msg.value = action.value.woo_poll_msg;
        draft.inputs.woo_tracking_pattern.value =
          action.value.woo_tracking_pattern;

        draft.isFetching = false;
        return;
      case "cantFetching":
        draft.isFetching = false;
        return;
      case "woo_checkout_otpChange":
        draft.inputs.woo_checkout_otp.hasErrors = false;
        draft.inputs.woo_checkout_otp.value = action.value;
        if (action.value === true) {
          draft.inputs.woo_checkout_otp_pattern.isDependencyUsed = true;
        } else {
          draft.inputs.woo_checkout_otp_pattern.isDependencyUsed = false;
        }
        return;
      case "woo_checkout_otp_patternChange":
        draft.inputs.woo_checkout_otp_pattern.hasErrors = false;
        draft.inputs.woo_checkout_otp_pattern.value = action.value;
        return;
      case "woo_pollChange":
        draft.inputs.woo_poll.hasErrors = false;
        draft.inputs.woo_poll.value = action.value;
        if (action.value === true) {
          draft.inputs.woo_poll_time.isDependencyUsed = true;
          draft.inputs.woo_poll_msg.isDependencyUsed = true;
        } else {
          draft.inputs.woo_poll_time.isDependencyUsed = false;
          draft.inputs.woo_poll_msg.isDependencyUsed = false;
        }
        return;
      case "woo_poll_timeChange":
        draft.inputs.woo_poll_time.hasErrors = false;
        draft.inputs.woo_poll_time.value = action.value;
        return;
      case "woo_poll_msgChange":
        draft.inputs.woo_poll_msg.hasErrors = false;
        draft.inputs.woo_poll_msg.value = action.value;
        return;
      case "woo_tracking_patternChange":
        draft.inputs.woo_tracking_pattern.hasErrors = false;
        draft.inputs.woo_tracking_pattern.value = action.value;
        return;

      case "submitOptions":
        draft.sendCount++;

        return;
      case "saveRequestStarted":
        draft.isSaving = true;
        return;
      case "saveRequestFinished":
        draft.isSaving = false;
        return;
    }
  }

  const [state, dispatch] = useImmerReducer(ourReduser, originalState);

  function handleSubmit(e) {
    e.preventDefault();
    dispatch({ type: "submitOptions" });
  }

  /**
   * Get options from DB rest routes
   *
   * @since 2.0.0
   */
  const endpoint = "/farazsms/v1/woocommerce_options";

  useFetchOptions(endpoint, dispatch);

  /**
   * Post options to DB
   *
   * @since 2.0.0
   */
  useSaveOptions(endpoint, state, dispatch, appDispatch);

  if (state.isFetching) return <LoadingSpinner />;

  /**
   * The settings form created by mapping over originalState as the main state.
   * For every value on inputs rendered a SettingsFormInput.
   *
   * @since 2.0.0
   */

  if (props.integratedPlugins.woocommerce.use) {
    return (
      <div>
        <SectionHeader sectionName={state.sectionName} />
        <div>
          <SettingsForm
            sectionName={state.sectionName}
            inputs={state.inputs}
            handleSubmit={handleSubmit}
            dispatch={dispatch}
            isSaving={state.isSaving}
          />
        </div>
      </div>
    );
  } else {
    return <SectionError sectionName={state.sectionName} />;
  }
}

export default Woocommerce;
