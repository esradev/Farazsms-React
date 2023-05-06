/**
 * Import remote dependencies.
 */
import React from "react";
import { __ } from "@wordpress/i18n";
import { sprintf } from "sprintf-js";

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
            {sprintf(__("%s  Attention Needed:", "farazsms"), sectionName)}
          </h5>
          <p className="card-text">
            {sprintf(
              __(
                "You have not checked %s in Integrations section. Please go first there and check %s usage toggle, Then come bake here.",
                "farazsms"
              ),
              sectionName,
              sectionName
            )}
          </p>
        </div>
      </div>
    </div>
  );
};

export default SectionError;
