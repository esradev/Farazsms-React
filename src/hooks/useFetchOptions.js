import { useEffect } from "react";
import AxiosWp from "../function/AxiosWp";

/**
 * Get options from DB rest routes
 *
 * @since 2.0.0
 */
function useFetchOptions(endpoint, dispatch) {
  useEffect(() => {
    async function getOptions() {
      try {
        const response = await AxiosWp.get(endpoint);
        if (response.data) {
          const optionsJson = JSON.parse(response.data);
          dispatch({ type: "fetchComplete", value: optionsJson });
        }
      } catch (e) {
        dispatch({ type: "cantFetching" });
        console.log(e);
      }
    }

    getOptions();
  }, []);
}

export default useFetchOptions;
