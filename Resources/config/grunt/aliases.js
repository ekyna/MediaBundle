module.exports = {
    'copy:media': [
        'copy:media_lib',
        'copy:media_img'
    ],
    'build:media': [
        'clean:media_pre',
        'copy:media',
        'cssmin:media',
        'uglify:media'
    ]
};
