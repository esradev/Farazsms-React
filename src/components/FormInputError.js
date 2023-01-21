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

const FormInputError = (props) => {
  const { errorMessage, hasErrors, ...inputProps } = props;

  return (
    <CSSTransition
      in={hasErrors}
      timeout={330}
      classNames="liveValidateMessage"
      unmountOnExit
    >
      <div className="alert alert-danger small liveValidateMessage">
        {errorMessage}
      </div>
    </CSSTransition>
  );
};

export default FormInputError;
