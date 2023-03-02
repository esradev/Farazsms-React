/**
 * Import remote dependencies.
 */
import React, { useState, useEffect, useContext } from "react";
import { useImmerReducer } from "use-immer";
import useConfirm from "../hooks/useConfirm";

// Used as const not import, for Loco translate plugin compatibility.
const __ = wp.i18n.__;

/**
 * Import local dependencies
 */
import AxiosWp from "../function/AxiosWp";
import DispatchContext from "../DispatchContext";
import FormInput from "../views/FormInput";
import SaveButton from "../views/SaveButton";
import FormInputError from "../views/FormInputError";
import SectionHeader from "../views/SectionHeader";
import SectionError from "../views/SectionError";
import LoadingSpinner from "../views/LoadingSpinner";

function GravityForms(props) {
  const appDispatch = useContext(DispatchContext);
  // Init States
  const originalState = {
    notUsedPlugins: {
      ...(!props.integratedPlugins.gravityForms.use && {
        gravityForms: {
          id: "gravityForms",
          name: "Gravity Forms",
        },
      }),
    },
    inputs: {
      ...(props.integratedPlugins.gravityForms.use && {
        gf_phonebook: {
          value: [],
          onChange: "gf_phonebookChange",
          name: "gf_phonebook",
          type: "select",
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
        gf_selected_field: {
          value: [],
          onChange: "gf_selected_fieldChange",
          name: "gf_selected_field",
          type: "select",
          label: __("Gravity Form fields:", "farazsms"),
          infoTitle: __("Info", "farazsms"),
          infoBody: __(
            "In this section, you can specify the fields you want to register in the Gravity Form phonebook",
            "farazsms"
          ),
          options: [],
          noOptionsMessage: __("No options is available", "farazsms"),
        },
      }),
    },
    gfSelectedFormId: "",
    gravityFormsActions: "",
    currentActions: 0,
    isFetching: true,
    isSaving: false,
    sendCount: 0,
    sectionName: __("Gravity Forms", "farazsms"),
  };

  function ourReduser(draft, action) {
    switch (action.type) {
      case "fetchComplete":
        //Init state values by action.value
        if (props.integratedPlugins.gravityForms.use) {
          draft.inputs.gf_phonebook.value = action.value.gf_phonebook;
          draft.inputs.gf_forms.value = action.value.gf_forms;
          draft.inputs.gf_selected_field.value = action.value.gf_selected_field;
        }
        draft.isFetching = false;
        return;
      case "cantFetching":
        draft.isFetching = false;
        return;
      case "all_phonebookOptions":
        if (props.integratedPlugins.gravityForms.use) {
          draft.inputs.gf_phonebook.options = action.value;
        }
        return;
      case "gf_formsOptions":
        if (props.integratedPlugins.gravityForms.use) {
          draft.inputs.gf_forms.options = action.value;
        }
        return;
      case "gf_selected_fieldOptions":
        if (
          props.integratedPlugins.gravityForms.use &&
          draft.inputs.gf_forms.options
        ) {
          draft.inputs.gf_selected_field.options = action.value;
        }
        return;
      case "gf_phonebookChange":
        draft.inputs.gf_phonebook.value = action.value;
        return;
      case "gf_formsChange":
        draft.inputs.gf_forms.value = action.value;
        return;
      case "setGfSelectedFormId":
        draft.gfSelectedFormId = action.value;
        return;
      case "gf_selected_fieldChange":
        draft.inputs.gf_selected_field.value = action.value;
        return;
      case "getGravityFormsActions":
        draft.gravityFormsActions = action.value;
        return;
      case "updateCurrentActions":
        draft.currentActions = action.value;
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

  // function handleSubmit(e) {
  //   e.preventDefault();
  //
  //   dispatch({ type: "submitOptions" });
  // }

  function handleSubmit(e) {
    e.preventDefault();

    async function add_new_gravity_forms_action_to_db() {
      try {
        const newAction = await AxiosWp.post(
          "/farazsms/v1/add_new_gravity_forms_action_to_db",
          {
            phonebook_id: 123,
            form_id: 555,
            field_id: 3,
            action: "after-submit",
          }
        );
        console.log(newAction);
      } catch (e) {
        console.log(e);
      }
    }
    add_new_gravity_forms_action_to_db();
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
          "/gf/v2/forms/" + "1" + "/field-filters",
          {}
        );
        const gfFormsFieldsArrayObject = Object.keys(getGfFormsFields.data).map(
          (field) => ({
            value: getGfFormsFields.data[field].key,
            label: getGfFormsFields.data[field].text,
          })
        );
        dispatch({
          type: "gf_selected_fieldOptions",
          value: gfFormsFieldsArrayObject,
        });
      } catch (e) {
        console.log(e);
      }
    }
    getGfFormsFields();
  }, []);

  /**
   * Get options from DB rest routes
   *
   * @since 2.0.0
   */
  useEffect(() => {
    async function getOptions() {
      try {
        const getOptions = await AxiosWp.get(
          "/farazsms/v1/phonebook_options",
          {}
        );
        if (getOptions.data) {
          const optionsJson = JSON.parse(getOptions.data);
          dispatch({ type: "fetchComplete", value: optionsJson });
          dispatch({
            type: "setGfSelectedFormId",
            value: optionsJson.gf_forms.value,
          });
        }
      } catch (e) {
        console.log(e);
        dispatch({ type: "cantFetching" });
      }
    }
    getOptions();
  }, []);

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
          type: "all_phonebookOptions",
          value: phonebooksArrayObject,
        });
      } catch (e) {
        console.log(e);
      }
    }
    getPhonebooks();
  }, []);

  /**
   * Post options to DB
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
            "/farazsms/v1/phonebook_options",
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

  /**
   * Get Gravity Forms action list from DB
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
  }, [[], state.currentSubscribers]);

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
          appDispatch({
            type: "flashMessage",
            value: {
              message: __("Congrats. Action deleted successfully.", "farazsms"),
            },
          });
          console.log(action.id);
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

  return (
    <>
      <SectionHeader sectionName={state.sectionName} />
      <div>
        <div className="container"></div>
        {Object.values(state.notUsedPlugins).map((plugin) => (
          <div key={plugin.id}>
            <SectionError sectionName={plugin.name} />
          </div>
        ))}
        <form onSubmit={handleSubmit}>
          {Object.values(state.inputs).map((input) => (
            <div key={input.name} className={"form-group"}>
              <FormInput
                isMulti={input.isMulti}
                {...input}
                onChange={(selectedOption) =>
                  dispatch({
                    type: input.onChange,
                    value: selectedOption,
                  })
                }
              />
              <FormInputError />
            </div>
          ))}
          <SaveButton isSaving={state.isSaving} />
        </form>
      </div>
      {state.gravityFormsActions && (
        <div className="list-contacts">
          <ol className="contact-list">
            {state.gravityFormsActions.map((action) => (
              <li key={action.id} className="contact-list-item">
                <div className="contact-details">
                  <p>{action.phonebook_id}</p>
                </div>
                <div className="contact-details">
                  <p className="contact-details">{action.action}</p>
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
}

export default GravityForms;
