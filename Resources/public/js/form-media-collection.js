define('ekyna-form/media-collection', ['jquery', 'ekyna-form/media-choice', 'jquery-ui'], function($, MediaChoice) {
    "use strict";

    var MediaCollectionWidget = function($elem) {
        this.$elem = $($elem);
        this.defaults = {limit: 0};
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

            that.$elem.sortable({
                delay: 150,
                items: '.ekyna-media-collection-media',
                placeholder: 'ekyna-media-collection-placeholder',
                containment: 'parent',
                update: function() {
                    that.updateCollection();
                }
            }).disableSelection();

            that.updateCollection();
            that.addMedia();
        },
        addMedia: function() {
            var that = this;

            if (that.$elem.find('.ekyna-media-collection-add').size() == 1) {
                return;
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
            MediaChoice.init($child.find('.ekyna-media-choice'));

            $child.on('ekyna.media-choice.selection', function() {
                $child.removeClass('ekyna-media-collection-add');
                that.updateCollection();
            });
        },
        updateCollection: function() {
            var that = this,
                $medias = that.$elem.find('.ekyna-media-collection-media').not('.ekyna-media-collection-add'),
                max = $medias.size() - 1;

            $medias.each(function(i) {
                var $media = $(this);
                $media.find('input[data-role="position"]').val(i);
                if (i == 0) {
                    $media.find('[data-role="move-left"]').addClass('disabled');
                } else {
                    $media.find('[data-role="move-left"]').removeClass('disabled');
                }
                if (i == max) {
                    $media.find('[data-role="move-right"]').addClass('disabled');
                } else {
                    $media.find('[data-role="move-right"]').removeClass('disabled');
                }
            });

            if (that.$elem.find('.ekyna-media-collection-add').size() == 0
                && (that.config.limit == 0 || max <= that.config.limit)) {
                that.addMedia();
            }
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
