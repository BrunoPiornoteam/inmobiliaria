import { src, dest, watch, series } from 'gulp'
import * as dartSass from 'sass'
import gulpSass from 'gulp-sass'
import concat from 'gulp-concat'
import terser from 'gulp-terser'
import sourcemaps from 'gulp-sourcemaps'

const sass = gulpSass(dartSass)

// --- JavaScript ---
export function js() {
    return src('src/js/**/*.js', { sourcemaps: true })
        .pipe(concat('bundle.js'))
        .pipe(terser())
        .pipe(dest('dist/js', { sourcemaps: '.' }))
}

// --- SCSS ---
export function css() {
    return src('src/scss/app.scss')
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(sourcemaps.write('.'))
        .pipe(dest('dist/css'))
}

// --- Watcher ---
export function dev() {
    watch('src/scss/**/*.scss', css)
    watch('src/js/**/*.js', js)
}

export default series(js, css, dev)