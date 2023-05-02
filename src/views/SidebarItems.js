/**
 * Import remote dependencies.
 */
import React from "react";
import { __ } from "@wordpress/i18n";

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
  AiOutlineUnorderedList,
  AiOutlineSend,
} from "react-icons/ai";
import { TbAffiliate } from "react-icons/tb";
import { BiSupport } from "react-icons/bi";

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
import GravityForms from "../components/GravityForms";
import SendSms from "../components/SendSms";
import Support from "../components/Support";

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
    path: "/gravity_forms",
    element: GravityForms,
    name: __("Gravity Forms", "farazsms"),
    icon: <AiOutlineUnorderedList />,
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
    name: __("Edd", "farazsms"),
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
    path: "/send_sms",
    element: SendSms,
    name: __("Send Sms", "farazsms"),
    icon: <AiOutlineSend />,
  },
  {
    path: "/support",
    element: Support,
    name: __("Support", "farazsms"),
    icon: <BiSupport />,
  },
  {
    path: "/integrations",
    element: Integrations,
    name: __("Integrations", "farazsms"),
    icon: <AiOutlineApartment />,
  },
];

export default SidebarItems;
