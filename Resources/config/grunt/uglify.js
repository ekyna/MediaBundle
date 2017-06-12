module.exports = function (grunt, options) {
    return {
        media: {
            files: [
                {
                    'src/Ekyna/Bundle/MediaBundle/Resources/public/lib/fancytree/fancytree.js': [
                        'src/Ekyna/Bundle/MediaBundle/Resources/public/tmp/jquery.ui-contextmenu.js',
                        'bower_components/jquery.fancytree/dist/src/jquery.fancytree.js',
                        'bower_components/jquery.fancytree/dist/src/jquery.fancytree.dnd.js',
                        'bower_components/jquery.fancytree/dist/src/jquery.fancytree.edit.js',
                        //'bower_components/jquery.fancytree/dist/src/jquery.fancytree.wide.js',
                        'bower_components/jquery.fancytree/dist/src/jquery.fancytree.glyph.js'
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
