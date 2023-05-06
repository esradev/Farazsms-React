/**
 * Import remote dependencies.
 */
import React, { useState, useEffect, useContext } from "react";
import { useImmerReducer } from "use-immer";
import Axios from "axios";
import { __ } from "@wordpress/i18n";

/**
 * Import local dependencies
 */
import AxiosWp from "../function/AxiosWp";
import DispatchContext from "../DispatchContext";
import SectionHeader from "../views/SectionHeader";
import FormInputError from "../views/FormInputError";
import SaveButton from "../views/SaveButton";
import LoadingSpinner from "../views/LoadingSpinner";
import useFetchOptions from "../hooks/useFetchOptions";
import useSaveOptions from "../hooks/useSaveOptions";
import SettingsForm from "../views/SettingsForm";

function Settings() {
  const appDispatch = useContext(DispatchContext);
  /**
   *
   * First init state.
   *
   */
  const originalState = {
    inputs: {
      apikey: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "apikeyChange",
        name: "apikey",
        type: "text",
        placeholder: __("API key", "farazsms"),
        label: __("Your API key:", "farazsms"),
        required: true,
        rules: "apikeyRules",
        checkCount: 0,
        tooltip: __(
          "To get the access key in your farazsms panel, refer to the web service menu in the access keys section",
          "farazsms"
        ),
      },
      username: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "usernameChange",
        name: "username",
        type: "text",
        placeholder: __("Username", "farazsms"),
        label: __("Username", "farazsms"),
        required: true,
        rules: "usernameRules",
        checkCount: 0,
      },
      password: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "passwordChange",
        name: "password",
        type: "text",
        placeholder: __("Password", "farazsms"),
        label: __("Password", "farazsms"),
        required: true,
        rules: "passwordRules",
        checkCount: 0,
      },
      admin_number: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "admin_numberChange",
        name: "admin_number",
        type: "text",
        placeholder: __("Admin Number", "farazsms"),
        label: __("Admin Number", "farazsms"),
        required: true,
        rules: "admin_numberRules",
        checkCount: 0,
      },
      from_number: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "from_numberChange",
        name: "from_number",
        type: "select",
        options: [],
        noOptionsMessage: __("No options is available", "farazsms"),
        label: __("Sender number", "farazsms"),
        rules: "from_numberRules",
      },
      from_number_adver: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "from_number_adverChange",
        name: "from_number_adver",
        type: "select",
        options: [],
        disabaledOptions: ["+983000505", "+985000125475"],
        noOptionsMessage: __("No options is available", "farazsms"),
        label: __("Advertising sender number", "farazsms"),
        rules: "from_number_adverRules",
        /*tooltip: __(
          "If you have Enamad, it is suggested that you purchase a dedicated 9000 line for sending web service SMS and sending SMS to customers. Send a support ticket for this.",
          "farazsms"
        ),*/
      },
    },
    isFetching: true,
    isSaving: false,
    sendCount: 0,
    sectionName: __("General", "farazsms"),
    ippanelUsername: "",
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
      case "cantFetching":
        draft.isFetching = false;
        return;
      case "apikeyChange":
        draft.inputs.apikey.hasErrors = false;
        draft.inputs.apikey.value = action.value;
        return;
      case "apikeyAfterDelay":
        draft.inputs.apikey.checkCount++;
        return;
      case "apikeyIsValidResults":
        if (action.value) {
          draft.inputs.apikey.hasErrors = true;
          draft.inputs.apikey.errorMessage = __(
            "That apikey is not valid.",
            "farazsms"
          );
        }
        return;
      case "apikeyIsEmpty":
        if (action.value) {
          draft.inputs.apikey.hasErrors = true;
          draft.inputs.apikey.errorMessage = __(
            "Please fill API key filed first",
            "farazsms"
          );
        }
        return;
      case "usernameChange":
        draft.inputs.username.hasErrors = false;
        draft.inputs.username.value = action.value;
        return;
      case "usernameAfterDelay":
        draft.inputs.username.checkCount++;
        return;
      case "usernameIsValid":
        if (action.value) {
          draft.inputs.username.hasErrors = true;
          draft.inputs.username.errorMessage = __(
            "That username is not valid.",
            "farazsms"
          );
        }
        return;
      case "usernameNotAccessApikey":
        if (action.value) {
          draft.inputs.username.hasErrors = true;
          draft.inputs.username.errorMessage = __(
            "That username is not access to the provided apikey.",
            "farazsms"
          );
        }
        return;
      case "passwordChange":
        draft.inputs.password.hasErrors = false;
        draft.inputs.password.value = action.value;
        return;
      case "admin_numberChange":
        draft.inputs.admin_number.hasErrors = false;
        draft.inputs.admin_number.value = action.value;
        return;
      case "admin_numberAfterDelay":
        if (!/^(\+98|0)?9\d{9}$/.test(draft.inputs.admin_number.value)) {
          draft.inputs.admin_number.hasErrors = true;
          draft.inputs.admin_number.errorMessage = __(
            "You must provide a valid phone number for admin.",
            "farazsms"
          );
        }
        draft.inputs.admin_number.checkCount++;
        return;
      case "from_numberChange":
        draft.inputs.from_number.hasErrors = false;
        draft.inputs.from_number.value = action.value;
        return;
      case "from_number_adverChange":
        draft.inputs.from_number_adver.hasErrors = false;
        draft.inputs.from_number_adver.value = action.value;
        return;
      case "from_number_linesOptions":
        draft.inputs.from_number.options = action.value;
        draft.inputs.from_number_adver.options = action.value;
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
      case "saveRequestFinished":
        draft.isSaving = false;
        return;
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
      case "ippanelUsername":
        draft.ippanelUsername = action.value;
        return;
    }
  }

  const [state, dispatch] = useImmerReducer(ourReduser, originalState);

  /**
   * HandelSubmit
   *
   * @since 2.0.0
   */
  function handleSubmit(e) {
    e.preventDefault();
    //Set every input to the state with dispatch function.
    Object.values(state.inputs).map((input) => {
      dispatch({ type: input.rules, value: input.value });
    });

    dispatch({ type: "submitOptions" });
  }

  /**
   * Get options from DB rest routes
   *
   * @since 2.0.0
   */
  const endpoint = "/farazsms/v1/settings_options";

  useFetchOptions(endpoint, dispatch);

  /**
   * Post options to DB
   *
   * @since 2.0.0
   */
  useSaveOptions(endpoint, state, dispatch, appDispatch);

  /**
   * Validate Apikey, check if the Apikey is existing on Ippanel.
   *
   * @since 2.0.0
   */
  useEffect(() => {
    if (state.inputs.apikey.value) {
      const delay = setTimeout(
        () => dispatch({ type: "apikeyAfterDelay" }),
        800
      );
      return () => clearTimeout(delay);
    }
  }, [state.inputs.apikey.value]);

  useEffect(() => {
    if (state.inputs.apikey.checkCount) {
      async function validateApikey() {
        let apikey;
        try {
          const validateApikey = await AxiosWp.post(
            "/farazsms/v1/validate_apikey",
            { apikey: state.inputs.apikey.value }
          );
          console.log(validateApikey);
          if (validateApikey.data.status === "OK") {
            dispatch({
              type: "ippanelUsername",
              value: validateApikey.data.data.user.username,
            });
          }
          if (validateApikey.data.status === "UNAUTHORIZED") {
            dispatch({
              type: "apikeyIsValidResults",
              value: true,
            });
          }
        } catch (e) {
          console.log(e);
        }
      }

      validateApikey();
    }
  }, [state.inputs.apikey.checkCount]);

  /**
   *
   * Validate username, check if the username has access to provided apikey.
   *
   * @since 2.0.0
   */
  useEffect(() => {
    if (state.inputs.username.value) {
      const delay = setTimeout(
        () => dispatch({ type: "usernameAfterDelay" }),
        800
      );
      return () => clearTimeout(delay);
    }
  }, [state.inputs.username.value]);

  useEffect(() => {
    if (state.inputs.username.checkCount) {
      async function validateUsername() {
        try {
          if (state.ippanelUsername) {
            if (state.inputs.username.value !== state.ippanelUsername) {
              dispatch({ type: "usernameNotAccessApikey", value: true });
            }
          }
        } catch (e) {
          console.log(e);
        }
      }

      validateUsername();
    }
  }, [state.inputs.username.checkCount]);

  /**
   * Init admin_numberAfterDelay on admin_number.value
   *
   * @since 2.0.0
   */
  useEffect(() => {
    if (state.inputs.admin_number.value) {
      const delay = setTimeout(
        () => dispatch({ type: "admin_numberAfterDelay" }),
        800
      );
      return () => clearTimeout(delay);
    }
  }, [state.inputs.admin_number.value]);

  /**
   * Get lines from IPPanel API
   *
   * @since 2.0.0
   */
  useEffect(() => {
    async function getLines() {
      try {
        const response = await AxiosWp.post("/farazsms/v1/get_lines", {
          username: state.inputs.username.value,
          password: state.inputs.password.value,
        });
        let rolesArrayObject = response.data
          .filter(
            (line) => line.number !== "+98resend" && line.number !== "+98voice"
          )
          .map((line) => ({
            value: line.number,
            label: line.number,
          }));
        console.log(rolesArrayObject);
        dispatch({
          type: "from_number_linesOptions",
          value: rolesArrayObject,
        });
      } catch (e) {
        console.log(e);
      }
    }
    getLines();
  }, [state.inputs.username.value, state.inputs.password.value]);

  if (state.isFetching) return <LoadingSpinner />;

  /**
   * The settings form created by mapping over originalState as the main state.
   * For every value on inputs rendered a SettingsFormInput.
   *
   * @since 2.0.0
   */
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
