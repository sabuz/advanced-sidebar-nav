const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

module.exports = {
	...defaultConfig,
	entry: {
		'block/build/index': './block/src/index.js',
	},
	output: {
		path: __dirname,
		filename: '[name].js',
	},
};
