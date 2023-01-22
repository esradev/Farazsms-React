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
import AxiosWp from "./AxiosWp";
import DispatchContext from "../DispatchContext";
import FormInput from "./FormInput";
import SaveButton from "./SaveButton";
import FormInputError from "./FormInputError";
import SectionHeader from "./SectionHeader";

function Membership() {
  const appDispatch = useContext(DispatchContext);
  /**
   *
   * First init state.
   *
   */
  const originalState = {
    inputs: {
      ihc_send_first_notify: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "ihc_send_first_notifyChange",
        id: "ihc_send_first_notify",
        name: "ihc_send_first_notify",
        type: "checkbox",
        label: __(
          "Send the first SMS warning of membership expiration?",
          "farazsms"
        ),
        groupTitle: __("Ultimate Membership PRO plugin settings:", "farazsms"),
        rules: "ihc_send_first_notifyRules",
      },
      ihc_send_second_notify: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "ihc_send_second_notifyChange",
        id: "ihc_send_second_notify",
        name: "ihc_send_second_notify",
        type: "checkbox",
        label: __(
          "Send the second SMS warning of membership expiration?",
          "farazsms"
        ),
        rules: "ihc_send_second_notifyRules",
      },
      ihc_send_third_notify: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "ihc_send_third_notifyChange",
        id: "ihc_send_third_notify",
        name: "ihc_send_third_notify",
        type: "checkbox",
        label: __(
          "Send the third SMS warning of membership expiration?",
          "farazsms"
        ),
        rules: "ihc_send_third_notifyRules",
      },
      ihc_first_notify_msg: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "ihc_first_notify_msgChange",
        id: "ihc_first_notify_msg",
        name: "ihc_first_notify_msg",
        type: "textarea",
        label: __("Membership expiration warning SMS text:", "farazsms"),
        rules: "ihc_first_notify_msgRules",
        infoTitle: __("Usable variables:", "farazsms"),
        infoBody: __(
          "username %name% | time remaining (to day) %time%",
          "farazsms"
        ),
      },
      pmp_send_expire_notify: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "pmp_send_expire_notifyChange",
        id: "pmp_send_expire_notify",
        name: "pmp_send_expire_notify",
        type: "checkbox",
        label: __("Send SMS membership expiration?", "farazsms"),
        rules: "pmp_send_expire_notifyRules",
        groupTitle: __("Paid Membership PRO plugin settings:", "farazsms"),
      },
      pmp_expire_notify_msg: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "pmp_expire_notify_msgChange",
        id: "pmp_expire_notify_msg",
        name: "pmp_expire_notify_msg",
        type: "textarea",
        label: __("The text of the membership expiration SMS:", "farazsms"),
        rules: "pmp_expire_notify_msgRules",
        infoTitle: __("Usable variables:", "farazsms"),
        infoBody: __("username %display_name%", "farazsms"),
      },
    },
    isFetching: true,
    isSaving: false,
    sendCount: 0,
    sectionName: __(
      "Ultimate Membership Pro and Paid Memberships Pro",
      "farazsms"
    ),
  };

  /**
   *
   * ourReduser function to switch bettwen cases.
   *
   * @since 2.0.0
   */
  function ourReduser(draft, action) {
    switch (action.type) {
      case "fetchComplete":
        //Init state values by action.value
        draft.inputs.ihc_send_first_notify.value =
          action.value.ihc_send_first_notify;
        draft.inputs.ihc_send_second_notify.value =
          action.value.ihc_send_second_notify;
        draft.inputs.ihc_send_third_notify.value =
          action.value.ihc_send_third_notify;
        draft.inputs.ihc_first_notify_msg.value =
          action.value.ihc_first_notify_msg;
        draft.inputs.pmp_send_expire_notify.value =
          action.value.pmp_send_expire_notify;
        draft.inputs.pmp_expire_notify_msg.value =
          action.value.pmp_expire_notify_msg;
        draft.isFetching = false;
        return;
      case "ihc_send_first_notifyChange":
        draft.inputs.ihc_send_first_notify.hasErrors = false;
        draft.inputs.ihc_send_first_notify.value = action.value;
        return;
      case "ihc_send_second_notifyChange":
        draft.inputs.ihc_send_second_notify.hasErrors = false;
        draft.inputs.ihc_send_second_notify.value = action.value;
        return;
      case "ihc_send_third_notifyChange":
        draft.inputs.ihc_send_third_notify.hasErrors = false;
        draft.inputs.ihc_send_third_notify.value = action.value;
        return;
      case "ihc_first_notify_msgChange":
        draft.inputs.ihc_first_notify_msg.hasErrors = false;
        draft.inputs.ihc_first_notify_msg.value = action.value;
        return;
      case "pmp_send_expire_notifyChange":
        draft.inputs.pmp_send_expire_notify.hasErrors = false;
        draft.inputs.pmp_send_expire_notify.value = action.value;
        return;
      case "pmp_expire_notify_msgChange":
        draft.inputs.pmp_expire_notify_msg.hasErrors = false;
        draft.inputs.pmp_expire_notify_msg.value = action.value;
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

  /**
   *
   * HandelSubmit function
   *
   * @since 2.0.0
   */
  function handleSubmit(e) {
    e.preventDefault();
    //Set every input to the state with dispatch function.
    Object.values(state.inputs).map((input) => {
      dispatch({ type: input.rules, value: input.value });
    });

    dispatch({ type: "submitOptions" });
  }

  /**
   *
   * Get Aff options from DB on Aff component loaded
   *
   * @since 2.0.0
   */
  useEffect(() => {
    async function getOptions() {
      try {
        // Use the AxiosWp object to call the /farazsms/v1/farazsms_membership_options
        const getOptions = await AxiosWp.get(
          "/farazsms/v1/membership_options",
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

  /**
   *
   * Save Aff options on DB when saveRequestFininshed = true
   *
   * @since 2.0.0
   */
  useEffect(() => {
    if (state.sendCount) {
      /**
       *
       * Get options values and set "name: value" in an array.
       * Then Convert array to key: value pair for send Axios.post request to DB.
       *
       * @return Object with arrays.
       */
      const optsionsArray = Object.values(state.inputs).map(
        ({ value, name }) => [name, value]
      );
      const optionsJsonForPost = Object.fromEntries(optsionsArray);
      console.log(optionsJsonForPost);

      dispatch({ type: "saveRequestStarted" });
      // postOptions function for save options on DB
      async function postOptions() {
        try {
          // Post Options from site DB Options table
          const postOptions = await AxiosWp.post(
            "/farazsms/v1/membership_options",
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
   *
   * The Membership form created by maping over originalState.
   * For every value on inputs rendered a SettingsFormInput.
   *
   * @since 2.0.0
   */
  return (
    <div>
      <SectionHeader sectionName={state.sectionName} />
      <div>
        <form onSubmit={handleSubmit}>
          {Object.values(state.inputs).map((input) => (
            <div
              key={input.id}
              className={
                input.type === "checkbox" ? "toggle-control" : "form-group"
              }
            >
              <FormInput
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
}

export default Membership;
