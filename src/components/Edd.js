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
import SectionError from "../views/SectionError";
import LoadingSpinner from "../views/LoadingSpinner";
import useFetchOptions from "../hooks/useFetchOptions";
import useSaveOptions from "../hooks/useSaveOptions";

function Edd(props) {
  const appDispatch = useContext(DispatchContext);
  /**
   *
   * First init state.
   *
   */
  const originalState = {
    inputs: {
      edd_phonebook: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "edd_phonebookChange",
        name: "edd_phonebook",
        type: "select_phonebook",
        label: __("Save the phone number in the phonebook?", "farazsms"),
        options: [],
        noOptionsMessage: __("No options is available", "farazsms"),
      },
      edd_send_to_user: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "edd_send_to_userChange",
        name: "edd_send_to_user",
        type: "checkbox",
        label: __("Send sms to the user?", "farazsms"),
      },
      edd_user_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "edd_user_patternChange",
        name: "edd_user_pattern",
        type: "text",
        label: __("SMS pattern code for the user:", "farazsms"),
        isDependencyUsed: false,
      },
      edd_send_to_admin: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "edd_send_to_adminChange",
        name: "edd_send_to_admin",
        type: "checkbox",
        label: __("Send sms to the admin?", "farazsms"),
      },
      edd_admin_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "edd_admin_patternChange",
        name: "edd_admin_pattern",
        type: "text",
        label: __("SMS pattern code for the admin:", "farazsms"),
        infoTitle: __("Usable variables:", "farazsms"),
        infoBody: __(
          "mobile number: %phone% | Email: %email% | Name: %first_name% | Last name: %last_name% | Purchased products: %product% | Total amount (not including discount): %price% | Total discount amount: %discount% | Paid amount (including discount): %total_price% | Direct download link (not encrypted): %link% | Order number: %payment_id%",
          "farazsms"
        ),
        isDependencyUsed: false,
      },
    },
    noPhonebooks: false,
    isFetching: true,
    isSaving: false,
    sendCount: 0,
    sectionName: __("Edd", "farazsms"),
  };

  function ourReduser(draft, action) {
    switch (action.type) {
      case "fetchComplete":
        //Init state values by action.value
        draft.inputs.edd_phonebook.value = action.value.edd_phonebook;
        draft.inputs.edd_send_to_user.value = action.value.edd_send_to_user;
        if (action.value.edd_send_to_user === true) {
          draft.inputs.edd_user_pattern.isDependencyUsed = true;
        } else {
          draft.inputs.edd_user_pattern.isDependencyUsed = false;
        }
        draft.inputs.edd_user_pattern.value = action.value.edd_user_pattern;
        draft.inputs.edd_send_to_admin.value = action.value.edd_send_to_admin;
        if (action.value.edd_send_to_admin === true) {
          draft.inputs.edd_admin_pattern.isDependencyUsed = true;
        } else {
          draft.inputs.edd_admin_pattern.isDependencyUsed = false;
        }
        draft.inputs.edd_admin_pattern.value = action.value.edd_admin_pattern;

        draft.isFetching = false;
        return;
      case "cantFetching":
        draft.isFetching = false;
        return;
      case "edd_phonebookChange":
        draft.inputs.edd_phonebook.hasErrors = false;
        draft.inputs.edd_phonebook.value = action.value;
        return;
      case "noPhonebooks":
        draft.noPhonebooks = true;
        return;
      case "edd_phonebookOptions":
        draft.inputs.edd_phonebook.options = action.value;
        return;
      case "edd_send_to_userChange":
        draft.inputs.edd_send_to_user.hasErrors = false;
        draft.inputs.edd_send_to_user.value = action.value;
        if (action.value === true) {
          draft.inputs.edd_user_pattern.isDependencyUsed = true;
        } else {
          draft.inputs.edd_user_pattern.isDependencyUsed = false;
        }
        return;
      case "edd_user_patternChange":
        draft.inputs.edd_user_pattern.hasErrors = false;
        draft.inputs.edd_user_pattern.value = action.value;
        return;
      case "edd_send_to_adminChange":
        draft.inputs.edd_send_to_admin.hasErrors = false;
        draft.inputs.edd_send_to_admin.value = action.value;
        if (action.value === true) {
          draft.inputs.edd_admin_pattern.isDependencyUsed = true;
        } else {
          draft.inputs.edd_admin_pattern.isDependencyUsed = false;
        }
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
   * Get phonebooks.
   *
   * @since 2.0.0
   */
  function handleNoPhonebooks() {
    dispatch({ type: "noPhonebooks" });
  }

  function handleAllPhonebooks(phonebooksArrayObject) {
    dispatch({
      type: "edd_phonebookOptions",
      value: phonebooksArrayObject,
    });
  }

  /**
   * Get options from DB rest routes
   *
   * @since 2.0.0
   */
  const endpoint = "/farazsms/v1/edd_options";

  useFetchOptions(endpoint, dispatch);

  /**
   * Post options to DB
   *
   * @since 2.0.0
   */
  useSaveOptions(endpoint, state, dispatch, appDispatch);

  if (state.isFetching) return <LoadingSpinner />;

  /**
   * The settings form created by mapping over originalState as the main state.
   * For every value on inputs rendered a SettingsFormInput.
   *
   * @since 2.0.0
   */
  if (props.integratedPlugins.edd.use) {
    return (
      <div>
        <SectionHeader sectionName={state.sectionName} />
        <div>
          <SettingsForm
            dispatchAllPhonebooks={handleAllPhonebooks}
            dispatchNoPhonebooks={handleNoPhonebooks}
            sectionName={state.sectionName}
            inputs={state.inputs}
            handleSubmit={handleSubmit}
            dispatch={dispatch}
            isSaving={state.isSaving}
          />
        </div>
      </div>
    );
  } else {
    return <SectionError sectionName={state.sectionName} />;
  }
}

export default Edd;
