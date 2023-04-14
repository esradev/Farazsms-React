import React from "react";
import FormInput from "./FormInput";
import FormInputError from "./FormInputError";
import SaveButton from "./SaveButton";
import SelectPhonebook from "./SelectPhonebook";

function SettingsForm({
  dispatchNoPhonebooks,
  dispatchAllPhonebooks,
  inputs,
  handleSubmit,
  dispatch,
  isSaving,
  buttonText,
}) {
  return (
    <form onSubmit={handleSubmit}>
      {Object.values(inputs).map((input) =>
        input.isDependencyUsed === false ? (
          <></>
        ) : (
          <div
            key={input.name}
            className={
              input.type === "checkbox" ? "toggle-control" : "form-group"
            }
          >
            {input.type === "select_phonebook" ? (
              <SelectPhonebook
                options={input.options}
                dispatchNoPhonebooks={dispatchNoPhonebooks}
                dispatchAllPhonebooks={dispatchAllPhonebooks}
              />
            ) : (
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
                onBlur={(e) =>
                  dispatch({ type: input.rules, value: e.target.value })
                }
              />
            )}

            <FormInputError />
          </div>
        )
      )}
      <SaveButton isSaving={isSaving} buttonText={buttonText} />
    </form>
  );
}

export default SettingsForm;
