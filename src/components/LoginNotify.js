import React, { useState, useEffect, useContext } from "react";
import { useImmerReducer } from "use-immer";
import Axios from "axios";
// Used as const not import, for Loco translate plugin compatibility.
const __ = wp.i18n.__;

import DispatchContext from "../DispatchContext";
import SettingsFormInput from "./SettingsFormInput";

function Settings() {
  const appDispatch = useContext(DispatchContext);
  // Init States
  const originalState = {
    inputs: {
      welcome_sms: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "welcome_smsChange",
        id: "welcome_sms",
        name: "welcome_sms",
        type: "checkbox",
        label: __("Send a welcome sms to the user?", "farazsms"),
        rules: "welcome_smsRules",
      },
      welcome_sms_use_p: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "welcome_sms_use_pChange",
        id: "welcome_sms_use_p",
        name: "welcome_sms_use_p",
        type: "checkbox",
        label: __("Send welcome sms via pattern?", "farazsms"),
        rules: "welcome_sms_use_pRules",
      },
      welcome_sms_p: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "welcome_sms_pChange",
        id: "welcome_sms_p",
        name: "welcome_sms_p",
        type: "text",
        label: __("Welcome sms pattern code:", "farazsms"),
        rules: "welcome_sms_pRules",
      },
      welcome_sms_message: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "welcome_sms_messageChange",
        id: "welcome_sms_message",
        name: "welcome_sms_message",
        type: "text",
        label: __("welcome message:", "farazsms"),
        rules: "welcome_sms_messageRules",
      },
      admin_login_notify: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "admin_login_notifyChange",
        id: "admin_login_notify",
        name: "admin_login_notify",
        type: "checkbox",
        label: __(
          "Notify admin when a user from selected rule(s) Login to the site?",
          "farazsms"
        ),
        rules: "admin_login_notifyRules",
      },
      select_roles: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "select_rolesChange",
        id: "select_roles",
        name: "select_roles",
        type: "select",
        label: __("Selecte rule(s):", "farazsms"),
        rules: "select_rolesRules",
      },
      admin_login_notify_p: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "admin_login_notify_pChange",
        id: "admin_login_notify_p",
        name: "admin_login_notify_p",
        type: "text",
        label: __("Notify admin pattern code:", "farazsms"),
        rules: "admin_login_notify_pRules",
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
        draft.inputs.welcome_sms.value = action.value.welcome_sms;
        draft.inputs.welcome_sms_use_p.value = action.value.welcome_sms_use_p;
        draft.inputs.welcome_sms_p.value = action.value.welcome_sms_p;
        draft.inputs.welcome_sms_message.value =
          action.value.welcome_sms_message;
        draft.inputs.admin_login_notify.value = action.value.admin_login_notify;
        draft.inputs.admin_login_notify_p.value =
          action.value.admin_login_notify_p;
        draft.inputs.select_roles.value = action.value.select_roles;

        draft.isFetching = false;
        return;

      case "welcome_smsChange":
        draft.inputs.welcome_sms.hasErrors = false;
        draft.inputs.welcome_sms.value = action.value;
        return;
      case "welcome_sms_use_pChange":
        draft.inputs.welcome_sms_use_p.hasErrors = false;
        draft.inputs.welcome_sms_use_p.value = action.value;

        return;
      case "welcome_sms_pChange":
        draft.inputs.welcome_sms_p.hasErrors = false;
        draft.inputs.welcome_sms_p.value = action.value;
        return;
      case "welcome_sms_messageChange":
        draft.inputs.welcome_sms_message.hasErrors = false;
        draft.inputs.welcome_sms_message.value = action.value;
        return;
      case "admin_login_notifyChange":
        draft.inputs.admin_login_notify.hasErrors = false;
        draft.inputs.admin_login_notify.value = action.value;
        return;
      case "admin_login_notify_pChange":
        draft.inputs.admin_login_notify_p.hasErrors = false;
        draft.inputs.admin_login_notify_p.value = action.value;
        return;
      case "select_rolesChange":
        draft.inputs.select_roles.hasErrors = false;
        draft.inputs.select_roles.value = action.value;
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
        const getOptions = await Axios.get(
          "http://faraz-sms.local/wp-json/farazsms/v1/farazsms_login_notify_options"
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
          const postOptions = await Axios.post(
            "http://faraz-sms.local/wp-json/farazsms/v1/farazsms_login_notify_options",
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

export default Settings;
