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
import DigitsLogo from "../../modules/farazsms/assets/images/digits-logo.png";
import EddLogo from "../../modules/farazsms/assets/images/edd-logo.png";
import BooklyLogo from "../../modules/farazsms/assets/images/bookly-logo.png";
import GravityFormsLogo from "../../modules/farazsms/assets/images/gravity-logo.png";
import IndeedMembershipProLogo from "../../modules/farazsms/assets/images/ultimatemembershippro-logo.png";
import PaidMembershipsProLogo from "../../modules/farazsms/assets/images/paidmembershipspro-logo.png";
import AffiliateWpLogo from "../../modules/farazsms/assets/images/affiliatewp-logo.png";
import IndeedAffiliateProLogo from "../../modules/farazsms/assets/images/ultimateaffiliatepro-logo.png";
import YithWoocommerceAffiliatesLogo from "../../modules/farazsms/assets/images/yithwoocommerceaffiliates-logo.png";

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
      imgUrl: { logo: WoocommerceLogo },
      slug: "woocommerce",
      status: "",
      onChange: "wooChange",
      plugin: "woocommerce/woocommerce",
    },
    digits: {
      name: "digits",
      use: false,
      isInstalled: false,
      isActivated: false,
      info: "",
      imgUrl: { logo: DigitsLogo },
      slug: "digits/digits",
      status: "",
      onChange: "digitsChange",
      plugin: "digits/digits",
    },
    edd: {
      name: "Edd",
      use: false,
      isInstalled: false,
      isActivated: false,
      info: "",
      imgUrl: { logo: EddLogo },
      slug: "easy-digital-downloads",
      status: "",
      onChange: "eddChange",
      plugin: "easy-digital-downloads/easy-digital-downloads",
    },
    bookly: {
      name: "Bookly",
      use: false,
      isInstalled: false,
      isActivated: false,
      info: "",
      imgUrl: { logo: BooklyLogo },
      slug: "bookly-responsive-appointment-booking-tool",
      status: "",
      onChange: "booklyChange",
      plugin: "bookly-responsive-appointment-booking-tool/main",
    },
    gravityForms: {
      name: "Gravity Forms",
      use: false,
      isInstalled: false,
      isActivated: false,
      info: "",
      imgUrl: { logo: GravityFormsLogo },
      slug: "gravityforms",
      status: "",
      onChange: "gfChange",
      plugin: "gravityforms/gravityforms",
    },
    indeedMembershipPro: {
      name: "Indeed Membership Pro",
      use: false,
      isInstalled: false,
      isActivated: false,
      info: "",
      imgUrl: { logo: IndeedMembershipProLogo },
      slug: "indeed-membership-pro",
      status: "",
      onChange: "impChange",
      plugin: "indeed-membership-pro/indeed-membership-pro",
    },
    paidMembershipsPro: {
      name: "Paid Memberships Pro",
      use: false,
      isInstalled: false,
      isActivated: false,
      info: "",
      imgUrl: { logo: PaidMembershipsProLogo },
      slug: "paid-memberships-pro",
      status: "",
      onChange: "pmpChange",
      plugin: "paid-memberships-pro/paid-memberships-pro",
    },
    affiliateWp: {
      name: "Affiliate Wp",
      use: false,
      isInstalled: false,
      isActivated: false,
      info: "",
      imgUrl: { logo: AffiliateWpLogo },
      slug: "affiliate-wp",
      status: "",
      onChange: "affChange",
      plugin: "affiliate-wp/affiliate-wp",
    },
    indeedAffiliatePro: {
      name: "Indeed Affiliate Pro",
      use: false,
      isInstalled: false,
      isActivated: false,
      info: "",
      imgUrl: { logo: IndeedAffiliateProLogo },
      slug: "indeed-affiliate-pro",
      status: "",
      onChange: "uapChange",
      plugin: "indeed-affiliate-pro/indeed-affiliate-pro",
    },
    yithWoocommerceAffiliates: {
      name: "Yith Woocommerce Affiliates",
      use: false,
      isInstalled: false,
      isActivated: false,
      info: "",
      imgUrl: { logo: YithWoocommerceAffiliatesLogo },
      slug: "yith-woocommerce-affiliates",
      status: "",
      onChange: "ywaChange",
      plugin: "yith-woocommerce-affiliates/init",
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

  /* useEffect(() => {
    async function getPlugins() {
      try {
        const getPlugins = await AxiosWp.get("/wp/v2/plugins", {});
        if (getPlugins.data) {
          const plugins = getPlugins.data;
          console.log(plugins);

          const findPlugin = Object.values(state).map((plugin) => {
            plugins.find((item) => item.plugin === plugin.plugin);
          });
          console.log(findPlugin);
        }
      } catch (e) {
        console.log(e);
      }
    }
    getPlugins();
  }, []); */

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
        {Object.values(state).map((plugin) => (
          <article key={plugin.name} className="plugins-card">
            <div className="card-header">
              <div>
                <span className="mx-1">
                  <img src={plugin.imgUrl.logo} />
                </span>
                <h5>{plugin.name}</h5>
              </div>
              <PluginsCardCheckbox />
            </div>
            <div className="card-body">
              <p>{plugin.info}</p>
            </div>
            <div className="card-footer">
              <a href="#">{plugin.slug}</a>
            </div>
          </article>
        ))}
      </div>
      <button
        type="submit"
        className="btn btn-primary faraz-btn"
        disabled={state.isSaving}
      >
        {__("Save Settings", "farazsms")}
      </button>
    </div>
  );
}

export default Integrations;
