/*
* If you don't have node.js installed, INSTALL NODE.JS
*
* If you don't have gulp installed:
* sudo npm install --global gulp
* sudo npm install --save-dev gulp
*
* Make sure gulpfile.js and package.json are in the same directory.
* Navigate to directory of gulpfile.js and package.json file and type `npm install`.
*
* In the gulp file, update all paths to location of current theme directory.
* Also update the browserSync proxy to the current project local URL
*
* RECOMMENDED: run 'gulp build' for initial setup and first time builds. run 'gulp watch' every time after that.
*
* Run 'gulp build' if you want to just run necessary tasks for building the theme
* Run 'gulp watch' to watch for any file changes while in development
* Run 'gulp' streamlined to run sass, lint and watch tasks
*
* NOTE: Browser Sync will run when running any task associated with the 'watch' task (including the default task). This will open a new tab at
* http://localhost:3000. In the command line you can find the links for local and external. There are also links for the UI to adjust options.
*
*/

var gulp = require('gulp');
var sass = require('gulp-sass');
var jshint = require('gulp-jshint');
var autoprefixer = require('gulp-autoprefixer');
var cleanCSS = require('gulp-clean-css');
var sourcemaps = require('gulp-sourcemaps');
var imagemin = require('gulp-imagemin');
var imageminPngcrush = require('imagemin-pngcrush');
var uglify = require('gulp-uglify');
var pump = require('pump');
var concat = require('gulp-concat');
var browserSync = require('browser-sync').create();
var favicons = require("gulp-favicons"),
    gutil = require("gulp-util");


var paths = {
    css: './client/src/css',
    cssany: ['./client/src/css/*.css'],
    sass: './client/src/css/scss',
    sassany: ['./client/src/css/scss/**/*.scss'],
    sassdist: './client/dist/css',
    jsany: ['./client/src/javascript/*.js'],
    jssrc: './client/src/javascript',
    jsdist: './client/dist/javascript',
    jsdistany: './client/dist/javascript/**/*.js'
};

var autoprefixerOptions = {
    browsers: ['last 2 versions', 'IE > 9']
};


// sass to css
gulp.task('sass', function () {
    gulp.src(paths.sassany)
    .pipe(sourcemaps.init())
    .pipe(sass().on('error', sass.logError))
    .pipe(autoprefixer(autoprefixerOptions))
    .pipe(cleanCSS())
    .pipe(sourcemaps.write('../sassmaps'))
    .pipe(gulp.dest(paths.sassdist))
    .pipe(browserSync.stream());
});

// lint all scripts, then run 'scripts' task
// NOTE: add any files you want ignored to the .jshintignore file
gulp.task('lint', ['scripts'], function() {
    return gulp.src(paths.jsany)
    .pipe(jshint())
    .pipe(jshint.reporter('default'));
});

// combine all scripts to one file, then uglify
gulp.task('scripts', function (cb) {
    pump([
        gulp.src(paths.jsany),
        concat('scripts.min.js'),
        gulp.dest(paths.jsdist),
        uglify(),
        gulp.dest(paths.jsdist)
    ],
    cb
    );
});

// copy BS js from node-modules
gulp.task('copy', function () {
    gulp.src(['./node_modules/bootstrap/dist/js/bootstrap.bundle.min.js'])
    .pipe(gulp.dest(paths.jssrc));
});

// compress all images in images folder (and subfolders)
gulp.task('images', function() {
    gulp.src('./client/src/images/**/*')
    .pipe(imagemin({
        progressive: true,
        interlaced: true,
        svgoPlugins: [{removeUnknownsAndDefaults: false}]
    }))
    .pipe(gulp.dest('./client/dist/images'))
});

gulp.task('watch', function() {
    gulp.watch(paths.sassany, ['sass']);
    //gulp.watch('./client/src/images/**/*', ['images']);
    gulp.watch(paths.jsany, ['lint']);

    //gulp.watch('./client/dist/images/**/*').on('change', browserSync.reload);
});

gulp.task("icons", function () {
    return gulp.src("./client/dist/images/DynamicLogo.png").pipe(favicons({
        appName: "My App",
        appDescription: "This is my application",
        developerName: "Dynamic, Inc.",
        developerURL: "http://dynamicagency.com/",
        background: "#FFFFFF",
        path: "./client/src/images/favicons",
        url: "http://dynamicagency.com/",
        display: "standalone",
        orientation: "portrait",
        start_url: "/?homescreen=1",
        version: 1.0,
        logging: false,
        online: false,
        html: "index.html",
        pipeHTML: true,
        replace: true
    }))
    .on("error", gutil.log)
    .pipe(gulp.dest("./client/src/images/favicons"));
});


gulp.task('build', ['sass', 'lint', 'images']);
gulp.task('default', ['sass', 'lint', 'watch']);

