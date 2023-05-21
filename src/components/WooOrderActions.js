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
  const [selectedActions, setSelectedActions] = useState([]);
  const isDeleteDisabled = selectedActions.length === 0;
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
        isMulti: "isMulti",
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
        isMulti: "isMulti",
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
            value: "on_hold",
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
            value: "authentication_required",
            label: __("Authentication required", "farazsms"),
          },
          {
            value: "checkout_draft ",
            label: __("Checkout draft", "farazsms"),
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
            label: __("Save customer mobile to phonebook", "farazsms"),
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
      mobile_meta_key: {
        value: "",
        onChange: "mobile_meta_keyChange",
        name: "mobile_meta_key",
        type: "select",
        label: __("Select meta key for vendor or customer mobile.", "farazsms"),
        options: [],
        isDependencyUsed: false,
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
            label: __("Immediately after order status change.", "farazsms"),
          },
          {
            value: "timed",
            label: __("Timed, X days after order status change.", "farazsms"),
          },
        ],
        isDependencyUsed: false,
      },
      time: {
        value: "",
        onChange: "timeChange",
        name: "time",
        type: "text",
        label: __("Action time:", "farazsms"),
        isDependencyUsed: false,
        infoTitle: __("Info:", "farazsms"),
        infoBody: __(
          "Specify X days the action run after order status change. (Enter number of days.)",
          "farazsms"
        ),
      },
      sms_pattern: {
        value: "",
        onChange: "sms_patternChange",
        name: "sms_pattern",
        type: "text",
        label: __("SMS Pattern:", "farazsms"),
        isDependencyUsed: false,
        infoTitle: __("Usable variables:", "farazsms"),
        infoBody: __(
          "%customer_name% | %vendor_name% | %order_id% | %total_price% | %products%",
          "farazsms"
        ),
      },
      sms_message: {
        value: "",
        onChange: "sms_messageChange",
        name: "sms_message",
        type: "textarea",
        label: __("SMS Message:", "farazsms"),
        isDependencyUsed: false,
        infoTitle: __("Usable variables:", "farazsms"),
        infoBody: __(
          "%customer_name% | %vendor_name% | %order_id% | %total_price% | %products%",
          "farazsms"
        ),
      },
    },
    wooOrderActions: "",
    currentWooOrderActions: 0,
    checkAction: false,
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
      case "titleChange":
        draft.inputs.title.value = action.value;
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
      case "minimum_order_totalChange":
        draft.inputs.minimum_order_total.value = action.value;
        return;
      case "maximum_order_totalChange":
        draft.inputs.maximum_order_total.value = action.value;
        return;
      case "order_turnChange":
        draft.inputs.order_turn.value = action.value;
        return;
      case "fetchWoocommerceProducts":
        draft.inputs.included_products.options = action.value;
        draft.inputs.excluded_products.options = action.value;
        return;
      case "included_productsChange":
        draft.inputs.included_products.value = action.value;
        return;
      case "excluded_productsChange":
        draft.inputs.excluded_products.value = action.value;
        return;
      case "order_statusChange":
        draft.inputs.order_status.value = action.value;
        return;
      case "actionChange":
        draft.inputs.action.value = action.value;
        if (action.value.value === "save_customer_mobile_to_phonebook") {
          draft.inputs.mobile_meta_key.isDependencyUsed = true;
          draft.inputs.woo_order_phonebook.isDependencyUsed = true;
          draft.inputs.action_time.isDependencyUsed = false;
          draft.inputs.sms_pattern.isDependencyUsed = false;
          draft.inputs.sms_message.isDependencyUsed = false;
        } else if (action.value.value === "send_sms_to_customer") {
          draft.inputs.mobile_meta_key.isDependencyUsed = true;
          draft.inputs.woo_order_phonebook.isDependencyUsed = false;
          draft.inputs.action_time.isDependencyUsed = true;
        } else if (action.value.value === "send_sms_to_vendor") {
          draft.inputs.mobile_meta_key.isDependencyUsed = true;
          draft.inputs.woo_order_phonebook.isDependencyUsed = false;
          draft.inputs.action_time.isDependencyUsed = true;
        } else {
          draft.inputs.mobile_meta_key.isDependencyUsed = false;
          draft.inputs.woo_order_phonebook.isDependencyUsed = false;
          draft.inputs.action_time.isDependencyUsed = true;
        }
        return;
      case "mobile_meta_keyOptions":
        draft.inputs.mobile_meta_key.options = action.value;
        return;
      case "mobile_meta_keyChange":
        draft.inputs.mobile_meta_key.value = action.value;
        return;
      case "woo_order_phonebookChange":
        draft.inputs.woo_order_phonebook.value = action.value;
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
      case "action_timeChange":
        draft.inputs.action_time.value = action.value;
        if (action.value.value === "immediately") {
          draft.inputs.time.isDependencyUsed = false;
          draft.inputs.sms_pattern.isDependencyUsed = true;
          draft.inputs.sms_message.isDependencyUsed = false;
        } else {
          draft.inputs.time.isDependencyUsed = true;
          draft.inputs.sms_pattern.isDependencyUsed = false;
          draft.inputs.sms_message.isDependencyUsed = true;
        }
        return;
      case "timeChange":
        draft.inputs.time.value = action.value;
        return;
      case "sms_patternChange":
        draft.inputs.sms_pattern.value = action.value;
        return;
      case "sms_messageChange":
        draft.inputs.sms_message.value = action.value;
        return;
      case "getWooOrderActions":
        draft.wooOrderActions = action.value;
        return;
      case "updateCurrentWooOrderActions":
        draft.currentWooOrderActions = action.value;
        return;
      case "checkActions":
        draft.checkAction = true;
        return;
      case "dontCheckActions":
        draft.checkAction = false;
        return;
      case "clearForm":
        draft.inputs.title.value = "";
        draft.inputs.order_type.value = "";
        draft.inputs.minimum_order_total.value = "";
        draft.inputs.maximum_order_total.value = "";
        draft.inputs.order_turn.value = "";
        draft.inputs.included_products.value = "";
        draft.inputs.excluded_products.value = "";
        draft.inputs.order_status.value = "";
        draft.inputs.action.value = "";
        draft.inputs.woo_order_phonebook.value = "";
        draft.inputs.action_time.value = "";
        draft.inputs.time.value = "";
        draft.inputs.sms_pattern.value = "";
        draft.inputs.sms_message.value = "";
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
            order_type: {
              type: state.inputs.order_type.value,
              minimum_order_total: state.inputs.minimum_order_total.value ?? "",
              maximum_order_total: state.inputs.maximum_order_total.value ?? "",
              order_turn: state.inputs.order_turn.value ?? "",
              included_products: state.inputs.included_products.value ?? "",
              excluded_products: state.inputs.excluded_products.value ?? "",
            },
            order_status: state.inputs.order_status.value,
            action: {
              type: state.inputs.action.value,
              phonebook: state.inputs.woo_order_phonebook.value ?? "",
              mobile_meta_key: state.inputs.mobile_meta_key.value ?? "",
              sms_pattern: state.inputs.sms_pattern.value ?? "",
              sms_message: state.inputs.sms_message.value ?? "",
              action_time: state.inputs.action_time.value ?? "",
              time: state.inputs.time.value ?? "",
            },
          }
        );
        dispatch({ type: "saveRequestFinished" });
        dispatch({ type: "clearForm" });
        dispatch({ type: "checkActions" });
        appDispatch({
          type: "flashMessage",
          value: {
            message: __("Action added successfully.", "farazsms"),
          },
        });
        console.log(newAction);
      } catch (e) {
        console.log(e);
      }
    }

    add_woocommerce_order_action_to_db();
  }

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
          type: "mobile_meta_keyOptions",
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
    dispatch({ type: "cantFetchPhonebooks" });
  }

  function handleAllPhonebooks(phonebooksArrayObject) {
    dispatch({
      type: "phonebookOptions",
      value: phonebooksArrayObject,
    });
  }

  useEffect(() => {
    async function getWoocommerceProducts() {
      try {
        let page = 1;
        let allProducts = [];

        while (true) {
          const res = await AxiosWp.get(
            `/wc/v3/products?per_page=100&page=${page}`
          );
          const products = res.data;

          if (products.length === 0) {
            break; // Exit the loop if no more products are returned
          }
          allProducts = allProducts.concat(products);
          page++;
        }

        const productArray = allProducts.map((product) => ({
          value: product.id,
          label: product.name,
        }));

        console.log(productArray);
        dispatch({ type: "fetchWoocommerceProducts", value: productArray });
      } catch (error) {
        console.log(error);
      }
    }

    getWoocommerceProducts();
  }, []);

  /**
   * Get WooCommerce order actions list from DB
   */
  useEffect(() => {
    async function get_woocommerce_order_actions_from_db() {
      try {
        const res = await AxiosWp.get(
          "/farazsms/v1/get_woocommerce_order_actions_from_db"
        );
        const data = JSON.parse(res.data);
        const parsedData = data.map((obj) => {
          return {
            id: obj.id,
            title: obj.title,
            order_type: JSON.parse(obj.order_type),
            order_status: JSON.parse(obj.order_status),
            action: JSON.parse(obj.action),
          };
        });
        if (parsedData) {
          console.log(parsedData);
          dispatch({
            type: "getWooOrderActions",
            value: parsedData,
          });
          dispatch({
            type: "updateCurrentWooOrderActions",
            value: parsedData,
          });
          dispatch({ type: "dontCheckActions" });
        }
      } catch (error) {
        console.log(error.message);
      }
    }

    get_woocommerce_order_actions_from_db();
  }, [state.checkAction]);

  const handleSyncActions = async () => {
    try {
      dispatch({ type: "checkActions" });
      appDispatch({
        type: "flashMessage",
        value: {
          message: __("Congrats. Actions synced successfully.", "farazsms"),
        },
      });
    } catch (e) {
      console.log(e);
    }
  };

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
          appDispatch({
            type: "flashMessage",
            value: {
              message: __("Congrats. Action deleted successfully.", "farazsms"),
            },
          });
          dispatch({ type: "checkActions" });
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

  const deleteActions = async (actions_ids) => {
    try {
      const res = await AxiosWp.post(
        "/farazsms/v1/delete_woocommerce_order_actions_from_db",
        {
          actions_ids: actions_ids,
        }
      );
      console.log(res);
      dispatch({ type: "checkActions" });
    } catch (e) {
      console.log(e);
    }
  };

  const handleSelectAction = (action_id) => {
    if (selectedActions.includes(action_id)) {
      setSelectedActions(selectedActions.filter((s) => s !== action_id));
    } else {
      setSelectedActions((prev) => [...prev, action_id]);
    }
  };

  const handleDeleteSelectedActions = async () => {
    const isConfirmed = await confirm(
      __("Do you want to delete the selected actions?", "farazsms")
    );

    if (isConfirmed) {
      await deleteActions(selectedActions);
      setSelectedActions([]);
      appDispatch({
        type: "flashMessage",
        value: {
          message: __("Selected Actions deleted successfully.", "farazsms"),
        },
      });
    } else {
      appDispatch({
        type: "flashMessage",
        value: {
          message: __("Canceled. Actions still there.", "farazsms"),
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
                  <th>{__("On Order status", "farazsms")}</th>
                  <th>{__("Action type", "farazsms")}</th>
                  <th>{__("Delete", "farazsms")}</th>
                </tr>
              </thead>
              <tbody>
                {state?.wooOrderActions.map((action, index) => (
                  <tr key={action.id}>
                    <td>
                      <input
                        type="checkbox"
                        checked={selectedActions.includes(action.id)}
                        onChange={() => handleSelectAction(action.id)}
                      />
                    </td>
                    <td>{action.title}</td>
                    <td>{action.order_status.label}</td>
                    <td>{action.action.type.label}</td>
                    <td>
                      <button
                        className="contact-delete"
                        onClick={() => deleteAction(action)}
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
                onClick={handleDeleteSelectedActions}
                disabled={isDeleteDisabled}
              >
                {__("Delete Selected Actions", "farazsms")}
              </button>
              <button className="contact-sync" onClick={handleSyncActions}>
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
