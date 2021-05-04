module.exports = function(grunt) {
 	 
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		
		sass: {                              // Task
			dist: {                          // Target
				options: {                   // Target options
					style: 'expanded'
				},
				files: {                     // Dictionary of files
					'css/styles_2020_unprefixed.css': 'css/sass/styles_2020.scss'       // 'destination': 'source'
				}
			}
		},

		postcss: {
			options: {
				processors: [
					require('autoprefixer')({browsers: 'last 2 versions'}) // add vendor prefixes
				]
			},
			dist: {
                src:  'css/styles_2020_unprefixed.css',
				dest: 'css/styles_2020.css'	   
			}
		},

		/*
		concat: {
			options: {
			},
			dist: {
			},
		},
		*/

		cssmin: { // minifying css task
			dist: {
				files: {
					'css/combined.min.css': 
					[
						'css/styles_2020.css',
						'css/photoswipe.css',
						'css/default-skin/default-skin.css'
					]
				}
			}
		},

	
		uglify: { 
			options: { 
				compress: true,
				sourceMap: true
			}, 
			applib: { 
				src: [
					'js/mobile-navigation.js', 
					'js/desktop-navigation.js', 
					'js/common_2020.js', 
					'js/photoswipe.js', 
					'js/photoswipe-ui-default.js', 
					'js/photoswipe_setup.js'
				], 
				dest: 'js/combined.min.js'  
			} 
		},
		  
		jshint: {
			all: [
				'js/mobile-navigation.js', 
				'js/desktop-navigation.js', 
				'js/common_2020.js'
			],
			options: {
				jshintrc: '.jshintrc'
			}
		},
	  
		watch: { // for development run 'grunt watch'
			css: {
				files: 'css/sass/*.scss',
				tasks: ['sass', 'postcss', 'cssmin'],
			},
			js: {
				files: 'js/*.js',
				tasks: ['jshint', 'uglify']
			}
		}
	});
	 
	grunt.loadNpmTasks('grunt-contrib-watch');
	// grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-postcss');
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-uglify-es');
	grunt.loadNpmTasks('grunt-contrib-watch');

	// Default task
	grunt.registerTask('default', ['sass', 'postcss', 'cssmin', 'jshint', 'uglify']); // , 'concat'
	grunt.registerTask('default', ['watch']);
}