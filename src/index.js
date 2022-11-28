import App from "./App";
import { render } from "@wordpress/element";

/**
 * Import the stylesheet for the plugin.
 */

import "./index.scss";

// Render the App component into the DOM
render(<App />, document.querySelector("#farazsms-react"));
