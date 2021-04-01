const glob = require('glob')
const path = require('path')

const MiniCssExtractPlugin = require('mini-css-extract-plugin')

module.exports = {
  entry: {
    ...{
        frontend: './src/js/src/frontend.js'
    },
    ...glob.sync('./src/skins/**/js/src/index.js').reduce((acc, path) => {
        let entry = path.replace('./src/skins/', '../../src/skins/')
        entry = entry.replace('/js/src/index.js', '/js/index')
        acc[entry] = path
        return acc
    }, {})
  },
  output: {
    filename: '[name].js',
    path: path.resolve(__dirname, 'src/js')
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
              outputPath: (url, resourcePath, context) => {
                const relativePath = path.relative(context, resourcePath);
                return '../../' + relativePath;
              },
              publicPath: '../fonts'
            }
        }]
      }
    ]
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: path => path.chunk.name.replace('/js/', '/css/') + '.css'
    })
  ]
}
