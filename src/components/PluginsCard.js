import React, { useEffect } from "react";

function PluginsCard() {
  return (
    <div className="plugins-card card-grid">
      <article className="card">
        <div className="card-header">
          <div>
            <span>
              <img src="https://assets.codepen.io/285131/zeplin.svg" />
            </span>
            <h3>Zeplin</h3>
          </div>
          <label className="toggle">
            <input type="checkbox" checked />
            <span></span>
          </label>
        </div>
        <div className="card-body">
          <p>Collaboration between designers and developers.</p>
        </div>
        <div className="card-footer">
          <a href="#">View integration</a>
        </div>
      </article>
    </div>
  );
}

export default PluginsCard;
