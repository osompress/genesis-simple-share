'use strict';

// Dependencies.
var gulp = require( 'gulp' );
var sass = require( 'gulp-sass' );
var uglify = require( 'gulp-uglify' );
var sourcemaps = require( 'gulp-sourcemaps' );
var postcss = require( 'gulp-postcss' );
var autoprefixer = require( 'autoprefixer' );
var rename = require( 'gulp-rename' );

// Path variables.
var PATHS = {
	dist: {
		css: './lib/assets/css/',
		js:  './lib/assets/js/'
	},
	src: {
		scss: './src/scss/',
		js:  './src/js/'
	}
};

// Compile SCSS src files.
gulp.task( 'scss', function () {

	gulp.src( [ PATHS.src.scss + '**/*.scss' ] )
		.pipe( sourcemaps.init() )
		.pipe( sass( { outputStyle: 'compressed' } ).on( 'error', sass.logError ) )
		.pipe( postcss([
			autoprefixer(),
		]))
		.pipe( rename({ extname: '.min.css' }) )
		.pipe( sourcemaps.write( 'map', {
			includeContent: false,
			sourceRoot: './'
		}))
		.pipe( gulp.dest( PATHS.dist.css ) );

});

// Compile JavaScript src files.
gulp.task( 'js', function () {

	gulp.src( PATHS.src.js + '**/*.js' )
		.pipe( sourcemaps.init() )
		.pipe( uglify() )
		.pipe( rename({ extname: '.min.js' }) )
		.pipe( sourcemaps.write( 'maps' ) )
		.pipe( gulp.dest( PATHS.dist.js ) );

});

// Watch JS and SCSS src files for changes.
gulp.task( 'watch', function () {
	gulp.watch( [ PATHS.src.scss + '**/*.scss', PATHS.src.js + '**/*.js' ], [ 'build' ] );
});

// Build JS and SCSS asset files.
gulp.task( 'build', [ 'scss', 'js' ] );
