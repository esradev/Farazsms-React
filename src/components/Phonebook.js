/**
 * Import remote dependencies.
 */
import React, { useState, useEffect, useContext } from "react";
import { useImmerReducer } from "use-immer";
import { __ } from "@wordpress/i18n";

/**
 * Import local dependencies
 */
import AxiosWp from "../function/AxiosWp";
import DispatchContext from "../DispatchContext";
import SaveButton from "../views/SaveButton";
import FormInputError from "../views/FormInputError";
import SectionHeader from "../views/SectionHeader";
import SectionError from "../views/SectionError";
import LoadingSpinner from "../views/LoadingSpinner";
import useFetchOptions from "../hooks/useFetchOptions";
import useSaveOptions from "../hooks/useSaveOptions";
import SettingsForm from "../views/SettingsForm";

function Phonebook(props) {
  const appDispatch = useContext(DispatchContext);
  // Init States
  const originalState = {
    notUsedPlugins: {
      ...(!props.integratedPlugins.digits.use && {
        digits: {
          id: "digits",
          name: "Digits",
        },
      }),
      ...(!props.integratedPlugins.woocommerce.use && {
        woocommerce: {
          id: "woocommerce",
          name: "Woocommerce",
        },
      }),
      ...(!props.integratedPlugins.bookly.use && {
        bookly: {
          id: "bookly",
          name: "Bookly",
        },
      }),
    },
    inputs: {
      custom_phonebook: {
        value: [],
        onChange: "custom_phonebookChange",
        name: "custom_phonebook",
        type: "select_phonebook",
        label: __("Select the custom phonebook:", "farazsms"),
        options: [],
        noOptionsMessage: __("No options is available", "farazsms"),
      },
      custom_phone_meta_keys: {
        value: [],
        onChange: "custom_phone_meta_keysChange",
        name: "custom_phone_meta_keys",
        type: "select",
        label: __("Select the mobile number custom field:", "farazsms"),
        options: [],
        noOptionsMessage: __("No options is available", "farazsms"),
      },
      ...(props.integratedPlugins.digits.use && {
        digits_phonebook: {
          value: [],
          onChange: "digits_phonebookChange",
          name: "digits_phonebook",
          type: "select_phonebook",
          label: __("Select phonebook for Digits:", "farazsms"),
          options: [],
          noOptionsMessage: __("No options is available", "farazsms"),
        },
      }),
      ...(props.integratedPlugins.woocommerce.use && {
        woo_phonebook: {
          value: [],
          onChange: "woo_phonebookChange",
          name: "woo_phonebook",
          type: "select_phonebook",
          label: __("select a phonebook for WooCommerce:", "farazsms"),
          options: [],
          noOptionsMessage: __("No options is available", "farazsms"),
        },
      }),
      ...(props.integratedPlugins.bookly.use && {
        bookly_phonebook: {
          value: [],
          onChange: "bookly_phonebookChange",
          name: "bookly_phonebook",
          type: "select_phonebook",
          label: __("Choosing a phonebook for Bookly:", "farazsms"),
          options: [],
          noOptionsMessage: __("No options is available", "farazsms"),
        },
      }),
    },
    noPhonebooks: true,
    isFetching: true,
    isSaving: false,
    sendCount: 0,
    sectionName: __("Phonebook", "farazsms"),
  };

  function ourReduser(draft, action) {
    switch (action.type) {
      case "fetchComplete":
        draft.inputs.custom_phonebook.value = action.value.custom_phonebook;
        draft.inputs.custom_phone_meta_keys.value =
          action.value.custom_phone_meta_keys;
        if (props.integratedPlugins?.digits?.use) {
          draft.inputs.digits_phonebook.value = action.value.digits_phonebook;
        }
        if (
          props.integratedPlugins?.woocommerce?.use &&
          draft.inputs.woo_phonebook
        ) {
          draft.inputs.woo_phonebook.value = action.value.woo_phonebook;
        }
        if (props.integratedPlugins?.bookly?.use) {
          draft.inputs.bookly_phonebook.value = action.value.bookly_phonebook;
        }
        draft.isFetching = false;
        return;
      case "cantFetching":
        draft.isFetching = false;
        return;
      case "all_phonebookOptions":
        draft.noPhonebooks = false;
        draft.inputs.custom_phonebook.options = action.value;
        if (props.integratedPlugins?.digits?.use) {
          draft.inputs.digits_phonebook.options = action.value;
        }
        if (
          props.integratedPlugins?.woocommerce?.use &&
          draft.inputs.woo_phonebook
        ) {
          draft.inputs.woo_phonebook.options = action.value;
        }
        if (props.integratedPlugins?.bookly?.use) {
          draft.inputs.bookly_phonebook.options = action.value;
        }
        return;
      case "custom_phone_meta_keysOptions":
        draft.inputs.custom_phone_meta_keys.options = action.value;
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
      case "noPhonebooks":
        draft.noPhonebooks = true;
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
  const endpoint = "/farazsms/v1/phonebook_options";

  useFetchOptions(endpoint, dispatch);

  /**
   * Post options to DB
   *
   * @since 2.0.0
   */
  useSaveOptions(endpoint, state, dispatch, appDispatch);

  /**
   * Get usermeta keys from DB rest routes
   *
   * @since 2.0.0
   */
  useEffect(() => {
    async function getUsermeta() {
      try {
        const getUsermeta = await AxiosWp.get("/farazsms/v1/usermeta", {});
        const usermetaArrayObject = Object.keys(getUsermeta.data).map(
          (key) => ({
            value: getUsermeta.data[key].meta_key,
            label: getUsermeta.data[key].meta_key,
          })
        );
        dispatch({
          type: "custom_phone_meta_keysOptions",
          value: usermetaArrayObject,
        });
      } catch (e) {
        console.log(e);
      }
    }

    getUsermeta();
  }, []);

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
      type: "all_phonebookOptions",
      value: phonebooksArrayObject,
    });
  }

  if (state.isFetching) return <LoadingSpinner />;

  return (
    <>
      <SectionHeader sectionName={state.sectionName} />
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
      {state.noPhonebooks ? (
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
      ) : (
        <></>
      )}

      <div>
        {Object.values(state.notUsedPlugins).map((plugin) => (
          <div key={plugin.id}>
            <SectionError sectionName={plugin.name} />
          </div>
        ))}
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
    </>
  );
}

export default Phonebook;
