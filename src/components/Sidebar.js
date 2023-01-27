/**
 * Import remote dependencies.
 */
import React from "react";
import { NavLink } from "react-router-dom";

/**
 * Import local dependencies
 */
import SidebarItems from "../views/SidebarItems";

function Sidebar({ children }) {
  return (
    <div className="container faraz-sidebar">
      <div style={{ width: "250px" }} className="sidebar">
        {SidebarItems.map((item, index) => (
          <NavLink
            to={item.path}
            key={index}
            className="link"
            activeclassName="active"
          >
            <div className="icon">{item.icon}</div>
            <div style={{ display: "block" }} className="link_text">
              {item.name}
            </div>
          </NavLink>
        ))}
      </div>
      <main>{children}</main>
    </div>
  );
}

export default Sidebar;
