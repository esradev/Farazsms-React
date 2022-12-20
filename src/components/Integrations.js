import React, { useState, useEffect, useContext } from "react";
import { useImmerReducer } from "use-immer";

// Used as const not import, for Loco translate plugin compatibility.
const __ = wp.i18n.__;

/**
 * Import local dependencies
 */
import PluginsCardCheckbox from "./PluginsCardCheckbox";
import AxiosWp from "./AxiosWp";
import DispatchContext from "../DispatchContext";

import WoocommerceLogo from "../assets/images/woocommerce-logo.png";

function Integrations() {
  /**
   * Define plugins state.
   */
  const originalState = {
    plugins: {
      woocommerce: {
        id: 1,
        name: "WooCommerce",
        info: "",
        imgUrl: { WoocommerceLogo },
        slug: "woocommerce",
        status: "",
        isInstalled: false,
        use: false,
        install: false,
        activate: false,
        smsSettings: "/woocommerce",
        onChange: "wooChange",
        hasErrors: false,
      },
    },
    isFetching: true,
    isSaving: false,
    sendCount: 0,
  };

  function ourReduser(draft, action) {
    switch (action.type) {
      case "fetchComplete":
        //Init state values by action.value
        draft.plugins.woocommerce.use = action.value.apikey;
        draft.isFetching = false;
        return;
      case "wooChange":
        draft.plugins.apikey.hasErrors = false;
        draft.plugins.apikey.value = action.value;
        return;
      case "submitOptions":
        draft.sendCount++;
        return;
      case "saveRequestStarted":
        draft.isSaving = true;
        return;
      case "saveRequestFininshed":
        draft.isSaving = false;
        return;
    }
  }

  const [state, dispatch] = useImmerReducer(ourReduser, originalState);

  function handleSubmit(e) {
    e.preventDefault();
    //Set every input to the state with dispatch function.
    const plugins = Object.plugins(state.plugins);
    dispatch({ type: "submitOptions" });
  }

  useEffect(() => {
    async function getPlugins() {
      try {
        /*
         * Use the AxiosWp object to call the /farazsms/v1/farazsms_settings_options
         * endpoint and retrieve the 10 latest posts.
         */
        const getPlugins = await AxiosWp.get(
          "/wp/v2/plugins/woocommerce/woocommerce",
          {}
        );
        if (getPlugins.data) {
          console.log(getPlugins.data);
        }
      } catch (e) {
        console.log(e);
      }
    }
    getPlugins();
  }, []);

  /**
   * The settings form created by maping over originalState as the main state.
   * For every value on inputs rendered a SettingsFormInput.
   *
   * @since 2.0.0
   */

  return (
    <div>
      <h3>{__("Integrations:", "farazsms")}</h3>

      <div className="plugins-card card-grid">
        <article className="plugins-card">
          <div className="card-header">
            <div>
              <span>
                <img src="https://ps.w.org/woocommerce/assets/icon-128x128.png?rev=2366418" />
              </span>
              <h3>{__("WooCommerce", "farazsms")}</h3>
            </div>
            <PluginsCardCheckbox />
          </div>
          <div className="card-body">
            <p>{__("Send sms after X days of any purchase", "farazsms")}</p>
          </div>
          <div className="card-footer">
            <a href="#">{__("View settings", "farazsms")}</a>
          </div>
        </article>
      </div>
    </div>
  );

  /*
   * Use the Axios Api object to call the /wp/v2/plugins
   * endpoint.
   */
  AxiosWp.post("/wp/v2/plugins", {
    slug: "woocommerce",
    status: "active",
  }).then(function (response) {
    console.log(response.data);
  });
}

export default Integrations;
