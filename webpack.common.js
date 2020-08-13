const glob = require('glob')
const path = require('path')

const MiniCssExtractPlugin = require('mini-css-extract-plugin')

module.exports = {
  entry: {
    ...{
        'src/js/frontend': './src/js/src/frontend.js'
    },
    ...glob.sync('./src/skins/**/js/src/index.js').reduce((acc, path) => {
        const entry = path.replace('/js/src/index.js', '/js/index')
        acc[entry] = path
        return acc
    }, {})
  },
  output: {
    filename: '[name].js',
    path: path.resolve(__dirname)
  },
  module: {
    rules: [
      {
        test: /\.(sa|sc|c)ss$/,
        exclude: /node_modules|build/,
        use: [
          {
            loader: MiniCssExtractPlugin.loader
          },
          {
            loader: 'css-loader'
          },
          {
            loader: 'sass-loader'
          }
        ]
      },
      {
        test: /\.(woff(2)?|ttf|eot|svg)(\?v=\d+\.\d+\.\d+)?$/,
        exclude: /node_modules|build/,
        use: [{
            loader: 'file-loader',
            options: {
              name: '[name].[ext]',
              outputPath: '../fonts/'
            }
        }]
      }
    ]
  },
  plugins: [
    new MiniCssExtractPlugin({
      moduleFilename: ({ name }) => `${name.replace('/js/', '/css/')}.css`,
    }),
  ]
}
