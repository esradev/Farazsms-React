import React, { useState, useEffect, useContext } from "react";
import { useImmerReducer } from "use-immer";
import Axios from "axios";
import DispatchContext from "../DispatchContext";

function Settings() {
  const appDispatch = useContext(DispatchContext);

  const originalState = {
    apikey: {
      value: "",
      hasErrors: false,
      message: "",
    },
    username: {
      value: "",
      hasErrors: false,
      message: "",
    },
    password: {
      value: "",
      hasErrors: false,
      message: "",
    },
    isFetching: true,
    isSaving: false,
    sendCount: 0,
  };

  function ourReduser(draft, action) {
    switch (action.type) {
      case "fetchComplete":
        draft.apikey.value = action.value.apikey;
        draft.username.value = action.value.username;
        draft.password.value = action.value.password;
        draft.isFetching = false;
        return;
      case "apikeyChange":
        draft.apikey.hasErrors = false;
        draft.apikey.value = action.value;
        return;
      case "usernameChange":
        draft.username.hasErrors = false;
        draft.username.value = action.value;
        return;
      case "passwordChange":
        draft.password.hasErrors = false;
        draft.password.value = action.value;
        return;
      case "submitOptions":
        if (
          !draft.apikey.hasErrors &&
          !draft.username.hasErrors &&
          !draft.password.hasErrors
        ) {
          draft.sendCount++;
        }
        return;
      case "saveRequestStarted":
        draft.isSaving = true;
        return;
      case "saveRequestFininshed":
        draft.isSaving = false;
        return;
      case "apikeyRules":
        if (!action.value.trim()) {
          draft.apikey.hasErrors = true;
          draft.apikey.message = "You must provide an API Key.";
        }
        return;
      case "usernameRules":
        if (!action.value.trim()) {
          draft.username.hasErrors = true;
          draft.username.message = "You must provide a Username.";
        }
        return;
      case "passwordRules":
        if (!action.value.trim()) {
          draft.password.hasErrors = true;
          draft.password.message = "You must provide a Password.";
        }
        return;
    }
  }

  const [state, dispatch] = useImmerReducer(ourReduser, originalState);

  function submitHandler(e) {
    e.preventDefault();
    dispatch({ type: "apikeyRules", value: state.apikey.value });
    dispatch({ type: "usernameRules", value: state.username.value });
    dispatch({ type: "passwordRules", value: state.password.value });
    dispatch({ type: "submitOptions" });
  }

  useEffect(() => {
    async function getOptions() {
      try {
        // Get Options from site DB Options table
        const getOptions = await Axios.get(
          "http://faraz-sms.local/wp-json/farazsms/v1/credentials_options"
        );
        if (getOptions.data) {
          const optsionsJson = JSON.parse(getOptions.data);
          console.log(optsionsJson);
          dispatch({ type: "fetchComplete", value: optsionsJson });
        }
      } catch (e) {
        console.log(e);
      }
    }
    getOptions();
  }, []);

  useEffect(() => {
    if (state.sendCount) {
      dispatch({ type: "saveRequestStarted" });
      async function postOptions() {
        try {
          // Post Options from site DB Options table
          const postOptions = await Axios.post(
            "http://faraz-sms.local/wp-json/farazsms/v1/credentials_options",
            {
              apikey: state.apikey.value,
              username: state.username.value,
              password: state.password.value,
            }
          );
          dispatch({ type: "saveRequestFininshed" });
          appDispatch({ type: "flashMessage", value: "Form was updated." });
        } catch (e) {
          console.log(e);
        }
      }
      postOptions();
    }
  }, [state.sendCount]);

  const [usercredit, setUsercredit] = useState();

  const authentication_data = {
    headers: {
      Authorization: "AccessKey " + [state.apikey.value],
    },
  };

  /* async function handleSubmit(e) {
    e.preventDefault();
    try {
      
      // Get user info from IPPanel REST API
      const ippanelData = await Axios.get(
        "http://rest.ippanel.com/v1/user",
        authentication_data
      );
      if (ippanelData.data) {
        console.log(ippanelData.data.data.user);
      } else {
        console.log("there was an error");
      }

      // Get credit from IPPanel REST API
      const ippanelCredit = await Axios.get(
        "http://rest.ippanel.com/v1/credit",
        authentication_data
      );
      setUsercredit(ippanelCredit.data.data.credit);
    } catch (e) {
      console.log(e);
    }
  } */

  return (
    <div>
      <h3>Settings:</h3>
      <div>
        <form onSubmit={submitHandler}>
          <div className="form-group">
            <label htmlFor="apikey" className="text-muted mb-1">
              Your Apikey:
            </label>
            <input
              onBlur={(e) =>
                dispatch({ type: "apikeyRules", value: e.target.value })
              }
              onChange={(e) =>
                dispatch({ type: "apikeyChange", value: e.target.value })
              }
              value={state.apikey.value}
              id="apikey"
              name="apikey"
              className="form-control form-control-lg form-control-title"
              type="text"
              placeholder="your apikey"
              autoComplete="off"
            />
            {state.apikey.hasErrors && (
              <div className="alert alert-danger small liveValidateMessage">
                {state.apikey.message}
              </div>
            )}
          </div>

          <div className="form-group">
            <label htmlFor="username" className="text-muted mb-1">
              Your Username:
            </label>
            <input
              onBlur={(e) =>
                dispatch({ type: "usernameRules", value: e.target.value })
              }
              onChange={(e) =>
                dispatch({ type: "usernameChange", value: e.target.value })
              }
              value={state.username.value}
              id="username"
              name="username"
              className="form-control form-control-lg form-control-title"
              type="text"
              placeholder="Your Username"
              autoComplete="off"
            />
            {state.username.hasErrors && (
              <div className="alert alert-danger small liveValidateMessage">
                {state.username.message}
              </div>
            )}
          </div>

          <div className="form-group">
            <label htmlFor="password" className="text-muted mb-1">
              Your Password:
            </label>
            <input
              onBlur={(e) =>
                dispatch({ type: "passwordRules", value: e.target.value })
              }
              onChange={(e) =>
                dispatch({ type: "passwordChange", value: e.target.value })
              }
              value={state.password.value}
              id="password"
              name="password"
              className="form-control form-control-lg form-control-title"
              type="text"
              placeholder="Your Password"
              autoComplete="off"
            />
            {state.password.hasErrors && (
              <div className="alert alert-danger small liveValidateMessage">
                {state.password.message}
              </div>
            )}
          </div>

          <button
            type="submit"
            className="btn btn-primary"
            disabled={state.isSaving}
          >
            Save Settings
          </button>
        </form>
      </div>
    </div>
  );
}

export default Settings;
