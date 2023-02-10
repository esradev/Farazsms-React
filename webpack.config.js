const defaultConfig = require("@wordpress/scripts/config/webpack.config");
const BrowserSyncPlugin = require("browser-sync-webpack-plugin");

module.exports = {
  ...defaultConfig,
  plugins: [
    ...defaultConfig.plugins,
    new BrowserSyncPlugin({
      host: "localhost",
      port: 3000,
      proxy: "http://localhost/farazsms/",
    }),
  ],
};
