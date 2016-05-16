module.exports = {
    'copy:media': [
        'copy:media_fancybox',
        'copy:media_swfobject',
        'copy:media_videojs',
        'copy:media_js'
    ],
    'build:media': [
        'clean:media',
        'copy:media',
        'cssmin:media',
        'uglify:media'
    ]
};
