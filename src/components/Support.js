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
import DispatchContext from "../DispatchContext";
import FormInput from "../views/FormInput";
import FormInputError from "../views/FormInputError";
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
  const reportIssues = async (e) => {
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
        <form onSubmit={reportIssues}>
          {Object.values(state.inputs).map((input) =>
            input.isDependencyUsed === false ? (
              <></>
            ) : (
              <div
                key={input.name}
                className={
                  input.type === "checkbox" ? "toggle-control" : "form-group"
                }
              >
                <FormInput
                  isMulti={input.isMulti}
                  {...input}
                  value={input.value}
                  checked={input.value}
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
                />
                <FormInputError />
              </div>
            )
          )}
          <button
            type="submit"
            className="btn btn-primary mt-3"
            disabled={state.isSaving}
          >
            {__("Report Issues", "farazsms")}
          </button>{" "}
        </form>
      </div>
    </div>
  );
}

export default Support;
