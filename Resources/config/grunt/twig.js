module.exports = function (grunt, options) {
    return {
        media: {
            options: {
                amd_wrapper: true,
                amd_define: 'ekyna-media/templates',
                variable: 'templates',
                template_key: function(path) {
                    var split = path.split('/');
                    return '@EkynaMedia/Js/' + split[split.length-1];
                }
            },
            files: {
                'src/Ekyna/Bundle/MediaBundle/Resources/public/js/templates.js': [
                    'src/Ekyna/Bundle/MediaBundle/Resources/views/Js/thumb.html.twig'
                ]
            }
        }
    }
};
