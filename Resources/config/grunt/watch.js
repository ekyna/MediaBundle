module.exports = function (grunt, options) {
    return {
        media_js: {
            files: ['src/Ekyna/Bundle/MediaBundle/Resources/private/js/*.js'],
            tasks: ['copy:media_js'],
            options: {
                spawn: false
            }
        }
    }
};