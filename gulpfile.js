const gulp       = require('gulp');
const package    = require('./package.json');
const $          = require('gulp-load-plugins')();
const browserify = require('browserify');
const babelify   = require('babelify');
const source     = require('vinyl-source-stream');
const buffer     = require('vinyl-buffer');
const gutil      = require('gulp-util');

// Manage Workflows
gulp.task('manage_workflows_scss', function () {
    return gulp.src('./assets/src/scss/admin/manage-workflows/app.scss')
        .pipe($.rename('wfm-manage-workflows.min.css'))
        .pipe($.sourcemaps.init())
        .pipe($.sass()
            .on('error', $.sass.logError))
        .pipe($.sourcemaps.init())
        // .pipe($.autoprefixer({
        //     browsers: ['last 2 versions', 'ie >= 9']
        // }))
        .pipe($.sass({outputStyle: 'compressed'}))
        .pipe($.sourcemaps.write())
        .pipe(gulp.dest('./assets/dist/css/'))
        .pipe($.notify({message: 'SASS Manage Workflows complete'}));
});

gulp.task('manage_workflows_js', function () {
    return browserify({
        transform: [
            [babelify, {
                presets: ["latest", "stage-2", "react"]
            }]
        ],
        entries: [
            './assets/src/js/admin/manage-workflows/index.js',
            // './assets/src/js/admin/manage-workflows/tests/tests.js'
        ],
        debug: true
    })
        .bundle()
        .pipe(source('wfm-manage-workflows.min.js'))
        .pipe(gulp.dest('./assets/dist/js/'))
        .pipe($.notify({message: 'JS Manage Workflows complete'}));
});

gulp.task('manage_workflows_js_prod', function () {
    return browserify({
        transform: [
            [babelify, {
                presets: ["es2015", "react"]
            }]
        ],
        entries: ['./assets/src/js/admin/manage-workflows/app.js'],
        debug: true
    })
        .bundle()
        .pipe(source('wfm-manage-workflows.min.js'))
        .pipe(buffer())
        .pipe($.sourcemaps.init({loadMaps: true}))
        .pipe($.uglify())
        .on('error', gutil.log)
        .pipe($.sourcemaps.write('./'))
        .pipe(gulp.dest('./assets/dist/js/'))
        .pipe($.notify({message: 'JS Manage Workflows complete'}));
});

gulp.task('apply-prod-environment', function () {
    process.stdout.write("Setting NODE_ENV to 'production'" + "\n");
    process.env.NODE_ENV = 'production';
    if ( process.env.NODE_ENV != 'production' ) {
        throw new Error("Failed to set NODE_ENV to production!!!!");
    } else {
        process.stdout.write("Successfully set NODE_ENV to production" + "\n");
    }
});

gulp.task('version', function () {
    return gulp.src(['**/*.{php,js,scss,txt}', '!node_modules/'], {base: './'})
        .pipe($.justReplace([
            {
                search: /\{\{VERSION}}/g,
                replacement: package.version
            },
            {
                search: /(\* Version: )\d\.\d\.\d/,
                replacement: "$1" + package.version
            }, {
                search: /(define\( 'WORKFLOWMANAGER_VERSION', ')\d\.\d\.\d/,
                replacement: "$1" + package.version
            }, {
                search: /(Stable tag: )\d\.\d\.\d/,
                replacement: "$1" + package.version
            }
        ]))
        .pipe(gulp.dest('./'));
});

gulp.task('generate_pot', function () {
    return gulp.src('./**/*.php')
        .pipe($.sort())
        .pipe($.wpPot({
            domain: 'workflow-manager',
            package: 'WorkflowManager',
        }))
        .pipe(gulp.dest('./languages/workflow-manager.pot'));
});

gulp.task('default', ['manage_workflows_scss', 'manage_workflows_js'], function () {
    gulp.watch([
        './assets/src/scss/global/**/*.scss',
        './assets/src/scss/admin/manage-workflows/**/*.scss'
    ], ['manage_workflows_scss']);
    gulp.watch(['./assets/src/js/admin/manage-workflows/**/*.js'], ['manage_workflows_js']);
});

gulp.task('build', [
    'version',
    'apply-prod-environment',
    'manage_workflows_scss',
    'manage_workflows_js_prod',
    'generate_pot'
]);
