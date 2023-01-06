/**
 * Import remote dependencies.
 */
import React, { useState, useEffect, useContext } from "react";
import { useImmerReducer } from "use-immer";
import { CSSTransition } from "react-transition-group";
import Axios from "axios";
// Used as const not import, for Loco translate plugin compatibility.
const __ = wp.i18n.__;

/**
 * Import local dependencies
 */
import AxiosWp from "./AxiosWp";
import DispatchContext from "../DispatchContext";
import PluginsCardCheckbox from "./PluginsCardCheckbox";
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
  const appDispatch = useContext(DispatchContext);
  /**
   * Define plugins state.
   */
  const originalState = {
    plugins: {
      woocommerce: {
        use: false,
        name: "woocommerce",
        label: "WooCommerce",
        isActivated: false,
        imgUrl: { logo: WoocommerceLogo },
        onChange: "wooChange",
        id: "wooId",
        plugin: "woocommerce/woocommerce",
        check: "wooCheck",
        hasErrors: false,
        errorMessage: "",
      },
      digits: {
        use: false,
        name: "digits",
        label: "digits",
        isActivated: false,
        imgUrl: { logo: DigitsLogo },
        onChange: "digitsChange",
        id: "digitsId",
        plugin: "digits/digits",
        check: "digitsCheck",
        hasErrors: false,
        errorMessage: "",
      },
      edd: {
        use: false,
        name: "edd",
        label: "Edd",
        isActivated: false,
        imgUrl: { logo: EddLogo },
        onChange: "eddChange",
        id: "eddId",
        plugin: "easy-digital-downloads/easy-digital-downloads",
        check: "eddCheck",
        hasErrors: false,
        errorMessage: "",
      },
      bookly: {
        use: false,
        name: "bookly",
        label: "Bookly",
        isActivated: false,
        imgUrl: { logo: BooklyLogo },
        onChange: "booklyChange",
        id: "booklyId",
        plugin: "bookly-responsive-appointment-booking-tool/main",
        check: "booklyCheck",
        hasErrors: false,
        errorMessage: "",
      },
      gravityForms: {
        use: false,
        name: "gravityForms",
        label: "Gravity Forms",
        isActivated: false,
        imgUrl: { logo: GravityFormsLogo },
        onChange: "gfChange",
        id: "gfId",
        plugin: "gravityforms/gravityforms",
        check: "gravityFormsCheck",
        hasErrors: false,
        errorMessage: "",
      },
      indeedMembershipPro: {
        use: false,
        name: "indeedMembershipPro",
        label: "Indeed Membership Pro",
        isActivated: false,
        imgUrl: { logo: IndeedMembershipProLogo },
        onChange: "impChange",
        id: "impId",
        plugin: "indeed-membership-pro/indeed-membership-pro",
        check: "indeedMembershipProCheck",
        hasErrors: false,
        errorMessage: "",
      },
      paidMembershipsPro: {
        use: false,
        name: "paidMembershipsPro",
        label: "Paid Memberships Pro",
        isActivated: false,
        imgUrl: { logo: PaidMembershipsProLogo },
        onChange: "pmpChange",
        id: "pmpId",
        plugin: "paid-memberships-pro/paid-memberships-pro",
        check: "paidMembershipsProCheck",
        hasErrors: false,
        errorMessage: "",
      },
      affiliateWp: {
        use: false,
        name: "affiliateWp",
        label: "Affiliate Wp",
        isActivated: false,
        imgUrl: { logo: AffiliateWpLogo },
        onChange: "affChange",
        id: "affId",
        plugin: "affiliate-wp/affiliate-wp",
        check: "affiliateWpCheck",
        hasErrors: false,
        errorMessage: "",
      },
      indeedAffiliatePro: {
        use: false,
        name: "indeedAffiliatePro",
        label: "Indeed Affiliate Pro",
        isActivated: false,
        imgUrl: { logo: IndeedAffiliateProLogo },
        onChange: "uapChange",
        id: "uapId",
        plugin: "indeed-affiliate-pro/indeed-affiliate-pro",
        check: "indeedAffiliateProCheck",
        hasErrors: false,
        errorMessage: "",
      },
      yithWoocommerceAffiliates: {
        use: false,
        name: "yithWoocommerceAffiliates",
        label: "Yith Woocommerce Affiliates",
        isActivated: false,
        imgUrl: { logo: YithWoocommerceAffiliatesLogo },
        onChange: "ywaChange",
        id: "ywaId",
        plugin: "yith-woocommerce-affiliates/init",
        check: "yithWoocommerceAffiliatesCheck",
        hasErrors: false,
        errorMessage: "",
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
        draft.plugins.woocommerce.use = action.value.woocommerce;
        draft.plugins.digits.use = action.value.digits;
        draft.plugins.edd.use = action.value.edd;
        draft.plugins.bookly.use = action.value.bookly;
        draft.plugins.gravityForms.use = action.value.gravityForms;
        draft.plugins.indeedMembershipPro.use =
          action.value.indeedMembershipPro;
        draft.plugins.paidMembershipsPro.use = action.value.paidMembershipsPro;
        draft.plugins.affiliateWp.use = action.value.affiliateWp;
        draft.plugins.indeedAffiliatePro.use = action.value.indeedAffiliatePro;
        draft.plugins.yithWoocommerceAffiliates.use =
          action.value.yithWoocommerceAffiliates;

        draft.isFetching = false;
        return;
      case "wooChange":
        draft.plugins.woocommerce.hasErrors = false;
        draft.plugins.woocommerce.use = action.value;
        return;
      case "digitsChange":
        draft.plugins.digits.hasErrors = false;
        draft.plugins.digits.use = action.value;
        return;
      case "eddChange":
        draft.plugins.edd.hasErrors = false;
        draft.plugins.edd.use = action.value;
        return;
      case "booklyChange":
        draft.plugins.bookly.hasErrors = false;
        draft.plugins.bookly.use = action.value;
        return;
      case "gfChange":
        draft.plugins.gravityForms.hasErrors = false;
        draft.plugins.gravityForms.use = action.value;
        return;
      case "impChange":
        draft.plugins.indeedMembershipPro.hasErrors = false;
        draft.plugins.indeedMembershipPro.use = action.value;
        return;
      case "pmpChange":
        draft.plugins.paidMembershipsPro.hasErrors = false;
        draft.plugins.paidMembershipsPro.use = action.value;
        return;
      case "affChange":
        draft.plugins.affiliateWp.hasErrors = false;
        draft.plugins.affiliateWp.use = action.value;
        return;
      case "uapChange":
        draft.plugins.indeedAffiliatePro.hasErrors = false;
        draft.plugins.indeedAffiliatePro.use = action.value;
        return;
      case "ywaChange":
        draft.plugins.yithWoocommerceAffiliates.hasErrors = false;
        draft.plugins.yithWoocommerceAffiliates.use = action.value;
        return;

      case "submitOptions":
        if (
          !draft.plugins.woocommerce.hasErrors &&
          !draft.plugins.digits.hasErrors &&
          !draft.plugins.edd.hasErrors &&
          !draft.plugins.bookly.hasErrors &&
          !draft.plugins.gravityForms.hasErrors &&
          !draft.plugins.indeedMembershipPro.hasErrors &&
          !draft.plugins.paidMembershipsPro.hasErrors &&
          !draft.plugins.affiliateWp.hasErrors &&
          !draft.plugins.indeedAffiliatePro.hasErrors &&
          !draft.plugins.yithWoocommerceAffiliates.hasErrors
        ) {
          draft.sendCount++;
        }

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

  /**
   *
   * HandelSubmit function
   *
   * @since 2.0.0
   */
  function handleSubmit(e) {
    e.preventDefault();
    //Set every plugin to the state with dispatch function.
    Object.values(state.plugins).map((plugin) => {
      dispatch({ type: plugin.rules, value: plugin.value });
    });

    dispatch({ type: "submitOptions" });
  }

  /**
   *
   * Get integrations options from DB on integrations component loaded
   *
   * @since 2.0.0
   */
  useEffect(() => {
    async function getOptions() {
      try {
        /*
         * Use the AxiosWp object to call the /farazsms/v1/farazsms_integrations_options
         * endpoint and retrieve the 10 latest posts.
         */
        const getOptions = await AxiosWp.get(
          "/farazsms/v1/integrations_options",
          {}
        );
        if (getOptions.data) {
          const optionsJson = JSON.parse(getOptions.data);
          console.log(optionsJson);
          dispatch({ type: "fetchComplete", value: optionsJson });
        }
      } catch (e) {
        console.log(e);
      }
    }
    getOptions();
  }, []);

  /**
   *
   * Save settings options on DB when saveRequestFininshed = true
   *
   * @since 2.0.0
   */
  useEffect(() => {
    if (state.sendCount) {
      /**
       * Get options uses and set "name: use" in an array.
       * Then Convert array to key: use pair for send Axios.post request to DB.
       * @return Object with arrays.
       */

      const optsionsArray = Object.values(state.plugins).map(
        ({ use, name }) => [name, use]
      );
      const optionsJsonForPost = Object.fromEntries(optsionsArray);
      console.log(optionsJsonForPost);

      dispatch({ type: "saveRequestStarted" });

      async function postOptions() {
        try {
          // Post Options from site DB Options table
          const postOptions = await AxiosWp.post(
            "/farazsms/v1/integrations_options",
            optionsJsonForPost
          );
          dispatch({ type: "saveRequestFininshed" });
          appDispatch({
            type: "flashMessage",
            value: __("Congrats. Form was updated successfully.", "farazsms"),
          });
        } catch (e) {
          console.log(e);
        }
      }
      postOptions();
    }
  }, [state.sendCount]);

  useEffect(() => {
    async function getPlugins() {
      try {
        const getPlugins = await AxiosWp.get("/wp/v2/plugins", {});
        if (getPlugins.data) {
          const plugins = getPlugins.data;
          console.log(plugins);

          const intPlugins = Object.values(state.plugins);
          intPlugins.map((plugin) =>
            plugins.find((item) => {
              if (item.plugin === plugin.plugin) {
                dispatch({ type: plugin.check, value: true });
                console.log(plugin);
              }
            })
          );
          console.log(state);
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
      <form onSubmit={handleSubmit}>
        <div className="plugins-card card-grid">
          {Object.values(state.plugins).map((plugin) => (
            <article key={plugin.id} className="plugins-card">
              <div className="card-header">
                <div>
                  <span className="mx-1">
                    <img src={plugin.imgUrl.logo} />
                  </span>
                  <h5>{plugin.label}</h5>
                </div>
                <PluginsCardCheckbox
                  {...plugin}
                  value={plugin.use}
                  checked={plugin.use}
                  onChange={(e) => {
                    dispatch({
                      type: plugin.onChange,
                      value: e.target.checked,
                    });
                  }}
                />
              </div>
              <div className="card-body">
                <p>{plugin.info}</p>
              </div>
              <div className="card-footer">
                <p>Every things is fine</p>
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
      </form>
    </div>
  );
}

export default Integrations;
