module.exports = {
    'cssmin:media': [
        'cssmin:media_browser'
    ],
    'copy:media': [
        'copy:media_lib',
        'copy:media_img'
    ],
    'build:media': [
        'clean:media_pre',
        'copy:media',
        'cssmin:media',
        'twig:media',
        'uglify:media_js',
        'clean:media_post'
    ]
};
