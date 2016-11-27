module.exports = {
	watch: true,
	devtool: 'eval',
	resolve: {
		modulesDirectories: ['assets/src/js'],
		extensions: ['', '.js']
	},
	output: {
		filename: 'App.js'
	},
	externals: {
		'jquery': '$',
		'react': 'React',
		'react-dom': 'ReactDOM'
	},
	module: {
		loaders: [
			{
				test: /\.(js)$/,
				loader: 'babel-loader',
				exclude: /node_modules/,
				query: {
					presets: ['babel-preset-es2015', 'babel-preset-react']
				}
			}
		]
	}
};