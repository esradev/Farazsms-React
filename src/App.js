/**
 * Import remote dependencies.
 */
import React, { useState, useReducer, useEffect } from "react";
import { useImmerReducer } from "use-immer";
import { HashRouter, Route, Routes } from "react-router-dom";
const __ = wp.i18n.__;

/**
 * Import local dependencies
 */

// Plugin Context
import StateContext from "./StateContext";
import DispatchContext from "./DispatchContext";
import { ConfirmContextProvider } from "./store/ConfirmContextProvider";
import ConfirmDialog from "./components/ConfirmDialog";
// Plugin Components
import Header from "./components/Header";
import FlashMessages from "./views/FlashMessages";
import Sidebar from "./components/Sidebar";
import SidebarItems from "./views/SidebarItems";
import Footer from "./components/Footer";
// Integration plugins logo
import WoocommerceLogo from "./assets/images/woocommerce-logo.jpg";
import DigitsLogo from "./assets/images/digits-logo.jpg";
import EddLogo from "./assets/images/edd-logo.jpg";
import BooklyLogo from "./assets/images/bookly-logo.jpg";
import GravityFormsLogo from "./assets/images/gravity-logo.jpg";
import IndeedMembershipProLogo from "./assets/images/ultimatemembershippro-logo.jpg";
import PaidMembershipsProLogo from "./assets/images/paidmembershipspro-logo.jpg";
import AffiliateWpLogo from "./assets/images/affiliatewp-logo.jpg";
import IndeedAffiliateProLogo from "./assets/images/ultimateaffiliatepro-logo.jpg";
import YithWoocommerceAffiliatesLogo from "./assets/images/yithwoocommerceaffiliates-logo.jpg";
import ElementorLogo from "./assets/images/elementor-logo.jpg";
import AxiosWp from "./function/AxiosWp";

function App() {
  const initialState = {
    flashMessages: {
      message: [],
      type: "",
    },
    confirm: {
      show: false,
      text: "",
    },
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
        info: "",
        checkCount: 0,
      },
      elementorPro: {
        use: false,
        name: "elementorPro",
        label: "Elementor Pro",
        imgUrl: { logo: ElementorLogo },
        onChange: "elementorProChange",
        check: "elementorProCheck",
        plugin: "elementor-pro/elementor-pro",
        hasErrors: false,
        errorMessage: "",
        info: __(
          "Add action after elementor pro forms submitting.",
          "farazsms"
        ),
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
        info: "",
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
        info: "",
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
        info: "",
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
        info: "",
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
        info: "",
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
        info: "",
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
        info: "",
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
        info: "",
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
        info: "",
        checkCount: 0,
      },
    },
    isFetching: true,
    isSaving: false,
    sendCount: 0,
  };

  function ourReducer(draft, action) {
    switch (action.type) {
      case "flashMessage":
        draft.flashMessages.message.push(action.value.message);
        draft.flashMessages.type = action.value.type;
        return;
      case "ShowConfirm":
        draft.confirm.show = true;
        draft.confirm.text = action.value;
        return;
      case "HideConfirm":
        draft.confirm.show = false;
        draft.confirm.text = action.value;
        return;
      case "fetchComplete":
        //Init state values by action.value
        draft.plugins.woocommerce.use = action.value.woocommerce;
        draft.plugins.elementorPro.use = action.value.elementorPro;
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
      case "elementorProChange":
        draft.plugins.elementorPro.hasErrors = false;
        draft.plugins.elementorPro.use = action.value;
        draft.plugins.elementorPro.checkCount++;
        return;
      case "elementorProCheck":
        draft.plugins.elementorPro.hasErrors = true;
        draft.plugins.elementorPro.errorMessage = __(
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
          !draft.plugins.elementorPro.hasErrors &&
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
      case "saveRequestFinished":
        draft.isSaving = false;
        return;
    }
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
          dispatch({ type: "fetchComplete", value: optionsJson });
        }
      } catch (e) {
        console.log(e);
      }
    }
    getOptions();
  }, []);

  const [state, dispatch] = useImmerReducer(ourReducer, initialState);

  return (
    <HashRouter>
      <StateContext.Provider value={state}>
        <DispatchContext.Provider value={dispatch}>
          <ConfirmContextProvider>
            <Header />
            <FlashMessages flashMessages={state.flashMessages} />
            <ConfirmDialog />
            <Sidebar>
              <Routes>
                {SidebarItems.map((item, index) => (
                  <Route
                    key={index}
                    path={item.path}
                    element={
                      <item.element
                        integratedPlugins={state.plugins}
                        sendCount={state.sendCount}
                      />
                    }
                  />
                ))}
              </Routes>
            </Sidebar>
            <Footer />
          </ConfirmContextProvider>
        </DispatchContext.Provider>
      </StateContext.Provider>
    </HashRouter>
  );
}

export default App;
