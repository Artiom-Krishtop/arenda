;(function(){
	"use strict";

	/**
	 * TODO add gulp-imagemin && add task for minify template styles and scripts
	 */

	const gulp 		= require('gulp'),
		concat 		= require('gulp-concat'),
		uglyfly     = require('gulp-uglyfly'),
		rename 		= require('gulp-rename'),
		sourcemaps  = require('gulp-sourcemaps'),
		plumber     = require('gulp-plumber'),
		clean       = require('gulp-clean'),
		sass        = require('gulp-sass'),
		sassVars    = require('gulp-sass-vars'),
		cache       = require('gulp-cache'),
		imagemin    = require('gulp-imagemin'),
        livereload  = require('gulp-livereload'),
		root        = './application/',
		cleanCSS    = require('gulp-clean-css'),
		watch		= require('gulp-watch'),
		util		= require('gulp-util'),
		PARAMS      = {
			source: {
				css: root+'src/**/*.css',
				img: root+'src/img/**/*',
				js: root+'src/**/*.js',
				theme: './themes/',
				component: './components/'
			},
			dist : root+'distr/',
			filename: 'app'
		},
		themes = {
			'blue': '#3598dc',
			'green': '#26ae01',
			'orange': '#ff8400',
			'purple': '#9c59b8',
			'red': '#ce3a12'
		};

	gulp.task('buildCss', function () {
		let css = gulp.src(PARAMS.source.css);
		css
			.pipe(sourcemaps.init())
			.pipe(concat(PARAMS.filename+'.css'))
			.pipe(sourcemaps.write('.',{includeContent: false, sourceRoot: '../src'}))
			.pipe(gulp.dest(PARAMS.dist))
            .pipe(livereload());

		css
			.pipe(sourcemaps.init())
			.pipe(concat(PARAMS.filename+'.min.css'))
			.pipe(cleanCSS())
			.pipe(sourcemaps.write('.',{includeContent: false, sourceRoot: '../src'}))
			.pipe(gulp.dest(PARAMS.dist))
            .pipe(livereload());

		return true;
	});
	gulp.task('buildJs', function () {
		let js = gulp.src(PARAMS.source.js);

		js
			.pipe(sourcemaps.init())
			.pipe(concat(PARAMS.filename+'.js'))
			.pipe(sourcemaps.write('.',{includeContent: false, sourceRoot: '../src'}))
			.pipe(gulp.dest(PARAMS.dist))
            .pipe(livereload());

		js
			.pipe(sourcemaps.init())
			.pipe(concat(PARAMS.filename+'.min.js'))
			.pipe(uglyfly())
			.pipe(sourcemaps.write('.',{includeContent: false, sourceRoot: '../src'}))
			.pipe(gulp.dest(PARAMS.dist))
            .pipe(livereload());

		return true;
	});
	gulp.task('buildImg', function () {
		return gulp.src(PARAMS.source.img)
			.pipe(cache(imagemin({ optimizationLevel: 3, progressive: true, interlaced: true })))
			.pipe(gulp.dest(PARAMS.dist+'/img/'))
            .pipe(livereload());
	});
    gulp.task('clean', function () {
		return gulp.src(PARAMS.dist+"*", {read: false})
				.pipe(clean());
	});

	//themes
	gulp.task('theme', function () {
		for ( let themeName in themes) {
			let themePath = PARAMS.source.theme + themeName+'/',
				color = themes[themeName];

			gulp.src(PARAMS.source.theme + '/src/*.scss')
				.pipe(sassVars({'primary-color': color}, { verbose: true }))
				.pipe(sourcemaps.init())
				.pipe(sass(/*{outputStyle: 'compressed'}*/).on('error', sass.logError))
				.pipe(sourcemaps.write('.',{includeContent: false, sourceRoot: '../'}))
				.pipe(gulp.dest(themePath))
                .pipe(livereload());
		}
	});
	gulp.task('theme:w', function () {
		gulp.watch(PARAMS.source.theme+'*.scss', ['theme']);
	});

	gulp.task('componentCss', function () {
		return gulp.src([PARAMS.source.component+'**/*.css', '!./**/*.min.css', '!./**/assets/**'], {dot: true})
				.pipe(cleanCSS({rebase: false}))
				.pipe(rename({suffix: '.min'}))
				.pipe(gulp.dest(PARAMS.source.component))
				.pipe(livereload());
	});
	gulp.task('componentJs', function () {
		return gulp.src([PARAMS.source.component+'**/*.js', '!./**/*.min.js', '!./**/assets/**'], {dot: true})
			.pipe(uglyfly())
			.pipe(rename({suffix: '.min'}))
			.pipe(gulp.dest(PARAMS.source.component))
			.pipe(livereload());
	});
	gulp.task('component:w', ['componentCss', 'componentJs'], function () {
		gulp.watch([PARAMS.source.component+'**/*.css', '!./**/*.min.css', '!./**/assets/**'], {dot: true}, ['componentCss']);
		gulp.watch([PARAMS.source.component+'**/*.js', '!./**/*.min.js', '!./**/assets/**'], {dot: true}, ['componentJs']);
		return true;
	});
	

	gulp.task("default", ['buildCss', 'buildJs', 'buildImg'], function  () {
		return true;
	});
	gulp.task("dev", ['theme:w'], function  () {
		gulp.watch(PARAMS.source.js, ['buildJs']);
		gulp.watch(PARAMS.source.css, ['buildCss']);
		gulp.watch(PARAMS.source.img, ['buildImg']);
		return true;
	});

	gulp.task("live", ['dev'], function  () {
		livereload({ start: true });
		watch(['components/**', 'header.php', 'footer.php', 'description.php']).on('change', function(file) {
			util.log(util.colors.yellow('Template file changed'));
			livereload.changed(file);
		});
		return true;
	});
})();