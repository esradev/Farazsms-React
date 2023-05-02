/**
 * Import remote dependencies.
 */
import React, { useState, useEffect, useContext } from "react";
import { useImmerReducer } from "use-immer";
import { __ } from "@wordpress/i18n";

/**
 * Import local dependencies
 */
import DispatchContext from "../DispatchContext";
import SectionError from "../views/SectionError";
import SectionHeader from "../views/SectionHeader";
import AxiosWp from "../function/AxiosWp";
import LoadingSpinner from "../views/LoadingSpinner";
import useFetchOptions from "../hooks/useFetchOptions";

function Synchronization(props) {
  const appDispatch = useContext(DispatchContext);
  /**
   *
   * First init state.
   *
   */
  const originalState = {
    plugins: {
      woocommerce: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "woocommerceChange",
        id: "woocommerce",
        selectedPhonebook: false,
      },
      bookly: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "booklyChange",
        id: "bookly",
        selectedPhonebook: false,
      },
      digits: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "digitsChange",
        id: "digits",
        selectedPhonebook: false,
      },
    },
    isFetching: true,
    isSaving: false,
    sendCount: 0,
    sectionName: __("Synchronization", "farazsms"),
  };

  function ourReduser(draft, action) {
    switch (action.type) {
      case "fetchComplete":
        //Init state values by action.value
        if (action.value.woo_phonebook !== []) {
          draft.plugins.woocommerce.selectedPhonebook = true;
        }
        if (action.value.digits_phonebook !== []) {
          draft.plugins.digits.selectedPhonebook = true;
        }
        if (action.value.bookly_phonebook !== []) {
          draft.plugins.bookly.selectedPhonebook = true;
        }

        draft.isFetching = false;
        return;
      case "cantFetching":
        draft.isFetching = false;
        return;
      case "saveRequestStarted":
        draft.isSaving = true;
        return;
      case "saveRequestFinished":
        draft.isSaving = false;
        return;
    }
  }

  const [state, dispatch] = useImmerReducer(ourReduser, originalState);

  /**
   * Get options from DB rest routes
   *
   * @since 2.0.0
   */
  const endpoint = "/farazsms/v1/phonebook_options";

  useFetchOptions(endpoint, dispatch);

  /**
   * Sync woocommerce user('s) with selected phonebook.
   *
   * @param e
   */
  function syncWoo(e) {
    e.preventDefault();

    async function syncWoo() {
      let res;
      try {
        res = await AxiosWp.post("/farazsms/v1/sync_woo");
        console.log(res);
        if (res.data === true) {
          appDispatch({
            type: "flashMessage",
            value: {
              message: __(
                "Congrats. Woocommerce user(s) synced successfully.",
                "farazsms"
              ),
            },
          });
        } else if (res.data === "noPhonebook") {
          appDispatch({
            type: "flashMessage",
            value: {
              message: __(
                "Warning! please select a phonebook first, in phonebooks section.",
                "farazsms"
              ),
              type: "error",
            },
          });
        } else if (res.data === false) {
          appDispatch({
            type: "flashMessage",
            value: {
              message: __(
                "Warning! an error occurred, please try again later.",
                "farazsms"
              ),
              type: "error",
            },
          });
        }
      } catch (e) {
        console.log(e);
      }
    }

    syncWoo();
  }

  /**
   * Sync digits user('s) with selected phonebook.
   *
   * @param e
   */
  function syncDigits(e) {
    e.preventDefault();

    async function syncDigits() {
      let res;
      try {
        res = await AxiosWp.post("/farazsms/v1/sync_digits");
        if (res.data === true) {
          appDispatch({
            type: "flashMessage",
            value: {
              message: __(
                "Congrats. Digits user(s) synced successfully.",
                "farazsms"
              ),
            },
          });
        } else if (res.data === "noPhonebook") {
          appDispatch({
            type: "flashMessage",
            value: {
              message: __(
                "Warning! please select a phonebook first, in phonebooks section.",
                "farazsms"
              ),
              type: "error",
            },
          });
        } else {
          appDispatch({
            type: "flashMessage",
            value: {
              message: __(
                "Warning! an error occurred, please try again later.",
                "farazsms"
              ),
              type: "error",
            },
          });
        }
      } catch (e) {
        console.log(e);
      }
    }

    syncDigits();
  }

  /**
   * Sync bookly user('s) with selected phonebook.
   *
   * @param e
   */
  function syncBookly(e) {
    e.preventDefault();

    async function syncBookly() {
      let res;
      try {
        res = await AxiosWp.post("/farazsms/v1/sync_bookly");
        console.log(res);
        if (res.data === true) {
          appDispatch({
            type: "flashMessage",
            value: {
              message: __(
                "Congrats. Bookly user(s) synced successfully.",
                "farazsms"
              ),
            },
          });
        } else if (res.data === "noPhonebook") {
          appDispatch({
            type: "flashMessage",
            value: {
              message: __(
                "Warning! please select a phonebook first, in phonebooks section.",
                "farazsms"
              ),
              type: "error",
            },
          });
        } else {
          appDispatch({
            type: "flashMessage",
            value: {
              message: __(
                "Warning! an error occurred, please try again later.",
                "farazsms"
              ),
              type: "error",
            },
          });
        }
      } catch (e) {
        console.log(e);
      }
    }

    syncBookly();
  }

  if (state.isFetching) return <LoadingSpinner />;

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
