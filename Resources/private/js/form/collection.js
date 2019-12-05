define('ekyna-media/form/collection',
    ['jquery', 'routing', 'ekyna-modal', 'ekyna-media/browser', 'ekyna-media/templates', 'jquery-ui/widgets/sortable'],
    function($, Router, Modal, Browser, Templates) {
    "use strict";

    var MediaCollectionWidget = function($elem) {
        this.$elem = $($elem);
        this.defaults = {
            types: [],
            controls: [
                {role: 'move-left', icon: 'arrow-left', title: 'Move left'},
                {role: 'remove', icon: 'remove', title: 'Remove'},
                {role: 'move-right', icon: 'arrow-right', title: 'Move right'}
            ],
            limit: 0
        };
        this.config = $.extend({}, this.defaults, this.$elem.data('config'));
    };

    MediaCollectionWidget.prototype = {
        constructor: MediaCollectionWidget,
        init: function () {
            var that = this;

            that.$elem.closest('form').on('submit', function() {
                that.$elem.find('.ekyna-media-collection-add input').remove();
            });

            that.$elem.on('click', '.ekyna-media-collection-media [data-role="move-left"]', function(e) {
                e.preventDefault();
                that.moveLeft($(e.target).closest('.ekyna-media-collection-media'));
            });
            that.$elem.on('click', '.ekyna-media-collection-media [data-role="move-right"]', function(e) {
                e.preventDefault();
                that.moveRight($(e.target).closest('.ekyna-media-collection-media'));
            });
            that.$elem.on('click', '.ekyna-media-collection-media [data-role="remove"]', function(e) {
                e.preventDefault();
                that.removeMedia($(e.target).closest('.ekyna-media-collection-media'), true);
            });

            that.$elem.on('click', '.ekyna-media-collection-add', function(e) {
                e.preventDefault();
                that.addMedias();
            });

            that.$elem.sortable({
                delay: 150,
                items: '.ekyna-media-collection-media',
                placeholder: 'ekyna-media-collection-placeholder',
                containment: 'parent',
                update: function() {
                    that.updateCollection();
                }
            });

            that.updateCollection();
        },
        addMedias: function() {
            var that = this,
                modal = new Modal(),
                browser;

            $(modal).on('ekyna.modal.content', function (e) {
                if (e.contentType === 'html') {
                    browser = new Browser(e.content);

                    // TODO Update media id edited in browser

                    // Remove media if deleted in browser
                    $(browser).bind('ekyna.media-browser.media_delete', function(e) {
                        if (e.hasOwnProperty('media')) {
                            var $medias = that.$elem.find('.ekyna-media-collection-media').not('.ekyna-media-collection-add');
                            $medias.each(function () {
                                var $media = $(this);
                                if (e.media.id === $media.find('input').val()) {
                                    that.removeMedia($media, false);
                                }
                            });
                        }
                    });
                } else {
                    throw "Unexpected modal content type.";
                }
            });

            $(modal).on('ekyna.modal.load_fail', function () {
                alert('Failed to load media browser.');
            });

            $(modal).on('ekyna.modal.button_click', function (e) {
                if ((e.buttonId === 'submit') && browser) {
                    var selection = browser.getSelection();
                    for (var i in selection) {
                        if (selection.hasOwnProperty(i)) {
                            that.createMedia(selection[i]);
                        }
                    }
                    that.updateCollection();
                    modal.getDialog().close();
                }
            });

            $(modal).on('ekyna.modal.shown', function () {
                if (browser) {
                    browser.init();
                }
            });

            $(modal).on('ekyna.modal.hide', function () {
                if (browser) {
                    browser = null;
                }
            });

            var params = {mode: 'multiple_selection'};
            if (that.config.types.length > 0) {
                params.types = this.config.types;
            }
            modal.load({url: Router.generate('ekyna_media_browser_admin_modal', params)});
        },
        updateCollection: function() {
            var that = this,
                $medias = that.$elem.find('.ekyna-media-collection-media').not('.ekyna-media-collection-add'),
                max = $medias.size() - 1;

            $medias.each(function(i) {
                var $media = $(this);
                $media.find('input[data-role="position"]').val(i);
                if (i === 0) {
                    $media.find('[data-role="move-left"]').addClass('disabled');
                } else {
                    $media.find('[data-role="move-left"]').removeClass('disabled');
                }
                if (i === max) {
                    $media.find('[data-role="move-right"]').addClass('disabled');
                } else {
                    $media.find('[data-role="move-right"]').removeClass('disabled');
                }
            });

            that.createAddButton();
        },
        createMedia: function (data) {
            var that = this;
            var child = this.$elem.attr('data-prototype'),
                prototypeName = this.$elem.attr('data-prototype-name'),
                count = this.$elem.find('.ekyna-media-collection-media').size();
            var childName = child.match(/id="(.*?)"/);
            var re = new RegExp(prototypeName, "g");
            while ($('#' + childName[1].replace(re, count)).size() > 0) {
                count++;
            }
            child = child.replace(re, count);
            child = child.replace(/__id__/g, childName[1].replace(re, count));

            var $child = $(child);
            $child.removeClass('ekyna-media-collection-add');

            var $thumb = $(Templates['@EkynaMedia/Js/thumb.html.twig'].render({
                media: data,
                controls: that.config.controls,
                selector: false
            }));
            $thumb.data('media', data);

            $child.find('.media-thumb').replaceWith($thumb);
            $child.find('input').val(data.id);

            that.$elem.append($child);
        },
        createAddButton: function() {
            var that = this;

            var $addButton = that.$elem.find('.ekyna-media-collection-add');
            if ($addButton.length === 1) {
                $addButton.detach().appendTo(that.$elem);
                return;
            } else if ($addButton.length > 1) {
                $addButton.remove();
            }

            var child = this.$elem.attr('data-prototype'),
                prototypeName = this.$elem.attr('data-prototype-name'),
                count = this.$elem.find('.ekyna-media-collection-media').size();

            // Check if an element with this ID already exists.
            // If it does, increase the count by one and try again
            var childName = child.match(/id="(.*?)"/);
            var re = new RegExp(prototypeName, "g");
            while ($('#' + childName[1].replace(re, count)).size() > 0) {
                count++;
            }

            child = child.replace(re, count);
            child = child.replace(/__id__/g, childName[1].replace(re, count));

            var $child = $(child);
            that.$elem.append($child);
        },
        moveLeft: function($media) {
            if (!$media.find('[data-role="move-left"]').hasClass('disabled')) {
                $media.prev().before($media.detach());
                this.updateCollection();
            }
        },
        moveRight: function($media) {
            if (!$media.find('[data-role="move-right"]').hasClass('disabled')) {
                $media.next().after($media.detach());
                this.updateCollection();
            }
        },
        removeMedia: function($media, askConfirm) {
            if (!$media.find('[data-role="remove"]').hasClass('disabled')) {
                if (askConfirm && !confirm('Souhaitez-vous réellement retirer cet élément ?')) {
                    return;
                }

                $media.remove();
                this.updateCollection();
            }
        }
    };

    return {
        init: function($element) {
            $element.each(function() {
                new MediaCollectionWidget($(this)).init();
            });
        }
    };
});
