/**
 * Import remote dependencies.
 */
import React, { useState, useEffect, useContext } from "react";
import { useImmerReducer } from "use-immer";
// Used as const not import, for Loco translate plugin compatibility.
const __ = wp.i18n.__;

/**
 * Import local dependencies
 */
import DispatchContext from "../DispatchContext";
import SectionHeader from "./SectionHeader";

function Synchronization() {
  const appDispatch = useContext(DispatchContext);
  const originalState = {
    sectionHeader: __("Synchronization settings:", "farazsms"),
  };
  const [state, dispatch] = useImmerReducer(ourReduser, originalState);
  function ourReduser(draft, action) {}

  /**
   * Bookly Sync function
   *
   * @since 2.0.0
   */

  // function booklySync(e) {
  //   e.preventDefault();
  //   const booklySync = farazsmsJsObject.booklySync;
  //   console.log(booklySync);
  // }

  return (
    <>
      <SectionHeader sectionHeader={state.sectionHeader} />
      <div className="card bg-light mb-3">
        <div className="card-body">
          <h5 className="card-title">
            {__("Synchronization bookley users with phonebook", "farazsms")}
          </h5>

          <a className="btn btn-info">
            {__("Bookley synchronization", "farazsms")}
          </a>
        </div>
      </div>
      <div className="card bg-light mb-3">
        <div className="card-body">
          <h5 className="card-title">
            {__("Synchronization woocommerce users with phonebook", "farazsms")}
          </h5>

          <a className="btn btn-info">
            {__("Woocommerce synchronization", "farazsms")}
          </a>
        </div>
      </div>
      <div className="card bg-light mb-3">
        <div className="card-body">
          <h5 className="card-title">
            {__("Synchronization digits users with phonebook", "farazsms")}
          </h5>

          <a className="btn btn-info">
            {__("Digits synchronization", "farazsms")}
          </a>
        </div>
      </div>
    </>
  );
}

export default Synchronization;
