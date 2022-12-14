import { render } from "@wordpress/element";

import App from "./App";
// Plugin stylesheet.
import "./index.scss";

// Render the App component into the DOM
render(<App />, document.querySelector("#farazsms-react"));
