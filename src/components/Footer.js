import React from "react";

function Footer() {
  return (
    <footer class="border-top text-center small text-muted py-3 container">
      <p>
        <a href="/" class="mx-1">
          Home
        </a>{" "}
        |{" "}
        <a class="mx-1" href="/about-us">
          About Us
        </a>{" "}
        |{" "}
        <a class="mx-1" href="/terms">
          Terms
        </a>
      </p>
      <p class="m-0">
        Copyright &copy; 2022{" "}
        <a href="/" class="text-muted">
          FarazSMS
        </a>
        . All rights reserved.
      </p>
    </footer>
  );
}

export default Footer;
