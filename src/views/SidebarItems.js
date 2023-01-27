/**
 * Import remote dependencies.
 */
import React from "react";
// Used as const not import, for Loco translate plugin compatibility.
const __ = wp.i18n.__;
import {
  AiOutlineNotification,
  AiOutlinePhone,
  AiOutlineApartment,
  AiOutlineSetting,
  AiOutlineSync,
  AiOutlineComment,
  AiOutlineShoppingCart,
  AiOutlineCloudDownload,
  AiOutlineLogin,
  AiOutlineUserSwitch,
} from "react-icons/ai";
import { TbAffiliate } from "react-icons/tb";

/**
 * Import local dependencies
 */
import Settings from "../components/Settings";
import LoginNotify from "../components/LoginNotify";
import Phonebook from "../components/Phonebook";
import Synchronization from "../components/Synchronization";
import Comments from "../components/Comments";
import WooCommerce from "../components/WooCommerce";
import Edd from "../components/Edd";
import Newsletter from "../components/Newsletter";
import Aff from "../components/Aff";
import Membership from "../components/Membership";
import Integrations from "../components/Integrations";

const SidebarItems = [
  {
    path: "/",
    element: Settings,
    name: __("Settings", "farazsms"),
    icon: <AiOutlineSetting />,
  },
  {
    path: "/login_notify",
    element: LoginNotify,
    name: __("Login Notify", "farazsms"),
    icon: <AiOutlineLogin />,
  },
  {
    path: "/phonebook",
    element: Phonebook,
    name: __("Phonebook", "farazsms"),
    icon: <AiOutlinePhone />,
  },
  {
    path: "/synchronization",
    element: Synchronization,
    name: __("Synchronization", "farazsms"),
    icon: <AiOutlineSync />,
  },
  {
    path: "/comments",
    element: Comments,
    name: __("Comments", "farazsms"),
    icon: <AiOutlineComment />,
  },
  {
    path: "/woocommerce",
    element: WooCommerce,
    name: __("WooCommerce", "farazsms"),
    icon: <AiOutlineShoppingCart />,
  },
  {
    path: "/edd",
    element: Edd,
    name: __("Edd Settings", "farazsms"),
    icon: <AiOutlineCloudDownload />,
  },
  {
    path: "/newsletter",
    element: Newsletter,
    name: __("Newsletter", "farazsms"),
    icon: <AiOutlineNotification />,
  },
  {
    path: "/aff",
    element: Aff,
    name: __("Affiliate", "farazsms"),
    icon: <TbAffiliate />,
  },
  {
    path: "/membership",
    element: Membership,
    name: __("Membership", "farazsms"),
    icon: <AiOutlineUserSwitch />,
  },
  {
    path: "/integrations",
    element: Integrations,
    name: __("Integrations", "farazsms"),
    icon: <AiOutlineApartment />,
  },
];

export default SidebarItems;
