import React, { useState, useEffect, useContext } from "react";
import FarazsmsLogo from "../../modules/farazsms/assets/images/farazsms-logo.png";
import Axios from "axios";
import {
  AiOutlineBell,
  AiOutlineDollar,
  AiOutlineSetting,
} from "react-icons/ai";
// Used as const not import, for Loco translate plugin compatibility.
const __ = wp.i18n.__;

import AxiosWp from "./AxiosWp";

function Header() {
  // const [apikey, setApikey] = useState();
  // const [usercredit, setUsercredit] = useState();

  // useEffect(() => {
  //   async function getOptions() {
  //     try {
  //       /*
  //        * Use the AxiosWp object to call the /farazsms/v1/farazsms_settings_options
  //        * endpoint and retrieve the 10 latest posts.
  //        */
  //       const getOptions = await AxiosWp.get(
  //         "/farazsms/v1/settings_options",
  //         {}
  //       );
  //       if (getOptions.data) {
  //         const optionsJson = JSON.parse(getOptions.data);
  //         console.log(optionsJson);
  //         setApikey(optionsJson.apikey);
  //       }
  //     } catch (e) {
  //       console.log(e);
  //     }
  //   }
  //   getOptions();
  // }, []);

  // async function ippanelData() {
  //   const authentication_data = {
  //     headers: {
  //       Authorization: "AccessKey " + [apikey],
  //     },
  //   };
  //   try {
  //     const ippanelData = await Axios.get(
  //       "http://rest.ippanel.com/v1/user",
  //       authentication_data
  //     );
  //     if (ippanelData.data) {
  //       console.log(ippanelData.data.data.user);
  //     } else {
  //       console.log("there was an error");
  //     }
  //     // Get credit from IPPanel REST API
  //     const ippanelCredit = await Axios.get(
  //       "http://rest.ippanel.com/v1/credit",
  //       authentication_data
  //     );
  //     setUsercredit(ippanelCredit.data.data.credit);
  //   } catch (e) {
  //     console.log(e);
  //   }
  // }
  // ippanelData();

  return (
    <header class="faraz-header container">
      <div class="header-content responsive-wrapper">
        <div class="header-logo">
          <a href="#">
            <div>
              <img src={FarazsmsLogo} />
            </div>
            <h2>{__("FarazSms", "farazsms")}</h2>
          </a>
        </div>
        <div class="header-navigation">
          <nav class="header-navigation-links">
            <a href="https://farazsms.com/" target="_blank">
              {__("Official Website", "farazsms")}
            </a>
            <a
              href="https://farazsms.com/farazsms-wordpress-plugin/"
              target="_blank"
            ></a>
            <a
              href="https://github.com/esradev/Farazsms-React/issues"
              target="_blank"
            >
              {__("Report Issues", "farazsms")}
            </a>
          </nav>
          <div class="header-navigation-actions">
            <a href="#" class="button">
              <AiOutlineDollar />
              <span>{__("Account credit: ", "farazsms")}</span>
            </a>
            <a href="#" class="icon-button">
              <AiOutlineSetting />
            </a>
            <a href="#" class="icon-button">
              <AiOutlineBell />
            </a>
          </div>
        </div>
      </div>
    </header>
  );
}

export default Header;
