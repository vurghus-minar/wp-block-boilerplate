const path = require('path');
const NODE_ENV = process.env.NODE_ENV || 'development';

const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const FixStyleOnlyEntriesPlugin = require("webpack-fix-style-only-entries");
const CopyPlugin = require('copy-webpack-plugin');
const CleanWebpackPlugin = require('clean-webpack-plugin');
const OptimizeCssAssetsPlugin = require('optimize-css-assets-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');


// Extract multiple stylesheet using 'mini-css-extract-plugin'
function recursiveIssuer(m) {
	if (m.issuer) {
	  return recursiveIssuer(m.issuer);
	} else if (m.name) {
	  return m.name;
	} else {
	  return false;
	}
}

const webpackConfig = {
	mode: NODE_ENV,
	entry: {
		script: path.resolve(__dirname, 'src/index.jsx'),
		style: path.resolve(__dirname, 'src/style.scss'),
		editor: path.resolve(__dirname, 'src/editor.scss'),
	},
	output: {
		filename: '[name].js',
		path: path.resolve(__dirname, 'dist/wp-block-plugin'),
	},
	optimization: {
		splitChunks: {
			cacheGroups: {
				styleCSS: {
					name: 'style',
					test: (m, c, entry = 'style') =>
					m.constructor.name === 'CssModule' && recursiveIssuer(m) === entry,
					chunks: 'all',
					enforce: true,
				},
				editorCSS: {
					name: 'editor',
					test: (m, c, entry = 'editor') =>
					m.constructor.name === 'CssModule' && recursiveIssuer(m) === entry,
					chunks: 'all',
					enforce: true,
				},
			},
		},
	},
	module: {
		rules: [
			{
				test: /\.jsx$/,
				exclude: /node_modules/,
				use: 'babel-loader',
			},
			{
				test: /\.scss$/,
				use: [
					MiniCssExtractPlugin.loader,
					'css-loader', 
					{
						loader: 'postcss-loader',
						options: {
							plugins: [
								require( 'autoprefixer' ),
							]
						}
					},
					'sass-loader',
				],
			},
		],
	},
	plugins: [
		// clean build dir before every build
		new CleanWebpackPlugin({
			// after each build clean ghost stylesheet file 
			cleanAfterEveryBuildPatterns: [path.resolve(__dirname,'dist/wp-block-plugin/script.css')]
		}),
		new MiniCssExtractPlugin({
			filename: '[name].css',
		}),
		// fix for mini-css-extract-plugin as outlined here:
		// https://github.com/webpack-contrib/mini-css-extract-plugin/issues/151
		new FixStyleOnlyEntriesPlugin(),
		new CopyPlugin([
			{ from: path.resolve(__dirname, 'index.php'), to: path.resolve(__dirname,'dist/wp-block-plugin') },
			{ from: path.resolve(__dirname, 'partials/*'), to: path.resolve(__dirname,'dist/wp-block-plugin') }
		]),
	],
};

if (NODE_ENV === 'production') {
	webpackConfig.optimization = 
		{
			minimizer: [
				new OptimizeCssAssetsPlugin(),
				new TerserPlugin(),
			],
		}
}

module.exports = webpackConfig;