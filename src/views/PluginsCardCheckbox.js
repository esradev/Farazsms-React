/**
 * Import remote dependencies.
 */
import React from "react";
import { __ } from "@wordpress/i18n";

/**
 * This component power the integrations component.
 *
 * @since 2.0.0
 */
const PluginsCardCheckbox = (props) => {
  const { errorMessage, onChange, name, use, ...inputProps } = props;

  return (
    <label className="toggle">
      <input
        id={name}
        value={use}
        checked={use}
        onChange={onChange}
        autoComplete="off"
        type="checkbox"
        {...inputProps}
      />
      <span className="control"></span>
    </label>
  );
};

export default PluginsCardCheckbox;
