/**
 * Import remote dependencies.
 */
import React, { useState, useEffect, useContext } from "react";
import FarazsmsLogo from "../assets/images/farazsms-logo.jpg";
import { AiOutlineBell } from "react-icons/ai";
import { BiSupport } from "react-icons/bi";
import { __ } from "@wordpress/i18n";

function Header() {
  const [credit, setCredit] = useState(0);
  /**
   * Get credit.
   *
   * @since 2.0.0
   */
  useEffect(() => {
    async function getCredit() {
      try {
        //farazsmsJsObject is declared on class-farazsms-settings.php under admin_enqueue_scripts function
        const credit = await farazsmsJsObject.getCredit;
        console.log(credit);
        setCredit(credit);
      } catch (e) {
        console.log(e);
      }
    }
    getCredit();
  }, []);

  return (
    <header className="faraz-header container">
      <div className="header-content responsive-wrapper">
        <div className="header-logo">
          <a href="#">
            <div>
              <img src={FarazsmsLogo} />
            </div>
            <h2>{__("Farazsms", "farazsms")}</h2>
          </a>
        </div>
        <div className="header-navigation">
          <nav className="header-navigation-links">
            <a href="https://farazsms.com/" target="_blank">
              {__("Official Website", "farazsms")}
            </a>
            <a href={`${farazsmsJsObject.settingsUrl}#/support`}>
              {__("Report Issues", "farazsms")}
            </a>
          </nav>
          <div className="header-navigation-actions">
            <p className="button">
              <span>
                {sprintf(__("Account credit: %s $IR_T", "farazsms"), credit)}
              </span>
            </p>
            <a
              href={`${farazsmsJsObject.settingsUrl}#/support`}
              className="icon-button"
            >
              <BiSupport />
            </a>
            <a href="#" className="icon-button">
              <AiOutlineBell />
            </a>
          </div>
        </div>
      </div>
    </header>
  );
}

export default Header;
