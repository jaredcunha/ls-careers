// npm install gulp-sass gulp-minify-css gulp-uglify gulp-concat gulp-rename gulp-jshint gulp-clean gulp-svgmin gulp-imagemin gulp-size

    // Gulp
    var gulp = require('gulp'),

    // Sass/CSS stuff
    sass = require('gulp-sass'),
    minifycss = require('gulp-minify-css'),

    // JavaScript
    uglify = require('gulp-uglify'),
    concat = require('gulp-concat'),
    rename = require("gulp-rename"),
    jshint = require('gulp-jshint'),
    clean = require('gulp-clean'),

    // Images,
    svgmin = require('gulp-svgmin'),
    imagemin = require('gulp-imagemin'),
    svgSprite = require("gulp-svg-sprites"),
    filter    = require('gulp-filter'),
    svg2png   = require('gulp-svg2png'),

    // Stats and Things
    size = require('gulp-size');

    // compile all your Sass
    gulp.task('sass', function (){
        gulp.src(['css/scss/global.scss'])
            .pipe(sass({
                includePaths: require('node-bourbon').includePaths,
                errLogToConsole: true,
                includePaths: ['dist/dev/css'],
                outputStyle: 'expanded'
            }))
            .pipe(gulp.dest('dist/dev/css'))
            .pipe(minifycss())
            .pipe(gulp.dest('dist/prod/css'));
    });

    // Scripts
    gulp.task('scripts', function() {
      gulp.src(['js/libs/*.js', 'js/plugins/*.js', 'js/scripts/*.js'])
        .pipe(concat('global.js'))
        .pipe(gulp.dest('dist/dev/js'))
        .pipe(uglify('comments:false'))
        .pipe(gulp.dest('dist/prod/js'))
    });

    gulp.task('lint', function() {
      return gulp.src('js/scripts/*.js')
        .pipe(jshint())
        .pipe(jshint.reporter('default'));
    });

    gulp.task('move', function(){
      gulp.src('js/polyfills/*.*')
      .pipe(gulp.dest('dist/prod/js'));
      gulp.src('assets/svg/*')
      .pipe(gulp.dest('dist/prod/svg'));
    });

    // Images
    gulp.task('svgmin', function() {
        gulp.src('images/*.svg')
        .pipe(svgmin())
        .pipe(gulp.dest('dist/dev/images/svg'))
        .pipe(gulp.dest('dist/prod/images/svg'));
    });

    gulp.task('sprites', function () {
        return gulp.src('images/icons/*.svg')
            .pipe(svgSprite({
                 cssFile: "scss/_sprite.scss",
                 padding: 4
             }))
            .pipe(gulp.dest("assets"))
            .pipe(filter("**/*.svg"))
            .pipe(svg2png())
            .pipe(gulp.dest("assets"));
    });

    gulp.task('imagemin', function () {
        gulp.src('./dev/img/**/*')
        .pipe(imagemin())
        .pipe(gulp.dest('./dev/img'))
        .pipe(gulp.dest('./prod/img'));
    });

    // Stats and Things
    gulp.task('stats', function () {
        gulp.src('./prod/**/*')
        .pipe(size())
        .pipe(gulp.dest('./prod'));
    });

//

    gulp.task('watch', function(){
        gulp.watch('css/scss/**/*.scss', ['sass']);
        gulp.watch(["js/**/*.js", "!js/build/**/*.js", "!js/build/*.js"], ['scripts', 'lint', 'move']);
    });

    gulp.task('default', ['watch', 'move'])