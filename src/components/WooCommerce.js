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
import FormInput from "../views/FormInput";
import SaveButton from "../views/SaveButton";
import FormInputError from "../views/FormInputError";
import AxiosWp from "../function/AxiosWp";
import SectionHeader from "../views/SectionHeader";
import SectionError from "../views/SectionError";

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
        rules: "woo_checkout_otpRules",
      },
      woo_checkout_otp_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "woo_checkout_otp_patternChange",
        name: "woo_checkout_otp_pattern",
        type: "text",
        label: __("Mobile number verification pattern code:", "farazsms"),
        rules: "woo_checkout_otp_patternRules",
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
        rules: "woo_pollRules",
      },
      woo_poll_time: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "woo_poll_timeChange",
        name: "woo_poll_time",
        type: "text",
        label: __("Days of sending SMS after placing the order:", "farazsms"),
        rules: "woo_poll_timeRules",
        isDependencyUsed: false,
      },
      woo_poll_msg: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "woo_poll_msgChange",
        name: "woo_poll_msg",
        type: "textarea",
        label: __("message content:", "farazsms"),
        rules: "woo_poll_msgRules",
        infoTitle: __("Usable variables:", "farazsms"),
        infoBody: __(
          "time %time% | store name %sitename% | product name %item% | product link %item_link%",
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
          const optionsJson = JSON.parse(getOptions.data);
          dispatch({ type: "fetchComplete", value: optionsJson });
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
       * Then Convert array to key: value pair for send Axios post request to DB.
       * @return Object with arrays.
       */

      const optionsArray = Object.values(state.inputs).map(
        ({ value, name }) => [name, value]
      );
      const optionsJsonForPost = Object.fromEntries(optionsArray);
      console.log(optionsJsonForPost);

      dispatch({ type: "saveRequestStarted" });
      async function postOptions() {
        try {
          // Post Options from site DB Options table
          const postOptions = await AxiosWp.post(
            "/farazsms/v1/woocommerce_options",
            optionsJsonForPost
          );
          dispatch({ type: "saveRequestFinished" });
          appDispatch({
            type: "flashMessage",
            value: {
              message: __(
                "Congrats. Form was updated successfully.",
                "farazsms"
              ),
            },
          });
        } catch (e) {
          console.log(e);
        }
      }
      postOptions();
    }
  }, [state.sendCount]);

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
          <form onSubmit={handleSubmit}>
            {Object.values(state.inputs).map((input) =>
              input.isDependencyUsed === false ? (
                <></>
              ) : (
                <div
                  key={input.name}
                  className={
                    input.type === "checkbox" ? "toggle-control" : "form-group"
                  }
                >
                  <FormInput
                    isMulti={input.isMulti}
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
              )
            )}
            <SaveButton isSaving={state.isSaving} />
          </form>
        </div>
      </div>
    );
  } else {
    return <SectionError sectionName={state.sectionName} />;
  }
}

export default Woocommerce;
