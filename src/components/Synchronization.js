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

  /**
   * Bookly Sync function
   *
   * @since 2.0.0
   */
  useEffect(() => {
    async function wooCustomerPhoneNum() {
      try {
        // Get CustomerPhoneNum from site DB Options table
        const wooCustomerPhoneNum = await AxiosWp.get("/wc/v3/customers");
        if (wooCustomerPhoneNum.data) {
          const customerArr = wooCustomerPhoneNum.data;
          const customerNumArr = customerArr.map((customer) => {
            return customer.billing.phone;
          });
          console.log(customerNumArr);
        }
      } catch (e) {
        console.log(e);
      }
    }
    wooCustomerPhoneNum();
  }, []);

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
      <SectionHeader sectionName={state.sectionName} />

      {props.integratedPlugins.bookly.use ? (
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

            <a className="btn btn-info">
              {__("Woocommerce synchronization", "farazsms")}
            </a>
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

            <a className="btn btn-info">
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
