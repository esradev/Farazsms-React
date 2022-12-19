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
      apikey: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "apikeyChange",
        id: "apikey",
        name: "apikey",
        type: "text",
        placeholder: __("API key", "farazsms"),
        label: __("Your API key:", "farazsms"),
        required: true,
        rules: "apikeyRules",
      },
      username: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "usernameChange",
        id: "username",
        name: "username",
        type: "text",
        placeholder: __("Your Username", "farazsms"),
        label: __("Username:", "farazsms"),
        required: true,
        rules: "usernameRules",
      },
      password: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "passwordChange",
        id: "password",
        name: "password",
        type: "text",
        placeholder: __("Password", "farazsms"),
        label: __("Your Password:", "farazsms"),
        required: true,
        rules: "passwordRules",
      },
      admin_number: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "admin_numberChange",
        id: "admin_number",
        name: "admin_number",
        type: "text",
        placeholder: __("Admin Number", "farazsms"),
        label: __("Admin Number:", "farazsms"),
        required: true,
        rules: "admin_numberRules",
      },
      from_number: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "from_numberChange",
        id: "from_number",
        name: "from_number",
        type: "text",
        placeholder: __("Service sender number", "farazsms"),
        label: __("Service sender number:", "farazsms"),
        required: true,
        rules: "from_numberRules",
      },
      from_number_adver: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "from_number_adverChange",
        id: "from_number_adver",
        name: "from_number_adver",
        type: "text",
        placeholder: __("Advertising sender number", "farazsms"),
        label: __("Advertising sender number:", "farazsms"),
        rules: "from_number_adverRules",
      },
      welcome_sms: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "welcome_smsChange",
        id: "welcome_sms",
        name: "welcome_sms",
        type: "checkbox",
        label: __("Send a welcome SMS to the user?", "farazsms"),
        rules: "welcome_smsRules",
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
        draft.inputs.apikey.value = action.value.apikey;
        draft.inputs.username.value = action.value.username;
        draft.inputs.password.value = action.value.password;
        draft.inputs.admin_number.value = action.value.admin_number;
        draft.inputs.from_number.value = action.value.from_number;
        draft.inputs.from_number_adver.value = action.value.from_number_adver;
        draft.inputs.welcome_sms.value = action.value.welcome_sms;
        draft.isFetching = false;
        return;
      case "apikeyChange":
        draft.inputs.apikey.hasErrors = false;
        draft.inputs.apikey.value = action.value;
        return;
      case "usernameChange":
        draft.inputs.username.hasErrors = false;
        draft.inputs.username.value = action.value;
        return;
      case "passwordChange":
        draft.inputs.password.hasErrors = false;
        draft.inputs.password.value = action.value;
        return;
      case "admin_numberChange":
        draft.inputs.admin_number.hasErrors = false;
        draft.inputs.admin_number.value = action.value;
        return;
      case "from_numberChange":
        draft.inputs.from_number.hasErrors = false;
        draft.inputs.from_number.value = action.value;
        return;
      case "from_number_adverChange":
        draft.inputs.from_number_adver.hasErrors = false;
        draft.inputs.from_number_adver.value = action.value;
        return;
      case "welcome_smsChange":
        draft.inputs.welcome_sms.hasErrors = false;
        draft.inputs.welcome_sms.value = action.value;
        return;
      case "submitOptions":
        if (
          //Check is any input hasErrors, and prevent form submit on that case.
          !draft.inputs.apikey.hasErrors &&
          !draft.inputs.username.hasErrors &&
          !draft.inputs.password.hasErrors &&
          !draft.inputs.admin_number.hasErrors &&
          !draft.inputs.from_number.hasErrors
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
      case "usernameRules":
        if (!action.value.trim()) {
          draft.inputs.username.hasErrors = true;
          draft.inputs.username.errorMessage = __(
            "You must provide a Username.",
            "farazsms"
          );
        }
        return;
      case "passwordRules":
        if (!action.value.trim()) {
          draft.inputs.password.hasErrors = true;
          draft.inputs.password.errorMessage = __(
            "You must provide a Password.",
            "farazsms"
          );
        }
        return;
      case "admin_numberRules":
        if (!action.value.trim()) {
          draft.inputs.admin_number.hasErrors = true;
          draft.inputs.admin_number.errorMessage = __(
            "You must provide your admin number.",
            "farazsms"
          );
        }
        return;
      case "from_numberRules":
        if (!action.value.trim()) {
          draft.inputs.from_number.hasErrors = true;
          draft.inputs.from_number.errorMessage = __(
            "You must provide a service sender number.",
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
        const getOptions = await Axios.get(
          "http://faraz-sms.local/wp-json/farazsms/v1/credentials_options"
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
            "http://faraz-sms.local/wp-json/farazsms/v1/credentials_options",
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
      <h3>{__("Settings:", "farazsms")}</h3>
      <div>
        <form onSubmit={handleSubmit}>
          {Object.values(state.inputs).map((input) => (
            <div key={input.id} className="form-group">
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
