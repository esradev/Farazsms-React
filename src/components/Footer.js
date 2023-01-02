/**
 * Import remote dependencies.
 */
import React from "react";
// Used as const not import, for Loco translate plugin compatibility.
const __ = wp.i18n.__;

function Footer() {
  return (
    <footer class="border-top text-center small text-muted py-3 container">
      <p>
        <a class="mx-1" href="https://farazsms.com/about/" target="_blank">
          {__("About Us", "farazsms")}
        </a>{" "}
        |{" "}
        <a class="mx-1" href="https://farazsms.com/faq/" target="_blank">
          {__("Terms", "farazsms")}
        </a>
      </p>
      <p class="m-0">
        {__("Copyright", "farazsms")} &copy; {__("2022", "farazsms")}{" "}
        <a href="https://farazsms.com/" target="_blank" class="text-muted">
          {__("FarazSMS", "farazsms")}
        </a>
        {__(". All rights reserved.", "farazsms")}
      </p>
    </footer>
  );
}

export default Footer;
