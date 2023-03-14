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
import LoadingSpinner from "../views/LoadingSpinner";

function Comments() {
  const appDispatch = useContext(DispatchContext);
  /**
   *
   * First init state.
   *
   */
  const originalState = {
    inputs: {
      disable_email_filed: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "disable_email_filedChange",
        name: "disable_email_filed",
        type: "checkbox",
        label: __("Remove email filed from comment form?", "farazsms"),
      },
      disable_website_filed: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "disable_website_filedChange",
        name: "disable_website_filed",
        type: "checkbox",
        label: __("Remove website filed from comment form?", "farazsms"),
      },
      disable_cookies: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "disable_cookiesChange",
        name: "disable_cookies",
        type: "checkbox",
        label: __("Remove cookies checkbox from comment form?", "farazsms"),
      },
      add_mobile_field: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "add_mobile_fieldChange",
        name: "add_mobile_field",
        type: "checkbox",
        label: __(
          "Add the mobile field to the comment submission form?",
          "farazsms"
        ),
      },
      required_mobile_field: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "required_mobile_fieldChange",
        name: "required_mobile_field",
        type: "checkbox",
        label: __(
          "Is the mobile number field in comments mandatory?",
          "farazsms"
        ),
        isDependencyUsed: false,
      },
      comment_phonebook: {
        value: [],
        hasErrors: false,
        errorMessage: "",
        onChange: "comment_phonebookChange",
        name: "comment_phonebook",
        type: "select",
        label: __("Save the phone number in the phonebook?", "farazsms"),
        options: [],
        noOptionsMessage: __("No options is available", "farazsms"),
        isDependencyUsed: false,
      },
      comment_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "comment_patternChange",
        name: "comment_pattern",
        type: "text",
        label: __("Comment submit pattern code:", "farazsms"),
        isDependencyUsed: false,
      },
      approved_comment_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "approved_comment_patternChange",
        name: "approved_comment_pattern",
        type: "text",
        label: __("Comment response pattern code:", "farazsms"),
        isDependencyUsed: false,
      },
      notify_admin_for_comment: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "notify_admin_for_commentChange",
        name: "notify_admin_for_comment",
        type: "checkbox",
        label: __(
          "Send notification SMS to admin when a comment add to site?",
          "farazsms"
        ),
      },
      notify_admin_for_comment_pattern: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "notify_admin_for_comment_patternChange",
        name: "notify_admin_for_comment_pattern",
        type: "text",
        label: __("Admin pattern code:", "farazsms"),
        infoTitle: __("Usable variables:", "farazsms"),
        infoBody: __(
          "Post title: %title% Comment authors name: %name% Comment authors email: %email% Comment link: %link% Comment text: %content%",
          "farazsms"
        ),
        isDependencyUsed: false,
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
        // Init state values by action.value
        draft.inputs.disable_email_filed.value =
          action.value.disable_email_filed;
        draft.inputs.disable_website_filed.value =
          action.value.disable_website_filed;
        draft.inputs.disable_cookies.value = action.value.disable_cookies;
        draft.inputs.add_mobile_field.value = action.value.add_mobile_field;
        if (action.value.add_mobile_field === true) {
          draft.inputs.required_mobile_field.isDependencyUsed = true;
          draft.inputs.comment_phonebook.isDependencyUsed = true;
          draft.inputs.comment_pattern.isDependencyUsed = true;
          draft.inputs.approved_comment_pattern.isDependencyUsed = true;
        } else {
          draft.inputs.required_mobile_field.isDependencyUsed = false;
          draft.inputs.comment_phonebook.isDependencyUsed = false;
          draft.inputs.comment_pattern.isDependencyUsed = false;
          draft.inputs.approved_comment_pattern.isDependencyUsed = false;
        }
        draft.inputs.required_mobile_field.value =
          action.value.required_mobile_field;
        draft.inputs.comment_phonebook.value = action.value.comment_phonebook;
        draft.inputs.comment_pattern.value = action.value.comment_pattern;
        draft.inputs.approved_comment_pattern.value =
          action.value.approved_comment_pattern;
        draft.inputs.notify_admin_for_comment.value =
          action.value.notify_admin_for_comment;
        if (action.value.notify_admin_for_comment === true) {
          draft.inputs.notify_admin_for_comment_pattern.isDependencyUsed = true;
        } else {
          draft.inputs.notify_admin_for_comment_pattern.isDependencyUsed = false;
        }
        draft.inputs.notify_admin_for_comment_pattern.value =
          action.value.notify_admin_for_comment_pattern;

        draft.isFetching = false;
        return;
      case "cantFetching":
        draft.isFetching = false;
        return;
      case "add_mobile_fieldChange":
        draft.inputs.add_mobile_field.hasErrors = false;
        draft.inputs.add_mobile_field.value = action.value;
        if (action.value === true) {
          draft.inputs.required_mobile_field.isDependencyUsed = true;
          draft.inputs.comment_phonebook.isDependencyUsed = true;
          draft.inputs.comment_pattern.isDependencyUsed = true;
          draft.inputs.approved_comment_pattern.isDependencyUsed = true;
        } else {
          draft.inputs.required_mobile_field.isDependencyUsed = false;
          draft.inputs.comment_phonebook.isDependencyUsed = false;
          draft.inputs.comment_pattern.isDependencyUsed = false;
          draft.inputs.approved_comment_pattern.isDependencyUsed = false;
        }
        return;
      case "disable_email_filedChange":
        draft.inputs.disable_email_filed.hasErrors = false;
        draft.inputs.disable_email_filed.value = action.value;
        return;
      case "disable_website_filedChange":
        draft.inputs.disable_website_filed.hasErrors = false;
        draft.inputs.disable_website_filed.value = action.value;
        return;
      case "disable_cookiesChange":
        draft.inputs.disable_cookies.hasErrors = false;
        draft.inputs.disable_cookies.value = action.value;
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
        if (action.value === true) {
          draft.inputs.notify_admin_for_comment_pattern.isDependencyUsed = true;
        } else {
          draft.inputs.notify_admin_for_comment_pattern.isDependencyUsed = false;
        }
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
      case "saveRequestFinished":
        draft.isSaving = false;
        return;
    }
  }

  const [state, dispatch] = useImmerReducer(ourReduser, originalState);

  function handleSubmit(e) {
    e.preventDefault();
    dispatch({ type: "submitOptions" });
  }

  /**
   * Get phonebooks.
   * Used wp_remote_post() from the php, for avoid No 'Access-Control-Allow-Origin' header is present on the requested resource. error when send this request with axios
   *
   * @since 2.0.0
   */
  useEffect(() => {
    async function getPhonebooks() {
      try {
        //farazsmsJsObject is declared on class-farazsms-settings.php under admin_enqueue_scripts function
        const phonebooks = await farazsmsJsObject.getPhonebooks;
        const phonebooksArrayObject = phonebooks.data.map(({ id, title }) => ({
          label: title,
          value: id,
        }));
        dispatch({
          type: "comment_phonebookOptions",
          value: phonebooksArrayObject,
        });
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
        const getOptions = await AxiosWp.get("/farazsms/v1/comments_options");
        if (getOptions.data) {
          const optionsJson = JSON.parse(getOptions.data);
          dispatch({ type: "fetchComplete", value: optionsJson });
        }
      } catch (e) {
        dispatch({ type: "cantFetching" });
        console.log(e);
      }
    }

    getOptions();
  }, []);

  /**
   * Post options to DB.
   *
   * @since 2.0.0
   */
  useEffect(() => {
    if (state.sendCount) {
      const optionsArray = Object.values(state.inputs).map(
        ({ value, name }) => [name, value]
      );
      const optionsJsonForPost = Object.fromEntries(optionsArray);

      dispatch({ type: "saveRequestStarted" });

      async function postOptions() {
        try {
          // Post Options from site DB Options table
          const postOptions = await AxiosWp.post(
            "/farazsms/v1/comments_options",
            optionsJsonForPost
          );
          dispatch({ type: "saveRequestFinished" });
          appDispatch({
            type: "flashMessage",
            value: {
              message: __(
                "Congrats. Form was updated successfully.",
                "farazsms"
              ),
            },
          });
        } catch (e) {
          console.log(e);
        }
      }

      postOptions();
    }
  }, [state.sendCount]);

  if (state.isFetching) return <LoadingSpinner />;

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
        <form onSubmit={handleSubmit}>
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
          <SaveButton isSaving={state.isSaving} />
        </form>
      </div>
    </div>
  );
}

export default Comments;
