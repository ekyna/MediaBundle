module.exports = function (grunt, options) {
    return {
        /*media_fancytree: {
            expand: true,
            cwd: 'bower_components/jquery.fancytree/dist',
            src: ['skin-xp/**'],
            dest: 'src/Ekyna/Bundle/MediaBundle/Resources/public/lib/fancytree'
        },*/
        media_fancybox: {
            expand: true,
            cwd: 'bower_components/fancyBox/source',
            src: ['**'],
            dest: 'src/Ekyna/Bundle/MediaBundle/Resources/public/lib/fancybox'
        },
        media_swfobject: {
            expand: true,
            cwd: 'bower_components/swfobject/swfobject',
            src: ['swfobject.js', 'expressInstall.swf'],
            dest: 'src/Ekyna/Bundle/MediaBundle/Resources/public/lib/swfobject'
        },
        media_videojs: {
            expand: true,
            cwd: 'bower_components/video.js/dist',
            src: ['font/**', 'lang/**', 'ie8/videojs-ie8.min.js', 'video.min.js', 'video-js.min.css', 'video-js.swf'],
            dest: 'src/Ekyna/Bundle/MediaBundle/Resources/public/lib/videojs',
            rename: function(dest, src) {
                return dest + '/' + src.replace(/\.min/, '');
            }
        },
        media_js: {
            expand: true,
            cwd: 'src/Ekyna/Bundle/MediaBundle/Resources/private',
            src: ['js/**', 'img/**'],
            dest: 'src/Ekyna/Bundle/MediaBundle/Resources/public'
        }
    }
};
