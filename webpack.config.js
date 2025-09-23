const defaultConfig = require('@wordpress/scripts/config/webpack.config');

module.exports = {
	...defaultConfig,
	entry: {
		'block/build/index': './block/src/index.js',
		'block/build/view': './block/src/view.js',
		'assets/style': './assets/style.scss',
	},
	output: {
		path: __dirname,
		filename: '[name].js',
	},
};
