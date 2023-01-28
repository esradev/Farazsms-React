import AxiosWp from "./AxiosWp";

function WoocommerceApi() {
  useEffect(() => {
    async function wooCustomerPhoneNum() {
      try {
        // Get Options from site DB Options table
        const wooCustomerPhoneNum = await AxiosWp.get("/wc/v3/customers");
        if (wooCustomerPhoneNum.data) {
          const optsionsJson = JSON.parse(wooCustomerPhoneNum.data);
          console.log(optsionsJson);
          dispatch({ type: "fetchComplete", value: optsionsJson });
        }
      } catch (e) {
        console.log(e);
      }
    }
    wooCustomerPhoneNum();
  }, []);
}

export default WoocommerceApi;
