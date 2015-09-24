var assets = './assets/';

var gulp = require('gulp');
var concat = require('gulp-concat');
var scss = require('gulp-sass');
var uglify = require('gulp-uglify');
var sourcemaps = require('gulp-sourcemaps');
var babel = require('gulp-babel');
var runSequence = require('run-sequence');

var files = {
	js : [
		assets + 'js/layout.js'
	],
	extJS : [
		'./node_modules/react/dist/react.js',
		'./node_modules/react/dist/react.min.js',
		'./node_modules/jquery/dist/jquery.js',
		'./node_modules/jquery/dist/jquery.min.js',
		'./node_modules/jquery/dist/jquery.min.map'
	],
	scss : [
		assets + 'css/layout.scss'
	]
};

// react.js compile
gulp.task('react', function(){
	return gulp.src(assets + 'js/*.jsx')
		.pipe(sourcemaps.init())
		.pipe(babel())
		.pipe(sourcemaps.write('../maps'))
		.pipe(gulp.dest(assets + 'js/'));
});

// concat javascript files
gulp.task('javascript', function(){
	gulp.src(files.js)
		.pipe(sourcemaps.init())
		.pipe(uglify())
		.pipe(concat('script.min.js', { newLine: '\n' }))
		.pipe(sourcemaps.write('../maps'))
		.pipe(gulp.dest(assets + 'js/'));
});

// react and javascript
gulp.task('react_and_javascript', function(callback){
	runSequence(
		'react',
		['javascript'],
		callback
	);
});

// watch react and javascript
gulp.task('react_and_javascript:watch', function(){
	gulp.watch([
		assets + 'js/layout.jsx'
	], ['react_and_javascript']);
});

// convert sass to css
gulp.task('scss', function(){
	gulp.src(files.scss)
		.pipe(sourcemaps.init())
		.pipe(scss({
			//outputStyle: 'compact'
			outputStyle: 'compressed'
		}).on('error', scss.logError))
		.pipe(sourcemaps.write('../maps'))
		.pipe(gulp.dest(assets + 'css/'))
	;
});
// set watcher scss
gulp.task('scss:watch', function(){
	gulp.watch(files.scss, ['scss']);
});

// external javascript library
gulp.task('copy_external_javascript', function(){
	files.extJS.forEach(function(o){
		gulp.src(o).pipe(gulp.dest( assets + 'js/external/' ));
	});
});


// default
gulp.task('default', function(){
	console.log('say hello');
});