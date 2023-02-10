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
import FormInput from "../views/FormInput";
import SaveButton from "../views/SaveButton";
import FormInputError from "../views/FormInputError";
import AxiosWp from "../function/AxiosWp";
import SectionHeader from "../views/SectionHeader";
import SectionError from "../views/SectionError";

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
        type: "select",
        label: __("Save the phone number in the phonebook?", "farazsms"),
        rules: "edd_phonebookRules",
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
        rules: "edd_send_to_userRules",
      },
      edd_user_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "edd_user_patternChange",
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
        name: "edd_admin_pattern",
        type: "text",
        label: __("SMS pattern code for the admin:", "farazsms"),
        rules: "edd_admin_patternRules",
        infoTitle: __("Usable variables:", "farazsms"),
        infoBody: __(
          "mobile number: %phone% | Email: %email% | Name: %first_name% | Last name: %last_name% | Purchased products: %product% | Total amount (not including discount): %price% | Total discount amount: %discount% | Paid amount (including discount): %total_price% | Direct download link (not encrypted): %link% | Order number: %payment_id%",
          "farazsms"
        ),
      },
    },
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
        draft.inputs.edd_user_pattern.value = action.value.edd_user_pattern;
        draft.inputs.edd_send_to_admin.value = action.value.edd_send_to_admin;
        draft.inputs.edd_admin_pattern.value = action.value.edd_admin_pattern;

        draft.isFetching = false;
        return;

      case "edd_phonebookChange":
        draft.inputs.edd_phonebook.hasErrors = false;
        draft.inputs.edd_phonebook.value = action.value;
        return;
      case "edd_phonebookOptions":
        draft.inputs.edd_phonebook.options = action.value;
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

  /**
   * Get phonebooks.
   * Used wp_remote_post() from the php, for avoid No 'Access-Control-Allow-Origin' header is present on the requested resource. error when send this request with axios
   * Axios.post("http://ippanel.com/api/select", {uname: "9300410381", pass: "Faraz@2282037154", op: "booklist",},{ headers: { "Content-Type": "application/json" } });
   *
   * @since 2.0.0
   */
  useEffect(() => {
    async function getPhonebooks() {
      try {
        //farazsmsJsObject is declared on class-farazsms-settings.php under admin_enqueue_scripts function
        const phonebooks = await farazsmsJsObject.getphonebooks;
        console.log(phonebooks);
        const phonebooksArrayObject = phonebooks.map(({ id, title }) => ({
          label: title,
          value: id,
        }));
        dispatch({
          type: "edd_phonebookOptions",
          value: phonebooksArrayObject,
        });
        console.log(phonebooksArrayObject);
      } catch (e) {
        console.log(e);
      }
    }
    getPhonebooks();
  }, []);

  /**
   * Get options from DB rest routes
   *
   * @since 2.0.0
   */
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
       * Then Convert array to key: value pair for send Axios post request to DB.
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
          <form onSubmit={handleSubmit}>
            {Object.values(state.inputs).map((input) => (
              <div
                key={input.name}
                className={
                  input.type === "checkbox" ? "toggle-control" : "form-group"
                }
              >
                <FormInput
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
                <FormInputError />
              </div>
            ))}
            <SaveButton isSaving={state.isSaving} />
          </form>
        </div>
      </div>
    );
  } else {
    return <SectionError sectionName={state.sectionName} />;
  }
}

export default Edd;
