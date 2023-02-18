import { render } from "@wordpress/element";
import React from "react";
const __ = wp.i18n.__;

import App from "./App";
// Plugin stylesheet.
import "./index.scss";

// Render the App component into the DOM
render(<App />, document.querySelector("#farazsms-admin-page"));
