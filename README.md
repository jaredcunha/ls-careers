# LivingSocial Careers

Welcome to the LivingSocial Careers Site. This is a wordpress theme. 

## Tasks

All tasks are run with [gulp](http://gulpjs.com). To run gulp, you'll also need to install [node](http://nodejs.org). 

Install gulp globally, then locally
```
$ npm install --global gulp
$ npm install --save-dev gulp
```

Install the gulp plugins (you may need to use sudo)
```
$ npm install --save-dev gulp-sass gulp-minify-css gulp-uglify gulp-concat gulp-rename gulp-jshint gulp-clean gulp-svgmin gulp-imagemin gulp-svg-sprites gulp-filter gulp-size gulp-svg2png
```

Gulp is already installed locally. To run, simply go to your project directory and type:
```
$ gulp
```

### SVG Icons
SVG Sprites are generated with gulp svg-sprites. To run first generate the sprites and corresponding CSS.
```
$ gulp sprites
```

Next, run the normal gulp task and save any CSS file.
```
$ gulp
```

## Packaging for WP Engine
go one directory back in `themes` with `$ cd ../`

zip the theme, but exclude the `node_modules` directory to avoid file size limits. 

`$ zip -r lscareers.zip ls-careers -x ls-careers/node_modules/\*`