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

function GravityForms(props) {
  const appDispatch = useContext(DispatchContext);
  // Init States
  const originalState = {
    notUsedPlugins: {
      ...(!props.integratedPlugins?.gravityForms?.use && {
        gravityForms: {
          id: "gravityForms",
          name: "Gravity Forms",
        },
      }),
    },
    inputs: {
      title: {
        value: "",
        onChange: "titleChange",
        name: "title",
        type: "text",
        label: __("Title:", "farazsms"),
        infoTitle: __("Info", "farazsms"),
        infoBody: __(
          "To use settings for Gravity Forms you must enable the REST API, by checking the Enable checkbox in forms > settings > REST API > Enable.",
          "farazsms"
        ),
      },
      gf_phonebook: {
        value: [],
        onChange: "gf_phonebookChange",
        name: "gf_phonebook",
        type: "select_phonebook",
        label: __("Select phonebook for Gravity Form:", "farazsms"),
        options: [],
        noOptionsMessage: __("No options is available", "farazsms"),
      },
      gf_forms: {
        value: [],
        onChange: "gf_formsChange",
        name: "gf_forms",
        type: "select",
        label: __("Gravity Form forms:", "farazsms"),
        infoTitle: __("Info", "farazsms"),
        infoBody: __(
          "In this section, you can specify the form you want to register in the Gravity Form phonebook",
          "farazsms"
        ),
        options: [],
        noOptionsMessage: __("No options is available", "farazsms"),
      },
      gf_field: {
        value: [],
        onChange: "gf_fieldChange",
        name: "gf_field",
        type: "select",
        label: __("Gravity Form phone number field:", "farazsms"),
        infoTitle: __("Info", "farazsms"),
        infoBody: __(
          "In this section, you must specify the mobile field, that you want to do the action on it.",
          "farazsms"
        ),
        options: [],
        noOptionsMessage: __("No options is available", "farazsms"),
      },
      gf_name_field: {
        value: [],
        onChange: "gf_name_fieldChange",
        name: "gf_name_field",
        type: "select",
        label: __("Gravity Form name field:", "farazsms"),
        infoTitle: __("Info", "farazsms"),
        infoBody: __(
          "In this section, you must specify the user name field, that you want save on phonebook or want to be on sms message. (use %name% variable in your pattern.)",
          "farazsms"
        ),
        options: [],
        noOptionsMessage: __("No options is available", "farazsms"),
      },
      gf_content_field: {
        value: [],
        onChange: "gf_content_fieldChange",
        name: "gf_content_field",
        type: "select",
        label: __("Gravity Form content field:", "farazsms"),
        infoTitle: __("Info", "farazsms"),
        infoBody: __(
          "In this section, you must specify the user content field, that you want to be on sms message. (use %conctent% variable in your pattern.)",
          "farazsms"
        ),
        options: [],
        noOptionsMessage: __("No options is available", "farazsms"),
      },
      gf_action: {
        value: [],
        onChange: "gf_actionChange",
        name: "gf_action",
        type: "select",
        label: __("Gravity Form action:", "farazsms"),
        infoTitle: __("Info", "farazsms"),
        infoBody: __(
          "In this section, you can specify the action you want to do with the value of the selected fields.",
          "farazsms"
        ),
        options: [
          {
            value: "saveToPhonebook",
            label: __("Save to phonebook", "farazsms"),
          },
          { value: "sendSmsToUser", label: __("Send sms to user", "farazsms") },
          {
            value: "sendSmsToAdmin",
            label: __("Send sms to admin", "farazsms"),
          },
        ],
        noOptionsMessage: __("No options is available", "farazsms"),
      },
      user_pattern_code: {
        value: "",
        onChange: "user_pattern_codeChange",
        name: "user_pattern_code",
        type: "text",
        label: __("User pattern code:", "farazsms"),
        isDependencyUsed: false,
      },
      admin_pattern_code: {
        value: "",
        onChange: "admin_pattern_codeChange",
        name: "admin_pattern_code",
        type: "text",
        label: __("Admin pattern code:", "farazsms"),
        isDependencyUsed: false,
      },
    },
    gfSelectedFormId: "",
    gravityFormsActions: "",
    currentActions: 0,
    isFetching: false,
    isSaving: false,
    sendCount: 0,
    sectionName: __("Gravity Forms", "farazsms"),
  };

  function ourReduser(draft, action) {
    switch (action.type) {
      case "fetchComplete":
        draft.isFetching = false;
        return;
      case "cantFetchPhonebooks":
        draft.isFetching = false;
        return;
      case "phonebookOptions":
        if (props.integratedPlugins?.gravityForms?.use) {
          draft.inputs.gf_phonebook.options = action.value;
        }
        draft.isFetching = false;
        return;
      case "gf_formsOptions":
        if (props.integratedPlugins?.gravityForms?.use) {
          draft.inputs.gf_forms.options = action.value;
        }
        return;
      case "gf_fieldOptions":
        if (
          props.integratedPlugins?.gravityForms?.use &&
          draft.inputs.gf_forms.options
        ) {
          draft.inputs.gf_field.options = action.value;
        }
        return;
      case "gf_name_fieldOptions":
        if (
          props.integratedPlugins?.gravityForms?.use &&
          draft.inputs.gf_forms.options
        ) {
          draft.inputs.gf_name_field.options = action.value;
        }
        return;
      case "gf_content_fieldOptions":
        if (
          props.integratedPlugins?.gravityForms?.use &&
          draft.inputs.gf_forms.options
        ) {
          draft.inputs.gf_content_field.options = action.value;
        }
        return;
      case "titleChange":
        draft.inputs.title.value = action.value;
        return;
      case "gf_phonebookChange":
        draft.inputs.gf_phonebook.value = action.value;
        return;
      case "gf_formsChange":
        draft.inputs.gf_forms.value = action.value;
        draft.gfSelectedFormId = action.value.value;
        draft.sendCount++;
        return;
      case "gf_fieldChange":
        draft.inputs.gf_field.value = action.value;
        return;
      case "gf_name_fieldChange":
        draft.inputs.gf_name_field.value = action.value;
        return;
      case "gf_content_fieldChange":
        draft.inputs.gf_content_field.value = action.value;
        return;
      case "gf_actionChange":
        draft.inputs.gf_action.value = action.value;
        if (action.value.value === "saveToPhonebook") {
          draft.inputs.admin_pattern_code.value = "";
          draft.inputs.user_pattern_code.value = "";
        }

        if (action.value.value === "sendSmsToUser") {
          draft.inputs.user_pattern_code.isDependencyUsed = true;
          draft.inputs.admin_pattern_code.value = "";
        } else {
          draft.inputs.user_pattern_code.isDependencyUsed = false;
        }
        if (action.value.value === "sendSmsToAdmin") {
          draft.inputs.admin_pattern_code.isDependencyUsed = true;
          draft.inputs.user_pattern_code.value = "";
        } else {
          draft.inputs.admin_pattern_code.isDependencyUsed = false;
        }
        return;
      case "user_pattern_codeChange":
        draft.inputs.user_pattern_code.value = action.value;
        return;
      case "admin_pattern_codeChange":
        draft.inputs.admin_pattern_code.value = action.value;
        return;
      case "getGravityFormsActions":
        draft.gravityFormsActions = action.value;
        return;
      case "updateCurrentActions":
        draft.currentActions = action.value;
        return;
      case "formId":
        draft.sendCount++;
        return;
      case "clearForm":
        draft.inputs.title.value = "";
        draft.inputs.gf_phonebook.value = [];
        draft.inputs.gf_forms.value = [];
        draft.inputs.gf_field.value = [];
        draft.inputs.gf_name_field.value = [];
        draft.inputs.gf_content_field.value = [];
        draft.inputs.gf_action.value = [];
        draft.inputs.user_pattern_code.value = "";
        draft.inputs.admin_pattern_code.value = "";

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

    async function add_gravity_forms_action_to_db() {
      try {
        const newAction = await AxiosWp.post(
          "/farazsms/v1/add_gravity_forms_action_to_db",
          {
            title: state.inputs.title.value,
            phonebook_label: state.inputs.gf_phonebook.value.label,
            phonebook_id: state.inputs.gf_phonebook.value.value,
            form_label: state.inputs.gf_forms.value.label,
            form_id: state.inputs.gf_forms.value.value,
            field_label: state.inputs.gf_field.value.label,
            field_id: state.inputs.gf_field.value.value,
            name_field_label: state.inputs.gf_name_field.value.label,
            name_field_id: state.inputs.gf_name_field.value.value,
            content_field_label: state.inputs.gf_content_field.value.label,
            content_field_id: state.inputs.gf_content_field.value.value,
            action_label: state.inputs.gf_action.value.label,
            action_type: state.inputs.gf_action.value.value,
            user_pattern_code: state.inputs.user_pattern_code.value,
            admin_pattern_code: state.inputs.admin_pattern_code.value,
          }
        );
        dispatch({ type: "saveRequestFinished" });
        dispatch({ type: "clearForm" });
        dispatch({
          type: "updateCurrentActions",
          value: state.currentActions + 1,
        });
        console.log(newAction);
      } catch (e) {
        console.log(e);
      }
    }

    add_gravity_forms_action_to_db();
  }

  /**
   * Get Gravity forms from /gf/v2/forms
   *
   * @since 2.0.0
   */
  useEffect(() => {
    async function getGfForms() {
      try {
        const getGfForms = await AxiosWp.get("/gf/v2/forms", {});
        const gfFormsArrayObject = Object.keys(getGfForms.data).map((form) => ({
          value: getGfForms.data[form].id,
          label: getGfForms.data[form].title,
        }));
        console.log(getGfForms);
        dispatch({
          type: "gf_formsOptions",
          value: gfFormsArrayObject,
        });
        dispatch({ type: "fetchComplete" });
      } catch (e) {
        console.log(e);
      }
    }

    getGfForms();
  }, []);

  /**
   * Get Gravity form filed /gf/v2/forms/1/field-filters
   * TODO: the /1/ should be dynamic form id selected from previous input filed
   * @since 2.0.0
   */
  useEffect(() => {
    async function getGfFormsFields() {
      try {
        console.log(state.gfSelectedFormId);
        const getGfFormsFields = await AxiosWp.get(
          "/gf/v2/forms/" + state.gfSelectedFormId + "/field-filters",
          {}
        );
        const gfFormsFieldsArrayObject = Object.keys(getGfFormsFields.data).map(
          (field) => ({
            value: getGfFormsFields.data[field].key,
            label: getGfFormsFields.data[field].text,
          })
        );
        dispatch({
          type: "gf_fieldOptions",
          value: gfFormsFieldsArrayObject,
        });
        dispatch({
          type: "gf_name_fieldOptions",
          value: gfFormsFieldsArrayObject,
        });
        dispatch({
          type: "gf_content_fieldOptions",
          value: gfFormsFieldsArrayObject,
        });
      } catch (e) {
        console.log(e);
      }
    }

    getGfFormsFields();
  }, [state.sendCount]);

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

  /**
   * Get Gravity Forms actions list from DB
   */
  useEffect(() => {
    async function get_gravity_forms_actions_from_db() {
      try {
        const getActions = await AxiosWp.get(
          "/farazsms/v1/get_gravity_forms_actions_from_db"
        );
        dispatch({
          type: "getGravityFormsActions",
          value: JSON.parse(getActions.data),
        });
        dispatch({
          type: "updateCurrentActions",
          value: JSON.parse(getActions.data),
        });
      } catch (e) {
        console.log(e);
      }
    }

    get_gravity_forms_actions_from_db();
  }, [[], state.currentActions]);

  /**
   * Delete Gravity Forms action from DB.
   * @param subscriber
   * @returns {Promise<void>}
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
            "/farazsms/v1/delete_gravity_forms_actions_from_db",
            {
              action_id: action.id,
            }
          );
          dispatch({
            type: "updateCurrentActions",
            value: state.currentActions - 1,
          });
          appDispatch({
            type: "flashMessage",
            value: {
              message: __("Congrats. Action deleted successfully.", "farazsms"),
            },
          });
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

  if (state.isFetching) return <LoadingSpinner />;

  if (props.integratedPlugins?.gravityForms?.use) {
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
        {state.gravityFormsActions && (
          <div className="list-contacts">
            <ol className="contact-list">
              {state.gravityFormsActions.map((action) => (
                <li key={action.id} className="contact-list-item">
                  <div className="contact-details">
                    <p>{action.title}</p>
                  </div>
                  <div className="contact-details">
                    <p>{action.phonebook_label}</p>
                  </div>
                  <div className="contact-details">
                    <p>{action.form_label}</p>
                  </div>
                  <div className="contact-details">
                    <p>{action.field_label}</p>
                  </div>
                  <div className="contact-details">
                    <p>{action.action_label}</p>
                  </div>
                  <button
                    className="contact-delete"
                    onClick={() => {
                      deleteAction(action);
                    }}
                  >
                    Delete
                  </button>
                </li>
              ))}
            </ol>
          </div>
        )}
      </>
    );
  } else {
    return <SectionError sectionName={state.sectionName} />;
  }
}

export default GravityForms;
