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
import SettingsForm from "../views/SettingsForm";
import SectionHeader from "../views/SectionHeader";
import SectionError from "../views/SectionError";
import LoadingSpinner from "../views/LoadingSpinner";
import useFetchOptions from "../hooks/useFetchOptions";
import useSaveOptions from "../hooks/useSaveOptions";

function Aff(props) {
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
        name: "aff_user_mobile_field",
        type: "select",
        label: __("Select the mobile number custom field:", "farazsms"),
        options: [],
        noOptionsMessage: __("No options is available", "farazsms"),
        groupTitle: __("Users settings:", "farazsms"),
      },
      aff_user_register: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "aff_user_registerChange",
        name: "aff_user_register",
        type: "checkbox",
        label: __("Send sms to user on registration:", "farazsms"),
      },
      aff_user_register_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "aff_user_register_patternChange",
        name: "aff_user_register_pattern",
        type: "text",
        label: __("User registration SMS pattern code:", "farazsms"),
      },
      aff_user_new_ref: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "aff_user_new_refChange",
        name: "aff_user_new_ref",
        type: "checkbox",
        label: __("Send sms to user on new referral:", "farazsms"),
      },
      aff_user_new_ref_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "aff_user_new_ref_patternChange",
        name: "aff_user_new_ref_pattern",
        type: "text",
        label: __("New referral SMS pattern code:", "farazsms"),
      },
      aff_user_on_approval: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "aff_user_on_approvalChange",
        name: "aff_user_on_approval",
        type: "checkbox",
        label: __(
          "Confirmation of the cooperation account in sales for user",
          "farazsms"
        ),
      },
      aff_user_on_approval_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "aff_user_on_approval_patternChange",
        name: "aff_user_on_approval_pattern",
        type: "text",
        label: __(
          "Account confirmation pattern code for cooperation in sales",
          "farazsms"
        ),
      },
      aff_admin_user_register: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "aff_admin_user_registerChange",
        name: "aff_admin_user_register",
        type: "checkbox",
        label: __("Send sms to admin on registration:", "farazsms"),
        groupTitle: __("Admin settings:", "farazsms"),
      },
      aff_admin_user_register_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "aff_admin_user_register_patternChange",
        name: "aff_admin_user_register_pattern",
        type: "text",
        label: __("User registration SMS pattern code:", "farazsms"),
      },
      aff_admin_user_new_ref: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "aff_admin_user_new_refChange",
        name: "aff_admin_user_new_ref",
        type: "checkbox",
        label: __("Send sms to admin on new referral:", "farazsms"),
      },
      aff_admin_user_new_ref_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "aff_admin_user_new_ref_patternChange",
        name: "aff_admin_user_new_ref_pattern",
        type: "text",
        label: __("New referral SMS pattern code:", "farazsms"),
      },
      aff_admin_user_on_approval: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "aff_admin_user_on_approvalChange",
        name: "aff_admin_user_on_approval",
        type: "checkbox",
        label: __(
          "Confirmation of the cooperation account in sales for user",
          "farazsms"
        ),
      },
      aff_admin_user_on_approval_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "aff_admin_user_on_approval_patternChange",
        name: "aff_admin_user_on_approval_pattern",
        type: "text",
        label: __(
          "Account confirmation pattern code for cooperation in sales",
          "farazsms"
        ),
        infoTitle: __("Usable variables:", "farazsms"),
        infoBody: __(
          "username %user_login% nickname %user_nickname% email %user_email% display name %display_name% mobile number %user_mobile% referral amount %amount%",
          "farazsms"
        ),
      },
    },
    isFetching: true,
    isSaving: false,
    sendCount: 0,
    sectionName: __(
      "Affiliate wp plugin or Ultimate Affiliate Pro or Yith Woocommerce Affiliate",
      "farazsms"
    ),
  };

  /**
   *
   * ourReduser function to switch between cases.
   *
   * @since 2.0.0
   */
  function ourReduser(draft, action) {
    switch (action.type) {
      case "fetchComplete":
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
      case "cantFetching":
        draft.isFetching = false;
        return;
      case "aff_user_mobile_fieldChange":
        draft.inputs.aff_user_mobile_field.hasErrors = false;
        draft.inputs.aff_user_mobile_field.value = action.value;
        return;
      case "aff_user_mobile_fieldOptions":
        draft.inputs.aff_user_mobile_field.options = action.value;
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
      case "saveRequestFinished":
        draft.isSaving = false;
        return;
    }
  }

  const [state, dispatch] = useImmerReducer(ourReduser, originalState);

  /**
   * HandelSubmit function
   *
   * @since 2.0.0
   */
  function handleSubmit(e) {
    e.preventDefault();
    dispatch({ type: "submitOptions" });
  }

  /**
   * Get options from DB rest routes
   *
   * @since 2.0.0
   */
  const endpoint = "/farazsms/v1/aff_options";

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
          type: "aff_user_mobile_fieldOptions",
          value: usermetaArrayObject,
        });
      } catch (e) {
        console.log(e);
      }
    }

    getUsermeta();
  }, []);

  if (state.isFetching) return <LoadingSpinner />;

  /**
   * The Aff form created by mapping over originalState.
   * For every value on inputs rendered a SettingsFormInput.
   *
   * @since 2.0.0
   */
  if (
    props.integratedPlugins.affiliateWp.use ||
    props.integratedPlugins.indeedAffiliatePro.use ||
    props.integratedPlugins.yithWoocommerceAffiliates.use
  ) {
    return (
      <div>
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
      </div>
    );
  } else {
    return <SectionError sectionName={state.sectionName} />;
  }
}

export default Aff;
