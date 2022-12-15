import React from "react";
import FarazsmsLogo from "../images/farazsms-logo.png";
import { IoSettingsOutline } from "react-icons/io5";
import { AiOutlineBell } from "react-icons/ai";
import { BiSupport } from "react-icons/bi";

function Header() {
  return (
    <header class="faraz-header container mt-4">
      <div class="header-content responsive-wrapper">
        <div class="header-logo">
          <a href="#">
            <div>
              <img src={FarazsmsLogo} />
            </div>
            <h2>FarazSMS</h2>
          </a>
        </div>
        <div class="header-navigation">
          <nav class="header-navigation-links">
            <a href="https://farazsms.com/" target="_blank">
              Official Website
            </a>
            <a
              href="https://farazsms.com/farazsms-wordpress-plugin/"
              target="_blank"
            >
              Documentation
            </a>
            <a
              href="https://github.com/esradev/Farazsms-React/issues"
              target="_blank"
            >
              Report Issues
            </a>
          </nav>
          <div class="header-navigation-actions">
            <a href="#" class="button">
              <BiSupport />
              <span>Support</span>
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
