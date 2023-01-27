/**
 * Import remote dependencies.
 */
import React from "react";
// Used as const not import, for Loco translate plugin compatibility.
const __ = wp.i18n.__;

/**
 * This component power the settings component.
 *
 * @since 2.0.0
 */

const SectionError = (props) => {
  const { sectionName, ...inputProps } = props;

  return (
    <div className="container">
      <div className="container card text-white bg-danger mb-3">
        <div class="card-header">{__("Warning!", "farazsms")}</div>
        <div className="card-body">
          <h5 className="card-title">
            {__(sectionName + " Attention Needed:", "farazsms")}
          </h5>
          <p className="card-text">
            {__(
              "You have not checked " +
                sectionName +
                " in Integrations section. Please go first there and check " +
                sectionName +
                " usage toggle, Then come bake here.",
              "farazsms"
            )}
          </p>
        </div>
      </div>
    </div>
  );
};

export default SectionError;
