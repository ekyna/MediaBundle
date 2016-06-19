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
        media_img: {
            expand: true,
            cwd: 'src/Ekyna/Bundle/MediaBundle/Resources/private',
            src: ['img/**'],
            dest: 'src/Ekyna/Bundle/MediaBundle/Resources/public'
        },
        media_js: { // for watch:media_js
            expand: true,
            cwd: 'src/Ekyna/Bundle/MediaBundle/Resources/private',
            src: ['js/**'],
            dest: 'src/Ekyna/Bundle/MediaBundle/Resources/public'
        }
    }
};
