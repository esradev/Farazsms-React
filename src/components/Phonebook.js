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
import AxiosWp from "../function/AxiosWp";
import DispatchContext from "../DispatchContext";
import FormInput from "../views/FormInput";
import SaveButton from "../views/SaveButton";
import FormInputError from "../views/FormInputError";
import SectionHeader from "../views/SectionHeader";
import SectionError from "../views/SectionError";
import LoadingSpinner from "../views/LoadingSpinner";

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
        type: "select",
        label: __("Select the custom field phonebook:", "farazsms"),
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
          type: "select",
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
          type: "select",
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
          type: "select",
          label: __("Choosing a phonebook for Bookly:", "farazsms"),
          options: [],
          noOptionsMessage: __("No options is available", "farazsms"),
        },
      }),
    },
    gfSelectedFormId: "",
    isFetching: true,
    isSaving: false,
    sendCount: 0,
    sectionName: __("Phonebook", "farazsms"),
  };

  function ourReduser(draft, action) {
    switch (action.type) {
      case "fetchIntegrationsOptions":
        return;
      case "fetchComplete":
        //Init state values by action.value
        draft.inputs.custom_phonebook.value = action.value.custom_phonebook;
        draft.inputs.custom_phone_meta_keys.value =
          action.value.custom_phone_meta_keys;
        if (props.integratedPlugins.digits.use) {
          draft.inputs.digits_phonebook.value = action.value.digits_phonebook;
        }
        if (props.integratedPlugins.woocommerce.use) {
          draft.inputs.woo_phonebook.value = action.value.woo_phonebook;
        }
        if (props.integratedPlugins.bookly.use) {
          draft.inputs.bookly_phonebook.value = action.value.bookly_phonebook;
        }
        draft.isFetching = false;
        return;
      case "cantFetching":
        draft.isFetching = false;
        return;
      case "all_phonebookOptions":
        draft.inputs.custom_phonebook.options = action.value;
        if (props.integratedPlugins.digits.use) {
          draft.inputs.digits_phonebook.options = action.value;
        }
        if (props.integratedPlugins.woocommerce.use) {
          draft.inputs.woo_phonebook.options = action.value;
        }
        if (props.integratedPlugins.bookly.use) {
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
   * Get integrations options from DB on integrations component loaded
   *
   * @since 2.0.0
   */
  useEffect(() => {
    async function getIntegrationsOptions() {
      try {
        /*
         * Use the AxiosWp object to call the /farazsms/v1/farazsms_integrations_options
         * endpoint and retrieve the 10 latest posts.
         */
        const getIntegrationsOptions = await AxiosWp.get(
          "/farazsms/v1/integrations_options",
          {}
        );
        if (getIntegrationsOptions.data) {
          const optionsJson = JSON.parse(getIntegrationsOptions.data);
          dispatch({ type: "fetchIntegrationsOptions", value: optionsJson });
        }
      } catch (e) {
        console.log(e);
      }
    }
    getIntegrationsOptions();
  }, []);

  /**
   * Get options from DB rest routes
   *
   * @since 2.0.0
   */
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
          dispatch({ type: "fetchComplete", value: optionsJson });
        }
      } catch (e) {
        console.log(e);
        dispatch({ type: "cantFetching" });
      }
    }
    getOptions();
  }, []);

  /**
   * Get usermeta keys from DB rest routes
   *
   * @since 2.0.0
   */
  useEffect(() => {
    async function getUsermeta() {
      try {
        /*
         * Use the AxiosWp object to call the /farazsms/v1/farazsms_usermeta
         * endpoint and retrieve the 10 latest posts.
         */
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
   * Used wp_remote_post() from the php, for avoid No 'Access-Control-Allow-Origin' header is present on the requested resource. error when send this request with axios
   * Axios.post("http://ippanel.com/api/select", {uname: "9300410381", pass: "Faraz@2282037154", op: "booklist",},{ headers: { "Content-Type": "application/json" } });
   *
   * @since 2.0.0
   */
  useEffect(() => {
    async function getPhonebooks() {
      try {
        //farazsmsJsObject is declared on class-farazsms-settings.php under admin_enqueue_scripts function
        const phonebooks = await farazsmsJsObject.getPhonebooks;

        const phonebooksArrayObject = phonebooks["data"].map(
          ({ id, title }) => ({
            label: title,
            value: id,
          })
        );
        dispatch({
          type: "all_phonebookOptions",
          value: phonebooksArrayObject,
        });
      } catch (e) {
        console.log(e);
      }
    }
    getPhonebooks();
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
            value: {
              message: __(
                "Congrats. Form was updated successfully.",
                "farazsms"
              ),
            },
          });
        } catch (e) {
          console.log(e);
        }
      }
      postOptions();
    }
  }, [state.sendCount]);

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
        {Object.values(state.notUsedPlugins).map((plugin) => (
          <div key={plugin.id}>
            <SectionError sectionName={plugin.name} />
          </div>
        ))}
        <form onSubmit={handleSubmit}>
          {Object.values(state.inputs).map((input) => (
            <div key={input.name} className={"form-group"}>
              <FormInput
                isMulti={input.isMulti}
                {...input}
                onChange={(selectedOption) =>
                  dispatch({
                    type: input.onChange,
                    value: selectedOption,
                  })
                }
              />
              <FormInputError />
            </div>
          ))}
          <SaveButton isSaving={state.isSaving} />
        </form>
      </div>
    </>
  );
}

export default Phonebook;
