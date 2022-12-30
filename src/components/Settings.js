import React, { useState, useEffect, useContext } from "react";
import { useImmerReducer } from "use-immer";
import { CSSTransition } from "react-transition-group";
import Axios from "axios";
// Used as const not import, for Loco translate plugin compatibility.
const __ = wp.i18n.__;

import AxiosWp from "./AxiosWp";
import DispatchContext from "../DispatchContext";
import SettingsFormInput from "./SettingsFormInput";
import LoadingDotsIcon from "./LoadingDotsIcon";

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
        id: "apikey",
        name: "apikey",
        type: "text",
        placeholder: __("API key", "farazsms"),
        label: __("Your API key:", "farazsms"),
        required: true,
        rules: "apikeyRules",
        isValid: false,
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
        id: "username",
        name: "username",
        type: "text",
        placeholder: __("Your Username", "farazsms"),
        label: __("Username:", "farazsms"),
        required: true,
        rules: "usernameRules",
        isValid: false,
        checkCount: 0,
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
        isValid: false,
        checkCount: 0,
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
        tooltip: __(
          "If you have Enamad, it is suggested that you purchase a dedicated 9000 line for sending web service SMS and sending SMS to customers. Send a support ticket for this.",
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
      case "apikeyAfterDelay":
        draft.inputs.apikey.checkCount++;
        return;
      case "apikeyIsValidResults":
        if (action.value) {
          draft.inputs.apikey.hasErrors = true;
          draft.inputs.apikey.isValid = false;
          draft.inputs.apikey.errorMessage = "That apikey is not valid.";
        } else {
          draft.inputs.apikey.isValid = true;
        }
        return;
      case "apikeyIsEmpty":
        if (action.value) {
          draft.inputs.apikey.hasErrors = true;
          draft.inputs.apikey.isValid = false;
          draft.inputs.apikey.errorMessage = "Please fill API key filed first";
        } else {
          draft.inputs.apikey.isValid = true;
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
          draft.inputs.username.isValid = false;
          draft.inputs.username.errorMessage = "That username is not valid.";
        } else {
          draft.inputs.username.isValid = true;
        }
        return;
      case "usernameNotAccessApikey":
        if (action.value) {
          draft.inputs.username.hasErrors = true;
          draft.inputs.username.isValid = false;
          draft.inputs.username.errorMessage =
            "That username is not access to the provided apikey.";
        } else {
          draft.inputs.username.isValid = true;
        }
        return;
      case "passwordChange":
        draft.inputs.password.hasErrors = false;
        draft.inputs.password.value = action.value;
        return;
      case "passwordAfterDelay":
        draft.inputs.password.checkCount++;
        return;
      case "passwordIsValid":
        if (action.value) {
          draft.inputs.password.hasErrors = true;
          draft.inputs.password.isValid = false;
          draft.inputs.password.errorMessage =
            "That password is not valid, check that agian correctly.";
        } else {
          draft.inputs.password.isValid = true;
        }
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

  /**
   *
   * HandelSubmit function
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
   *
   * Get settings options from DB on settings component loaded
   *
   * @since 2.0.0
   */
  useEffect(() => {
    async function getOptions() {
      try {
        /*
         * Use the AxiosWp object to call the /farazsms/v1/farazsms_settings_options
         * endpoint and retrieve the 10 latest posts.
         */
        const getOptions = await AxiosWp.get(
          "/farazsms/v1/settings_options",
          {}
        );
        if (getOptions.data) {
          const optionsJson = JSON.parse(getOptions.data);
          console.log(optionsJson);
          dispatch({ type: "fetchComplete", value: optionsJson });
        }
      } catch (e) {
        console.log(e);
      }
    }
    getOptions();
  }, []);

  /**
   *
   * Save settings options on DB when saveRequestFininshed = true
   *
   * @since 2.0.0
   */
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
            "/farazsms/v1/settings_options",
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
   *
   * Validate Apikey, check if the Apikey is exist on Ippanel.
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
        const authentication_data = {
          headers: {
            Authorization: "AccessKey " + state.inputs.apikey.value,
          },
        };
        try {
          const ippanelData = await Axios.get(
            "http://rest.ippanel.com/v1/user",
            authentication_data
          );
        } catch (e) {
          dispatch({
            type: "apikeyIsValidResults",
            value: true,
          });
          console.log(e);
        }
      }
      validateApikey();
    }
  }, [state.inputs.apikey.checkCount]);

  /**
   *
   * Validarte username, check if the username has access to provided apikey.
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
        if (!state.inputs.apikey.value) {
          dispatch({
            type: "apikeyIsEmpty",
            value: true,
          });
        } else {
          const authentication_data = {
            headers: {
              Authorization: "AccessKey " + state.inputs.apikey.value,
            },
          };
          try {
            const ippanelData = await Axios.get(
              "http://rest.ippanel.com/v1/user",
              authentication_data
            );
            console.log(ippanelData);
            const ippanelResponseUsername = ippanelData.data.data.user.username;
            console.log(ippanelResponseUsername);
            if (state.inputs.username.value == ippanelResponseUsername) {
              dispatch({ type: "usernameNotAccessApikey", value: false });
            } else {
              dispatch({ type: "usernameNotAccessApikey", value: true });
            }
          } catch (e) {
            console.log(e);
          }
        }
      }
      validateUsername();
    }
  }, [state.inputs.username.checkCount]);

  /**
   *
   * Validarte username and password, check if the username and password are correct.
   *
   * @since 2.0.0
   */
  useEffect(() => {
    if (state.inputs.password.value) {
      const delay = setTimeout(
        () => dispatch({ type: "passwordAfterDelay" }),
        800
      );
      return () => clearTimeout(delay);
    }
  }, [state.inputs.password.value]);

  // useEffect(() => {
  //   if (state.inputs.password.checkCount) {
  //     async function validateUser() {
  //       try {
  //         const ippanelData = await Axios.post(
  //           "http://reg.ippanel.com/parent/farazsms",
  //           { username: "9300410381", password: "Faraz@2282037154" },
  //         );
  //         console.log(ippanelData);
  //       } catch (e) {
  //         console.log(e);
  //       }
  //     }
  //     validateUser();
  //   }
  // }, [state.inputs.password.checkCount]);

  /**
   * The settings form created by maping over originalState as the main state.
   * For every value on inputs rendered a SettingsFormInput.
   *
   * @since 2.0.0
   */
  return (
    <div>
      <h3 className="p-3 mb-4 border-bottom border-dark bg-light rounded">
        {__("Settings:", "farazsms")}
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
              <CSSTransition
                in={input.hasErrors}
                timeout={330}
                classNames="liveValidateMessage"
                unmountOnExit
              >
                <div className="alert alert-danger small liveValidateMessage">
                  {input.errorMessage}
                </div>
              </CSSTransition>
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
