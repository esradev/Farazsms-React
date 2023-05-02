/**
 * Import remote dependencies.
 */
import React, { useState, useEffect, useContext } from "react";
import { useImmerReducer } from "use-immer";
import { __ } from "@wordpress/i18n";

/**
 * Import local dependencies
 */

import DispatchContext from "../DispatchContext";
import SettingsForm from "../views/SettingsForm";
import SectionHeader from "../views/SectionHeader";
import SectionError from "../views/SectionError";
import LoadingSpinner from "../views/LoadingSpinner";
import useFetchOptions from "../hooks/useFetchOptions";
import useSaveOptions from "../hooks/useSaveOptions";

function Membership(props) {
  const appDispatch = useContext(DispatchContext);
  /**
   *
   * First init state.
   *
   */
  const originalState = {
    notUsedPlugins: {
      ...(!props.integratedPlugins.indeedMembershipPro.use && {
        indeedMembershipPro: {
          id: "indeedMembershipPro",
          name: "Indeed Membership Pro",
        },
      }),
      ...(!props.integratedPlugins.paidMembershipsPro.use && {
        paidMembershipsPro: {
          id: "paidMembershipsPro",
          name: "Paid MembershipsPro",
        },
      }),
    },
    inputs: {
      ...(props.integratedPlugins.indeedMembershipPro.use && {
        ihc_send_first_notify: {
          value: "",
          hasErrors: false,
          errorMessage: "",
          onChange: "ihc_send_first_notifyChange",
          name: "ihc_send_first_notify",
          type: "checkbox",
          label: __(
            "Send the first SMS warning of membership expiration?",
            "farazsms"
          ),
          groupTitle: __(
            "Ultimate Membership PRO plugin settings:",
            "farazsms"
          ),
        },
        ihc_send_second_notify: {
          value: "",
          hasErrors: false,
          errorMessage: "",
          onChange: "ihc_send_second_notifyChange",
          name: "ihc_send_second_notify",
          type: "checkbox",
          label: __(
            "Send the second SMS warning of membership expiration?",
            "farazsms"
          ),
        },
        ihc_send_third_notify: {
          value: "",
          hasErrors: false,
          errorMessage: "",
          onChange: "ihc_send_third_notifyChange",
          name: "ihc_send_third_notify",
          type: "checkbox",
          label: __(
            "Send the third SMS warning of membership expiration?",
            "farazsms"
          ),
        },
        ihc_first_notify_msg: {
          value: "",
          hasErrors: false,
          errorMessage: "",
          onChange: "ihc_first_notify_msgChange",
          name: "ihc_first_notify_msg",
          type: "textarea",
          label: __("Membership expiration warning SMS text:", "farazsms"),
          infoTitle: __("Usable variables:", "farazsms"),
          infoBody: __(
            "username %name% | time remaining (to day) %time%",
            "farazsms"
          ),
        },
      }),

      ...(props.integratedPlugins.paidMembershipsPro.use && {
        pmp_send_expire_notify: {
          value: "",
          hasErrors: false,
          errorMessage: "",
          onChange: "pmp_send_expire_notifyChange",
          name: "pmp_send_expire_notify",
          type: "checkbox",
          label: __("Send sms membership expiration?", "farazsms"),
          groupTitle: __("Paid Membership PRO plugin settings:", "farazsms"),
        },
        pmp_expire_notify_msg: {
          value: "",
          hasErrors: false,
          errorMessage: "",
          onChange: "pmp_expire_notify_msgChange",
          name: "pmp_expire_notify_msg",
          type: "textarea",
          label: __("The text of the membership expiration SMS:", "farazsms"),
          infoTitle: __("Usable variables:", "farazsms"),
          infoBody: __("username %display_name%", "farazsms"),
        },
      }),
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
   * ourReduser function to switch between cases.
   *
   * @since 2.0.0
   */
  function ourReduser(draft, action) {
    switch (action.type) {
      case "fetchComplete":
        //Init state values by action.value
        if (props.integratedPlugins.indeedMembershipPro.use) {
          draft.inputs.ihc_send_first_notify.value =
            action.value.ihc_send_first_notify;
          draft.inputs.ihc_send_second_notify.value =
            action.value.ihc_send_second_notify;
          draft.inputs.ihc_send_third_notify.value =
            action.value.ihc_send_third_notify;
          draft.inputs.ihc_first_notify_msg.value =
            action.value.ihc_first_notify_msg;
        }
        if (props.integratedPlugins.paidMembershipsPro.use) {
          draft.inputs.pmp_send_expire_notify.value =
            action.value.pmp_send_expire_notify;
          draft.inputs.pmp_expire_notify_msg.value =
            action.value.pmp_expire_notify_msg;
        }
        draft.isFetching = false;
        return;
      case "cantFetching":
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
   * Get options from DB rest routes
   *
   * @since 2.0.0
   */
  const endpoint = "/farazsms/v1/membership_options";

  useFetchOptions(endpoint, dispatch);

  /**
   * Post options to DB
   *
   * @since 2.0.0
   */
  useSaveOptions(endpoint, state, dispatch, appDispatch);

  if (state.isFetching) return <LoadingSpinner />;

  /**
   * The Membership form created by mapping over originalState.
   * For every value on inputs rendered a SettingsFormInput.
   *
   * @since 2.0.0
   */
  return (
    <div>
      <SectionHeader sectionName={state.sectionName} />
      <div>
        {Object.values(state.notUsedPlugins).map((plugin) => (
          <div key={plugin.name}>
            <SectionError sectionName={plugin.name} />
          </div>
        ))}
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
}

export default Membership;
