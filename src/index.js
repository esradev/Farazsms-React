import { render } from "@wordpress/element";
import React, { useState, useReducer } from "react";
import { useImmerReducer } from "use-immer";
import { BrowserRouter, Switch, Route } from "react-router-dom";

// Plugin stylesheet.
import "./index.scss";

// Plugin Context
import StateContext from "./StateContext";
import DispatchContext from "./DispatchContext";

// Plugin Components
import Header from "./components/Header";
import FlashMessages from "./components/FlashMessages";
import Settings from "./components/Settings";
import About from "./components/About";
import Terms from "./components/Terms";
import Footer from "./components/Footer";

function App() {
  const initialState = {
    flashMessages: [],
  };

  function ourReducer(draft, action) {
    switch (action.type) {
      case "flashMessage":
        draft.flashMessages.push(action.value);
        return;
    }
  }

  const [state, dispatch] = useImmerReducer(ourReducer, initialState);

  return (
    <StateContext.Provider value={state}>
      <DispatchContext.Provider value={dispatch}>
        <Header />
        <FlashMessages messages={state.flashMessages} />
        <Settings />
        <Footer />
      </DispatchContext.Provider>
    </StateContext.Provider>
  );
}

// Render the App component into the DOM
render(<App />, document.querySelector("#farazsms-react"));
