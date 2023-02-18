/**
 * Import remote dependencies.
 */
import React, { useEffect } from "react";

function FlashMessages(props) {
  return (
    <div className="floating-alerts">
      {props.flashMessages.message.map((msg, index) => {
        return (
          <div
            key={index}
            className={
              "alert text-center floating-alert shadow-sm " +
              (props.flashMessages.type === "error"
                ? "alert-danger"
                : "alert-success")
            }
          >
            {msg}
          </div>
        );
      })}
    </div>
  );
}

export default FlashMessages;
