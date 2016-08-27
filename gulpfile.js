function log(o){console.log(o);}

var gulp = require('gulp');
var scss = require('gulp-sass');
var uglify = require('gulp-uglify');
var sourcemaps = require('gulp-sourcemaps');
var rename = require('gulp-rename');
var colors = require('colors');


// get parameter
var getParams = function(optionKey)
{
	var o = process.argv.indexOf("--" + optionKey);
	return process.argv[o+1];
};

// get Dir
var getDir = function(pwd)
{
	return pwd.replace(/[^\/]*$/, ''); // for linux
	//return pwd.replace(/[^\\]*$/, ''); // for windows
};

// get filename
var getFilename = function(pwd)
{
	return pwd.replace(/^.*[\\\/]/, '');
};

// get date now
var getDateNow = function(isDate, isTime)
{
	var o = new Date();
	var date = o.getFullYear() + '-' + (o.getMonth()+1) + '-' + o.getDate();
	var time = o.getHours() + ':' + o.getMinutes() + ':' + o.getSeconds();
	var result = '';
	result += (isDate) ? date : '';
	result += (isDate && isTime) ? ' ' : '';
	result += (isTime) ? time : '';
	return result;
};


// scss to css [watch]
gulp.task('scss:watch', function(){
	gulp.watch('**/*.scss')
		.on('change', function(file){
			// skip import file (xyz.src.scss)
			if ( /src.scss$/.test(getFilename(file.path)) ) return;

			log(('[' + getDateNow(true, true) + '] ').cyan + file.path);

			// convert scss file
			gulp.src(file.path)
				.pipe(sourcemaps.init())
				.pipe(scss({
					//outputStyle: 'compact'
					outputStyle: 'compressed'
				}).on('error', scss.logError))
				.pipe(sourcemaps.write('.'))
				.pipe(gulp.dest( getDir(file.path) ));
		});
});


// compress javascript [watch]
gulp.task('js:watch', function(){
	// do not compile script files
	gulp.watch('./module/**/*.js')
		.on('change', function(file){
			if ( /node_module\/|gulpfile.js|min.js$/.test(file.path) ) return;

			log(('[' + getDateNow(true, true) + '] ').cyan + file.path);

			// convert script file
			gulp.src(file.path)
				.pipe(uglify())
				.pipe(rename({ extname : '.min.js' }))
				.pipe(gulp.dest( getDir(file.path) ));
		});
});


// default
gulp.task('default', function(){
	console.log('say hello');
});
