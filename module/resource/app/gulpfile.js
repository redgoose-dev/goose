function log(o){console.log(o);}

var gulp = require('gulp');
var uglify = require('gulp-uglify');
var babel = require('gulp-babel');
var concat = require('gulp-concat');


// compile react
gulp.task('react', function(){
	return gulp.src([ 'src/**/!(router)*.jsx', 'src/**/router.jsx'])
		.pipe(babel({
			plugins : ['transform-react-jsx'],
			presets : ['es2015']
		}))
		.pipe(uglify())
		.pipe(concat('app.js', { newLine: '\n' }))
		.pipe(gulp.dest("dist"));
});

gulp.task('react:watch', function(){
	gulp.watch([ 'src/**/!(head)*.jsx', 'src/**/head.jsx'], ['react']);
});