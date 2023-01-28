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
import SaveButton from "../views/SaveButton";
import FormInputError from "../views/FormInputError";
import AxiosWp from "../function/AxiosWp";
import SectionHeader from "../views/SectionHeader";

function Comments() {
  const appDispatch = useContext(DispatchContext);
  /**
   *
   * First init state.
   *
   */
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
          "Add the mobile field to the comment submission form?",
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
      comment_phonebook: {
        value: [],
        hasErrors: false,
        errorMessage: "",
        onChange: "comment_phonebookChange",
        id: "comment_phonebook",
        name: "comment_phonebook",
        type: "select",
        label: __("Save the phone number in the phonebook?", "farazsms"),
        rules: "comment_phonebookRules",
        options: [],
        noOptionsMessage: __("No options is available", "farazsms"),
      },
      comment_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "comment_patternChange",
        id: "comment_pattern",
        name: "comment_pattern",
        type: "text",
        label: __("Comment submit pattern code:", "farazsms"),
        rules: "comment_patternRules",
      },
      approved_comment_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "approved_comment_patternChange",
        id: "approved_comment_pattern",
        name: "approved_comment_pattern",
        type: "text",
        label: __("Comment response pattern code:", "farazsms"),
        rules: "approved_comment_patternRules",
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
      notify_admin_for_comment_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "notify_admin_for_comment_patternChange",
        id: "notify_admin_for_comment_pattern",
        name: "notify_admin_for_comment_pattern",
        type: "text",
        label: __("Admin pattern code:", "farazsms"),
        rules: "notify_admin_for_comment_patternRules",
        infoTitle: __("Usable variables:", "farazsms"),
        infoBody: __(
          "Post title: %title% Comment authors name: %name% Comment authors email: %email% Comment link: %link% Comment text: %content%",
          "farazsms"
        ),
      },
    },
    isFetching: true,
    isSaving: false,
    sendCount: 0,
    sectionName: __("Comments", "farazsms"),
  };

  function ourReduser(draft, action) {
    switch (action.type) {
      case "fetchComplete":
        //Init state values by action.value
        draft.inputs.add_mobile_field.value = action.value.add_mobile_field;
        draft.inputs.required_mobile_field.value =
          action.value.required_mobile_field;
        draft.inputs.comment_phonebook.value = action.value.comment_phonebook;
        draft.inputs.comment_pattern.value = action.value.comment_pattern;
        draft.inputs.approved_comment_pattern.value =
          action.value.approved_comment_pattern;
        draft.inputs.notify_admin_for_comment.value =
          action.value.notify_admin_for_comment;
        draft.inputs.notify_admin_for_comment_pattern.value =
          action.value.notify_admin_for_comment_pattern;

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
      case "comment_phonebookChange":
        draft.inputs.comment_phonebook.hasErrors = false;
        draft.inputs.comment_phonebook.value = action.value;
        return;
      case "comment_phonebookOptions":
        draft.inputs.comment_phonebook.options = action.value;
        return;
      case "comment_patternChange":
        draft.inputs.comment_pattern.hasErrors = false;
        draft.inputs.comment_pattern.value = action.value;
        return;
      case "approved_comment_patternChange":
        draft.inputs.approved_comment_pattern.hasErrors = false;
        draft.inputs.approved_comment_pattern.value = action.value;
        return;
      case "notify_admin_for_commentChange":
        draft.inputs.notify_admin_for_comment.hasErrors = false;
        draft.inputs.notify_admin_for_comment.value = action.value;
        return;
      case "notify_admin_for_comment_patternChange":
        draft.inputs.notify_admin_for_comment_pattern.hasErrors = false;
        draft.inputs.notify_admin_for_comment_pattern.value = action.value;
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

  /**
   * Get phonebooks.
   * Used wp_remote_post() from the php, for avoid No 'Access-Control-Allow-Origin' header is present on the requested resource. error when send this request with axios
   * Axios.post("http://ippanel.com/api/select", {uname: "9300410381", pass: "Faraz@2282037154", op: "booklist",},{ headers: { "Content-Type": "application/json" } });
   *
   * @since 2.0.0
   */
  useEffect(() => {
    async function getPhonebooks() {
      try {
        //farazsmsJsObject is declared on class-farazsms-admin.php under admin_enqueue_scripts function
        const phonebooks = await farazsmsJsObject.getphonebooks;
        console.log(phonebooks);
        const phonebooksArrayObject = phonebooks.map(({ id, title }) => ({
          label: title,
          value: id,
        }));
        dispatch({
          type: "comment_phonebookOptions",
          value: phonebooksArrayObject,
        });
        console.log(phonebooksArrayObject);
      } catch (e) {
        console.log(e);
      }
    }
    getPhonebooks();
  }, []);

  /**
   * Get options from DB rest routes
   *
   * @since 2.0.0
   */
  useEffect(() => {
    async function getOptions() {
      try {
        // Get Options from site DB Options table
        const getOptions = await AxiosWp.get("/farazsms/v1/comments_options");
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
       * Then Convert array to key: value pair for send Axios post request to DB.
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
          const postOptions = await AxiosWp.post(
            "/farazsms/v1/comments_options",
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
   * The settings form created by mapping over originalState as the main state.
   * For every value on inputs rendered a SettingsFormInput.
   *
   * @since 2.0.0
   */

  return (
    <div>
      <SectionHeader sectionHeader={state.sectionHeader} />
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

export default Comments;
