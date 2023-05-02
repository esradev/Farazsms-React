/**
 * Import remote dependencies.
 */
import React, { useState, useEffect, useContext } from "react";
import { useImmerReducer } from "use-immer";
import { __ } from "@wordpress/i18n";

/**
 * Import local dependencies
 */
import AxiosWp from "../function/AxiosWp";
import DispatchContext from "../DispatchContext";
import PluginsCardCheckbox from "../views/PluginsCardCheckbox";
import SaveButton from "../views/SaveButton";
import SectionHeader from "../views/SectionHeader";
import useSaveOptions from "../hooks/useSaveOptions";

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
   * HandelSubmit
   *
   * @since 2.0.0
   */
  function handleSubmit(e) {
    e.preventDefault();
    appDispatch({ type: "submitOptions" });
  }

  /**
   * Post options to DB
   *
   * @since 2.0.0
   */
  /**
   * Post options to DB
   *
   * @since 2.0.0
   */
  useEffect(() => {
    if (props.sendCount) {
      const optionsArray = Object.values(props.integratedPlugins).map(
        ({ use, name }) => [name, use]
      );
      const optionsJsonForPost = Object.fromEntries(optionsArray);
      appDispatch({ type: "saveRequestStarted" });

      async function postOptions() {
        try {
          // Post Options from site DB Options table
          const postOptions = await AxiosWp.post(
            "/farazsms/v1/integrations_options",
            optionsJsonForPost
          );
          appDispatch({ type: "saveRequestFinished" });
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
  }, [props.sendCount]);

  /**
   *
   * Check user chosen plugin is installed and activated.
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
                if (plugin.plugin !== "digits/digits") {
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
                } else {
                  if (!farazsmsJsObject.isDigitsInstalled) {
                    appDispatch({
                      type: plugin.check,
                    });
                  }
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
   * Disable plugin usage when user deactivated or uninstalled the plugin.
   */
  pluginsInt.map((plugin) => {
    useEffect(() => {
      if (plugin.use) {
        async function deactivatePlugin() {
          try {
            const getPlugins = await AxiosWp.get("/wp/v2/plugins", {});
            if (getPlugins.data) {
              if (plugin.plugin !== "digits/digits") {
                const findPlugin = getPlugins.data.find(
                  (element) => element.plugin === plugin.plugin
                );
                if (findPlugin) {
                  if (findPlugin.status === "inactive") {
                    appDispatch({
                      type: plugin.inactive,
                    });
                    appDispatch({ type: "submitOptions" });
                  }
                } else {
                  appDispatch({
                    type: plugin.inactive,
                  });
                  appDispatch({ type: "submitOptions" });
                }
              } else {
                if (!farazsmsJsObject.isDigitsInstalled) {
                  appDispatch({
                    type: plugin.inactive,
                  });
                  appDispatch({ type: "submitOptions" });
                }
              }
            }
          } catch (e) {
            console.log(e);
          }
        }

        deactivatePlugin();
      }
    }, [plugin.use]);
  });

  /**
   * The settings form created by mapping over originalState as the main state.
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
