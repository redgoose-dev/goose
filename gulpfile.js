function log(o){console.log(o);}

var gulp = require('gulp');
var concat = require('gulp-concat');
var scss = require('gulp-sass');
var uglify = require('gulp-uglify');
var sourcemaps = require('gulp-sourcemaps');
var rename = require('gulp-rename');


// get parameter
var getParams = function(optionKey)
{
	var o = process.argv.indexOf("--" + optionKey);
	return process.argv[o+1];
};


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


// compress javascript
gulp.task('javascript', function(){
	if (!getParams('option'))
	{
		log('please parameter "--option [javascript location]"');
		return false;
	}

	var path = getParams('option').replace(/[^\/]*$/, '');

	return gulp.src(getParams('option'))
		.pipe(uglify())
		.pipe(rename({ extname : '.min.js' }))
		.pipe(gulp.dest(path));
});


// default
gulp.task('default', function(){
	console.log('say hello');
});
