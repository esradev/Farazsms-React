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
import DispatchContext from "../DispatchContext";

function Header() {
  const appDispatch = useContext(DispatchContext);

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
