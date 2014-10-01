# LivingSocial Careers

Welcome to the LivingSocial Careers Site. This is a wordpress theme. 

## Tasks

All tasks are run with [gulp](http://gulpjs.com). To run gulp, you'll also need to install [node](http://nodejs.org). 

Install gulp globally
```
$ sudo npm install -g gulp
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

I'll make this a little more streamlined