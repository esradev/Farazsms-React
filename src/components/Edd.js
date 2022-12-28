import React, { useState, useEffect, useContext } from "react";
import { useImmerReducer } from "use-immer";
// Used as const not import, for Loco translate plugin compatibility.
const __ = wp.i18n.__;

import DispatchContext from "../DispatchContext";
import SettingsFormInput from "./SettingsFormInput";
import AxiosWp from "./AxiosWp";

function Edd() {
  const appDispatch = useContext(DispatchContext);
  // Init States
  const originalState = {
    inputs: {
      edd_phonebooks_choice: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "edd_phonebooks_choiceChange",
        id: "edd_phonebooks_choice",
        name: "edd_phonebooks_choice",
        type: "select",
        label: __("Save the phone number in the phonebook?", "farazsms"),
        rules: "edd_phonebooks_choiceRules",
      },
      edd_send_to_user: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "edd_send_to_userChange",
        id: "edd_send_to_user",
        name: "edd_send_to_user",
        type: "checkbox",
        label: __("Send sms to the user?", "farazsms"),
        rules: "edd_send_to_userRules",
      },
      edd_user_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "edd_user_patternChange",
        id: "edd_user_pattern",
        name: "edd_user_pattern",
        type: "text",
        label: __("SMS pattern code for the user:", "farazsms"),
        rules: "edd_user_patternRules",
      },
      edd_send_to_admin: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "edd_send_to_adminChange",
        id: "edd_send_to_admin",
        name: "edd_send_to_admin",
        type: "checkbox",
        label: __("Send sms to the admin?", "farazsms"),
        rules: "edd_send_to_adminRules",
      },
      edd_admin_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "edd_admin_patternChange",
        id: "edd_admin_pattern",
        name: "edd_admin_pattern",
        type: "text",
        label: __("SMS pattern code for the admin:", "farazsms"),
        rules: "edd_admin_patternRules",
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
        draft.inputs.edd_phonebooks_choice.value =
          action.value.edd_phonebooks_choice;
        draft.inputs.edd_send_to_user.value = action.value.edd_send_to_user;
        draft.inputs.edd_user_pattern.value = action.value.edd_user_pattern;
        draft.inputs.edd_send_to_admin.value = action.value.edd_send_to_admin;
        draft.inputs.edd_admin_pattern.value = action.value.edd_admin_pattern;

        draft.isFetching = false;
        return;

      case "edd_phonebooks_choiceChange":
        draft.inputs.edd_phonebooks_choice.hasErrors = false;
        draft.inputs.edd_phonebooks_choice.value = action.value;
        return;
      case "edd_send_to_userChange":
        draft.inputs.edd_send_to_user.hasErrors = false;
        draft.inputs.edd_send_to_user.value = action.value;
        return;
      case "edd_user_patternChange":
        draft.inputs.edd_user_pattern.hasErrors = false;
        draft.inputs.edd_user_pattern.value = action.value;
        return;
      case "edd_send_to_adminChange":
        draft.inputs.edd_send_to_admin.hasErrors = false;
        draft.inputs.edd_send_to_admin.value = action.value;
        return;
      case "edd_admin_patternChange":
        draft.inputs.edd_admin_pattern.hasErrors = false;
        draft.inputs.edd_admin_pattern.value = action.value;
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
        const getOptions = await AxiosWp.get("/farazsms/v1/edd_options");
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
            "/farazsms/v1/edd_options",
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
      <h3>{__("EDD Settings:", "farazsms")}</h3>
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
      <div className="container">
        <div className="container card bg-info mb-3">
          <div className="card-body">
            <h5 className="card-title">
              {__("Usable variables:", "farazsms")}
            </h5>
            <p className="card-text">
              {__(
                "mobile number: %phone% | Email: %email% | Name: %first_name% | Last name: %last_name% | Purchased products: %product% | Total amount (not including discount): %price% | Total discount amount: %discount% | Paid amount (including discount): %total_price% | Direct download link (not encrypted): %link% | Order number: %payment_id%",
                "farazsms"
              )}
            </p>
          </div>
        </div>
      </div>
    </div>
  );
}

export default Edd;
