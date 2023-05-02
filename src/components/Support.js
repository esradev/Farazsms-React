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
import AxiosWp from "../function/AxiosWp";

function Support(props) {
  const appDispatch = useContext(DispatchContext);
  // Init States
  const originalState = {
    inputs: {
      subject: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "subjectChange",
        name: "subject",
        type: "text",
        label: __("Subject:", "farazsms"),
      },
      message: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "messageChange",
        name: "message",
        type: "textarea",
        label: __("Message:", "farazsms"),
      },
    },
    sectionName: __("Report Issues", "farazsms"),
  };

  function ourReduser(draft, action) {
    switch (action.type) {
      case "subjectChange":
        draft.inputs.subject.hasErrors = false;
        draft.inputs.subject.value = action.value;
        return;
      case "messageChange":
        draft.inputs.message.hasErrors = false;
        draft.inputs.message.value = action.value;
        return;
      case "clearForm":
        draft.inputs.subject.value = "";
        draft.inputs.message.value = "";
    }
  }

  const [state, dispatch] = useImmerReducer(ourReduser, originalState);

  /**
   * Report issues to server.
   */
  const handleSubmit = async (e) => {
    e.preventDefault();

    async function reportIssues() {
      try {
        await AxiosWp.post("/farazsms/v1/send_feedback_message", {
          subject: state.inputs.subject.value,
          message: state.inputs.message.value,
        });
        appDispatch({
          type: "flashMessage",
          value: {
            message: __(
              "Congrats. Report Issues Posted successfully.",
              "farazsms"
            ),
          },
        });
        dispatch({ type: "clearForm" });
      } catch (e) {
        console.log(e);
      }
    }

    reportIssues();
  };

  /**
   * The settings form created by mapping over originalState as the main state.
   * For every value on inputs rendered a SettingsFormInput.
   *
   * @since 2.0.0
   */
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
          buttonText={__("Report Issues", "farazsms")}
        />
      </div>
    </div>
  );
}

export default Support;
