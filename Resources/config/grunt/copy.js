module.exports = function (grunt, options) {
    return {
        media_lib: {
            files: [
                {
                    expand: true,
                    cwd: 'bower_components/fancyBox/source',
                    src: ['**'],
                    dest: 'src/Ekyna/Bundle/MediaBundle/Resources/public/lib/fancybox'
                },
                {
                    expand: true,
                    cwd: 'bower_components/swfobject/swfobject',
                    src: ['swfobject.js', 'expressInstall.swf'],
                    dest: 'src/Ekyna/Bundle/MediaBundle/Resources/public/lib/swfobject'
                },
                {
                    expand: true,
                    cwd: 'bower_components/video.js/dist',
                    src: ['font/**', 'lang/**', 'ie8/videojs-ie8.min.js', 'video.min.js', 'video-js.min.css', 'video-js.swf'],
                    dest: 'src/Ekyna/Bundle/MediaBundle/Resources/public/lib/videojs',
                    rename: function (dest, src) {
                        return dest + '/' + src.replace(/\.min/, '');
                    }
                }
            ]
        },
        media_libs_fix: {
            files: [
                {
                    src: 'bower_components/ui-contextmenu/jquery.ui-contextmenu.js',
                    dest: 'src/Ekyna/Bundle/MediaBundle/Resources/public/tmp/jquery.ui-contextmenu.js' // tmp to uglify
                }
            ],
            options: {
                process: function (content, srcpath) {
                    if (/jquery\.ui-contextmenu/.test(srcpath)) {
                        content = content.replace(/jquery-ui\/ui\/widgets\/menu/g, 'jquery-ui/menu');
                    }

                    return content;
                }
            }
        },
        media_img: {
            expand: true,
            cwd: 'src/Ekyna/Bundle/MediaBundle/Resources/private',
            src: ['img/**'],
            dest: 'src/Ekyna/Bundle/MediaBundle/Resources/public'
        },
        media_js: { // for watch:media_js
            expand: true,
            cwd: 'src/Ekyna/Bundle/MediaBundle/Resources/private/js',
            src: ['**'],
            dest: 'src/Ekyna/Bundle/MediaBundle/Resources/public/js'
        },
        media_css: { // for watch:media_css
            expand: true,
            cwd: 'src/Ekyna/Bundle/MediaBundle/Resources/private/css',
            src: ['browser.css', 'form.css'],
            dest: 'src/Ekyna/Bundle/MediaBundle/Resources/public/css'
        }
    }
};
