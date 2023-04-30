import { useEffect } from "react";
import AxiosWp from "../function/AxiosWp";

/**
 * Get options from DB rest routes
 *
 * @since 2.0.0
 */
function useFetchOptions(endpoint, dispatch) {
  useEffect(() => {
    AxiosWp.get(endpoint)
      .then((res) => {
        const optionsJson = JSON.parse(res.data);
        console.log(optionsJson);
        dispatch({ type: "fetchComplete", value: optionsJson });
      })
      .catch((err) => {
        dispatch({ type: "cantFetching" });
      });
  }, []);
}

export default useFetchOptions;
