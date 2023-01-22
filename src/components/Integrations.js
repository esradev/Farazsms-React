/**
 * Import remote dependencies.
 */
import React, { useState, useEffect, useContext } from "react";
import { useImmerReducer } from "use-immer";
// Used as const not import, for Loco translate plugin compatibility.
const __ = wp.i18n.__;

/**
 * Import local dependencies
 */
import AxiosWp from "./AxiosWp";
import DispatchContext from "../DispatchContext";
import PluginsCardCheckbox from "./PluginsCardCheckbox";
import SaveButton from "./SaveButton";
import SectionHeader from "./SectionHeader";
import { Prev } from "react-bootstrap/esm/PageItem";

function Integrations(props) {
  const appDispatch = useContext(DispatchContext);
  /**
   * Define plugins state.
   */
  const originalState = {
    sectionName: __("Integrations", "farazsms"),
  };

  function ourReduser(draft, action) {
    switch (action.type) {
    }
  }

  const [state, dispatch] = useImmerReducer(ourReduser, originalState);

  /**
   *
   * HandelSubmit function
   *
   * @since 2.0.0
   */
  function handleSubmit(e) {
    e.preventDefault();
    appDispatch({ type: "submitOptions" });
  }

  /**
   *
   * Get integrations options from DB on integrations component loaded
   *
   * @since 2.0.0
   */
  useEffect(() => {
    async function getOptions() {
      try {
        /*
         * Use the AxiosWp object to call the /farazsms/v1/farazsms_integrations_options
         * endpoint and retrieve the 10 latest posts.
         */
        const getOptions = await AxiosWp.get(
          "/farazsms/v1/integrations_options",
          {}
        );
        if (getOptions.data) {
          const optionsJson = JSON.parse(getOptions.data);
          console.log(optionsJson);
          appDispatch({ type: "fetchComplete", value: optionsJson });
        }
      } catch (e) {
        console.log(e);
      }
    }
    getOptions();
  }, []);

  /**
   *
   * Save settings options on DB when saveRequestFininshed = true
   *
   * @since 2.0.0
   */
  useEffect(() => {
    if (props.sendCount) {
      /**
       * Get options uses and set "name: use" in an array.
       * Then Convert array to key: use pair for send Axios.post request to DB.
       * @return Object with arrays.
       */

      const optsionsArray = Object.values(props.integratedPlugins).map(
        ({ use, name }) => [name, use]
      );
      const optionsJsonForPost = Object.fromEntries(optsionsArray);
      console.log(optionsJsonForPost);

      appDispatch({ type: "saveRequestStarted" });

      async function postOptions() {
        try {
          // Post Options from site DB Options table
          const postOptions = await AxiosWp.post(
            "/farazsms/v1/integrations_options",
            optionsJsonForPost
          );
          appDispatch({ type: "saveRequestFininshed" });
          appDispatch({
            type: "flashMessage",
            value: __("Congrats. Form was updated successfully.", "farazsms"),
          });
        } catch (e) {
          console.log(e);
        }
      }
      postOptions();
    }
  }, [props.sendCount]);

  /**
   *
   * Check user choosen plugin is installed and activated.
   *
   * @since 2.0.0
   */

  const pluginsInt = Object.values(props.integratedPlugins);
  pluginsInt.map((plugin) => {
    useEffect(() => {
      if (plugin.checkCount) {
        if (plugin.use) {
          async function checkPlugin() {
            try {
              const getPlugins = await AxiosWp.get("/wp/v2/plugins", {});
              if (getPlugins.data) {
                const findPlugin = getPlugins.data.find(
                  (element) => element.plugin === plugin.plugin
                );
                if (findPlugin) {
                  if (findPlugin.status === "inactive") {
                    appDispatch({
                      type: plugin.check,
                    });
                  }
                } else {
                  appDispatch({
                    type: plugin.check,
                  });
                }
              }
            } catch (e) {
              console.log(e);
            }
          }
          checkPlugin();
        }
      }
    }, [plugin.checkCount]);
  });

  /**
   * The settings form created by maping over originalState as the main state.
   * For every value on inputs rendered a SettingsFormInput.
   *
   * @since 2.0.0
   */

  return (
    <div>
      <SectionHeader sectionName={state.sectionName} />
      <form onSubmit={handleSubmit}>
        <div className="plugins-card card-grid">
          {Object.values(props.integratedPlugins).map((plugin) => (
            <article key={plugin.name} className="plugins-card">
              <div className="card-header">
                <div>
                  <span className="mx-1">
                    <img src={plugin.imgUrl.logo} />
                  </span>
                  <h5>{plugin.label}</h5>
                </div>
                <PluginsCardCheckbox
                  {...plugin}
                  value={plugin.use}
                  checked={plugin.use}
                  onChange={(e) => {
                    appDispatch({
                      type: plugin.onChange,
                      value: e.target.checked,
                    });
                  }}
                />
              </div>
              <div className="card-body">
                <p>
                  {plugin.info
                    ? plugin.info
                    : __("Add some cool options for the ", "farazsms") +
                      plugin.label}
                </p>
                {plugin.errorMessage && plugin.use === true && (
                  <div className="alert alert-danger small m-0 p-1 text-center">
                    {plugin.errorMessage}
                  </div>
                )}
              </div>
            </article>
          ))}
        </div>
        <SaveButton isSaving={state.isSaving} />
      </form>
    </div>
  );
}

export default Integrations;
