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
import LoadingSpinner from "../views/LoadingSpinner";
import AxiosWp from "../function/AxiosWp";

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
        rules: "messageRules",
        name: "message",
        type: "textarea",
        label: __("Message:", "farazsms"),
        required: true,
        infoTitle: __("Usable variables:", "farazsms"),
        infoBody: __(
          "You can use %name% on message content if you want to send sms to subscribers.",
          "farazsms"
        ),
      },
      senderNumber: {
        value: [],
        hasErrors: false,
        errorMessage: "",
        onChange: "senderNumberChange",
        rules: "senderNumberRules",
        name: "senderNumber",
        type: "select",
        label: __("Select sender number:", "farazsms"),
        options: [
          { value: "1", label: __("Servicing number", "farazsms") },
          { value: "2", label: __("Advertising number", "farazsms") },
        ],
        noOptionsMessage: __("No options is available", "farazsms"),
        required: true,
      },
      toSubscribers: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "toSubscribersChange",
        rules: "toSubscribersRules",
        name: "toSubscribers",
        type: "checkbox",
        label: __("Send to newsletter subscribers:", "farazsms"),
      },
      manuallyNumbers: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "manuallyNumbersChange",
        rules: "manuallyNumbersRules",
        name: "manuallyNumbers",
        type: "textarea",
        label: __("Enter numbers manually:", "farazsms"),
        tooltip: __(
          "Separate numbers with English commas without any spaces.",
          "farazsms"
        ),
      },
      phonebooks: {
        value: [],
        hasErrors: false,
        errorMessage: "",
        onChange: "phonebooksChange",
        rules: "phonebooksRules",
        name: "phonebooks",
        type: "select",
        label: __("Select phonebooks:", "farazsms"),
        options: [],
        noOptionsMessage: __("No options is available", "farazsms"),
        isMulti: "isMulti",
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
      case "cantFetching":
        draft.isFetching = false;
        return;
      case "all_phonebooksOptions":
        draft.noPhonebooks = false;
        draft.inputs.phonebooks.options = action.value;
        return;
      case "noPhonebooks":
        draft.noPhonebooks = true;
        return;
      case "messageChange":
        draft.inputs.message.hasErrors = false;
        draft.inputs.message.value = action.value;
        return;
      case "messageRules":
        if (!action.value.trim()) {
          draft.inputs.message.hasErrors = true;
          draft.inputs.message.errorMessage = __(
            "You must provide a message.",
            "farazsms"
          );
        }
        return;
      case "senderNumberChange":
        draft.inputs.senderNumber.hasErrors = false;
        draft.inputs.senderNumber.value = action.value;
        return;
      case "toSubscribersChange":
        draft.inputs.toSubscribers.hasErrors = false;
        draft.inputs.toSubscribers.value = action.value;
        return;
      case "manuallyNumbersChange":
        draft.inputs.manuallyNumbers.hasErrors = false;
        draft.inputs.manuallyNumbers.value = action.value;
        return;
      case "phonebooksChange":
        draft.inputs.phonebooks.hasErrors = false;
        draft.inputs.phonebooks.value = action.value;
        return;
      case "submitOptions":
        if (
          !draft.inputs.message.hasErrors &&
          !draft.inputs.phonebooks.hasErrors &&
          !draft.inputs.toSubscribers.hasErrors &&
          !draft.inputs.manuallyNumbers.hasErrors &&
          !draft.inputs.senderNumber.hasErrors
        ) {
          draft.sendCount++;
        }
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
    async function sendSms() {
      if (
        !state.inputs.manuallyNumbers.value &&
        !state.inputs.phonebooks.value &&
        !state.inputs.toSubscribers.value
      ) {
        appDispatch({
          type: "flashMessage",
          value: {
            type: "error",
            message: __(
              "Please select at least one phonebook or enter manual number or chose send to newsletter subscribers.",
              "farazsms"
            ),
          },
        });
      } else {
        try {
          const res = await AxiosWp.post("/farazsms/v1/send_sms", {
            message: state.inputs.message.value,
            phonebooks: state.inputs.phonebooks.value,
            send_to_subscribers: state.inputs.toSubscribers.value,
            send_fromnum_choice: state.inputs.senderNumber.value.value,
            phones: state.inputs.manuallyNumbers.value,
          });
          if (res.data === "noSubscribers") {
            appDispatch({
              type: "flashMessage",
              value: {
                type: "error",
                message: __(
                  "Sorry. No one is subscriber of newsletter yet.",
                  "farazsms"
                ),
              },
            });
          } else if (res.data === "uncorrectedFormat") {
            appDispatch({
              type: "flashMessage",
              value: {
                type: "error",
                message: __(
                  "Please enter manually numbers in the correct format. separate every number with an english comma.",
                  "farazsms"
                ),
              },
            });
          } else {
            appDispatch({
              type: "flashMessage",
              value: {
                message: __(
                  "Congrats. Message was sent successfully.",
                  "farazsms"
                ),
              },
            });
          }
          console.log(res);
        } catch (e) {
          console.log(e);
        }
      }
    }
    sendSms();
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
                  onBlur={(e) =>
                    dispatch({ type: input.rules, value: e.target.value })
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
