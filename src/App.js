import React, { useState, useReducer } from "react";
import { useImmerReducer } from "use-immer";
import { BrowserRouter, Route, Routes } from "react-router-dom";

// Plugin Context
import StateContext from "./StateContext";
import DispatchContext from "./DispatchContext";

// Plugin Components
import Header from "./components/Header";
import FlashMessages from "./components/FlashMessages";
import Settings from "./components/Settings";
import Sidebar from "./components/Sidebar";

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
    <BrowserRouter>
      <StateContext.Provider value={state}>
        <DispatchContext.Provider value={dispatch}>
          <Header />
          <FlashMessages messages={state.flashMessages} />
          <Sidebar>
            <Routes>
              <Route path="/" element={<Settings />} />
            </Routes>
          </Sidebar>
          <Footer />
        </DispatchContext.Provider>
      </StateContext.Provider>
    </BrowserRouter>
  );
}

export default App;
