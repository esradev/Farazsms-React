import React from "react";
import FarazsmsLogo from "../images/farazsms-logo.png";

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
            <a href="#"> Dashboard </a>
          </nav>
          <div class="header-navigation-actions">
            <a href="#" class="button">
              <i class="ph-lightning-bold"></i>
              <span>Contact Us</span>
            </a>
            <a href="#" class="icon-button">
              <i class="ph-gear-bold"></i>
            </a>
            <a href="#" class="icon-button">
              <i class="ph-bell-bold"></i>
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
