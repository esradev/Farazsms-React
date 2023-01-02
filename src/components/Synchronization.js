/**
 * Import remote dependencies.
 */
import React, { useEffect } from "react";
// Used as const not import, for Loco translate plugin compatibility.
const __ = wp.i18n.__;

function Synchronization() {
  return (
    <>
      <h3 className="p-3 mb-4 border-bottom border-dark bg-light rounded">
        {__("Synchronization settings:", "farazsms")}
      </h3>

      <div className="card bg-light mb-3">
        <div className="card-body">
          <h5 className="card-title">
            {__("Synchronization bookley users with phonebook", "farazsms")}
          </h5>

          <a href="#" className="btn btn-info">
            {__("Bookley synchronization", "farazsms")}
          </a>
        </div>
      </div>
      <div className="card bg-light mb-3">
        <div className="card-body">
          <h5 className="card-title">
            {__("Synchronization woocommerce users with phonebook", "farazsms")}
          </h5>

          <a href="#" className="btn btn-info">
            {__("Woocommerce synchronization", "farazsms")}
          </a>
        </div>
      </div>
      <div className="card bg-light mb-3">
        <div className="card-body">
          <h5 className="card-title">
            {__("Synchronization digits users with phonebook", "farazsms")}
          </h5>

          <a href="#" className="btn btn-info">
            {__("Digits synchronization", "farazsms")}
          </a>
        </div>
      </div>
    </>
  );
}

export default Synchronization;
