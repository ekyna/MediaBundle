module.exports = function (grunt, options) {
    return {
        media: {
            files: {
                'src/Ekyna/Bundle/MediaBundle/Resources/public/tmp/bootstrap.css':
                    'src/Ekyna/Bundle/MediaBundle/Resources/private/less/bootstrap.less'
            }
        }
    }
};
