import React, { useEffect } from "react";
// Used as const not import, for Loco translate plugin compatibility.
const __ = wp.i18n.__;

function Phonebook() {
  return (
    <div className="container">
      <div className="card bg-light mb-3">
        <div className="card-body">
          <h5 className="card-title">Light card title</h5>
          <p className="card-text">
            {__(
              "Special Offer: If you have a physical store, use the mobile number storage device to collect your customers mobile numbers. Click on the link below to see the details",
              "farazsms"
            )}
          </p>
          <a
            href="https://farazsms.com/pos/"
            className="btn btn-success"
            target="_blank"
          >
            {__("Buying a mobile number storage device", "farazsms")}
          </a>
        </div>
      </div>
    </div>
  );
}

export default Phonebook;
