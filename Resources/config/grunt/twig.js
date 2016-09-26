module.exports = function (grunt, options) {
    return {
        options: {
            amd_wrapper: true,
            amd_define: 'ekyna-media-templates',
            variable: 'templates',
            template_key: function(path) {
                var split = path.split('/');
                return split[split.length-1];
            }
        },
        media: {
            files: {
                'src/Ekyna/Bundle/MediaBundle/Resources/public/js/ekyna-media-templates.js': [
                    'src/Ekyna/Bundle/MediaBundle/Resources/views/thumb.html.twig'
                ]
            }
        }
    }
};
