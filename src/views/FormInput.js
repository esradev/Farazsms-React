/**
 * Import remote dependencies.
 */
import React from "react";
import Select from "react-select";
import makeAnimated from "react-select/animated";
import { AiOutlineExclamationCircle } from "react-icons/ai";
import Button from "react-bootstrap/Button";
import OverlayTrigger from "react-bootstrap/OverlayTrigger";
import Tooltip from "react-bootstrap/Tooltip";
import { CSSTransition } from "react-transition-group";
import { __ } from "@wordpress/i18n";

const animatedComponents = makeAnimated();

/**
 * This component power the settings component.
 *
 * @since 2.0.0
 */

const FormInput = (props) => {
  const {
    label,
    htmlFor,
    errorMessage,
    onChange,
    onBlur,
    id,
    value,
    type,
    tooltip,
    infoTitle,
    infoBody,
    options,
    noOptionsMessage,
    groupTitle,
    hasErrors,
    isMulti,
    disabaledOptions,
    ...inputProps
  } = props;

  const isOptionDisabled = (option) => {
    // Check if option value is in the disabledValues array
    if (disabaledOptions) {
      return disabaledOptions.includes(option.value);
    }
  };

  return (
    <>
      <div className="formInput">
        <h4 className="mb-4 fw-bold">{groupTitle}</h4>
        <label htmlFor={id} className="mb-1 form-control-label">
          {label}
          {tooltip && (
            <OverlayTrigger
              placement="top"
              overlay={<Tooltip>{tooltip}</Tooltip>}
            >
              <Button variant="outline-dark" size="sm" className="m-2">
                {__("Info ", "farazsms")}
                <AiOutlineExclamationCircle />
              </Button>
            </OverlayTrigger>
          )}

          {type === "text" && (
            <input
              id={id}
              value={value}
              type={type}
              onChange={onChange}
              onBlur={onBlur}
              autoComplete="off"
              {...inputProps}
            />
          )}

          {type === "checkbox" && (
            <input
              id={id}
              value={value}
              checked={value}
              type={type}
              onChange={onChange}
              onBlur={onBlur}
              autoComplete="off"
              {...inputProps}
            />
          )}
          {type === "checkbox" && <span className="control"></span>}

          {type === "textarea" && (
            <textarea
              value={value}
              onChange={onChange}
              onBlur={onBlur}
              autoComplete="off"
              {...inputProps}
              className="form-control"
              rows="5"
            />
          )}
          {type === "select" && (
            <Select
              isOptionDisabled={isOptionDisabled}
              isMulti={isMulti}
              value={value}
              placeholder="Select..."
              options={options}
              onChange={onChange}
              components={animatedComponents}
              noOptionsMessage={() => noOptionsMessage}
              {...inputProps}
            />
          )}
        </label>
      </div>
      {infoTitle && (
        <div className="container">
          <div className="container card bg-info mb-3 p-0">
            <div className="card-body">
              <h5 className="card-title">{infoTitle}</h5>
              <h6 className="card-text h6">{infoBody}</h6>
            </div>
          </div>
        </div>
      )}
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
    </>
  );
};

export default FormInput;
