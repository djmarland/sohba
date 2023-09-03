const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const { WebpackManifestPlugin } = require("webpack-manifest-plugin");

const path = require("path");
const autoprefixer = require("autoprefixer");

const settings = {
  entry: {
    app: path.resolve(__dirname, "./src/index.jsx"),
  },
  output: {
    path: path.resolve(__dirname, "../app/public/static"),
    publicPath: "/static/",
    filename: "[contenthash:10].[name].js",
  },
  resolve: {
    extensions: [".js", ".jsx", ".json"],
  },
  module: {
    rules: [
      {
        test: /.jsx?$/,
        loader: "babel-loader",
        options: {
          presets: ["@babel/preset-env", "@babel/preset-react"],
          plugins: ["@babel/plugin-proposal-class-properties"],
        },
      },
      {
        test: /\.(png|svg|ico)$/,
        loader: "file-loader",
        options: {
          name: "[contenthash:10].[name].[ext]",
          sourceMap: true,
        },
      },
      {
        test: /\.s?css$/,
        use: [
          MiniCssExtractPlugin.loader,
          {
            loader: "css-loader",
            options: {
              url: false,
              sourceMap: true,
            },
          },
          {
            loader: "postcss-loader",
            options: {
              postcssOptions: {
                plugins: [autoprefixer],
              },
            },
          },
          {
            loader: "sass-loader",
            options: {
              sourceMap: true,
            },
          },
        ],
      },
    ],
  },
  optimization: {
    moduleIds: "deterministic",
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: "[contenthash:10].[name].css",
    }),
    new WebpackManifestPlugin({
      fileName: path.resolve(
        __dirname,
        "../app/public/static/assets-manifest.json"
      ),
    }),
  ],
};

module.exports = settings;
