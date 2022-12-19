import React, { useState, useEffect, useContext } from "react";
import { useImmerReducer } from "use-immer";
import Axios from "axios";
// Used as const not import, for Loco translate plugin compatibility.
const __ = wp.i18n.__;

import DispatchContext from "../DispatchContext";
import SettingsFormInput from "./SettingsFormInput";

function Comments() {
  const appDispatch = useContext(DispatchContext);
  // Init States
  const originalState = {
    inputs: {
      add_mobile_field: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "add_mobile_fieldChange",
        id: "add_mobile_field",
        name: "add_mobile_field",
        type: "checkbox",
        label: __(
          "Add the mobile field to the comment submmition form?",
          "farazsms"
        ),
        rules: "add_mobile_fieldRules",
      },
      required_mobile_field: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "required_mobile_fieldChange",
        id: "required_mobile_field",
        name: "required_mobile_field",
        type: "checkbox",
        label: __(
          "Is the mobile number field in comments mandatory?",
          "farazsms"
        ),
        rules: "required_mobile_fieldRules",
      },
      comment_p: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "comment_pChange",
        id: "comment_p",
        name: "comment_p",
        type: "text",
        label: __("Comment submit pattern code:", "farazsms"),
        rules: "comment_pRules",
      },
      approved_comment_p: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "approved_comment_pChange",
        id: "approved_comment_p",
        name: "approved_comment_p",
        type: "text",
        label: __("Comment response pattern code:", "farazsms"),
        rules: "approved_comment_pRules",
      },
      notify_admin_for_comment: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "notify_admin_for_commentChange",
        id: "notify_admin_for_comment",
        name: "notify_admin_for_comment",
        type: "checkbox",
        label: __(
          "Send notification SMS to admin when a comment add to site?",
          "farazsms"
        ),
        rules: "notify_admin_for_commentRules",
      },
    },
    isFetching: true,
    isSaving: false,
    sendCount: 0,
  };

  function ourReduser(draft, action) {
    switch (action.type) {
      case "fetchComplete":
        //Init state values by action.value
        draft.inputs.add_mobile_field.value = action.value.add_mobile_field;
        draft.inputs.required_mobile_field.value =
          action.value.required_mobile_field;
        draft.inputs.comment_p.value = action.value.comment_p;
        draft.inputs.approved_comment_p.value = action.value.approved_comment_p;
        draft.inputs.notify_admin_for_comment.value =
          action.value.notify_admin_for_comment;

        draft.isFetching = false;
        return;

      case "add_mobile_fieldChange":
        draft.inputs.add_mobile_field.hasErrors = false;
        draft.inputs.add_mobile_field.value = action.value;
        return;
      case "required_mobile_fieldChange":
        draft.inputs.required_mobile_field.hasErrors = false;
        draft.inputs.required_mobile_field.value = action.value;
        return;
      case "comment_pChange":
        draft.inputs.comment_p.hasErrors = false;
        draft.inputs.comment_p.value = action.value;
        return;
      case "approved_comment_pChange":
        draft.inputs.approved_comment_p.hasErrors = false;
        draft.inputs.approved_comment_p.value = action.value;
        return;
      case "notify_admin_for_commentChange":
        draft.inputs.notify_admin_for_comment.hasErrors = false;
        draft.inputs.notify_admin_for_comment.value = action.value;
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

  function handleSubmit(e) {
    e.preventDefault();
    //Set every input to the state with dispatch function.
    Object.values(state.inputs).map((input) => {
      dispatch({ type: input.rules, value: input.value });
    });

    dispatch({ type: "submitOptions" });
  }

  useEffect(() => {
    async function getOptions() {
      try {
        // Get Options from site DB Options table
        const getOptions = await Axios.get(
          "http://faraz-sms.local/wp-json/farazsms/v1/farazsms_comments_options"
        );
        if (getOptions.data) {
          const optsionsJson = JSON.parse(getOptions.data);
          console.log(optsionsJson);
          dispatch({ type: "fetchComplete", value: optsionsJson });
        }
      } catch (e) {
        console.log(e);
      }
    }
    getOptions();
  }, []);

  useEffect(() => {
    if (state.sendCount) {
      /**
       * Get options values and set "name: value" in an array.
       * Then Convert array to key: value pair for send Axios.post request to DB.
       * @return Object with arrays.
       */

      const optsionsArray = Object.values(state.inputs).map(
        ({ value, name }) => [name, value]
      );
      const optionsJsonForPost = Object.fromEntries(optsionsArray);
      console.log(optionsJsonForPost);

      dispatch({ type: "saveRequestStarted" });
      async function postOptions() {
        try {
          // Post Options from site DB Options table
          const postOptions = await Axios.post(
            "http://faraz-sms.local/wp-json/farazsms/v1/farazsms_comments_options",
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
   * The settings form created by maping over originalState as the main state.
   * For every value on inputs rendered a SettingsFormInput.
   *
   * @since 2.0.0
   */

  return (
    <div>
      <h3>{__("Login Notify Settings:", "farazsms")}</h3>
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
              {input.hasErrors && (
                <div className="alert alert-danger small liveValidateMessage">
                  {input.errorMessage}
                </div>
              )}
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

export default Comments;
