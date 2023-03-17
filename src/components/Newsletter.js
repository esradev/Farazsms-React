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
import AxiosWp from "../function/AxiosWp";
import SectionHeader from "../views/SectionHeader";
import LoadingSpinner from "../views/LoadingSpinner";
import usePhonebooks from "../hooks/usePhonebooks";
import useFetchOptions from "../hooks/useFetchOptions";
import useSaveOptions from "../hooks/useSaveOptions";
import SettingsForm from "../views/SettingsForm";

function Newsletter() {
  const appDispatch = useContext(DispatchContext);
  /**
   *
   * First init state.
   *
   */
  const originalState = {
    inputs: {
      news_phonebook: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "news_phonebookChange",
        name: "news_phonebook",
        type: "select",
        label: __("Select phone book for newsletter", "farazsms"),
        options: [],
        noOptionsMessage: __("No options is available", "farazsms"),
      },
      news_send_verify_via_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "news_send_verify_via_patternChange",
        name: "news_send_verify_via_pattern",
        type: "checkbox",
        label: __(
          "Confirm subscription by sending verification code?",
          "farazsms"
        ),
      },
      news_send_verify_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "news_send_verify_patternChange",
        name: "news_send_verify_pattern",
        type: "text",
        label: __(
          "Newsletter membership verification pattern code:",
          "farazsms"
        ),
        infoTitle: __("Usable variables:", "farazsms"),
        infoBody: __("%name% and confirmation code: %code%", "farazsms"),
        isDependencyUsed: false,
      },
      news_welcome: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "news_welcomeChange",
        name: "news_welcome",
        type: "checkbox",
        label: __("Welcome SMS to subscriber of the newsletter?", "farazsms"),
      },
      news_welcome_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "news_welcome_patternChange",
        name: "news_welcome_pattern",
        type: "text",
        label: __("Welcome SMS pattern code", "farazsms"),
        infoTitle: __("Usable variables:", "farazsms"),
        infoBody: __("%name%", "farazsms"),
        isDependencyUsed: false,
      },
      news_post_notify: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "news_post_notifyChange",
        name: "news_post_notify",
        type: "checkbox",
        label: __("Send new posts to newsletter members?", "farazsms"),
      },
      news_post_notify_msg: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "news_post_notify_msgChange",
        name: "news_post_notify_msg",
        type: "textarea",
        label: __("Message content for new post", "farazsms"),
        infoTitle: __("Usable variables:", "farazsms"),
        infoBody: __(
          "the title of the article %title% and the address of the article %url%",
          "farazsms"
        ),
        isDependencyUsed: false,
      },
      news_product_notify: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "news_product_notifyChange",
        name: "news_product_notify",
        type: "checkbox",
        label: __("Send new product to newsletter members?", "farazsms"),
      },
      news_product_notify_msg: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "news_product_notify_msgChange",
        name: "news_product_notify_msg",
        type: "textarea",
        label: __("Message content for new product", "farazsms"),
        infoTitle: __("Usable variables:", "farazsms"),
        infoBody: __(
          "site title %site_title% product name %product_name% price %price% and product link %url%",
          "farazsms"
        ),
        isDependencyUsed: false,
      },
    },
    noPhonebooks: false,
    newsPhonebookID: "",
    newsletterSubscribers: "",
    currentSubscribers: 0,
    isFetching: true,
    isSaving: false,
    sendCount: 0,
    sectionName: __("Newsletter", "farazsms"),
  };

  function ourReduser(draft, action) {
    switch (action.type) {
      case "fetchComplete":
        //Init state values by action.value
        draft.inputs.news_phonebook.value = action.value.news_phonebook;
        draft.inputs.news_send_verify_via_pattern.value =
          action.value.news_send_verify_via_pattern;
        if (action.value.news_send_verify_via_pattern === true) {
          draft.inputs.news_send_verify_pattern.isDependencyUsed = true;
        } else {
          draft.inputs.news_send_verify_pattern.isDependencyUsed = false;
        }
        draft.inputs.news_send_verify_pattern.value =
          action.value.news_send_verify_pattern;

        draft.inputs.news_welcome.value = action.value.news_welcome;
        if (action.value.news_welcome === true) {
          draft.inputs.news_welcome_pattern.isDependencyUsed = true;
        } else {
          draft.inputs.news_welcome_pattern.isDependencyUsed = false;
        }
        draft.inputs.news_welcome_pattern.value =
          action.value.news_welcome_pattern;
        draft.inputs.news_post_notify.value = action.value.news_post_notify;
        if (action.value.news_post_notify === true) {
          draft.inputs.news_post_notify_msg.isDependencyUsed = true;
        } else {
          draft.inputs.news_post_notify_msg.isDependencyUsed = false;
        }
        draft.inputs.news_post_notify_msg.value =
          action.value.news_post_notify_msg;
        draft.inputs.news_product_notify.value =
          action.value.news_product_notify;
        if (action.value.news_product_notify === true) {
          draft.inputs.news_product_notify_msg.isDependencyUsed = true;
        } else {
          {
            draft.inputs.news_product_notify_msg.isDependencyUsed = false;
          }
        }
        draft.inputs.news_product_notify_msg.value =
          action.value.news_product_notify_msg;

        draft.isFetching = false;
        return;
      case "cantFetching":
        draft.isFetching = false;
        return;
      case "news_phonebookChange":
        draft.inputs.news_phonebook.hasErrors = false;
        draft.inputs.news_phonebook.value = action.value;
        return;

      case "news_phonebookOptions":
        draft.inputs.news_phonebook.options = action.value;
        return;
      case "noPhonebooks":
        draft.noPhonebooks = true;
        return;
      case "news_send_verify_via_patternChange":
        draft.inputs.news_send_verify_via_pattern.hasErrors = false;
        draft.inputs.news_send_verify_via_pattern.value = action.value;
        if (action.value === true) {
          draft.inputs.news_send_verify_pattern.isDependencyUsed = true;
        } else {
          draft.inputs.news_send_verify_pattern.isDependencyUsed = false;
        }
        return;

      case "news_send_verify_patternChange":
        draft.inputs.news_send_verify_pattern.hasErrors = false;
        draft.inputs.news_send_verify_pattern.value = action.value;
        return;

      case "news_welcomeChange":
        draft.inputs.news_welcome.hasErrors = false;
        draft.inputs.news_welcome.value = action.value;
        if (action.value === true) {
          draft.inputs.news_welcome_pattern.isDependencyUsed = true;
        } else {
          draft.inputs.news_welcome_pattern.isDependencyUsed = false;
        }
        return;

      case "news_welcome_patternChange":
        draft.inputs.news_welcome_pattern.hasErrors = false;
        draft.inputs.news_welcome_pattern.value = action.value;
        return;

      case "news_post_notifyChange":
        draft.inputs.news_post_notify.hasErrors = false;
        draft.inputs.news_post_notify.value = action.value;
        if (action.value === true) {
          draft.inputs.news_post_notify_msg.isDependencyUsed = true;
        } else {
          draft.inputs.news_post_notify_msg.isDependencyUsed = false;
        }
        return;

      case "news_post_notify_msgChange":
        draft.inputs.news_post_notify_msg.hasErrors = false;
        draft.inputs.news_post_notify_msg.value = action.value;
        return;

      case "news_product_notifyChange":
        draft.inputs.news_product_notify.hasErrors = false;
        draft.inputs.news_product_notify.value = action.value;
        if (action.value === true) {
          draft.inputs.news_product_notify_msg.isDependencyUsed = true;
        } else {
          draft.inputs.news_product_notify_msg.isDependencyUsed = false;
        }
        return;

      case "news_product_notify_msgChange":
        draft.inputs.news_product_notify_msg.hasErrors = false;
        draft.inputs.news_product_notify_msg.value = action.value;
        return;

      case "setNewsPhonebookID":
        draft.newsPhonebookID = action.value;
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
   *
   * @since 2.0.0
   */
  function handleNoPhonebooks() {
    dispatch({ type: "noPhonebooks" });
  }

  function handleAllPhonebooks(phonebooksArrayObject) {
    dispatch({
      type: "news_phonebookOptions",
      value: phonebooksArrayObject,
    });
  }

  usePhonebooks(handleNoPhonebooks, handleAllPhonebooks);

  /**
   * Get options from DB rest routes
   *
   * @since 2.0.0
   */
  const endpoint = "/farazsms/v1/newsletter_options";

  useFetchOptions(endpoint, dispatch);

  /**
   * Post options to DB
   *
   * @since 2.0.0
   */
  useSaveOptions(endpoint, state, dispatch, appDispatch);

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
   * Delete Subscriber from DB.
   * @param subscriber
   * @returns {Promise<void>}
   */
  const { confirm } = useConfirm();

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
        <SettingsForm
          sectionName={state.sectionName}
          inputs={state.inputs}
          handleSubmit={handleSubmit}
          dispatch={dispatch}
          isSaving={state.isSaving}
        />
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

export default Newsletter;
