define('ekyna-form/media-choice',
    ['jquery', 'routing', 'twig', 'ekyna-modal', 'ekyna-media-browser'],
    function($, Router, Twig, Modal, Browser) {
    "use strict";

    var MediaChoiceWidget = function($elem) {
        this.$elem = $($elem);
        this.defaults = {types: [], controls: []};
        this.config = $.extend({}, this.defaults, this.$elem.data('config'));
    };

    MediaChoiceWidget.prototype = {
        constructor: MediaChoiceWidget,
        init: function () {
            var that = this;
            this.$elem.on('click', '.media-thumb [data-role="select"]', function() {
                that.selectMedia();
            });
            /*this.$elem.on('click', '.media-thumb [data-role="show"]', function() {

            });*/
            this.$elem.on('click', '.media-thumb [data-role="edit"]', function() {
                /* TODO */
            });
            this.$elem.on('click', '.media-thumb [data-role="download"]', function() {
                /* TODO */
            });
        },
        selectMedia: function() {
            var that = this, modal = new Modal();

            $(modal).on('ekyna.modal.content', function (e) {
                if (e.contentType == 'html') {
                    var browser = new Browser(e.content);
                    browser.init();
                    $(browser).bind('ekyna.media-browser.selection', function(e) {
                        if (e.hasOwnProperty('media')) {
                            var $thumb = $(Twig.render(media_thumb_template, {
                                media: e.media,
                                controls: that.config.controls
                            }));
                            $thumb.data(e.media);

                            that.$elem.find('.media-thumb').replaceWith($thumb);
                            that.$elem.find('input').val(e.media.id);

                            $(that.$elem).trigger(jQuery.Event('ekyna.media-choice.selection'));
                        }
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
            modal.load({url: Router.generate('ekyna_media_browser_admin_modal', params)});
        }
    };

    return {
        init: function($element) {
            $element.each(function() {
                new MediaChoiceWidget($(this)).init();
            });
        }
    };
});
