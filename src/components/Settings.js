import React, { useState, useEffect, useContext } from "react";
import { useImmerReducer } from "use-immer";
import Axios from "axios";
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
    },
    isFetching: true,
    isSaving: false,
    sendCount: 0,
  };

  function ourReduser(draft, action) {
    switch (action.type) {
      case "fetchComplete":
        draft.inputs.apikey.value = action.value.apikey;
        draft.inputs.username.value = action.value.username;
        draft.inputs.password.value = action.value.password;
        draft.inputs.admin_number.value = action.value.admin_number;
        draft.inputs.from_number.value = action.value.from_number;
        draft.inputs.from_number_adver.value = action.value.from_number_adver;
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
      case "submitOptions":
        if (
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
      case "apikeyRules":
        if (!action.value.trim()) {
          draft.inputs.apikey.hasErrors = true;
          draft.inputs.apikey.errorMessage = "You must provide an API Key.";
        }
        return;
      case "usernameRules":
        if (!action.value.trim()) {
          draft.inputs.username.hasErrors = true;
          draft.inputs.username.errorMessage = "You must provide a Username.";
        }
        return;
      case "passwordRules":
        if (!action.value.trim()) {
          draft.inputs.password.hasErrors = true;
          draft.inputs.password.errorMessage = "You must provide a Password.";
        }
        return;
      case "admin_numberRules":
        if (!action.value.trim()) {
          draft.inputs.admin_number.hasErrors = true;
          draft.inputs.admin_number.errorMessage =
            "You must provide a admin_number.";
        }
        return;
      case "from_numberRules":
        if (!action.value.trim()) {
          draft.inputs.from_number.hasErrors = true;
          draft.inputs.from_number.errorMessage =
            "You must provide a from_number.";
        }
        return;
    }
  }

  const [state, dispatch] = useImmerReducer(ourReduser, originalState);

  function handleSubmit(e) {
    e.preventDefault();
    dispatch({ type: "apikeyRules", value: state.inputs.apikey.value });
    dispatch({ type: "usernameRules", value: state.inputs.username.value });
    dispatch({ type: "passwordRules", value: state.inputs.password.value });
    dispatch({
      type: "admin_numberRules",
      value: state.inputs.admin_number.value,
    });
    dispatch({
      type: "from_numberRules",
      value: state.inputs.from_number.value,
    });
    dispatch({
      type: "from_number_adverRules",
      value: state.inputs.from_number_adver.value,
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
      dispatch({ type: "saveRequestStarted" });
      async function postOptions() {
        try {
          // Post Options from site DB Options table
          const postOptions = await Axios.post(
            "http://faraz-sms.local/wp-json/farazsms/v1/credentials_options",
            {
              apikey: state.inputs.apikey.value,
              username: state.inputs.username.value,
              password: state.inputs.password.value,
              admin_number: state.inputs.admin_number.value,
              from_number: state.inputs.from_number.value,
              from_number_adver: state.inputs.from_number_adver.value,
            }
          );
          dispatch({ type: "saveRequestFininshed" });
          appDispatch({ type: "flashMessage", value: "Form was updated." });
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
          {Object.values(state.inputs).map((value) => (
            <div key={value.id} className="form-group">
              <SettingsFormInput
                className="form-control form-control-lg form-control-title"
                {...value}
                value={value.value}
                onChange={(e) => {
                  dispatch({
                    type: value.onChange,
                    value: e.target.value,
                  });
                }}
                onBlur={(e) =>
                  dispatch({ type: value.rules, value: e.target.value })
                }
              />
              {value.hasErrors && (
                <div className="alert alert-danger small liveValidateMessage">
                  {value.errorMessage}
                </div>
              )}
            </div>
          ))}
          <button
            type="submit"
            className="btn btn-primary"
            disabled={state.isSaving}
          >
            Save Settings
          </button>
        </form>
      </div>
    </div>
  );
}

export default Settings;
