import React, { useState, useEffect, useContext } from "react";
import { useImmerReducer } from "use-immer";
// Used as const not import, for Loco translate plugin compatibility.
const __ = wp.i18n.__;

import DispatchContext from "../DispatchContext";
import SettingsFormInput from "./SettingsFormInput";
import AxiosWp from "./AxiosWp";

function Newsletters() {
  const appDispatch = useContext(DispatchContext);
  // Init States
  const originalState = {
    inputs: {
      news_phonebooks: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "news_phonebooksChange",
        id: "news_phonebooks",
        name: "news_phonebooks",
        type: "select",
        label: __("Select phone book for newsletter", "farazsms"),
        rules: "news_phonebooksRules",
      },
      news_send_verify_code: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "news_send_verify_codeChange",
        id: "news_send_verify_code",
        name: "news_send_verify_code",
        type: "checkbox",
        label: __(
          "Confirm subscription by sending verification code?",
          "farazsms"
        ),
        rules: "news_send_verify_codeRules",
      },
      news_send_verify_code_p: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "news_send_verify_code_pChange",
        id: "news_send_verify_code_p",
        name: "news_send_verify_code_p",
        type: "text",
        label: __(
          "Newsletter membership verification pattern code:",
          "farazsms"
        ),
        rules: "news_send_verify_code_pRules",
      },
      news_welcome: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "news_welcomeChange",
        id: "news_welcome",
        name: "news_welcome",
        type: "checkbox",
        label: __("Welcome SMS to subscriber of the newsletter?", "farazsms"),
        rules: "news_welcomeRules",
      },
      news_welcome_p: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "news_welcome_pChange",
        id: "news_welcome_p",
        name: "news_welcome_p",
        type: "text",
        label: __("Welcome SMS pattern code", "farazsms"),
        rules: "news_welcome_pRules",
      },
      news_post_notification: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "news_post_notificationChange",
        id: "news_post_notification",
        name: "news_post_notification",
        type: "checkbox",
        label: __("Send new posts to newsletter members?", "farazsms"),
        rules: "news_post_notificationRules",
      },
      news_post_notification_message: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "news_post_notification_messageChange",
        id: "news_post_notification_message",
        name: "news_post_notification_message",
        type: "text",
        label: __("Message content for new post", "farazsms"),
        rules: "news_post_notification_messageRules",
      },
      news_product_notification: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "news_product_notificationChange",
        id: "news_product_notification",
        name: "news_product_notification",
        type: "checkbox",
        label: __("Send new product to newsletter members?", "farazsms"),
        rules: "news_product_notificationRules",
      },
      news_product_notification_message: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "news_product_notification_messageChange",
        id: "news_product_notification_message",
        name: "news_product_notification_message",
        type: "text",
        label: __("Message content for new product", "farazsms"),
        rules: "news_product_notification_messageRules",
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
        draft.inputs.add_mobile_field.value = action.value.add_mobile_field;

        draft.isFetching = false;
        return;

      case "add_mobile_fieldChange":
        draft.inputs.add_mobile_field.hasErrors = false;
        draft.inputs.add_mobile_field.value = action.value;
        return;

      case "submitOptions":
        if (
          //Check is any input hasErrors, and prevent form submit on that case.
          !draft.inputs.apikey.hasErrors
        ) {
          draft.sendCount++;
        }
        return;
      case "saveRequestStarted":
        draft.isSaving = true;
        return;
      case "saveRequestFininshed":
        draft.isSaving = false;
        return;
      //Input Rules and logic validations, and set errorMessages.
      case "apikeyRules":
        if (!action.value.trim()) {
          draft.inputs.apikey.hasErrors = true;
          draft.inputs.apikey.errorMessage = __(
            "You must provide an API Key.",
            "farazsms"
          );
        }
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
        const getOptions = await AxiosWp.get("/farazsms/v1/comments_options");
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
            "/farazsms/v1/comments_options",
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
      <h3>{__("Login Notify Settings:", "farazsms")}</h3>
      <div>
        <form onSubmit={handleSubmit}>
          {Object.values(state.inputs).map((input) => (
            <div
              key={input.id}
              className={
                input.type === "checkbox" ? "toggle-control" : "form-group"
              }
            >
              <SettingsFormInput
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
              {input.hasErrors && (
                <div className="alert alert-danger small liveValidateMessage">
                  {input.errorMessage}
                </div>
              )}
            </div>
          ))}
          <button
            type="submit"
            className="btn btn-primary faraz-btn"
            disabled={state.isSaving}
          >
            {__("Save Settings", "farazsms")}
          </button>
        </form>
      </div>
    </div>
  );
}

export default Newsletters;
