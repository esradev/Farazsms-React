/**
 * Import remote dependencies.
 */
import React, { useState, useEffect, useContext } from "react";
import FarazsmsLogo from "../assets/images/farazsms-logo.jpg";
import { AiOutlineBell } from "react-icons/ai";
// Used as const not import, for Loco translate plugin compatibility.
const __ = wp.i18n.__;

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
          <div className="header-navigation-actions">
            <p className="button">
              <span>
                {__("Account credit: ", "farazsms") +
                  credit +
                  __(" $IR_T", "farazsms")}
              </span>
            </p>
            {/* <a href="#" className="icon-button">
              <AiOutlineSetting />
            </a> */}
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
