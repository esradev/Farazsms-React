/**
 * Import remote dependencies.
 */
import React from "react";
// Used as const not import, for Loco translate plugin compatibility.
const __ = wp.i18n.__;

/**
 * This component power the integrations component.
 *
 * @since 2.0.0
 */
const PluginsCardCheckbox = (props) => {
  const { errorMessage, onChange, id, use, ...inputProps } = props;

  return (
    <label className="toggle">
      <input
        id={id}
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
