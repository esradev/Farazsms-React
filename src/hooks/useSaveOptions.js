import { useEffect } from "react";
import AxiosWp from "../function/AxiosWp";

import { __ } from "@wordpress/i18n";

function useSaveAffOptions(endpoint, state, dispatch, appDispatch) {
  useEffect(() => {
    if (state.sendCount) {
      /**
       * Get options values and set "name: value" in an array.
       * Then Convert array to key: value pair for send Axios post request to DB.
       * @return Object with arrays.
       */
      const optionsArray = Object.values(state.inputs).map(
        ({ value, name }) => [name, value]
      );
      const optionsJsonForPost = Object.fromEntries(optionsArray);
      dispatch({ type: "saveRequestStarted" });

      // postOptions function for save options on DB
      async function postOptions() {
        try {
          // Post Options from site DB Options table
          const postOptions = await AxiosWp.post(endpoint, optionsJsonForPost);
          dispatch({ type: "saveRequestFinished" });
          appDispatch({
            type: "flashMessage",
            value: {
              message: __(
                "Congrats. Form was updated successfully.",
                "farazsms"
              ),
            },
          });
        } catch (e) {
          console.log(e);
        }
      }

      postOptions();
    }
  }, [state.sendCount]);
}

export default useSaveAffOptions;
