import React from "react";

import {
  AiOutlineSend,
  AiOutlineNotification,
  AiOutlinePhone,
  AiOutlineAppstore,
  AiOutlineSetting,
  AiOutlineSync,
  AiOutlineComment,
  AiOutlineShoppingCart,
  AiOutlineCloudDownload,
} from "react-icons/ai";

import { NavLink } from "react-router-dom";

function Sidebar({ children }) {
  const menuItem = [
    {
      path: "/",
      name: "Settings",
      icon: <AiOutlineSetting />,
    },
    {
      path: "/phonebook",
      name: "Phonebook",
      icon: <AiOutlinePhone />,
    },
    {
      path: "/synchronization",
      name: "Synchronization",
      icon: <AiOutlineSync />,
    },
    {
      path: "/comments",
      name: "Comments",
      icon: <AiOutlineComment />,
    },
    {
      path: "/sendsms",
      name: "Send SMS",
      icon: <AiOutlineSend />,
    },
    {
      path: "/woocommerce",
      name: "WooCommerce",
      icon: <AiOutlineShoppingCart />,
    },
    {
      path: "/edd",
      name: "Edd",
      icon: <AiOutlineCloudDownload />,
    },
    {
      path: "/newsletters",
      name: "Newsletters",
      icon: <AiOutlineNotification />,
    },
    {
      path: "/otherplugins",
      name: "Other Plugins",
      icon: <AiOutlineAppstore />,
    },
  ];
  return (
    <div className="container faraz-sidebar">
      <div style={{ width: "250px" }} className="sidebar">
        {menuItem.map((item, index) => (
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
