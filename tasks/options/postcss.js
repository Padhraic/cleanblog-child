module.exports = {
	dist: {
		options: {
			processors: [
				require('autoprefixer')({browsers: 'last 2 versions'})
			]
		},
		files: { 
			'assets/css/cleanblog-child.css': [ 'assets/css/cleanblog-child.css' ]
		}
	}
};