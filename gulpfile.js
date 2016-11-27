var gulp = require('gulp');
var concat = require('gulp-concat');
var sourcemaps = require('gulp-sourcemaps');
var uglify = require('gulp-uglify');
var scss = require('gulp-sass');
var rename = require('gulp-rename');
var webpack = require('webpack-stream');

var vendors = [
	// './node_modules/react/dist/react.js',
	// './node_modules/react-dom/dist/react-dom.js',
	'./node_modules/react/dist/react.min.js',
	'./node_modules/react-dom/dist/react-dom.min.js',
	'./node_modules/jquery/dist/jquery.min.js',
	'./node_modules/imagesloaded/imagesloaded.pkgd.min.js'
];

// build scss
gulp.task('scss', function(){
	gulp.src('assets/src/scss/layout.scss')
		.pipe(sourcemaps.init())
		.pipe(scss({
			//outputStyle : 'compact'
			outputStyle: 'compressed'
		}).on('error', scss.logError))
		.pipe(sourcemaps.write('maps'))
		.pipe(gulp.dest('assets/dist/css'));
});
gulp.task('scss:watch', function(){
	gulp.watch('assets/src/scss/*.scss', ['scss']);
});


gulp.task('vendors', function(){
	gulp.src(vendors)
		.pipe(concat('vendor.js', {newLine: '\n\n'}))
		.pipe(gulp.dest('assets/dist/js'));
});

// build app
gulp.task('js', function() {
	return gulp.src('assets/src/js/App.js')
		.pipe(
			webpack(
				require('./webpack.config.js')
			)
		)
		.pipe(gulp.dest('assets/dist/js/'));
});
