/**
 * Import remote dependencies.
 */
import React, { useState, useEffect, useContext } from "react";
import { useImmerReducer } from "use-immer";
import { CSSTransition } from "react-transition-group";
// Used as const not import, for Loco translate plugin compatibility.
const __ = wp.i18n.__;

/**
 * Import local dependencies
 */
import AxiosWp from "./AxiosWp";
import DispatchContext from "../DispatchContext";
import SettingsFormInput from "./SettingsFormInput";

function Aff() {
  const appDispatch = useContext(DispatchContext);
  /**
   *
   * First init state.
   *
   */
  const originalState = {
    inputs: {
      aff_user_mobile_field: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "aff_user_mobile_fieldChange",
        id: "aff_user_mobile_field",
        name: "aff_user_mobile_field",
        type: "select",
        label: __("Select the mobile number custom field:", "farazsms"),
        rules: "aff_user_mobile_fieldRules",
        options: [],
        noOptionsMessage: __("No options is avilable", "farazsms"),
      },
      aff_user_register: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "aff_user_registerChange",
        id: "aff_user_register",
        name: "aff_user_register",
        type: "checkbox",
        label: __("Send sms to user on registration:", "farazsms"),
        rules: "aff_user_registerRules",
      },
      aff_user_register_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "aff_user_register_patternChange",
        id: "aff_user_register_pattern",
        name: "aff_user_register_pattern",
        type: "text",
        label: __("User registration SMS pattern code:", "farazsms"),
        rules: "aff_user_register_patternRules",
      },
      aff_user_new_ref: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "aff_user_new_refChange",
        id: "aff_user_new_ref",
        name: "aff_user_new_ref",
        type: "checkbox",
        label: __("Send sms to user on new referral:", "farazsms"),
        rules: "aff_user_new_refRules",
      },
      aff_user_new_ref_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "aff_user_new_ref_patternChange",
        id: "aff_user_new_ref_pattern",
        name: "aff_user_new_ref_pattern",
        type: "text",
        label: __("New referral SMS pattern code:", "farazsms"),
        rules: "aff_user_new_ref_patternRules",
      },
      aff_user_on_approval: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "aff_user_on_approvalChange",
        id: "aff_user_on_approval",
        name: "aff_user_on_approval",
        type: "checkbox",
        label: __(
          "Confirmation of the cooperation account in sales for user",
          "farazsms"
        ),
        rules: "aff_user_on_approvalRules",
      },
      aff_user_on_approval_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "aff_user_on_approval_patternChange",
        id: "aff_user_on_approval_pattern",
        name: "aff_user_on_approval_pattern",
        type: "text",
        label: __(
          "Account confirmation pattern code for cooperation in sales",
          "farazsms"
        ),
        rules: "aff_user_on_approval_patternRules",
      },
      aff_admin_user_register: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "aff_admin_user_registerChange",
        id: "aff_admin_user_register",
        name: "aff_admin_user_register",
        type: "checkbox",
        label: __("Send sms to admin on registration:", "farazsms"),
        rules: "aff_admin_user_registerRules",
      },
      aff_admin_user_register_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "aff_admin_user_register_patternChange",
        id: "aff_admin_user_register_pattern",
        name: "aff_admin_user_register_pattern",
        type: "text",
        label: __("User registration SMS pattern code:", "farazsms"),
        rules: "aff_admin_user_register_patternRules",
      },
      aff_admin_user_new_ref: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "aff_admin_user_new_refChange",
        id: "aff_admin_user_new_ref",
        name: "aff_admin_user_new_ref",
        type: "checkbox",
        label: __("Send sms to admin on new referral:", "farazsms"),
        rules: "aff_admin_user_new_refRules",
      },
      aff_admin_user_new_ref_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "aff_admin_user_new_ref_patternChange",
        id: "aff_admin_user_new_ref_pattern",
        name: "aff_admin_user_new_ref_pattern",
        type: "text",
        label: __("New referral SMS pattern code:", "farazsms"),
        rules: "aff_admin_user_new_ref_patternRules",
      },
      aff_admin_user_on_approval: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "aff_admin_user_on_approvalChange",
        id: "aff_admin_user_on_approval",
        name: "aff_admin_user_on_approval",
        type: "checkbox",
        label: __(
          "Confirmation of the cooperation account in sales for user",
          "farazsms"
        ),
        rules: "aff_admin_user_on_approvalRules",
      },
      aff_admin_user_on_approval_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "aff_admin_user_on_approval_patternChange",
        id: "aff_admin_user_on_approval_pattern",
        name: "aff_admin_user_on_approval_pattern",
        type: "text",
        label: __(
          "Account confirmation pattern code for cooperation in sales",
          "farazsms"
        ),
        rules: "aff_admin_user_on_approval_patternRules",
        infoTitle: __("Usable variables:", "farazsms"),
        infoBody: __(
          "username %user_login% nickname %user_nicename% email %user_email% display name %display_name% mobile number %user_mobile% referral amount %amount%",
          "farazsms"
        ),
      },
    },
    isFetching: true,
    isSaving: false,
    sendCount: 0,
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
        draft.inputs.aff_user_mobile_field.value =
          action.value.aff_user_mobile_field;
        draft.inputs.aff_user_register.value = action.value.aff_user_register;
        draft.inputs.aff_user_register_pattern.value =
          action.value.aff_user_register_pattern;
        draft.inputs.aff_user_new_ref.value = action.value.aff_user_new_ref;
        draft.inputs.aff_user_new_ref_pattern.value =
          action.value.aff_user_new_ref_pattern;
        draft.inputs.aff_user_on_approval.value =
          action.value.aff_user_on_approval;
        draft.inputs.aff_user_on_approval_pattern.value =
          action.value.aff_user_on_approval_pattern;
        draft.inputs.aff_admin_user_register.value =
          action.value.aff_admin_user_register;
        draft.inputs.aff_admin_user_register_pattern.value =
          action.value.aff_admin_user_register_pattern;
        draft.inputs.aff_admin_user_new_ref.value =
          action.value.aff_admin_user_new_ref;
        draft.inputs.aff_admin_user_new_ref_pattern.value =
          action.value.aff_admin_user_new_ref_pattern;
        draft.inputs.aff_admin_user_on_approval.value =
          action.value.aff_admin_user_on_approval;
        draft.inputs.aff_admin_user_on_approval_pattern.value =
          action.value.aff_admin_user_on_approval_pattern;

        draft.isFetching = false;
        return;
      case "aff_user_mobile_fieldChange":
        draft.inputs.aff_user_mobile_field.hasErrors = false;
        draft.inputs.aff_user_mobile_field.value = action.value;
        return;
      case "aff_user_registerChange":
        draft.inputs.aff_user_register.hasErrors = false;
        draft.inputs.aff_user_register.value = action.value;
        return;
      case "aff_user_register_patternChange":
        draft.inputs.aff_user_register_pattern.hasErrors = false;
        draft.inputs.aff_user_register_pattern.value = action.value;
        return;
      case "aff_user_new_refChange":
        draft.inputs.aff_user_new_ref.hasErrors = false;
        draft.inputs.aff_user_new_ref.value = action.value;
        return;
      case "aff_user_new_ref_patternChange":
        draft.inputs.aff_user_new_ref_pattern.hasErrors = false;
        draft.inputs.aff_user_new_ref_pattern.value = action.value;
        return;
      case "aff_user_on_approvalChange":
        draft.inputs.aff_user_on_approval.hasErrors = false;
        draft.inputs.aff_user_on_approval.value = action.value;
        return;
      case "aff_user_on_approval_patternChange":
        draft.inputs.aff_user_on_approval_pattern.hasErrors = false;
        draft.inputs.aff_user_on_approval_pattern.value = action.value;
        return;
      case "aff_admin_user_registerChange":
        draft.inputs.aff_admin_user_register.hasErrors = false;
        draft.inputs.aff_admin_user_register.value = action.value;
        return;
      case "aff_admin_user_register_patternChange":
        draft.inputs.aff_admin_user_register_pattern.hasErrors = false;
        draft.inputs.aff_admin_user_register_pattern.value = action.value;
        return;
      case "aff_admin_user_new_refChange":
        draft.inputs.aff_admin_user_new_ref.hasErrors = false;
        draft.inputs.aff_admin_user_new_ref.value = action.value;
        return;
      case "aff_admin_user_new_ref_patternChange":
        draft.inputs.aff_admin_user_new_ref_pattern.hasErrors = false;
        draft.inputs.aff_admin_user_new_ref_pattern.value = action.value;
        return;
      case "aff_admin_user_on_approvalChange":
        draft.inputs.aff_admin_user_on_approval.hasErrors = false;
        draft.inputs.aff_admin_user_on_approval.value = action.value;
        return;
      case "aff_admin_user_on_approval_patternChange":
        draft.inputs.aff_admin_user_on_approval_pattern.hasErrors = false;
        draft.inputs.aff_admin_user_on_approval_pattern.value = action.value;
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
        // Use the AxiosWp object to call the /farazsms/v1/farazsms_aff_options
        const getOptions = await AxiosWp.get("/farazsms/v1/aff_options", {});
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
            "/farazsms/v1/aff_options",
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
   * The Aff form created by maping over originalState.
   * For every value on inputs rendered a SettingsFormInput.
   *
   * @since 2.0.0
   */
  return (
    <div>
      <h3 className="p-3 mb-4 border-bottom border-dark bg-light rounded">
        {__(
          "Affiliate wp plugin or Ultimate Affiliate Pro or Yith Woocommerce Affiliate:",
          "farazsms"
        )}
      </h3>
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
    </div>
  );
}

export default Aff;
