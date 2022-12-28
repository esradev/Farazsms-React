import React, { useState, useEffect, useContext } from "react";
import { useImmerReducer } from "use-immer";
// Used as const not import, for Loco translate plugin compatibility.
const __ = wp.i18n.__;

import DispatchContext from "../DispatchContext";
import SettingsFormInput from "./SettingsFormInput";
import AxiosWp from "./AxiosWp";

function Newsletter() {
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
      news_post_notify: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "news_post_notifyChange",
        id: "news_post_notify",
        name: "news_post_notify",
        type: "checkbox",
        label: __("Send new posts to newsletter members?", "farazsms"),
        rules: "news_post_notifyRules",
      },
      news_post_notify_msg: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "news_post_notify_msgChange",
        id: "news_post_notify_msg",
        name: "news_post_notify_msg",
        type: "text",
        label: __("Message content for new post", "farazsms"),
        rules: "news_post_notify_msgRules",
      },
      news_product_notify: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "news_product_notifyChange",
        id: "news_product_notify",
        name: "news_product_notify",
        type: "checkbox",
        label: __("Send new product to newsletter members?", "farazsms"),
        rules: "news_product_notifyRules",
      },
      news_product_notify_msg: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "news_product_notify_msgChange",
        id: "news_product_notify_msg",
        name: "news_product_notify_msg",
        type: "text",
        label: __("Message content for new product", "farazsms"),
        rules: "news_product_notify_msgRules",
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
        draft.inputs.news_phonebooks.value = action.value.news_phonebooks;
        draft.inputs.news_send_verify_code.value =
          action.value.news_send_verify_code;
        draft.inputs.news_send_verify_code_p.value =
          action.value.news_send_verify_code_p;
        draft.inputs.news_welcome.value = action.value.news_welcome;
        draft.inputs.news_welcome_p.value = action.value.news_welcome_p;
        draft.inputs.news_post_notify.value = action.value.news_post_notify;
        draft.inputs.news_post_notify_msg.value =
          action.value.news_post_notify_msg;
        draft.inputs.news_product_notify.value =
          action.value.news_product_notify;
        draft.inputs.news_product_notify_msg.value =
          action.value.news_product_notify_msg;

        draft.isFetching = false;
        return;

      case "news_phonebooksChange":
        draft.inputs.news_phonebooks.hasErrors = false;
        draft.inputs.news_phonebooks.value = action.value;
        return;
      case "news_send_verify_codeChange":
        draft.inputs.news_send_verify_code.hasErrors = false;
        draft.inputs.news_send_verify_code.value = action.value;
        return;
      case "news_send_verify_code_pChange":
        draft.inputs.news_send_verify_code_p.hasErrors = false;
        draft.inputs.news_send_verify_code_p.value = action.value;
        return;
      case "news_welcomeChange":
        draft.inputs.news_welcome.hasErrors = false;
        draft.inputs.news_welcome.value = action.value;
        return;
      case "news_welcome_pChange":
        draft.inputs.news_welcome_p.hasErrors = false;
        draft.inputs.news_welcome_p.value = action.value;
        return;
      case "news_post_notifyChange":
        draft.inputs.news_post_notify.hasErrors = false;
        draft.inputs.news_post_notify.value = action.value;
        return;
      case "news_post_notify_msgChange":
        draft.inputs.news_post_notify_msg.hasErrors = false;
        draft.inputs.news_post_notify_msg.value = action.value;
        return;
      case "news_product_notifyChange":
        draft.inputs.news_product_notify.hasErrors = false;
        draft.inputs.news_product_notify.value = action.value;
        return;
      case "news_product_notify_msgChange":
        draft.inputs.news_product_notify_msg.hasErrors = false;
        draft.inputs.news_product_notify_msg.value = action.value;
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
        const getOptions = await AxiosWp.get("/farazsms/v1/newsletter_options");
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
            "/farazsms/v1/newsletter_options",
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

export default Newsletter;