module.exports = function (grunt, options) {
    return {
        media_js: {
            files: [
                {
                    expand: true,
                    cwd: 'src/Ekyna/Bundle/MediaBundle/Resources/private/js',
                    src: '**/*.js',
                    dest: 'src/Ekyna/Bundle/MediaBundle/Resources/public/js'
                },
                {
                    expand: true,
                    cwd: 'src/Ekyna/Bundle/MediaBundle/Resources/public/tmp/fancytree',
                    src: '*.js',
                    dest: 'src/Ekyna/Bundle/MediaBundle/Resources/public/lib/fancytree'
                }
            ]
        }
    }
};
