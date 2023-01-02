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
import PluginsCardCheckbox from "./PluginsCardCheckbox";
import AxiosWp from "./AxiosWp";
import DispatchContext from "../DispatchContext";
import WoocommerceLogo from "../../modules/farazsms/assets/images/woocommerce-logo.png";

function Integrations() {
  /**
   * Define plugins state.
   */
  const originalState = {
    woocommerce: {
      name: "WooCommerce",
      use: false,
      isInstalled: false,
      isActivated: false,
      info: "",
      imgUrl: { WoocommerceLogo },
      slug: "woocommerce",
      status: "",
      onChange: "wooChange",
    },
    digits: {
      name: "digits",
      use: false,
      isInstalled: false,
      isActivated: false,
      info: "",
      imgUrl: { WoocommerceLogo },
      slug: "digits/digits",
      status: "",
      onChange: "digitsChange",
    },
    edd: {
      name: "WooCommerce",
      use: false,
      isInstalled: false,
      isActivated: false,
      info: "",
      imgUrl: { WoocommerceLogo },
      slug: "easy-digital-downloads",
      status: "",
      onChange: "eddChange",
    },
    bookly: {
      name: "bookly",
      use: false,
      isInstalled: false,
      isActivated: false,
      info: "",
      imgUrl: { WoocommerceLogo },
      slug: "bookly-responsive-appointment-booking-tool",
      status: "",
      onChange: "booklyChange",
    },
    gravityforms: {
      name: "gravityforms",
      use: false,
      isInstalled: false,
      isActivated: false,
      info: "",
      imgUrl: { WoocommerceLogo },
      slug: "gravityforms",
      status: "",
      onChange: "gfChange",
    },
    indeedMembershipPro: {
      name: "indeed-membership-pro",
      use: false,
      isInstalled: false,
      isActivated: false,
      info: "",
      imgUrl: { WoocommerceLogo },
      slug: "indeed-membership-pro",
      status: "",
      onChange: "impChange",
    },
    paidMembershipsPro: {
      name: "paid-memberships-pro",
      use: false,
      isInstalled: false,
      isActivated: false,
      info: "",
      imgUrl: { WoocommerceLogo },
      slug: "paid-memberships-pro",
      status: "",
      onChange: "pmpChange",
    },
    affiliateWp: {
      name: "affiliate-wp",
      use: false,
      isInstalled: false,
      isActivated: false,
      info: "",
      imgUrl: { WoocommerceLogo },
      slug: "affiliate-wp",
      status: "",
      onChange: "affChange",
    },
    indeedAffiliatePro: {
      name: "indeed-affiliate-pro",
      use: false,
      isInstalled: false,
      isActivated: false,
      info: "",
      imgUrl: { WoocommerceLogo },
      slug: "indeed-affiliate-pro",
      status: "",
      onChange: "uapChange",
    },
    yithWoocommerceAffiliates: {
      name: "yith-woocommerce-affiliates",
      use: false,
      isInstalled: false,
      isActivated: false,
      info: "",
      imgUrl: { WoocommerceLogo },
      slug: "yith-woocommerce-affiliates",
      status: "",
      onChange: "ywaChange",
    },
  };

  function ourReduser(draft, action) {
    switch (action.type) {
      case "fetchComplete":
        //Init state use params by action.value
        draft.woocommerce.use = action.value.woocommerce;
        return;
      case "wooChange":
        draft.woocommerce.use = action.value;
        return;
    }
  }

  const [state, dispatch] = useImmerReducer(ourReduser, originalState);

  function handleClick(e) {}

  useEffect(() => {
    async function getPlugins() {
      try {
        const getPlugins = await AxiosWp.get("/wp/v2/plugins", {});
        if (getPlugins.data) {
          console.log(getPlugins.data);
          const plugins = getPlugins.data;
          const findPlugin = plugins.find(
            (element) => element.plugin === "woocommerce/woocommerce"
          );
          console.log(findPlugin);
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
      <h3 className="p-3 mb-4 border-bottom border-dark bg-light rounded">
        {__("Integrations:", "farazsms")}
      </h3>

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
