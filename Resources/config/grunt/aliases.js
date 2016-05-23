module.exports = {
    'copy:media': [
        'copy:media_fancybox',
        'copy:media_swfobject',
        'copy:media_videojs',
        'copy:media_js'
    ],
    'build:media': [
        'clean:media_pre',
        'copy:media',
        'less:media',
        'cssmin:media',
        'uglify:media',
        'clean:media_post'
    ]
};
