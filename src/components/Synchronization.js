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
import SectionError from "../views/SectionError";
import SectionHeader from "../views/SectionHeader";
import AxiosWp from "../function/AxiosWp";

function Synchronization(props) {
  const appDispatch = useContext(DispatchContext);
  const originalState = {
    sectionName: __("Synchronization", "farazsms"),
  };
  const [state, dispatch] = useImmerReducer(ourReduser, originalState);
  function ourReduser(draft, action) {}

  function syncWoo(e) {
    e.preventDefault();
    async function syncWoo() {
      try {
        await AxiosWp.post("/farazsms/v1/sync_woo");
        console.log("Sync success!");
      } catch (e) {
        console.log(e);
      }
    }
    syncWoo();
  }

  function syncDigits(e) {
    e.preventDefault();
    async function syncDigits() {
      try {
        await AxiosWp.post("/farazsms/v1/sync_woo");
        console.log("Sync success!");
      } catch (e) {
        console.log(e);
      }
    }
    syncDigits();
  }

  function syncBookly(e) {
    e.preventDefault();
    async function syncBookly() {
      try {
        await AxiosWp.post("/farazsms/v1/sync_woo");
        console.log("Sync success!");
      } catch (e) {
        console.log(e);
      }
    }
    syncBookly();
  }

  return (
    <>
      <SectionHeader sectionName={state.sectionName} />

      {props.integratedPlugins.bookly.use ? (
        <div className="card bg-light mb-3">
          <div className="card-body">
            <h5 className="card-title">
              {__("Synchronization bookley users with phonebook", "farazsms")}
            </h5>
            <a className="btn btn-info" onClick={syncBookly}>
              {__("Bookley synchronization", "farazsms")}
            </a>
          </div>
        </div>
      ) : (
        <SectionError sectionName="Bookly" />
      )}

      {props.integratedPlugins.woocommerce.use ? (
        <div className="card bg-light mb-3">
          <div className="card-body">
            <h5 className="card-title">
              {__(
                "Synchronization woocommerce users with phonebook",
                "farazsms"
              )}
            </h5>
            <button className="btn btn-info" onClick={syncWoo}>
              {__("Woocommerce synchronization", "farazsms")}
            </button>
          </div>
        </div>
      ) : (
        <SectionError sectionName="Woocommerce" />
      )}

      {props.integratedPlugins.digits.use ? (
        <div className="card bg-light mb-3">
          <div className="card-body">
            <h5 className="card-title">
              {__("Synchronization digits users with phonebook", "farazsms")}
            </h5>
            <a className="btn btn-info" onClick={syncDigits}>
              {__("Digits synchronization", "farazsms")}
            </a>
          </div>
        </div>
      ) : (
        <SectionError sectionName="Digits" />
      )}
    </>
  );
}

export default Synchronization;
