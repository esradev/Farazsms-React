import React from "react";

/**
 * This component power the settings component.
 *
 * @since 2.0.0
 */

const SettingsFormInput = (props) => {
  const {
    label,
    htmlFor,
    errorMessage,
    onChange,
    onBlur,
    id,
    value,
    ...inputProps
  } = props;

  return (
    <div className="formInput">
      <label htmlFor={id} className="mb-1 form-control-label">
        {label}
      </label>
      <input
        id={id}
        value={value}
        onChange={onChange}
        onBlur={onBlur}
        autoComplete="off"
        {...inputProps}
      />
    </div>
  );
};

export default SettingsFormInput;
