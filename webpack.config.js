const defaultConfig = require('@wordpress/scripts/config/webpack.config');

module.exports = {
	...defaultConfig,
	entry: {
		'blocks/advanced-sidebar-nav/build/index': './blocks/advanced-sidebar-nav/src/index.js',
		'blocks/advanced-sidebar-nav/build/view': './blocks/advanced-sidebar-nav/src/view.js',
	},
	output: {
		path: __dirname,
		filename: '[name].js',
	},
};
