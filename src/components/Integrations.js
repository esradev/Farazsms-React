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
        imgUrl: { logo: WoocommerceLogo },
        onChange: "wooChange",
        check: "wooCheck",
        plugin: "woocommerce/woocommerce",
        hasErrors: false,
        errorMessage: "",
        checkCount: 0,
      },
      digits: {
        use: false,
        name: "digits",
        label: "digits",
        imgUrl: { logo: DigitsLogo },
        onChange: "digitsChange",
        check: "digitsCheck",
        plugin: "digits/digits",
        hasErrors: false,
        errorMessage: "",
        checkCount: 0,
      },
      edd: {
        use: false,
        name: "edd",
        label: "Edd",
        imgUrl: { logo: EddLogo },
        onChange: "eddChange",
        check: "eddCheck",
        plugin: "easy-digital-downloads/easy-digital-downloads",
        hasErrors: false,
        errorMessage: "",
        checkCount: 0,
      },
      bookly: {
        use: false,
        name: "bookly",
        label: "Bookly",
        imgUrl: { logo: BooklyLogo },
        onChange: "booklyChange",
        check: "booklyCheck",
        plugin: "bookly-responsive-appointment-booking-tool/main",
        hasErrors: false,
        errorMessage: "",
        checkCount: 0,
      },
      gravityForms: {
        use: false,
        name: "gravityForms",
        label: "Gravity Forms",
        imgUrl: { logo: GravityFormsLogo },
        onChange: "gfChange",
        check: "gfCheck",
        plugin: "gravityforms/gravityforms",
        hasErrors: false,
        errorMessage: "",
        checkCount: 0,
      },
      indeedMembershipPro: {
        use: false,
        name: "indeedMembershipPro",
        label: "Indeed Membership Pro",
        imgUrl: { logo: IndeedMembershipProLogo },
        onChange: "impChange",
        check: "impCheck",
        plugin: "indeed-membership-pro/indeed-membership-pro",
        hasErrors: false,
        errorMessage: "",
        checkCount: 0,
      },
      paidMembershipsPro: {
        use: false,
        name: "paidMembershipsPro",
        label: "Paid Memberships Pro",
        imgUrl: { logo: PaidMembershipsProLogo },
        onChange: "pmpChange",
        check: "pmpCheck",
        plugin: "paid-memberships-pro/paid-memberships-pro",
        hasErrors: false,
        errorMessage: "",
        checkCount: 0,
      },
      affiliateWp: {
        use: false,
        name: "affiliateWp",
        label: "Affiliate Wp",
        imgUrl: { logo: AffiliateWpLogo },
        onChange: "affChange",
        check: "affCheck",
        plugin: "affiliate-wp/affiliate-wp",
        hasErrors: false,
        errorMessage: "",
        checkCount: 0,
      },
      indeedAffiliatePro: {
        use: false,
        name: "indeedAffiliatePro",
        label: "Indeed Affiliate Pro",
        imgUrl: { logo: IndeedAffiliateProLogo },
        onChange: "uapChange",
        check: "uapCheck",
        plugin: "indeed-affiliate-pro/indeed-affiliate-pro",
        hasErrors: false,
        errorMessage: "",
        checkCount: 0,
      },
      yithWoocommerceAffiliates: {
        use: false,
        name: "yithWoocommerceAffiliates",
        label: "Yith Woocommerce Affiliates",
        imgUrl: { logo: YithWoocommerceAffiliatesLogo },
        onChange: "ywaChange",
        check: "ywaCheck",
        plugin: "yith-woocommerce-affiliates/init",
        hasErrors: false,
        errorMessage: "",
        checkCount: 0,
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
        draft.plugins.woocommerce.checkCount++;
        return;
      case "wooCheck":
        draft.plugins.woocommerce.hasErrors = true;
        draft.plugins.woocommerce.errorMessage = __(
          "First install & activate plugin.",
          "farazsms"
        );
        return;
      case "digitsChange":
        draft.plugins.digits.hasErrors = false;
        draft.plugins.digits.use = action.value;
        draft.plugins.digits.checkCount++;
        return;
      case "digitsCheck":
        draft.plugins.digits.hasErrors = true;
        draft.plugins.digits.errorMessage = __(
          "First install & activate plugin.",
          "farazsms"
        );
        return;
      case "eddChange":
        draft.plugins.edd.hasErrors = false;
        draft.plugins.edd.use = action.value;
        draft.plugins.edd.checkCount++;
        return;
      case "eddCheck":
        draft.plugins.edd.hasErrors = true;
        draft.plugins.edd.errorMessage = __(
          "First install & activate plugin.",
          "farazsms"
        );
        return;
      case "booklyChange":
        draft.plugins.bookly.hasErrors = false;
        draft.plugins.bookly.use = action.value;
        draft.plugins.bookly.checkCount++;
        return;
      case "booklyCheck":
        draft.plugins.bookly.hasErrors = true;
        draft.plugins.bookly.errorMessage = __(
          "First install & activate plugin.",
          "farazsms"
        );
        return;
      case "gfChange":
        draft.plugins.gravityForms.hasErrors = false;
        draft.plugins.gravityForms.use = action.value;
        draft.plugins.gravityForms.checkCount++;
        return;
      case "gfCheck":
        draft.plugins.gravityForms.hasErrors = true;
        draft.plugins.gravityForms.errorMessage = __(
          "First install & activate plugin.",
          "farazsms"
        );
        return;
      case "impChange":
        draft.plugins.indeedMembershipPro.hasErrors = false;
        draft.plugins.indeedMembershipPro.use = action.value;
        draft.plugins.indeedMembershipPro.checkCount++;
        return;
      case "impCheck":
        draft.plugins.indeedMembershipPro.hasErrors = true;
        draft.plugins.indeedMembershipPro.errorMessage = __(
          "First install & activate plugin.",
          "farazsms"
        );
        return;
      case "pmpChange":
        draft.plugins.paidMembershipsPro.hasErrors = false;
        draft.plugins.paidMembershipsPro.use = action.value;
        draft.plugins.paidMembershipsPro.checkCount++;
        return;
      case "pmpCheck":
        draft.plugins.paidMembershipsPro.hasErrors = true;
        draft.plugins.paidMembershipsPro.errorMessage = __(
          "First install & activate plugin.",
          "farazsms"
        );
        return;
      case "affChange":
        draft.plugins.affiliateWp.hasErrors = false;
        draft.plugins.affiliateWp.use = action.value;
        draft.plugins.affiliateWp.checkCount++;
        return;
      case "affCheck":
        draft.plugins.affiliateWp.hasErrors = true;
        draft.plugins.affiliateWp.errorMessage = __(
          "First install & activate plugin.",
          "farazsms"
        );
        return;
      case "uapChange":
        draft.plugins.indeedAffiliatePro.hasErrors = false;
        draft.plugins.indeedAffiliatePro.use = action.value;
        draft.plugins.indeedAffiliatePro.checkCount++;
        return;
      case "uapCheck":
        draft.plugins.indeedAffiliatePro.hasErrors = true;
        draft.plugins.indeedAffiliatePro.errorMessage = __(
          "First install & activate plugin.",
          "farazsms"
        );
        return;
      case "ywaChange":
        draft.plugins.yithWoocommerceAffiliates.hasErrors = false;
        draft.plugins.yithWoocommerceAffiliates.use = action.value;
        draft.plugins.yithWoocommerceAffiliates.checkCount++;
        return;
      case "ywaCheck":
        draft.plugins.yithWoocommerceAffiliates.hasErrors = true;
        draft.plugins.yithWoocommerceAffiliates.errorMessage = __(
          "First install & activate plugin.",
          "farazsms"
        );
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

  /**
   *
   * Check user choosen plugin is installed and activated.
   *
   * @since 2.0.0
   */

  const pluginsInt = Object.values(state.plugins);
  pluginsInt.map((plugin) => {
    useEffect(() => {
      if (plugin.checkCount) {
        if (plugin.use) {
          async function checkPlugin() {
            try {
              const getPlugins = await AxiosWp.get("/wp/v2/plugins", {});
              if (getPlugins.data) {
                const findPlugin = getPlugins.data.find(
                  (element) => element.plugin === plugin.plugin
                );
                if (findPlugin) {
                  if (findPlugin.status === "inactive") {
                    dispatch({
                      type: plugin.check,
                    });
                  }
                } else {
                  dispatch({
                    type: plugin.check,
                  });
                }
              }
            } catch (e) {
              console.log(e);
            }
          }
          checkPlugin();
        }
      }
    }, [plugin.checkCount]);
  });

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
            <article key={plugin.name} className="plugins-card">
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
                {plugin.errorMessage && plugin.use === true && (
                  <div className="alert alert-danger small m-0 p-1 text-center">
                    {plugin.errorMessage}
                  </div>
                )}
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
