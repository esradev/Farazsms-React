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
  const { use, ...inputProps } = props;
  return (
    <label className="toggle">
      <input type="checkbox" checked={use} />
      <span></span>
    </label>
  );
};

export default PluginsCardCheckbox;
