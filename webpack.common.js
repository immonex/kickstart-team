const glob = require('glob')
const path = require('path')

const MiniCssExtractPlugin = require('mini-css-extract-plugin')

module.exports = {
  entry: {
    ...{
        frontend: './src/js/frontend.js'
    },
    ...glob.sync('./src/skins/**/js/index.js', { dotRelative: true }).reduce((acc, path) => {
        let entry = path.replace('./src/skins/', '../../skins/')
        entry = entry.replace('/js/index.js', '/assets/index')
        acc[entry] = path
        return acc
    }, {}),
  },
  output: {
    filename: '[name].js',
    path: path.resolve(__dirname, 'src/assets/js'),
    chunkFilename: '[name].[chunkhash:8].js'
  },
  resolve: {
    fallback: {
      'fs': false,
      'buffer': false,
      'http': false,
      'https': false,
      'url': false
    }
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
            loader: 'css-loader',
            options: { url: false }
          },
          {
            loader: 'sass-loader'
          }
        ]
      },
      {
          test: /\.(woff(2)?|ttf|eot)$/,
          exclude: /node_modules|build/,
          type: 'asset/resource',
          generator: {
            filename: '[name][ext]',
            emit: false
          }
      }
    ]
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: path => path.chunk.name.indexOf('/js/') !== -1 ?
        path.chunk.name.replace('/js/', '/css/') + '.css' :
        '../css/[name].css',
      chunkFilename: path => path.chunk.name.indexOf('/js/') !== -1 ?
        path.chunk.name.replace('/js/', '/css/') + '.css' :
        '../css/[name].[chunkhash:8].css'
    })
  ]
}