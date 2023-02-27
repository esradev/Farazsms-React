/**
 * Import remote dependencies.
 */
import React from "react";
// Used as const not import, for Loco translate plugin compatibility.
const { __, sprintf } = wp.i18n;

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
            {sprintf(__("%s  Attention Needed:", "farazsms"), "farazsms")}
          </h5>
          <p className="card-text">
            {sprintf(
              __(
                "You have not checked %s in Integrations section. Please go first there and check %s usage toggle, Then come bake here.",
                "farazsms"
              ),
              sectionName
            )}
          </p>
        </div>
      </div>
    </div>
  );
};

export default SectionError;
