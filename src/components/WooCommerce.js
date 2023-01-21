/**
 * Import remote dependencies.
 */
import React, { useState, useEffect, useContext } from "react";
import { useImmerReducer } from "use-immer";
// Used as const not import, for Loco translate plugin compatibility.
const __ = wp.i18n.__;

/**
 * Import local dependencies
 */
import DispatchContext from "../DispatchContext";
import FormInput from "./FormInput";
import SaveButton from "./SaveButton";
import FormInputError from "./FormInputError";
import AxiosWp from "./AxiosWp";

function Woocommerce() {
  const appDispatch = useContext(DispatchContext);
  // Init States
  const originalState = {
    inputs: {
      woo_checkout_otp: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "woo_checkout_otpChange",
        id: "woo_checkout_otp",
        name: "woo_checkout_otp",
        type: "checkbox",
        label: __(
          "Mobile number confirmation on the account checkout page?",
          "farazsms"
        ),
        rules: "woo_checkout_otpRules",
      },
      woo_checkout_otp_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "woo_checkout_otp_patternChange",
        id: "woo_checkout_otp_pattern",
        name: "woo_checkout_otp_pattern",
        type: "text",
        label: __("Mobile number verification pattern code:", "farazsms"),
        rules: "woo_checkout_otp_patternRules",
        infoTitle: __("Usable variables:", "farazsms"),
        infoBody: __("The verification code variable is %code%", "farazsms"),
      },
      woo_poll: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "woo_pollChange",
        id: "woo_poll",
        name: "woo_poll",
        type: "checkbox",
        label: __("Sending a timed survey SMS for WooCommerce?", "farazsms"),
        rules: "woo_pollRules",
      },
      woo_poll_time: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "woo_poll_timeChange",
        id: "woo_poll_time",
        name: "woo_poll_time",
        type: "text",
        label: __("Days of sending SMS after placing the order:", "farazsms"),
        rules: "woo_poll_timeRules",
      },
      woo_poll_msg: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "woo_poll_msgChange",
        id: "woo_poll_msg",
        name: "woo_poll_msg",
        type: "textarea",
        label: __("message content:", "farazsms"),
        rules: "woo_poll_msgRules",
        infoTitle: __("Usable variables:", "farazsms"),
        infoBody: __(
          "time %time% | store name %sitename% | product name %item% | product link %item_link%",
          "farazsms"
        ),
      },
      woo_tracking_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "woo_tracking_patternChange",
        id: "woo_tracking_pattern",
        name: "woo_tracking_pattern",
        type: "text",
        label: __("Pattern code to send tracking code:", "farazsms"),
        rules: "woo_tracking_patternRules",
        infoTitle: __("Usable variables:", "farazsms"),
        infoBody: __(
          "tracking code %tracking_code% (required) | order number %order_id% | order status %order_status% | full name in billing address %billing_full_name% | full name in shipping address %shipping_full_name%",
          "farazsms"
        ),
      },
    },
    isFetching: true,
    isSaving: false,
    sendCount: 0,
  };

  function ourReduser(draft, action) {
    switch (action.type) {
      case "fetchComplete":
        //Init state values by action.value
        draft.inputs.woo_checkout_otp.value = action.value.woo_checkout_otp;
        draft.inputs.woo_checkout_otp_pattern.value =
          action.value.woo_checkout_otp_pattern;
        draft.inputs.woo_poll.value = action.value.woo_poll;
        draft.inputs.woo_poll_time.value = action.value.woo_poll_time;
        draft.inputs.woo_poll_msg.value = action.value.woo_poll_msg;
        draft.inputs.woo_tracking_pattern.value =
          action.value.woo_tracking_pattern;

        draft.isFetching = false;
        return;

      case "woo_checkout_otpChange":
        draft.inputs.woo_checkout_otp.hasErrors = false;
        draft.inputs.woo_checkout_otp.value = action.value;
        return;
      case "woo_checkout_otp_patternChange":
        draft.inputs.woo_checkout_otp_pattern.hasErrors = false;
        draft.inputs.woo_checkout_otp_pattern.value = action.value;
        return;
      case "woo_pollChange":
        draft.inputs.woo_poll.hasErrors = false;
        draft.inputs.woo_poll.value = action.value;
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
      case "saveRequestFininshed":
        draft.isSaving = false;
        return;
    }
  }

  const [state, dispatch] = useImmerReducer(ourReduser, originalState);

  function handleSubmit(e) {
    e.preventDefault();
    //Set every input to the state with dispatch function.
    Object.values(state.inputs).map((input) => {
      dispatch({ type: input.rules, value: input.value });
    });

    dispatch({ type: "submitOptions" });
  }

  useEffect(() => {
    async function getOptions() {
      try {
        // Get Options from site DB Options table
        const getOptions = await AxiosWp.get(
          "/farazsms/v1/woocommerce_options"
        );
        if (getOptions.data) {
          const optsionsJson = JSON.parse(getOptions.data);
          console.log(optsionsJson);
          dispatch({ type: "fetchComplete", value: optsionsJson });
        }
      } catch (e) {
        console.log(e);
      }
    }
    getOptions();
  }, []);

  useEffect(() => {
    if (state.sendCount) {
      /**
       * Get options values and set "name: value" in an array.
       * Then Convert array to key: value pair for send Axios.post request to DB.
       * @return Object with arrays.
       */

      const optsionsArray = Object.values(state.inputs).map(
        ({ value, name }) => [name, value]
      );
      const optionsJsonForPost = Object.fromEntries(optsionsArray);
      console.log(optionsJsonForPost);

      dispatch({ type: "saveRequestStarted" });
      async function postOptions() {
        try {
          // Post Options from site DB Options table
          const postOptions = await AxiosWp.post(
            "/farazsms/v1/woocommerce_options",
            optionsJsonForPost
          );
          dispatch({ type: "saveRequestFininshed" });
          appDispatch({
            type: "flashMessage",
            value: __("Congrats. Form was updated successfully.", "farazsms"),
          });
        } catch (e) {
          console.log(e);
        }
      }
      postOptions();
    }
  }, [state.sendCount]);

  /**
   * The settings form created by maping over originalState as the main state.
   * For every value on inputs rendered a SettingsFormInput.
   *
   * @since 2.0.0
   */

  return (
    <div>
      <h3 className="p-3 mb-4 border-bottom border-dark bg-light rounded">
        {__("Woocommerce Settings:", "farazsms")}
      </h3>
      <div>
        <form onSubmit={handleSubmit}>
          {Object.values(state.inputs).map((input) => (
            <div
              key={input.id}
              className={
                input.type === "checkbox" ? "toggle-control" : "form-group"
              }
            >
              <FormInput
                {...input}
                value={input.value}
                checked={input.value}
                onChange={(e) => {
                  dispatch({
                    type: input.onChange,
                    value:
                      input.type === "checkbox"
                        ? e.target.checked
                        : e.target.value,
                  });
                }}
                onBlur={(e) =>
                  dispatch({ type: input.rules, value: e.target.value })
                }
              />
              <FormInputError />
            </div>
          ))}
          <SaveButton />
        </form>
      </div>
    </div>
  );
}

export default Woocommerce;
