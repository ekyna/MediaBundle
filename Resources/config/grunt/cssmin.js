module.exports = function (grunt, options) {
    return {
        media: {
            files: {
                'src/Ekyna/Bundle/MediaBundle/Resources/public/lib/fancytree/fancytree.css': [
                    'bower_components/jquery.fancytree/dist/skin-bootstrap/ui.fancytree.css'
                ],
                'src/Ekyna/Bundle/MediaBundle/Resources/public/css/form.css': [
                    'src/Ekyna/Bundle/MediaBundle/Resources/private/css/thumb.css',
                    'src/Ekyna/Bundle/MediaBundle/Resources/private/css/form.css'
                ],
                'src/Ekyna/Bundle/MediaBundle/Resources/public/css/browser.css': [
                    'src/Ekyna/Bundle/MediaBundle/Resources/private/css/browser.css'
                ]
            }
        }
    }
};
