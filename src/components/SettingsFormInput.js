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
// Used as const not import, for Loco translate plugin compatibility.
const __ = wp.i18n.__;

const animatedComponents = makeAnimated();

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
    type,
    tooltip,
    infoTitle,
    infoBody,
    options,
    onSelect,
    noOptionsMessage,
    ...inputProps
  } = props;

  return (
    <>
      <div className="formInput">
        <label htmlFor={id} className="mb-1 form-control-label">
          {label}
          {tooltip && (
            <OverlayTrigger
              placement="top"
              overlay={<Tooltip>{tooltip}</Tooltip>}
            >
              <Button variant="outline-dark" size="sm" className="mx-2">
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
              type={type}
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
          <div className="container card bg-light mb-3 p-1">
            <div className="card-body">
              <h5 className="card-title">
                <strong>{infoTitle}</strong>
              </h5>
              <h6 className="card-text h6">{infoBody}</h6>
            </div>
          </div>
        </div>
      )}
    </>
  );
};

export default SettingsFormInput;
