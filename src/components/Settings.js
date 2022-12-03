import React, { useState, useEffect } from "react";
import Axios from "axios";

function Settings() {
  // Authentication IPPanel user
  const [apikey, setApikey] = useState();
  const [usercredit, setUsercredit] = useState();

  const authentication_data = {
    headers: {
      Authorization: "AccessKey " + [apikey],
    },
  };
  async function handleSubmit(e) {
    e.preventDefault();
    try {
      const response = await Axios.get(
        "http://rest.ippanel.com/v1/user",
        authentication_data
      );
      if (response.data) {
        console.log(response.data.data.user);
      } else {
        console.log("there was an error");
      }

      const credit = await Axios.get(
        "http://rest.ippanel.com/v1/credit",
        authentication_data
      );
      setUsercredit(credit.data.data.credit);
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
                <small>Apikey</small>
              </label>
              <input
                onChange={(e) => setApikey(e.target.value)}
                id="faraz-apikey"
                name="apikey"
                className="form-control"
                type="text"
                placeholder="your apikey"
                autoComplete="off"
              />
            </div>
            <div>
              <p>your credit is {usercredit}</p>
            </div>
            {/* <div className="form-group">
              <label htmlFor="email-register" className="text-muted mb-1">
                <small>Email</small>
              </label>
              <input
                id="email-register"
                name="email"
                className="form-control"
                type="text"
                placeholder="you@example.com"
                autoComplete="off"
              />
            </div>
            <div className="form-group">
              <label htmlFor="password-register" className="text-muted mb-1">
                <small>Password</small>
              </label>
              <input
                id="password-register"
                name="password"
                className="form-control"
                type="password"
                placeholder="Create a password"
              />
            </div> */}
            <button
              type="submit"
              className="py-3 mt-4 btn btn-lg btn-success btn-block"
            >
              Sign up for ComplexApp
            </button>
          </form>
        </div>
      </div>
    </div>
  );
}

export default Settings;
