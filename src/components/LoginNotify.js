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
import LoadingSpinner from "../views/LoadingSpinner";
import useFetchOptions from "../hooks/useFetchOptions";
import useSaveOptions from "../hooks/useSaveOptions";

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
        name: "welcome_sms",
        type: "checkbox",
        label: __("Send a welcome sms to the user?", "farazsms"),
      },
      welcome_sms_use_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "welcome_sms_use_patternChange",
        name: "welcome_sms_use_pattern",
        type: "checkbox",
        label: __("Send welcome sms via pattern?", "farazsms"),
        isDependencyUsed: false,
      },
      welcome_sms_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "welcome_sms_patternChange",
        name: "welcome_sms_pattern",
        type: "text",
        label: __("Welcome sms pattern code:", "farazsms"),
        infoTitle: __("Usable variables:", "farazsms"),
        infoBody: __("%display_name% and %username%", "farazsms"),
        isDependencyUsed: false,
      },
      welcome_sms_msg: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "welcome_sms_msgChange",
        name: "welcome_sms_msg",
        type: "textarea",
        label: __("Welcome message:", "farazsms"),
        infoTitle: __("Usable variables:", "farazsms"),
        infoBody: __("%display_name% and %username%", "farazsms"),
        isDependencyUsed: false,
      },
      admin_login_notify: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "admin_login_notifyChange",
        name: "admin_login_notify",
        type: "checkbox",
        label: __(
          "Notify admin when a user from selected rule(s) Login to the site?",
          "farazsms"
        ),
      },
      select_roles: {
        value: [],
        hasErrors: false,
        errorMessage: "",
        onChange: "select_rolesChange",
        name: "select_roles",
        type: "select",
        label: __("Select rule(s):", "farazsms"),
        options: [],
        noOptionsMessage: __("No options is available", "farazsms"),
        isDependencyUsed: false,
        isMulti: "isMulti",
      },
      admin_login_notify_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "admin_login_notify_patternChange",
        name: "admin_login_notify_pattern",
        type: "text",
        label: __("Notify admin pattern code:", "farazsms"),
        infoTitle: __("Usable variables:", "farazsms"),
        infoBody: __(
          "username %user_login% and user name %display_name% and login date %date%",
          "farazsms"
        ),
        isDependencyUsed: false,
      },
    },
    isFetching: true,
    isSaving: false,
    sendCount: 0,
    sectionName: __("Login Notify", "farazsms"),
  };

  function ourReduser(draft, action) {
    switch (action.type) {
      case "fetchComplete":
        //Init state values by action.value
        draft.inputs.welcome_sms.value = action.value.welcome_sms;
        if (action.value.welcome_sms === true) {
          draft.inputs.welcome_sms_use_pattern.isDependencyUsed = true;
        } else {
          draft.inputs.welcome_sms_use_pattern.isDependencyUsed = false;
        }
        draft.inputs.welcome_sms_use_pattern.value =
          action.value.welcome_sms_use_pattern;
        if (action.value.welcome_sms_use_pattern === true) {
          draft.inputs.welcome_sms_pattern.isDependencyUsed = true;
          draft.inputs.welcome_sms_msg.isDependencyUsed = false;
        } else {
          draft.inputs.welcome_sms_pattern.isDependencyUsed = false;
          draft.inputs.welcome_sms_msg.isDependencyUsed = true;
        }
        draft.inputs.welcome_sms_pattern.value =
          action.value.welcome_sms_pattern;
        draft.inputs.welcome_sms_msg.value = action.value.welcome_sms_msg;
        draft.inputs.admin_login_notify.value = action.value.admin_login_notify;
        if (action.value.admin_login_notify === true) {
          draft.inputs.admin_login_notify_pattern.isDependencyUsed = true;
          draft.inputs.select_roles.isDependencyUsed = true;
        }
        draft.inputs.admin_login_notify_pattern.value =
          action.value.admin_login_notify_pattern;
        draft.inputs.select_roles.value = action.value.select_roles;
        draft.isFetching = false;
        return;
      case "cantFetching":
        draft.isFetching = false;
        return;
      case "welcome_smsChange":
        draft.inputs.welcome_sms.hasErrors = false;
        draft.inputs.welcome_sms.value = action.value;
        if (action.value === true) {
          draft.inputs.welcome_sms_use_pattern.isDependencyUsed = true;
        } else {
          draft.inputs.welcome_sms_use_pattern.isDependencyUsed = false;
        }
        return;

      case "welcome_sms_use_patternChange":
        draft.inputs.welcome_sms_use_pattern.hasErrors = false;
        draft.inputs.welcome_sms_use_pattern.value = action.value;
        if (action.value === true) {
          draft.inputs.welcome_sms_pattern.isDependencyUsed = true;
          draft.inputs.welcome_sms_msg.isDependencyUsed = false;
        } else {
          draft.inputs.welcome_sms_pattern.isDependencyUsed = false;
          draft.inputs.welcome_sms_msg.isDependencyUsed = true;
        }
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
        if (action.value === true) {
          draft.inputs.admin_login_notify_pattern.isDependencyUsed = true;
          draft.inputs.select_roles.isDependencyUsed = true;
        } else {
          draft.inputs.admin_login_notify_pattern.isDependencyUsed = false;
          draft.inputs.select_roles.isDependencyUsed = false;
        }
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
  const endpoint = "/farazsms/v1/login_notify_options";

  useFetchOptions(endpoint, dispatch);

  /**
   * Post options to DB
   *
   * @since 2.0.0
   */
  useSaveOptions(endpoint, state, dispatch, appDispatch);

  /**
   * Get user roles keys from DB
   *
   * @since 2.0.0
   */
  useEffect(() => {
    /**
     * Get the global $wp_roles; and make it work for Select react
     *
     * @since 2.0.0
     */
    const rolesObject = farazsmsJsObject.wproules.role_names;
    const rolesArrayObject = Object.keys(rolesObject).map((key) => ({
      value: key,
      label: rolesObject[key],
    }));
    dispatch({
      type: "select_rolesOptions",
      value: rolesArrayObject,
    });
  }, []);

  /**
   * The settings form created by mapping over originalState as the main state.
   * For every value on inputs rendered a SettingsFormInput.
   *
   * @since 2.0.0
   */

  if (state.isFetching) return <LoadingSpinner />;

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
}

export default Settings;
