define('ekyna-form/media-choice', ['jquery', 'ekyna-modal', 'ekyna-media-browser', 'routing'], function($, Modal, Browser) {
    "use strict";

    var MediaChoiceWidget = function($elem) {
        this.$elem = $($elem);
        this.defaults = {};
        this.config = $.extend({}, this.defaults, this.$elem.data('config'));
        this.$thumb = this.$elem.find('.media-choice');
    };

    MediaChoiceWidget.prototype = {
        constructor: MediaChoiceWidget,
        defaults: {
            multiple: false,
            types: [],
            limit: 1
        },
        init: function () {
            var that = this;
            this.$thumb.bind('click', function() {
                that.addMedia();
            });
        },
        addMedia: function() {
            var that = this,
                modal = new Modal();
            $(modal).on('ekyna.modal.content', function (e) {
                if (e.contentType == 'html') {
                    var browser = new Browser(e.content);
                    browser.init();
                    $(browser).bind('ekyna.media-browser.selection', function(e) {
                        that.$thumb.find('img').attr('src', e.media.thumb);
                        that.$thumb.find('.name').text(e.media.title);
                        that.$thumb.find('input').val(e.media.id);
                        modal.getDialog().close();
                    });
                } else {
                    throw "Unexpected modal content type.";
                }
            });

            /*$(modal).on('ekyna.modal.load_fail', function () {

            });*/
            var params = {};
            if (that.config.types.length > 0) {
                params = {types: this.config.types};
            }
            modal.load({url: Routing.generate('ekyna_media_browser_admin_modal', params)});
        }
    };

    MediaChoiceWidget.defaults = MediaChoiceWidget.prototype.defaults;

    return {
        init: function($element) {
            new MediaChoiceWidget($element).init();
        }
    };
});
