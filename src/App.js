/**
 * External dependencies
 */
import React, { useState, useReducer } from "react";
import { useImmerReducer } from "use-immer";
import { HashRouter, Route, Routes } from "react-router-dom";
const __ = wp.i18n.__;

/**
 * Internal dependencies
 */
// Plugin Context
import StateContext from "./StateContext";
import DispatchContext from "./DispatchContext";

// Plugin Components
import Header from "./components/Header";
import FlashMessages from "./components/FlashMessages";
import Sidebar from "./components/Sidebar";
import SidebarItems from "./components/SidebarItems";
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
    <HashRouter>
      <StateContext.Provider value={state}>
        <DispatchContext.Provider value={dispatch}>
          <Header />
          <FlashMessages messages={state.flashMessages} />
          <Sidebar>
            <Routes>
              {SidebarItems.map((item, index) => (
                <Route
                  key={index}
                  path={item.path}
                  element={<item.element />}
                />
              ))}
            </Routes>
          </Sidebar>
          <Footer />
        </DispatchContext.Provider>
      </StateContext.Provider>
    </HashRouter>
  );
}

export default App;
