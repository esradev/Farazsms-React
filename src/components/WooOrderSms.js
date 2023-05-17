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
import AxiosWp from "../function/AxiosWp";
import DispatchContext from "../DispatchContext";
import SettingsForm from "../views/SettingsForm";
import SectionHeader from "../views/SectionHeader";
import SectionError from "../views/SectionError";
import LoadingSpinner from "../views/LoadingSpinner";

function WooOrderSms(props) {
  const appDispatch = useContext(DispatchContext);
  // Init States
  const originalState = {
    notUsedPlugins: {
      ...(!props.integratedPlugins?.woocommerce?.use && {
        woocommerce: {
          id: "woocommerce",
          name: "WooCommerce",
        },
      }),
    },
    inputs: {
      title: {
        value: "",
        onChange: "titleChange",
        name: "title",
        type: "text",
        label: __("Title:", "farazsms"),
      },
      woo_order_phonebook: {
        value: [],
        onChange: "woo_order_phonebookChange",
        name: "woo_order_phonebook",
        type: "select_phonebook",
        label: __(
          "Select phonebook for Woocommerce order save to phonebook action:",
          "farazsms"
        ),
        options: [],
        noOptionsMessage: __("No options is available", "farazsms"),
      },
      order_type: {
        value: [],
        onChange: "order_typeChange",
        name: "order_type",
        type: "select",
        label: __("Order type:", "farazsms"),
        infoTitle: __("Info", "farazsms"),
        infoBody: __(
          "In this section, you can specify what type of orders you want to use the following actions.",
          "farazsms"
        ),
        options: [],
        noOptionsMessage: __("No options is available", "farazsms"),
      },
      order_status: {
        value: [],
        onChange: "order_statusChange",
        name: "order_status",
        type: "select",
        label: __("Gravity Form phone number field:", "farazsms"),
        infoTitle: __("Info", "farazsms"),
        infoBody: __(
          "In this section, you must specify the mobile field, that you want to do the action on it.",
          "farazsms"
        ),
        options: [],
        noOptionsMessage: __("No options is available", "farazsms"),
      },
      action: {
        value: [],
        onChange: "actionChange",
        name: "action",
        type: "select",
        label: __("Gravity Form name field:", "farazsms"),
        infoTitle: __("Info", "farazsms"),
        infoBody: __(
          "In this section, you must specify the user name field, that you want save on phonebook or want to be on sms message. (use %name% variable in your pattern.)",
          "farazsms"
        ),
        options: [],
        noOptionsMessage: __("No options is available", "farazsms"),
      },
    },
    wooOrderActions: "",
    currentActions: 0,
    isFetching: false,
    isSaving: false,
    sendCount: 0,
    sectionName: __("WooCommerce Order SMS", "farazsms"),
  };

  function ourReduser(draft, action) {
    switch (action.type) {
      case "fetchComplete":
        draft.isFetching = false;
        return;
      case "cantFetchPhonebooks":
        draft.isFetching = false;
        return;
      case "phonebookOptions":
        if (props.integratedPlugins?.woocommerce?.use) {
          draft.inputs.woo_order_phonebook.options = action.value;
        }
        draft.isFetching = false;
        return;
      case "order_typeOptions":
        return;
      case "order_statusOptions":
        return;
      case "actionOptions":
        return;
      case "titleChange":
        draft.inputs.title.value = action.value;
        return;
      case "woo_order_phonebookChange":
        draft.inputs.woo_order_phonebook.value = action.value;
        return;
      case "order_typeChange":
        draft.inputs.order_type.value = action.value;
        return;
      case "order_statusChange":
        draft.inputs.order_status.value = action.value;
        return;
      case "actionChange":
        draft.inputs.action.value = action.value;
        return;
      case "getWooOrderActions":
        draft.wooOrderActions = action.value;
        return;
      case "updateCurrentActions":
        draft.currentActions = action.value;
        return;
      case "formId":
        draft.sendCount++;
        return;
      case "clearForm":
        draft.inputs.title.value = "";
        draft.inputs.woo_order_phonebook.value = [];
        draft.inputs.order_type.value = [];
        draft.inputs.order_status.value = [];
        draft.inputs.action.value = [];

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
    dispatch({ type: "saveRequestStarted" });

    async function add_woocommerce_order_action_to_db() {
      try {
        const newAction = await AxiosWp.post(
          "/farazsms/v1/add_woocommerce_order_action_to_db",
          {
            title: state.inputs.title.value,
            phonebook: null,
            order_type: null,
            order_status: null,
            action: null,
            customer_data: null,
            vendor_data: null,
            admin_data: null,
            time: null,
          }
        );
        dispatch({ type: "saveRequestFinished" });
        dispatch({ type: "clearForm" });
        dispatch({
          type: "updateCurrentActions",
          value: state.currentActions + 1,
        });
        console.log(newAction);
      } catch (e) {
        console.log(e);
      }
    }

    add_woocommerce_order_action_to_db();
  }

  /**
   * Get phonebooks.
   *
   * @since 2.0.0
   */
  function handleNoPhonebooks() {
    dispatch({ type: "cantFetchPhonebooks" });
  }

  function handleAllPhonebooks(phonebooksArrayObject) {
    dispatch({
      type: "phonebookOptions",
      value: phonebooksArrayObject,
    });
  }

  /**
   * Get WooCommerce order actions list from DB
   */
  useEffect(() => {
    async function get_woocommerce_order_action_from_db() {
      try {
        const getActions = await AxiosWp.get(
          "/farazsms/v1/get_woocommerce_order_action_from_db"
        );
        dispatch({
          type: "getWooOrderActions",
          value: JSON.parse(getActions.data),
        });
        dispatch({
          type: "updateCurrentActions",
          value: JSON.parse(getActions.data),
        });
      } catch (e) {
        console.log(e);
      }
    }

    get_woocommerce_order_action_from_db();
  }, [[], state.currentActions]);

  /**
   * Delete WooCommerce order action from DB.
   */
  const { confirm } = useConfirm();
  const deleteAction = async (action) => {
    const isConfirmed = await confirm(
      __("Do you want to delete that action?", "farazsms")
    );

    if (isConfirmed) {
      async function deleteActionFromDb() {
        try {
          await AxiosWp.post(
            "/farazsms/v1/delete_woocommerce_order_action_from_db",
            {
              action_id: action.id,
            }
          );
          dispatch({
            type: "updateCurrentActions",
            value: state.currentActions - 1,
          });
          appDispatch({
            type: "flashMessage",
            value: {
              message: __("Congrats. Action deleted successfully.", "farazsms"),
            },
          });
        } catch (e) {
          console.log(e);
        }
      }

      deleteActionFromDb();
    } else {
      appDispatch({
        type: "flashMessage",
        value: {
          message: __("Canceled. Action still there.", "farazsms"),
          type: "error",
        },
      });
    }
  };

  if (state.isFetching) return <LoadingSpinner />;

  if (props.integratedPlugins?.woocommerce?.use) {
    return (
      <>
        <SectionHeader sectionName={state.sectionName} />
        <div>
          <div className="container"></div>
          <SettingsForm
            dispatchAllPhonebooks={handleAllPhonebooks}
            dispatchNoPhonebooks={handleNoPhonebooks}
            sectionName={state.sectionName}
            inputs={state.inputs}
            handleSubmit={handleSubmit}
            dispatch={dispatch}
            isSaving={state.isSaving}
            buttonText={__("Add Action", "farazsms")}
          />
        </div>
        {state.wooOrderActions && (
          <div className="list-contacts">
            <ol className="contact-list">
              {state.wooOrderActions.map((action) => (
                <li key={action.id} className="contact-list-item">
                  <div className="contact-details">
                    <p>{action.title}</p>
                  </div>
                  <button
                    className="contact-delete"
                    onClick={() => {
                      deleteAction(action);
                    }}
                  >
                    Delete
                  </button>
                </li>
              ))}
            </ol>
          </div>
        )}
      </>
    );
  } else {
    return <SectionError sectionName={state.sectionName} />;
  }
}

export default WooOrderSms;
