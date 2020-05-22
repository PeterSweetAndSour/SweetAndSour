module.exports = function(grunt) {
	const sass = require('node-sass');
 	 
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		
		sass: {
			options: {
				implementation: sass,
				sourceMap: true
			},
			dist: {
				files: {
					'css/styles_2020.css': 'css/sass/styles_2020.scss'
				}
			}
		},

		// https://github.com/nDmitry/grunt-autoprefixer
		autoprefixer: { 
			options: {
				browsers: ['last 3 versions', 'android 3', 'ie 9', 'bb 10']
			},
			no_dest: {
				src: ['css/styles_2020.css']
			}
		},

		/*
		concat: {
			options: {
				separator: ';',
			},
			dist: {
				src: ['js/common_2020.js'],
				dest: 'js/common.js',
			},
		},
		*/

		jshint: {
			all: [
				'js/common_2020.js'
			],
			options: {
				jshintrc: '.jshintrc'
			}
		},
	  
		watch: { // for development run 'grunt watch'
			tasks: ['sass', 'autoprefixer', 'jshint']
		}
	});
	 
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-sass');
	grunt.loadNpmTasks('grunt-autoprefixer');
	grunt.loadNpmTasks('grunt-contrib-jshint');

	// Default task
	grunt.registerTask('default', ['sass', 'autoprefixer', 'jshint']);  // 'concat', 
}