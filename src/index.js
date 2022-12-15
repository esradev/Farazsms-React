import { render } from "@wordpress/element";
const __ = wp.i18n.__;

import App from "./App";
// Plugin stylesheet.
import "./index.scss";

// Render the App component into the DOM
render(<App />, document.querySelector("#farazsms-react"));
