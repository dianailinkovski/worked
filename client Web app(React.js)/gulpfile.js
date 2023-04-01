// including plugins
var gulp = require('gulp');
var concat = require("gulp-concat");
var babel = require("gulp-babel");
const sourcemaps = require('gulp-sourcemaps');
const uglify = require('gulp-uglify');

// task
gulp.task('default', function () {
    gulp.src('./src/**/**/*.js') // path to your files
        .pipe(sourcemaps.init())
        .pipe(concat('app.min.js'))  // concat and name it "concat.js"
        .pipe(babel({presets: ['react']}))
//        .pipe(uglify()) doesn't work with react
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('dist'));
});