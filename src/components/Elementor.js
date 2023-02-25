/**
 * Import remote dependencies.
 */
import React, { useState, useEffect, useContext } from "react";
import { useImmerReducer } from "use-immer";
import useConfirm from "../hooks/useConfirm";
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
import LoadingSpinner from "../views/LoadingSpinner";

function Elementor() {
  const appDispatch = useContext(DispatchContext);
  /**
   *
   * First init state.
   *
   */
  const originalState = {
    inputs: {
      elementor_phonebook: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "elementor_phonebookChange",
        name: "elementor_phonebook",
        type: "select",
        label: __(
          "Select phonebook for action after submit on elementor pro form widget.",
          "farazsms"
        ),
        options: [],
        noOptionsMessage: __("No options is available", "farazsms"),
      },
    },
    elementorPhonebookID: "",
    newsletterSubscribers: "",
    currentSubscribers: 0,
    isFetching: true,
    isSaving: false,
    sendCount: 0,
    sectionName: __("Elementor", "farazsms"),
  };

  function ourReduser(draft, action) {
    switch (action.type) {
      case "fetchComplete":
        draft.inputs.elementor_phonebook.value =
          action.value.elementor_phonebook;
        draft.isFetching = false;
        return;
      case "cantFetching":
        draft.isFetching = false;
        return;
      case "elementor_phonebookChange":
        draft.inputs.elementor_phonebook.hasErrors = false;
        draft.inputs.elementor_phonebook.value = action.value;
        return;
      case "elementor_phonebookOptions":
        draft.inputs.elementor_phonebook.options = action.value;
        return;
      case "setNewsPhonebookID":
        draft.elementorPhonebookID = action.value;
        return;

      case "getNewsletterSubscribers":
        draft.newsletterSubscribers = action.value;
        return;

      case "updateCurrentSubscribers":
        draft.currentSubscribers = action.value;
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

  /**
   * HandelSubmit
   *
   * @since 2.0.0
   */
  function handleSubmit(e) {
    e.preventDefault();
    dispatch({ type: "submitOptions" });
  }

  /**
   * Get phonebooks.
   * Used wp_remote_post() from the php, for avoid No 'Access-Control-Allow-Origin' header is present on the requested resource. error when send this request with axios
   *
   * @since 2.0.0
   */
  useEffect(() => {
    async function getPhonebooks() {
      try {
        //farazsmsJsObject is declared on class-farazsms-settings.php under admin_enqueue_scripts function
        const phonebooks = await farazsmsJsObject.getPhonebooks;
        const phonebooksArrayObject = phonebooks.data.map(({ id, title }) => ({
          label: title,
          value: id,
        }));
        dispatch({
          type: "elementor_phonebookOptions",
          value: phonebooksArrayObject,
        });
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
        const getOptions = await AxiosWp.get("/farazsms/v1/elementor_options");
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
   * Get subscribers list from DB
   */
  useEffect(() => {
    async function get_subscribers_from_db() {
      try {
        const getSubscribers = await AxiosWp.get(
          "/farazsms/v1/get_subscribers_from_db"
        );
        dispatch({
          type: "getNewsletterSubscribers",
          value: JSON.parse(getSubscribers.data),
        });
        dispatch({
          type: "updateCurrentSubscribers",
          value: JSON.parse(getSubscribers.data),
        });
      } catch (e) {
        console.log(e);
      }
    }
    get_subscribers_from_db();
  }, [[], state.currentSubscribers]);

  /**
   * Post options to DB
   */
  useEffect(() => {
    if (state.sendCount) {
      const optionsArray = Object.values(state.inputs).map(
        ({ value, name }) => [name, value]
      );
      const optionsJsonForPost = Object.fromEntries(optionsArray);
      dispatch({ type: "saveRequestStarted" });

      async function postOptions() {
        try {
          // Post Options from site DB Options table
          const postOptions = await AxiosWp.post(
            "/farazsms/v1/elementor_options",
            optionsJsonForPost
          );
          dispatch({ type: "saveRequestFinished" });
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

  const { confirm } = useConfirm();

  /**
   * Delete Subscriber from DB.
   * @param subscriber
   * @returns {Promise<void>}
   */

  const deleteSubscriber = async (subscriber) => {
    const isConfirmed = await confirm(
      __("Do you want to delete that subscriber?", "farazsms")
    );

    if (isConfirmed) {
      async function deleteSubscriberFromDb() {
        try {
          await AxiosWp.post("/farazsms/v1/delete_subscriber_from_db", {
            subscriber_id: subscriber.id,
          });
          dispatch({
            type: "updateCurrentSubscribers",
            value: state.currentSubscribers - 1,
          });
          appDispatch({
            type: "flashMessage",
            value: {
              message: __(
                "Congrats. Subscriber deleted successfully.",
                "farazsms"
              ),
            },
          });
          console.log(subscriber.id);
        } catch (e) {
          console.log(e);
        }
      }
      deleteSubscriberFromDb();
    } else {
      appDispatch({
        type: "flashMessage",
        value: {
          message: __("Canceled. Subscriber still there.", "farazsms"),
          type: "error",
        },
      });
    }
  };

  if (state.isFetching) return <LoadingSpinner />;
  /**
   * The settings form created by mapping over originalState as the main state.
   * For every value on inputs rendered a SettingsFormInput.
   *
   * @since 2.0.0
   */
  return (
    <div className="farazsms-newsletter-component">
      <SectionHeader sectionName={state.sectionName} />
      <div>
        <form onSubmit={handleSubmit}>
          {Object.values(state.inputs).map((input) =>
            input.isDependencyUsed === false ? (
              <></>
            ) : (
              <div
                key={input.name}
                className={
                  input.type === "checkbox" ? "toggle-control" : "form-group"
                }
              >
                <FormInput
                  isMulti={input.isMulti}
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
                />
                <FormInputError />
              </div>
            )
          )}
          <SaveButton isSaving={state.isSaving} />
        </form>
      </div>

      {state.newsletterSubscribers && (
        <div className="list-contacts">
          <ol className="contact-list">
            {state.newsletterSubscribers.map((subscriber) => (
              <li key={subscriber.id} className="contact-list-item">
                <div className="contact-details">
                  <p>{subscriber.name}</p>
                </div>
                <div className="contact-details">
                  <p className="contact-details">{subscriber.phone}</p>
                </div>
                <button
                  className="contact-delete"
                  onClick={() => {
                    deleteSubscriber(subscriber);
                  }}
                >
                  Delete
                </button>
              </li>
            ))}
          </ol>
        </div>
      )}
    </div>
  );
}

export default Elementor;
