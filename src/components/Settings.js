import React, { useEffect } from "react";
import axios from "axios";

// Authentication IPPanel user
// Optionally the request above could also be done as
const authentication_data = {
  headers: {
    Authorization: "AccessKey LWqlmg6mE2zdxATLB4s8sd0cQdG0a4mmXIh9mfE1GNU=",
  },
};
axios
  .get("http://rest.ippanel.com/v1/user", authentication_data)
  .then(function (response) {
    console.log(response);
  })
  .catch(function (error) {
    console.log(error);
  })
  .finally(function () {
    // always executed
  });

function Settings() {
  return (
    <div className="Settings">
      <div className="card">
        <h3>Settings</h3>
        <p>
          Edit Settings component at <code>src/components/Dashboard.jsx</code>
        </p>
      </div>
    </div>
  );
}

export default Settings;
