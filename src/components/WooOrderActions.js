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

function WooOrderActions(props) {
  const appDispatch = useContext(DispatchContext);
  // Init States
  const originalState = {
    inputs: {
      title: {
        value: "",
        onChange: "titleChange",
        name: "title",
        type: "text",
        label: __("Title:", "farazsms"),
      },
      order_type: {
        value: [],
        onChange: "order_typeChange",
        name: "order_type",
        type: "select",
        label: __("Orders type:", "farazsms"),
        infoTitle: __("Info", "farazsms"),
        infoBody: __(
          "In this section, you can specify what type of orders you want to use the following actions.",
          "farazsms"
        ),
        options: [
          {
            value: "all_orders",
            label: __("All orders", "farazsms"),
          },
          {
            value: "orders_more_than",
            label: __("Orders more than:", "farazsms"),
          },
          {
            value: "orders_less_than",
            label: __("Orders less than:", "farazsms"),
          },
          {
            value: "x_orders",
            label: __("X orders", "farazsms"),
          },
          {
            value: "orders_that_include",
            label: __("Orders that include:", "farazsms"),
          },
          {
            value: "orders_that_not_include",
            label: __("Orders that not include:", "farazsms"),
          },
        ],
      },
      minimum_order_total: {
        value: "",
        onChange: "minimum_order_totalChange",
        name: "minimum_order_total",
        type: "text",
        label: __("Minimum order total:", "farazsms"),
        infoTitle: __("Info", "farazsms"),
        infoBody: __(
          "Specify the Minimum order total, that you want to do action on orders those more that this price. example: 100000 (the price should be a number on toman)",
          "farazsms"
        ),
        isDependencyUsed: false,
      },
      maximum_order_total: {
        value: "",
        onChange: "maximum_order_totalChange",
        name: "maximum_order_total",
        type: "text",
        label: __("Maximum order total:", "farazsms"),
        infoTitle: __("Info", "farazsms"),
        infoBody: __(
          "Specify the Maximum order total, that you want to do action on orders those less than this price. example: 200000 (the price should be a number on toman)",
          "farazsms"
        ),
        isDependencyUsed: false,
      },
      order_turn: {
        value: "",
        onChange: "order_turnChange",
        name: "order_turn",
        type: "text",
        label: __("Order turn:", "farazsms"),
        isDependencyUsed: false,
      },
      included_products: {
        value: [],
        onChange: "included_productsChange",
        name: "included_products",
        type: "select",
        label: __("Included Products:", "farazsms"),
        infoTitle: __("Info", "farazsms"),
        infoBody: __(
          "In this section, you must specify Included Products, that you want to do the action on any order that has them.",
          "farazsms"
        ),
        option: [],
        isDependencyUsed: false,
      },
      excluded_products: {
        value: [],
        onChange: "excluded_productsChange",
        name: "excluded_products",
        type: "select",
        label: __("Excluded products:", "farazsms"),
        infoTitle: __("Info", "farazsms"),
        infoBody: __(
          "In this section, you must specify Excluded products, that you don't want to do the action on any order that has them.",
          "farazsms"
        ),
        option: [],
        isDependencyUsed: false,
      },
      order_status: {
        value: [],
        onChange: "order_statusChange",
        name: "order_status",
        type: "select",
        label: __("Orders status:", "farazsms"),
        infoTitle: __("Info", "farazsms"),
        infoBody: __(
          "In this section, you must specify the order status, that you want to do the action on it.",
          "farazsms"
        ),
        options: [
          {
            value: "pending_payment",
            label: __("Pending Payment", "farazsms"),
          },
          {
            value: "failed",
            label: __("Failed", "farazsms"),
          },
          {
            value: "processing",
            label: __("Processing", "farazsms"),
          },
          {
            value: "completed",
            label: __("Completed", "farazsms"),
          },
          {
            value: "on-hold",
            label: __("On-Hold", "farazsms"),
          },
          {
            value: "cancelled",
            label: __("Cancelled", "farazsms"),
          },
          {
            value: "refunded",
            label: __("Refunded", "farazsms"),
          },
          {
            value: "partially_refunded",
            label: __("Partially Refunded", "farazsms"),
          },
          {
            value: "pending",
            label: __("Pending", "farazsms"),
          },
          {
            value: "archived",
            label: __("Archived", "farazsms"),
          },
        ],
      },
      action: {
        value: [],
        onChange: "actionChange",
        name: "action",
        type: "select",
        label: __("Action type:", "farazsms"),
        infoTitle: __("Info", "farazsms"),
        infoBody: __(
          "In this section, you must specify the action type, that you want to do with selected orders.",
          "farazsms"
        ),
        options: [
          {
            value: "save_customer_mobile_to_phonebook",
            label: __("Save user number to phonebook", "farazsms"),
          },
          {
            value: "send_sms_to_admin",
            label: __("Send SMS to admin", "farazsms"),
          },
          {
            value: "send_sms_to_customer",
            label: __("Send SMS to customer", "farazsms"),
          },
          {
            value: "send_sms_to_vendor",
            label: __("Send SMS to vendor", "farazsms"),
          },
        ],
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
        isDependencyUsed: false,
      },
      action_time: {
        value: [],
        onChange: "action_timeChange",
        name: "action_time",
        type: "select",
        label: __("Action time:", "farazsms"),
        infoTitle: __("Info", "farazsms"),
        infoBody: __(
          "In this section, you must specify the action time, that you want to run the action.",
          "farazsms"
        ),
        options: [
          {
            value: "immediately",
            label: __("Immediately after order status change", "farazsms"),
          },
          {
            value: "timed",
            label: __("Timed, X days after order status change", "farazsms"),
          },
        ],
        isDependencyUsed: false,
      },
      sms_pattern: {
        value: "",
        onChange: "sms_patternChange",
        name: "sms_pattern",
        type: "text",
        label: __("SMS Pattern:", "farazsms"),
        isDependencyUsed: false,
      },
      sms_message: {
        value: "",
        onChange: "sms_messageChange",
        name: "sms_message",
        type: "textarea",
        label: __("SMS Message:", "farazsms"),
        isDependencyUsed: false,
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
      case "titleChange":
        draft.inputs.title.value = action.value;
        return;
      case "woo_order_phonebookChange":
        draft.inputs.woo_order_phonebook.value = action.value;
        return;
      case "order_typeChange":
        draft.inputs.order_type.value = action.value;
        if (action.value.value === "all_orders") {
          draft.inputs.minimum_order_total.isDependencyUsed = false;
          draft.inputs.maximum_order_total.isDependencyUsed = false;
          draft.inputs.order_turn.isDependencyUsed = false;
          draft.inputs.included_products.isDependencyUsed = false;
          draft.inputs.excluded_products.isDependencyUsed = false;
        }
        if (action.value.value === "orders_more_than") {
          draft.inputs.minimum_order_total.isDependencyUsed = true;
          draft.inputs.maximum_order_total.isDependencyUsed = false;
          draft.inputs.order_turn.isDependencyUsed = false;
          draft.inputs.included_products.isDependencyUsed = false;
          draft.inputs.excluded_products.isDependencyUsed = false;
        } else if (action.value.value === "orders_less_than") {
          draft.inputs.minimum_order_total.isDependencyUsed = false;
          draft.inputs.maximum_order_total.isDependencyUsed = true;
          draft.inputs.order_turn.isDependencyUsed = false;
          draft.inputs.included_products.isDependencyUsed = false;
          draft.inputs.excluded_products.isDependencyUsed = false;
        } else if (action.value.value === "x_orders") {
          draft.inputs.minimum_order_total.isDependencyUsed = false;
          draft.inputs.maximum_order_total.isDependencyUsed = false;
          draft.inputs.order_turn.isDependencyUsed = true;
          draft.inputs.included_products.isDependencyUsed = false;
          draft.inputs.excluded_products.isDependencyUsed = false;
        } else if (action.value.value === "orders_that_include") {
          draft.inputs.minimum_order_total.isDependencyUsed = false;
          draft.inputs.maximum_order_total.isDependencyUsed = false;
          draft.inputs.order_turn.isDependencyUsed = false;
          draft.inputs.included_products.isDependencyUsed = true;
          draft.inputs.excluded_products.isDependencyUsed = false;
        } else if (action.value.value === "orders_that_not_include") {
          draft.inputs.minimum_order_total.isDependencyUsed = false;
          draft.inputs.maximum_order_total.isDependencyUsed = false;
          draft.inputs.order_turn.isDependencyUsed = false;
          draft.inputs.included_products.isDependencyUsed = false;
          draft.inputs.excluded_products.isDependencyUsed = true;
        }
        return;
      case "order_statusChange":
        draft.inputs.order_status.value = action.value;
        return;
      case "actionChange":
        draft.inputs.action.value = action.value;
        if (action.value.value === "save_customer_mobile_to_phonebook") {
          draft.inputs.woo_order_phonebook.isDependencyUsed = true;
          draft.inputs.action_time.isDependencyUsed = false;
        } else {
          draft.inputs.woo_order_phonebook.isDependencyUsed = false;
          draft.inputs.action_time.isDependencyUsed = true;
        }
        return;
      case "getWooOrderActions":
        draft.wooOrderActions = action.value;
        return;
      case "updateCurrentActions":
        draft.currentActions = action.value;
        return;
      case "action_timeChange":
        draft.inputs.action_time.value = action.value;
        if (action.value.value === "immediately") {
          draft.inputs.sms_pattern.isDependencyUsed = true;
          draft.inputs.sms_message.isDependencyUsed = false;
        } else {
          draft.inputs.sms_pattern.isDependencyUsed = false;
          draft.inputs.sms_message.isDependencyUsed = true;
        }
        return;
      case "clearForm":
        draft.inputs.title.value = "";
        draft.inputs.woo_order_phonebook.value = [];
        draft.inputs.order_type.value = [];
        draft.inputs.order_status.value = [];
        draft.inputs.action.value = [];
        draft.inputs.action_time.value = [];
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
        const res = await AxiosWp.get(
          "/farazsms/v1/get_woocommerce_order_actions_from_db"
        );
        const data = res.data;
        console.log(res);
        dispatch({
          type: "getWooOrderActions",
          value: JSON.parse(data),
        });
        dispatch({
          type: "updateCurrentActions",
          value: JSON.parse(data),
        });
      } catch (error) {
        console.log(error);
      }
    }

    get_woocommerce_order_action_from_db();
  }, []);

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
            <table className="contact-list">
              <thead>
                <tr>
                  <th>{__("Select", "farazsms")}</th>
                  <th>{__("Title", "farazsms")}</th>
                  <th>{__("Phonebook", "farazsms")}</th>
                  <th>{__("Orders type", "farazsms")}</th>
                  <th>{__("Orders status", "farazsms")}</th>
                  <th>{__("Action type", "farazsms")}</th>
                  <th>{__("Time of action", "farazsms")}</th>
                  <th>{__("Delete", "farazsms")}</th>
                </tr>
              </thead>
              <tbody>
                {state?.wooOrderActions.map((action, index) => (
                  <tr key={action.id}>
                    <td>
                      <input
                        type="checkbox"
                        // checked={selectedSubscribers.includes(action.id)}
                        // onChange={() => handleSelectSubscriber(action.id)}
                      />
                    </td>
                    <td>{action.title}</td>
                    <td>{action.title}</td>
                    <td>{action.title}</td>
                    <td>{action.title}</td>
                    <td>{action.title}</td>
                    <td>{action.title}</td>

                    <td>
                      <button
                        className="contact-delete"
                        // onClick={() => deleteAction(action)}
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
                // onClick={handleDeleteSelectedSubscribers}
                // disabled={isDeleteDisabled}
              >
                {__("Delete Selected Actions", "farazsms")}
              </button>
              <button
                className="contact-sync"
                // onClick={handleSyncSubscribers}
              >
                {__("Sync Actions", "farazsms")}
              </button>
            </div>
          </div>
        )}
      </>
    );
  } else {
    return <SectionError sectionName={state.sectionName} />;
  }
}

export default WooOrderActions;
