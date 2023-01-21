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

function Settings() {
  const appDispatch = useContext(DispatchContext);
  /**
   *
   * First init state.
   *
   */
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
      welcome_sms_use_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "welcome_sms_use_patternChange",
        id: "welcome_sms_use_pattern",
        name: "welcome_sms_use_pattern",
        type: "checkbox",
        label: __("Send welcome sms via pattern?", "farazsms"),
        rules: "welcome_sms_use_patternRules",
      },
      welcome_sms_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "welcome_sms_patternChange",
        id: "welcome_sms_pattern",
        name: "welcome_sms_pattern",
        type: "text",
        label: __("Welcome sms pattern code:", "farazsms"),
        rules: "welcome_sms_patternRules",
      },
      welcome_sms_msg: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "welcome_sms_msgChange",
        id: "welcome_sms_msg",
        name: "welcome_sms_msg",
        type: "textarea",
        label: __("welcome message:", "farazsms"),
        rules: "welcome_sms_msgRules",
        infoTitle: __("Usable variables:", "farazsms"),
        infoBody: __("%display_name% and %username%", "farazsms"),
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
        options: [],
        noOptionsMessage: __("No options is avilable", "farazsms"),
      },
      admin_login_notify_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "admin_login_notify_patternChange",
        id: "admin_login_notify_pattern",
        name: "admin_login_notify_pattern",
        type: "text",
        label: __("Notify admin pattern code:", "farazsms"),
        rules: "admin_login_notify_patternRules",
        infoTitle: __("Usable variables:", "farazsms"),
        infoBody: __(
          "username %user_login% and user name %display_name% and login date %date%",
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
        draft.inputs.welcome_sms.value = action.value.welcome_sms;
        draft.inputs.welcome_sms_use_pattern.value =
          action.value.welcome_sms_use_pattern;
        draft.inputs.welcome_sms_pattern.value =
          action.value.welcome_sms_pattern;
        draft.inputs.welcome_sms_msg.value = action.value.welcome_sms_msg;
        draft.inputs.admin_login_notify.value = action.value.admin_login_notify;
        draft.inputs.admin_login_notify_pattern.value =
          action.value.admin_login_notify_pattern;
        draft.inputs.select_roles.value = action.value.select_roles;
        draft.isFetching = false;
        return;

      case "welcome_smsChange":
        draft.inputs.welcome_sms.hasErrors = false;
        draft.inputs.welcome_sms.value = action.value;
        return;
      case "welcome_sms_use_patternChange":
        draft.inputs.welcome_sms_use_pattern.hasErrors = false;
        draft.inputs.welcome_sms_use_pattern.value = action.value;

        return;
      case "welcome_sms_patternChange":
        draft.inputs.welcome_sms_pattern.hasErrors = false;
        draft.inputs.welcome_sms_pattern.value = action.value;
        return;
      case "welcome_sms_msgChange":
        draft.inputs.welcome_sms_msg.hasErrors = false;
        draft.inputs.welcome_sms_msg.value = action.value;
        return;
      case "admin_login_notifyChange":
        draft.inputs.admin_login_notify.hasErrors = false;
        draft.inputs.admin_login_notify.value = action.value;
        return;
      case "admin_login_notify_patternChange":
        draft.inputs.admin_login_notify_pattern.hasErrors = false;
        draft.inputs.admin_login_notify_pattern.value = action.value;
        return;
      case "select_rolesChange":
        draft.inputs.select_roles.hasErrors = false;
        draft.inputs.select_roles.value = action.value;
        return;
      case "select_rolesOptions":
        draft.inputs.select_roles.options = action.value;
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
          "/farazsms/v1/login_notify_options"
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
            "/farazsms/v1/login_notify_options",
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
   * Get user roules keys from DB
   *
   * @since 2.0.0
   */
  useEffect(() => {
    /**
     * Get the global $wp_roles; and make it work for Select react
     *
     * @since 2.0.0
     */
    const roulesObject = farazsmsJsObject.wproules.role_names;
    const roulesArrayObject = Object.keys(roulesObject).map((key) => ({
      value: key,
      label: roulesObject[key],
    }));
    console.log(roulesArrayObject);
    dispatch({
      type: "select_rolesOptions",
      value: roulesArrayObject,
    });
  }, []);

  /**
   * The settings form created by maping over originalState as the main state.
   * For every value on inputs rendered a SettingsFormInput.
   *
   * @since 2.0.0
   */

  return (
    <div>
      <h3 className="p-3 mb-4 border-bottom border-dark bg-light rounded">
        {__("Login Notify Settings:", "farazsms")}
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
                onChange={
                  input.type === "select"
                    ? (selectedOption) =>
                        dispatch({
                          type: input.onChange,
                          value: selectedOption,
                        })
                    : (e) => {
                        dispatch({
                          type: input.onChange,
                          value:
                            input.type === "checkbox"
                              ? e.target.checked
                              : e.target.value,
                        });
                      }
                }
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

export default Settings;
