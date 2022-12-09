import React, { useState, useEffect } from "react";
import Axios from "axios";

function Settings() {
  const [usercredit, setUsercredit] = useState();

  const [apikeyOption, setApikeyOption] = useState();
  const [usernameOption, setUsernameOption] = useState();
  const [passwordOption, setPasswordOption] = useState();

  const post_credentials_options = {
    apikey: apikeyOption,
    username: usernameOption,
    password: passwordOption,
  };

  const authentication_data = {
    headers: {
      Authorization: "AccessKey " + [apikeyOption],
    },
  };

  async function handleSubmit(e) {
    e.preventDefault();
    try {
      // Post Options to site DB Options table
      const postOptions = await Axios.post(
        "http://faraz-sms.local/wp-json/farazsms/v1/credentials_options",
        post_credentials_options
      );

      // Get Options from site DB Options table
      const getOptions = await Axios.get(
        "http://faraz-sms.local/wp-json/farazsms/v1/credentials_options"
      );
      if (getOptions.data) {
        const optsionsJson = JSON.parse(getOptions.data);
      }

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
  }

  return (
    <div className="Settings">
      <div className="card">
        <h3>Settings</h3>
        <div className="col-lg-5 pl-lg-5 pb-3 py-lg-5">
          <form onSubmit={handleSubmit}>
            <div className="form-group">
              <label htmlFor="faraz-apikey" className="text-muted mb-1">
                <small>You Apikey:</small>
              </label>
              <input
                onChange={(e) => setApikeyOption(e.target.value)}
                id="faraz-apikey"
                name="apikey"
                className="form-control"
                type="text"
                placeholder="your apikey"
                autoComplete="off"
              />
            </div>

            <div className="form-group">
              <label htmlFor="faraz-username" className="text-muted mb-1">
                <small>You username:</small>
              </label>
              <input
                onChange={(e) => setUsernameOption(e.target.value)}
                id="faraz-username"
                name="username"
                className="form-control"
                type="text"
                placeholder="your username"
                autoComplete="off"
              />
            </div>

            <div className="form-group">
              <label htmlFor="faraz-password" className="text-muted mb-1">
                <small>You password:</small>
              </label>
              <input
                onChange={(e) => setPasswordOption(e.target.value)}
                id="faraz-password"
                name="password"
                className="form-control"
                type="text"
                placeholder="your password"
                autoComplete="off"
              />
            </div>

            <button
              type="submit"
              className="py-3 mt-4 btn btn-lg btn-success btn-block"
            >
              Save Settings
            </button>
          </form>
        </div>
      </div>
    </div>
  );
}

export default Settings;
