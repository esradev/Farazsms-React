/**
 * Import remote dependencies.
 */
import React from "react";
import { CSSTransition } from "react-transition-group";
// Used as const not import, for Loco translate plugin compatibility.
const __ = wp.i18n.__;

/**
 * This component power the settings component.
 *
 * @since 2.0.0
 */

const SectionHeader = (props) => {
  const { sectionName } = props;

  return (
    <h3 className="p-3 mb-4 border-bottom border-dark bg-light rounded section-header">
      {sectionName}
    </h3>
  );
};

export default SectionHeader;
