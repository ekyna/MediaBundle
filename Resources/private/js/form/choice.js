define('ekyna-media/form/choice',
    ['jquery', 'routing', 'ekyna-modal', 'ekyna-media/browser', 'ekyna-media/templates'],
    function($, Router, Modal, Browser, Templates) {
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
            this.$elem.on('click', '.media-thumb [data-role="select"]', function(e) {
                that.selectMedia($(e.target).parents('.media-thumb').eq(0).data('media')['folder_id']);
            });
            this.$elem.on('click', '.media-thumb [data-role="remove"]', function() {
                that.removeMedia();
            });
        },
        selectMedia: function(folderId) {
            var that = this,
                modal = new Modal(),
                browser;

            $(modal).on('ekyna.modal.content', function (e) {
                if (e.contentType == 'html') {
                    browser = new Browser(e.content);

                    // Handle browser selection
                    $(browser).bind('ekyna.media-browser.selection', function(e) {
                        if (e.hasOwnProperty('media')) {
                            var $thumb = $(Templates['@EkynaMedia/Js/thumb.html.twig'].render({
                                media: e.media,
                                controls: that.config.controls,
                                selector: false
                            }));
                            $thumb.data('media', e.media);

                            that.$elem.find('.media-thumb').replaceWith($thumb);
                            that.$elem.find('input').val(e.media.id);
                        }
                        modal.getDialog().close();
                    });

                    // TODO Update media id edited in browser

                    // Clear media if deleted in browser
                    $(browser).bind('ekyna.media-browser.media_delete', function(e) {
                        if (e.hasOwnProperty('media') && e.media.id == that.$elem.find('input').val()) {
                            that.removeMedia();
                        }
                    });
                } else {
                    throw "Unexpected modal content type.";
                }
            });

            $(modal).on('ekyna.modal.load_fail', function () {
                alert('Failed to load media browser.');
            });

            $(modal).on('ekyna.modal.shown', function () {
                if (browser) {
                    browser.init({folderId: folderId});
                }
            });

            $(modal).on('ekyna.modal.hide', function () {
                if (browser) {
                    browser = null;
                }
            });

            var params = {mode: 'single_selection'};
            if (that.config.types.length > 0) {
                params.types = this.config.types;
            }
            modal.load({url: Router.generate('ekyna_media_browser_admin_modal', params)});
        },
        removeMedia: function() {
            var $empty = $(this.$elem.data('empty-thumb'));
            this.$elem.find('.media-thumb').replaceWith($empty);
            this.$elem.find('input').val('');
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
