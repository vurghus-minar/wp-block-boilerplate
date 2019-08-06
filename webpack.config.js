const path = require('path');
const NODE_ENV = process.env.NODE_ENV || 'development';

const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const FixStyleOnlyEntriesPlugin = require("webpack-fix-style-only-entries");
const CleanWebpackPlugin = require('clean-webpack-plugin');
const OptimizeCssAssetsPlugin = require('optimize-css-assets-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');

const externals = require('./externals');
const helper = require('./helper');

const webpackConfig = {
	mode: NODE_ENV,
	entry: {
		script: path.resolve(__dirname, 'src/index.js'),
		style: path.resolve(__dirname, 'src/style.scss'),
		editor: path.resolve(__dirname, 'src/editor.scss'),
	},
	output: {
		filename: 'assets/js/[name].js',
		path: path.resolve(__dirname, 'dist/wp-block-plugin'),
	},
	externals,
	optimization: {
		splitChunks: {
			cacheGroups: {
				styleCSS: {
					name: 'style',
					test: (m, c, entry = 'style') =>
					m.constructor.name === 'CssModule' && helper.recursiveIssuer(m) === entry,
					chunks: 'all',
					enforce: true,
				},
				editorCSS: {
					name: 'editor',
					test: (m, c, entry = 'editor') =>
					m.constructor.name === 'CssModule' && helper.recursiveIssuer(m) === entry,
					chunks: 'all',
					enforce: true,
				},
			},
		},
	},
	module: {
		rules: [
			{
				test: /\.js$/,
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
			//before every build, delete dist files
			cleanOnceBeforeBuildPatterns: [path.resolve(__dirname,'dist/*')],
			// after each build clean ghost stylesheet file 
			cleanAfterEveryBuildPatterns: [path.resolve(__dirname,'dist/wp-block-plugin/assets/css/script.css')]
		}),
		new MiniCssExtractPlugin({
			filename: 'assets/css/[name].css',
		}),
		// fix for mini-css-extract-plugin as outlined here:
		// https://github.com/webpack-contrib/mini-css-extract-plugin/issues/151
		new FixStyleOnlyEntriesPlugin(),
		{
			apply: (compiler) => {
			  compiler.hooks.afterEmit.tap('AfterEmitFunctions', (compilation) => {
				helper.copyFileSync([
						'./plugin_src/index.php',
						'./plugin_src/plugin.php',
						'./plugin_src/README.txt',
						'./plugin_src/LICENSE'
					],
					'./dist/wp-block-plugin'
				);
				helper.copyFolderRecursiveSync('./plugin_src/partials', './dist/wp-block-plugin');
				helper.copyFolderRecursiveSync('./plugin_src/languages', './dist/wp-block-plugin');
			  });
			}
		}
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