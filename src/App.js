import React, { useState, useReducer } from "react";
import { useImmerReducer } from "use-immer";
import { HashRouter, Route, Routes } from "react-router-dom";

// Plugin Context
import StateContext from "./StateContext";
import DispatchContext from "./DispatchContext";

// Plugin Components
import Header from "./components/Header";
import FlashMessages from "./components/FlashMessages";
import Sidebar from "./components/Sidebar";
import SettingsRoutes from "./components/SettingsRoutes";

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
              {SettingsRoutes.map((route, index) => (
                <Route
                  key={index}
                  path={route.path}
                  element={<route.element />}
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
