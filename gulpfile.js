var gulp = require('gulp');
var concat = require('gulp-concat');
var scss = require('gulp-sass');
var uglify = require('gulp-uglify');
var sourcemaps = require('gulp-sourcemaps');


var source = {
	layout : './module/layout'
};

// convert sass to css
gulp.task('layout-scss', function(){
	gulp.src([
		source.layout + '/**/layout.scss'
	])
		.pipe(sourcemaps.init())
		.pipe(scss({
			//outputStyle: 'compact'
			outputStyle: 'compressed'
		}).on('error', scss.logError))
		.pipe(sourcemaps.write('.'))
		.pipe(gulp.dest(source.layout))
	;
});
// set watcher scss
gulp.task('layout-scss:watch', function(){
	gulp.watch(source.layout + '/**/layout.scss', ['layout-scss']);
});


// default
gulp.task('default', function(){
	console.log('say hello');
});
