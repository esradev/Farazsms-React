/**
 * Import remote dependencies.
 */
import React, { useState, useEffect, useContext } from "react";
import { useImmerReducer } from "use-immer";
import useConfirm from "../hooks/useConfirm";
import { __ } from "@wordpress/i18n";

/**
 * Import local dependencies
 */
import DispatchContext from "../DispatchContext";
import AxiosWp from "../function/AxiosWp";
import SectionHeader from "../views/SectionHeader";
import LoadingSpinner from "../views/LoadingSpinner";
import useFetchOptions from "../hooks/useFetchOptions";
import useSaveOptions from "../hooks/useSaveOptions";
import SettingsForm from "../views/SettingsForm";

function Newsletter() {
  const [selectedSubscribers, setSelectedSubscribers] = useState([]);
  const isDeleteDisabled = selectedSubscribers.length === 0;
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
        type: "select_phonebook",
        label: __("Select phone book for newsletter", "farazsms"),
        options: [],
        noOptionsMessage: __("No options is available", "farazsms"),
        infoTitle: __("Shortcode:", "farazsms"),
        infoBody: __(
          "Use [farazsms-newsletter] as shortcode to display the newsletter form in your site.",
          "farazsms"
        ),
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
        draft.inputs.news_send_verify_pattern.isDependencyUsed =
          action.value.news_send_verify_via_pattern === true;
        draft.inputs.news_send_verify_pattern.value =
          action.value.news_send_verify_pattern;

        draft.inputs.news_welcome.value = action.value.news_welcome;
        draft.inputs.news_welcome_pattern.isDependencyUsed =
          action.value.news_welcome === true;
        draft.inputs.news_welcome_pattern.value =
          action.value.news_welcome_pattern;
        draft.inputs.news_post_notify.value = action.value.news_post_notify;
        draft.inputs.news_post_notify_msg.isDependencyUsed =
          action.value.news_post_notify === true;
        draft.inputs.news_post_notify_msg.value =
          action.value.news_post_notify_msg;
        draft.inputs.news_product_notify.value =
          action.value.news_product_notify;
        draft.inputs.news_product_notify_msg.isDependencyUsed =
          action.value.news_product_notify === true;
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
        draft.inputs.news_send_verify_pattern.isDependencyUsed =
          action.value === true;
        return;

      case "news_send_verify_patternChange":
        draft.inputs.news_send_verify_pattern.hasErrors = false;
        draft.inputs.news_send_verify_pattern.value = action.value;
        return;

      case "news_welcomeChange":
        draft.inputs.news_welcome.hasErrors = false;
        draft.inputs.news_welcome.value = action.value;
        draft.inputs.news_welcome_pattern.isDependencyUsed =
          action.value === true;
        return;

      case "news_welcome_patternChange":
        draft.inputs.news_welcome_pattern.hasErrors = false;
        draft.inputs.news_welcome_pattern.value = action.value;
        return;

      case "news_post_notifyChange":
        draft.inputs.news_post_notify.hasErrors = false;
        draft.inputs.news_post_notify.value = action.value;
        draft.inputs.news_post_notify_msg.isDependencyUsed =
          action.value === true;
        return;

      case "news_post_notify_msgChange":
        draft.inputs.news_post_notify_msg.hasErrors = false;
        draft.inputs.news_post_notify_msg.value = action.value;
        return;

      case "news_product_notifyChange":
        draft.inputs.news_product_notify.hasErrors = false;
        draft.inputs.news_product_notify.value = action.value;
        draft.inputs.news_product_notify_msg.isDependencyUsed =
          action.value === true;
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
  async function fetchSubscribers() {
    try {
      const getSubscribers = await AxiosWp.get(
        "/farazsms/v1/get_subscribers_from_db"
      );
      return JSON.parse(getSubscribers.data);
    } catch (e) {
      console.log(e);
    }
  }

  useEffect(() => {
    async function first_load_subscribers() {
      try {
        const subscribers = await fetchSubscribers();
        dispatch({
          type: "getNewsletterSubscribers",
          value: subscribers,
        });
        dispatch({
          type: "updateCurrentSubscribers",
          value: subscribers,
        });
      } catch (e) {
        console.log(e);
      }
    }
    first_load_subscribers();
  }, []);

  const handleSyncSubscribers = async () => {
    try {
      const subscribers = await fetchSubscribers();
      dispatch({
        type: "getNewsletterSubscribers",
        value: subscribers,
      });
      dispatch({
        type: "updateCurrentSubscribers",
        value: subscribers,
      });
      appDispatch({
        type: "flashMessage",
        value: {
          message: __("Congrats. Subscribers synced successfully.", "farazsms"),
        },
      });
      console.log(subscribers);
    } catch (e) {
      console.log(e);
    }
  };

  /**
   * Delete Subscriber from DB.
   * @param subscriber
   * @returns {Promise<void>}
   */
  const { confirm } = useConfirm();
  const deleteSubscriber = async (subscriber_id) => {
    try {
      await AxiosWp.post("/farazsms/v1/delete_subscriber_from_db", {
        subscriber_id: subscriber_id,
      });
      dispatch({
        type: "updateCurrentSubscribers",
        value: state.currentSubscribers - 1,
      });
      await handleSyncSubscribers();
    } catch (e) {
      console.log(e);
    }
  };

  const deleteSubscribers = async (subscribers_id) => {
    try {
      await AxiosWp.post("/farazsms/v1/delete_subscribers_from_db", {
        subscriber_id: subscribers_id,
      });
      dispatch({
        type: "updateCurrentSubscribers",
        value: state.currentSubscribers - 1,
      });
      await handleSyncSubscribers();
    } catch (e) {
      console.log(e);
    }
  };

  const deleteOneSubscriber = async (subscriber) => {
    const isConfirmed = await confirm(
      __("Do you want to delete that subscriber?", "farazsms")
    );
    if (isConfirmed) {
      await deleteSubscriber(subscriber.id);
      appDispatch({
        type: "flashMessage",
        value: {
          message: __("subscriber deleted successfully.", "farazsms"),
        },
      });
    } else {
      appDispatch({
        type: "flashMessage",
        value: {
          message: __("Canceled. The subscriber still there.", "farazsms"),
          type: "error",
        },
      });
    }
  };

  const handleSelectSubscriber = (subscriber_id) => {
    if (selectedSubscribers.includes(subscriber_id)) {
      setSelectedSubscribers(
        selectedSubscribers.filter((s) => s !== subscriber_id)
      );
    } else {
      setSelectedSubscribers((prev) => [...prev, subscriber_id]);
    }
  };

  const handleDeleteSelectedSubscribers = async () => {
    const isConfirmed = await confirm(
      __("Do you want to delete the selected subscribers?", "farazsms")
    );

    if (isConfirmed) {
      await deleteSubscribers(selectedSubscribers);
      setSelectedSubscribers([]);
      appDispatch({
        type: "flashMessage",
        value: {
          message: __("Selected subscribers deleted successfully.", "farazsms"),
        },
      });
    } else {
      appDispatch({
        type: "flashMessage",
        value: {
          message: __("Canceled. Subscribers still there.", "farazsms"),
          type: "error",
        },
      });
    }
  };

  if (state.isFetching) return <LoadingSpinner />;

  return (
    <div className="farazsms-newsletter-component">
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

      {state.newsletterSubscribers && (
        <div className="list-contacts">
          <table className="contact-list">
            <thead>
              <tr>
                <th>{__("Select", "farazsms")}</th>
                <th>{__("Name", "farazsms")}</th>
                <th>{__("Phone Number", "farazsms")}</th>
                <th>{__("Delete", "farazsms")}</th>
              </tr>
            </thead>
            <tbody>
              {state?.newsletterSubscribers.map((subscriber, index) => (
                <tr key={subscriber.id}>
                  <td>
                    <input
                      type="checkbox"
                      checked={selectedSubscribers.includes(subscriber.id)}
                      onChange={() => handleSelectSubscriber(subscriber.id)}
                    />
                  </td>
                  <td>{subscriber.name}</td>
                  <td>{subscriber.phone}</td>
                  <td>
                    <button
                      className="contact-delete"
                      onClick={() => deleteOneSubscriber(subscriber)}
                    >
                      {__("Delete", "farazsms")}
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
          <div className="contact-list-actions">
            <button
              className="contact-delete"
              onClick={handleDeleteSelectedSubscribers}
              disabled={isDeleteDisabled}
            >
              {__("Delete Selected Subscribers", "farazsms")}
            </button>
            <button className="contact-sync" onClick={handleSyncSubscribers}>
              {__("Sync Subscribers", "farazsms")}
            </button>
          </div>
        </div>
      )}
    </div>
  );
}

export default Newsletter;
