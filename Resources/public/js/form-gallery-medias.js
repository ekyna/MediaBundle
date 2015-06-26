define('ekyna-form/gallery-medias', ['jquery', 'ekyna-form/media-choice', 'jquery-ui'], function($, MediaChoice) {
    "use strict";

    var MediaGalleryImagesWidget = function($elem) {
        this.$elem = $($elem);
        this.defaults = {};
        this.config = $.extend({}, this.defaults, this.$elem.data('config'));
        //this.$thumb = this.$elem.find('.media-choice');
    };

    MediaGalleryImagesWidget.prototype = {
        constructor: MediaGalleryImagesWidget,
        defaults: {
            types: [],
            limit: 1
        },
        init: function () {
            var that = this;

        },
        addMedia: function() {

        }
    };

    MediaGalleryImagesWidget.defaults = MediaGalleryImagesWidget.prototype.defaults;

    return {
        init: function($element) {
            new MediaGalleryImagesWidget($element).init();
        }
    };
});
