module.exports = function (grunt, options) {
    return {
        media_js: {
            files: [
                {
                    'src/Ekyna/Bundle/MediaBundle/Resources/public/lib/fancytree/fancytree.js': [
                        'node_modules/jquery.fancytree/dist/src/jquery.fancytree.js',
                        'node_modules/jquery.fancytree/dist/src/jquery.fancytree.dnd.js',
                        'node_modules/jquery.fancytree/dist/src/jquery.fancytree.edit.js',
                        'node_modules/jquery.fancytree/dist/src/jquery.fancytree.glyph.js'
                    ]
                },
                {
                    expand: true,
                    cwd: 'src/Ekyna/Bundle/MediaBundle/Resources/private/js',
                    src: '**/*.js',
                    dest: 'src/Ekyna/Bundle/MediaBundle/Resources/public/js'
                }
            ]
        }
    }
};
