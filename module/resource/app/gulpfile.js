function log(o){console.log(o);}

var gulp = require('gulp');
var uglify = require('gulp-uglify');
var babel = require('gulp-babel');
var concat = require('gulp-concat');
var browserify = require('gulp-browserify');


// compile react
gulp.task('react', function(){
	return gulp.src([ 'src/**/!(index)*.jsx', 'src/**/index.jsx'])
		.pipe(babel({
			plugins : ['transform-react-jsx'],
			presets : ['es2015']
		}))
		.pipe(uglify())
		.pipe(concat('app.js', { newLine: '\n' }))
		.pipe(gulp.dest("dist"));
});


gulp.task('react:watch', function(){
	gulp.watch('src/**/*.jsx', ['react']);
});