const { watch, series, src, dest } = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const browserSync = require('browser-sync').create();
const postcss = require('gulp-postcss');
const autoprefixer = require('autoprefixer');

/* 編譯sass*/
function buildStyles() {
  return src('./scss/*.scss')
    .pipe(sass({outputStyle: 'expanded'}).on('error', sass.logError)) // outputStyle可修改編譯風格(expanded, compact, compressed)
    .pipe(postcss([autoprefixer()]))// 自動css前綴，請到.browserslistrc中填寫需要的版本
    .pipe(dest('./css'))
    .pipe(browserSync.stream()); // sass編譯後自動重整
};

exports.buildStyles = buildStyles;

/* 監聽資料夾、建立server */
function watchFile() {
  browserSync.init({
    proxy: 'localhost/TomatoClock', // 目標網址，請修改成專案位址
  })
  watch('./scss', series(buildStyles)); // sass 監聽，請將路徑修改成sass所在資料夾
  // 監聽的檔案，建議範圍不要太大
  watch('./*.*', {
    ignored: ['./node_modules'], // 忽略的檔案
  }).on('change', browserSync.reload); // 頁面自動重整
}

exports.watchFile = watchFile;

exports.default = series(buildStyles, watchFile);