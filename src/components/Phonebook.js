import React, { useState, useEffect, useContext } from "react";
import { useImmerReducer } from "use-immer";
import { CSSTransition } from "react-transition-group";
import Axios from "axios";

// Used as const not import, for Loco translate plugin compatibility.
const __ = wp.i18n.__;

import AxiosWp from "./AxiosWp";
import DispatchContext from "../DispatchContext";
import SettingsFormInput from "./SettingsFormInput";

function Phonebook() {
  const appDispatch = useContext(DispatchContext);
  // Init States
  const originalState = {
    inputs: {
      custom_phonebook: {
        value: "",
        onChange: "custom_phonebookChange",
        id: "custom_phonebook",
        name: "custom_phonebook",
        type: "select",
        label: __("Select the custom field phonebook:", "farazsms"),
      },
      custom_phone_meta_keys: {
        value: "",
        onChange: "custom_phone_meta_keysChange",
        id: "custom_phone_meta_keys",
        name: "custom_phone_meta_keys",
        type: "select",
        label: __("Select the mobile number custom field:", "farazsms"),
      },
      digits_phonebook: {
        value: "",
        onChange: "digits_phonebookChange",
        id: "digits_phonebook",
        name: "digits_phonebook",
        type: "select",
        label: __("Select phonebook for Digits:", "farazsms"),
      },
      woo_phonebook: {
        value: "",
        onChange: "woo_phonebookChange",
        id: "woo_phonebook",
        name: "woo_phonebook",
        type: "select",
        label: __("select a phonebook for WooCommerce:", "farazsms"),
      },
      bookly_phonebook: {
        value: "",
        onChange: "bookly_phonebookChange",
        id: "bookly_phonebook",
        name: "bookly_phonebook",
        type: "select",
        label: __("Choosing a phonebook for Bookley:", "farazsms"),
      },
      gf_phonebook: {
        value: "",
        onChange: "gf_phonebookChange",
        id: "gf_phonebook",
        name: "gf_phonebook",
        type: "select",
        label: __("Select phonebook for Gravity Form:", "farazsms"),
      },
      gf_selected_field: {
        value: "",
        onChange: "gf_selected_fieldChange",
        id: "gf_selected_field",
        name: "gf_selected_field",
        type: "select",
        label: __("Gravity Form Settings:", "farazsms"),
        tooltip: __(
          "In this section, you can specify the fields you want to register in the Gravity Form phonebook",
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
        draft.inputs.custom_phonebook.value = action.value.custom_phonebook;
        draft.inputs.custom_phone_meta_keys.value =
          action.value.custom_phone_meta_keys;
        draft.inputs.digits_phonebook.value = action.value.digits_phonebook;
        draft.inputs.woo_phonebook.value = action.value.woo_phonebook;
        draft.inputs.bookly_phonebook.value = action.value.bookly_phonebook;
        draft.inputs.gf_phonebook.value = action.value.gf_phonebook;
        draft.inputs.gf_selected_field.value = action.value.gf_selected_field;
        draft.isFetching = false;
        return;
      case "custom_phonebookChange":
        draft.inputs.custom_phonebook.value = action.value;
        return;
      case "custom_phone_meta_keysChange":
        draft.inputs.custom_phone_meta_keys.value = action.value;
        return;
      case "digits_phonebookChange":
        draft.inputs.digits_phonebook.value = action.value;
        return;
      case "woo_phonebookChange":
        draft.inputs.woo_phonebook.value = action.value;
        return;
      case "bookly_phonebookChange":
        draft.inputs.bookly_phonebook.value = action.value;
        return;
      case "gf_selected_fieldChange":
        draft.inputs.gf_selected_field.value = action.value;
        return;
      case "gf_selected_fieldChange":
        draft.inputs.gf_selected_field.value = action.value;
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
        /*
         * Use the AxiosWp object to call the /farazsms/v1/farazsms_phonebook_options
         * endpoint and retrieve the 10 latest posts.
         */
        const getOptions = await AxiosWp.get(
          "/farazsms/v1/phonebook_options",
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
            "/farazsms/v1/phonebook_options",
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

  return (
    <>
      <h3 className="p-3 mb-4 border-bottom border-dark bg-light rounded">
        {__("Phonebook settings:", "farazsms")}
      </h3>
      <div className="container">
        <div className="container card bg-light mb-3 mt-1">
          <div className="card-body">
            <h5 className="card-title">{__("Special Offer:", "farazsms")}</h5>
            <p className="card-text">
              {__(
                "If you have a physical store, use the mobile number storage device to collect your customers mobile numbers. Click on the link below to see the details",
                "farazsms"
              )}
            </p>
            <a
              href="https://farazsms.com/pos/"
              className="btn btn-success"
              target="_blank"
            >
              {__("Buying a mobile number storage device", "farazsms")}
            </a>
          </div>
        </div>
      </div>
      <div className="container">
        <div className="container card bg-warning mb-3">
          <div className="card-body">
            <h5 className="card-title">{__("Warning:", "farazsms")}</h5>
            <p className="card-text">
              {__(
                "You have not registered a phone book yet. Please create your phone book in the FarazSMS panel first.",
                "farazsms"
              )}
            </p>
          </div>
        </div>
      </div>
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
    </>
  );
}

export default Phonebook;
