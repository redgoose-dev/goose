const gulp = require('gulp');
const scss = require('gulp-sass');
const uglify = require('gulp-uglify');
const sourcemaps = require('gulp-sourcemaps');
const rename = require('gulp-rename');
const colors = require('colors');


// get Dir
const getDir = function(pwd)
{
	return pwd.replace(/[^\/]*$/, ''); // for linux
	//return pwd.replace(/[^\\]*$/, ''); // for windows
};

// get filename
const getFilename = function(pwd)
{
	return pwd.replace(/^.*[\\\/]/, '');
};

// get date now
const getDateNow = function(isDate, isTime)
{
	const o = new Date();
	const date = o.getFullYear() + '-' + (o.getMonth()+1) + '-' + o.getDate();
	const time = o.getHours() + ':' + o.getMinutes() + ':' + o.getSeconds();
	let result = '';
	result += (isDate) ? date : '';
	result += (isDate && isTime) ? ' ' : '';
	result += (isTime) ? time : '';
	return result;
};


// scss to css [watch]
gulp.task('scss:watch', function(){
	gulp.watch(['./mod/**/*.scss'])
		.on('change', function(file){
			// skip import file (xyz.src.scss)
			if ( /src.scss$/.test(getFilename(file)) ) return;

			console.log(('[' + getDateNow(true, true) + '] ').yellow + '/' + file);

			// convert scss file
			gulp.src(file)
				.pipe(sourcemaps.init())
				.pipe(scss({
					//outputStyle: 'compact'
					outputStyle: 'compressed'
				}).on('error', scss.logError))
				.pipe(sourcemaps.write('.'))
				.pipe(gulp.dest( getDir(file) ));
		});
});


// compress javascript [watch]
gulp.task('js:watch', function(){
	// do not compile script files
	gulp.watch([ './mod/**/*.js' ])
		.on('change', function(file){
			if ( /node_module\/|gulpfile.js|min.js$/.test(file) ) return;

			console.log(('[' + getDateNow(true, true) + '] ').yellow + '/' + file);

			// convert script file
			gulp.src(file)
				.pipe(uglify())
				.pipe(rename({ extname : '.min.js' }))
				.pipe(gulp.dest( getDir(file) ));
		});
});
