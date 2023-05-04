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
              <>
                <div className="formInput" key={input.id}>
                  <label htmlFor={input.id} className="mb-1 form-control-label">
                    {input.label}
                    <SelectPhonebook
                      isMulti={input?.isMulti}
                      value={input.value}
                      options={input.options}
                      onChange={(selectedOption) =>
                        dispatch({
                          type: input.onChange,
                          value: selectedOption,
                        })
                      }
                      dispatchNoPhonebooks={dispatchNoPhonebooks}
                      dispatchAllPhonebooks={dispatchAllPhonebooks}
                    />
                  </label>
                </div>
                {input.infoTitle && (
                  <div className="container">
                    <div className="container card bg-info mb-3 p-0">
                      <div className="card-body">
                        <h5 className="card-title">{input.infoTitle}</h5>
                        <h6 className="card-text h6">{input.infoBody}</h6>
                      </div>
                    </div>
                  </div>
                )}
              </>
            ) : (
              <FormInput
                disabaledOptions={input?.disabaledOptions}
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
