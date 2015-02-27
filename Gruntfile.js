module.exports = function(grunt) {

    //Initializing the configuration object
    grunt.initConfig({

        // Task configuration
        less: {
            development: {
                options: {
                    compress: true  //minifying the result
                },
                files: {
                    //compiling main.less into main.css
                    "./public/assets/stylesheets/main.css":"./resources/assets/stylesheets/main.less",
                    "./public/assets/stylesheets/login.css":"./resources/assets/stylesheets/login.less"
                }
            }
        },
        concat: {
            options: {
                separator: '\n'
            },
            angular: {
                src: [
                    './bower_components/angular/angular.min.js',
                    './bower_components/angular-route/angular-route.min.js',
                    './bower_components/angular-bootstrap/ui-bootstrap-tpls.min.js'
                ],
                dest: './public/assets/javascript/angular.js'
            },
            masonry: {
                src: [
                    './bower_components/imagesloaded/imagesloaded.pkgd.min.js',
                    './bower_components/masonry/dist/masonry.pkgd.min.js',
                    './bower_components/angular-masonry/angular-masonry.js'
                ],
                dest: './public/assets/javascript/masonry.js'
            },
            ngflow:{
                src: [
                    './bower_components/ng-flow/dist/ng-flow-standalone.min.js'
                ],
                dest: './public/assets/javascript/ng-flow.js'
            },
            app:{
                src: [
                    './resources/assets/javascript/app.js'
                ],
                dest: './public/assets/javascript/app.js'
            },
            controllers:{
                src: [
                    './resources/assets/javascript/controllers.js'
                ],
                dest: './public/assets/javascript/controllers.js'
            },
            main: {
                src: [
                    './bower_components/jquery/dist/jquery.min.js',
                    './bower_components/bootstrap/dist/js/bootstrap.min.js',
                    './resources/assets/javascript/main.js'
                ],
                dest: './public/assets/javascript/main.js'
            }
        },
        copy: {
            glyphicons: {
                files: [
                    {expand: true, cwd: './bower_components/bootstrap/fonts/', src: ['**'], dest: './public/assets/fonts/'}
                ]
            }
        },
        uglify: {
            options: {
                mangle: false  // Use if you want the names of your functions and variables unchanged
            },
            main: {
                files: {
                    './public/assets/javascript/main.js': './public/assets/javascript/main.js'
                }
            }
        },
        phpunit: {
            classes: {
            },
            options: {
            }
        },
        watch: {
            main: {
                files: [
                    //watched files
                    './bower_components/jquery/jquery.js',
                    './bower_components/bootstrap/dist/js/bootstrap.js',
                    './resources/assets/javascript/main.js'
                ],
                tasks: ['concat:main','uglify:main'],     //tasks to run
                options: {
                    livereload: true                        //reloads the browser
                }
            },
            less: {
                files: ['./resources/assets/stylesheets/*.less'],  //watched files
                tasks: ['less'],                          //tasks to run
                options: {
                    livereload: true                        //reloads the browser
                }
            },
            tests: {
                files: ['app/Controllers/*.php','app/Models/*.php'],  //the task will run only when you save files in this location
                tasks: ['phpunit']
            }
        }
    });

    // Plugin loading
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-phpunit');

    // Task definition
    grunt.registerTask('default', ['less','copy','concat','uglify']);

};
