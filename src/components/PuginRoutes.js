/**
 * Internal dependencies
 */
import Settings from "./Settings";
import Phonebook from "./Phonebook";
import Synchronization from "./Synchronization";
import Comments from "./Comments";
import SendSms from "./SendSms";
import WooCommerce from "./WooCommerce";
import Edd from "./Edd";
import Newsletters from "./Newsletters";
import OtherPlugins from "./OtherPlugins";

const PuginRoutes = [
  {
    path: "/",
    element: Settings,
  },
  {
    path: "/phonebook",
    element: Phonebook,
  },
  {
    path: "/synchronization",
    element: Synchronization,
  },
  {
    path: "/comments",
    element: Comments,
  },
  {
    path: "/sendsms",
    element: SendSms,
  },
  {
    path: "/woocommerce",
    element: WooCommerce,
  },
  {
    path: "/edd",
    element: Edd,
  },
  {
    path: "/newsletters",
    element: Newsletters,
  },
  {
    path: "/otherplugins",
    element: OtherPlugins,
  },
];

export default PuginRoutes;
