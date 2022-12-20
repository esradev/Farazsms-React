import React from "react";
import FarazsmsLogo from "../assets/images/farazsms-logo.png";
import { IoSettingsOutline } from "react-icons/io5";
import { AiOutlineBell } from "react-icons/ai";
import { BiSupport } from "react-icons/bi";
// Used as const not import, for Loco translate plugin compatibility.
const __ = wp.i18n.__;

function Header() {
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
              <BiSupport />
              <span>{__("Support", "farazsms")}</span>
            </a>
            <a href="#" class="icon-button">
              <IoSettingsOutline />
            </a>
            <a href="#" class="icon-button">
              <AiOutlineBell />
            </a>
            <a href="#" class="avatar">
              <img src="https://assets.codepen.io/285131/hat-man.png" alt="" />
            </a>
          </div>
        </div>
      </div>
    </header>
  );
}

export default Header;
