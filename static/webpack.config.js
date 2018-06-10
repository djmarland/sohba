const Webpack = require("webpack");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const ManifestPlugin = require("webpack-manifest-plugin");

const path = require("path");
const autoprefixer = require("autoprefixer");

const settings = {
  entry: {
    app: path.resolve(__dirname, "./src/index.jsx")
  },
  output: {
    path: path.resolve(__dirname, "../public_html/static"),
    publicPath: "/static/",
    filename: "[chunkhash:10].[name].js"
  },
  resolve: {
    extensions: [".js", ".jsx", ".json"]
  },
  module: {
    rules: [
      {
        test: /.jsx?$/,
        loader: "babel-loader",
        options: {
          presets: ["env", "react", "stage-2"]
        }
      },
      {
        test: /\.(png|svg|ico)$/,
        use: "file-loader?name=[hash:10].[name].[ext]"
      },
      {
        test: /\.scss$/,
        use: [
          MiniCssExtractPlugin.loader,
          {
            loader: "css-loader",
            options: {
              url: false,
              minimize: true,
              sourceMap: true
            }
          },
          {
            loader: "postcss-loader",
            options: {
              plugins: [autoprefixer]
            }
          },
          {
            loader: "sass-loader",
            options: {
              sourceMap: true
            }
          }
        ]
      }
    ]
  },
  plugins: [
    new Webpack.HashedModuleIdsPlugin(),
    new MiniCssExtractPlugin({
      filename: "[hash:10].[name].css"
    }),
    new ManifestPlugin({
      fileName: path.resolve(
        __dirname,
        "../public_html/static/assets-manifest.json"
      )
    })
  ]
};

module.exports = settings;
