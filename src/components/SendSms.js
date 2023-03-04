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
import SectionHeader from "../views/SectionHeader";
import LoadingSpinner from "../views/LoadingSpinner";

function SendSms(props) {
  const appDispatch = useContext(DispatchContext);
  // Init States
  const originalState = {
    inputs: {
      message: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "messageChange",
        name: "message",
        type: "textarea",
        label: __("Message:", "farazsms"),
      },
      toSubscribers: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "toSubscribersChange",
        name: "toSubscribers",
        type: "checkbox",
        label: __("Send to newsletter subscribers:", "farazsms"),
      },
      manuallyNumbers: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "manuallyNumbersChange",
        name: "manuallyNumbers",
        type: "textarea",
        label: __("Enter numbers manually:", "farazsms"),
        tooltip: __("Separate numbers with English commas.", "farazsms"),
      },
      phonebook: {
        value: [],
        hasErrors: false,
        errorMessage: "",
        onChange: "phonebookChange",
        name: "phonebook",
        type: "select",
        label: __("Select phonebook:", "farazsms"),
        options: [],
        noOptionsMessage: __("No options is available", "farazsms"),
      },
      senderNumber: {
        value: [],
        hasErrors: false,
        errorMessage: "",
        onChange: "senderNumberChange",
        name: "senderNumber",
        type: "select",
        label: __("Select sender number:", "farazsms"),
        options: [
          { value: "1", label: __("Servicing number", "farazsms") },
          { value: "2", label: __("Advertising number", "farazsms") },
        ],
        noOptionsMessage: __("No options is available", "farazsms"),
      },
    },
    noPhonebooks: true,
    isFetching: false,
    isSaving: false,
    sendCount: 0,
    sectionName: __("Send Sms", "farazsms"),
  };

  function ourReduser(draft, action) {
    switch (action.type) {
      case "fetchComplete":
        //Init state values by action.value
        draft.inputs.message.value = action.value.message;
        draft.inputs.phonebook.value = action.value.phonebook;
        draft.inputs.toSubscribers.value = action.value.toSubscribers;
        draft.inputs.manuallyNumbers.value = action.value.manuallyNumbers;
        draft.inputs.senderNumber.value = action.value.senderNumber;

        draft.isFetching = false;
        return;
      case "cantFetching":
        draft.isFetching = false;
        return;
      case "all_phonebookOptions":
        draft.noPhonebooks = false;
        draft.inputs.phonebook.options = action.value;
        return;
      case "noPhonebooks":
        draft.noPhonebooks = true;
        return;
      case "messageChange":
        draft.inputs.message.hasErrors = false;
        draft.inputs.message.value = action.value;
        return;
      case "phonebookChange":
        draft.inputs.phonebook.hasErrors = false;
        draft.inputs.phonebook.value = action.value;
        return;
      case "toSubscribersChange":
        draft.inputs.toSubscribers.hasErrors = false;
        draft.inputs.toSubscribers.value = action.value;
        return;
      case "manuallyNumbersChange":
        draft.inputs.manuallyNumbers.hasErrors = false;
        draft.inputs.manuallyNumbers.value = action.value;
        return;
      case "senderNumberChange":
        draft.inputs.senderNumber.hasErrors = false;
        draft.inputs.senderNumber.value = action.value;
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
        if (phonebooks.data.length === 0) {
          dispatch({ type: "noPhonebooks" });
        } else {
          const phonebooksArrayObject = phonebooks.data.map(
            ({ id, title }) => ({
              label: title,
              value: id,
            })
          );
          dispatch({
            type: "all_phonebookOptions",
            value: phonebooksArrayObject,
          });
        }
      } catch (e) {
        console.log(e);
      }
    }
    getPhonebooks();
  }, []);

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
            {__("Send Sms", "farazsms")}
          </button>{" "}
        </form>
      </div>
    </div>
  );
}

export default SendSms;
