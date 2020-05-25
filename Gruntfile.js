module.exports = function(grunt) {
 	 
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		
		// https://github.com/nDmitry/grunt-autoprefixer
		autoprefixer: { 
			options: {
				browsers: ['last 3 versions', 'ie 11']
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

		sass: {                              // Task
			dist: {                          // Target
				options: {                   // Target options
					style: 'expanded'
				},
				files: {                     // Dictionary of files
					'css/styles_2020.css': 'css/sass/styles_2020.scss'       // 'destination': 'source'
				}
			}
		},

		cssmin: { // minifying css task
			dist: {
				files: {
					'css/styles_2020.min.css': 'css/styles_2020.css'
				}
			}
		},
		  
		jshint: {
			all: [
				'js/common_2020.js'
			],
			options: {
				jshintrc: '.jshintrc'
			}
		},
	  
		watch: { // for development run 'grunt watch'
			css: {
				files: 'css/sass/*.scss',
				tasks: ['sass', 'autoprefixer', 'cssmin'],
			},
			js: {
				files: 'js/*.js',
				tasks: ['jshint'],
			}
		}
	});
	 
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-autoprefixer');
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-watch');

	// Default task
	grunt.registerTask('default', ['sass', 'autoprefixer', 'cssmin', 'jshint']);  // 'concat' 
	grunt.registerTask('default', ['watch']);
}