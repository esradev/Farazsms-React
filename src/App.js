import React, { useState, useReducer } from "react";
import { useImmerReducer } from "use-immer";

// Plugin Context
import StateContext from "./StateContext";
import DispatchContext from "./DispatchContext";

// Plugin Components
import Settings from "./components/Settings";
import FlashMessages from "./components/flashMessages";

const App = () => {
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
        <FlashMessages messages={state.flashMessages} />
        <Settings />
      </DispatchContext.Provider>
    </StateContext.Provider>
  );
};

export default App;
