/**
 * External dependencies
 */
import React from "react";
const __ = wp.i18n.__; // Used as const not import, for Loco translate plugin compatibility.
// Import Icons
import {
  AiOutlineSend,
  AiOutlineNotification,
  AiOutlinePhone,
  AiOutlineApartment,
  AiOutlineSetting,
  AiOutlineSync,
  AiOutlineComment,
  AiOutlineShoppingCart,
  AiOutlineCloudDownload,
  AiOutlineLogin,
} from "react-icons/ai";
/**
 * Internal dependencies
 */
import Settings from "./Settings";
import LoginNotify from "./LoginNotify";
import Phonebook from "./Phonebook";
import Synchronization from "./Synchronization";
import Comments from "./Comments";
import SendSms from "./SendSms";
import WooCommerce from "./WooCommerce";
import Edd from "./Edd";
import Newsletter from "./Newsletter";
import Integrations from "./Integrations";

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
    path: "/sendsms",
    element: SendSms,
    name: __("Send SMS", "farazsms"),
    icon: <AiOutlineSend />,
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
    path: "/integrations",
    element: Integrations,
    name: __("Integrations", "farazsms"),
    icon: <AiOutlineApartment />,
  },
];

export default SidebarItems;
