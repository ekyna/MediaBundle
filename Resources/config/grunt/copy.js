module.exports = function (grunt, options) {
    return {
        media_lib: {
            files: [
                {
                    expand: true,
                    cwd: 'node_modules/@fancyapps/fancybox/dist',
                    src: ['**'],
                    dest: 'src/Ekyna/Bundle/MediaBundle/Resources/public/lib/fancybox'
                },
                {
                    src: 'node_modules/swfobject-amd/swfobject.js',
                    dest: 'src/Ekyna/Bundle/MediaBundle/Resources/public/lib/swfobject/swfobject.js'
                },
                {
                    src: 'node_modules/swfobject-amd/lib/expressInstall.swf',
                    dest: 'src/Ekyna/Bundle/MediaBundle/Resources/public/lib/swfobject/expressInstall.swf'
                },
                {
                    expand: true,
                    cwd: 'node_modules/video.js/dist',
                    src: ['font/**', 'lang/**', 'video.min.js', 'video-js.min.css'],
                    dest: 'src/Ekyna/Bundle/MediaBundle/Resources/public/lib/videojs',
                    rename: function (dest, src) {
                        return dest + '/' + src.replace(/\.min/, '');
                    }
                },
                {
                    src: 'node_modules/jquery.fancytree/dist/skin-bootstrap/ui.fancytree.min.css',
                    dest: 'src/Ekyna/Bundle/MediaBundle/Resources/public/lib/fancytree/fancytree.css'
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
