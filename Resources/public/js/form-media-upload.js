define('ekyna-form/media-upload', ['jquery', 'jquery/fileupload', 'jquery/qtip'], function ($) {
    "use strict";

    var MediaUploadWidget = function ($elem) {
        this.$elem = $($elem);
        this.defaults = {};
        this.config = $.extend({}, this.defaults, this.$elem.data('config'));
    };

    MediaUploadWidget.prototype = {
        constructor: MediaUploadWidget,
        init: function () {
            var that = this;

            var $input       = that.$elem.find('.ekyna-media-upload-input').eq(0);
            var $collection  = that.$elem.find('.ekyna-collection').eq(0);
            var $addButton   = $collection.find('[data-collection-role="add"]').eq(0).hide();

            var $form = $input.closest('form');
            var $submitButton = $form.find('[type=submit]');
            if ($submitButton.length == 0) {
                $submitButton = $form.closest('.modal-content').find('button#submit'); // For modals
            }

            that.$elem.find('.file-input-button').on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $input.trigger('click');
            });

            /* UPLOAD */
            $input
                .fileupload({
                    dropZone: that.$elem.find('.ekyna-media-upload-drop-zone').eq(0)
                })
                .bind('fileuploadadd', function (e, data) {
                    $.each(data.files, function (index, file) {
                        $collection.one('ekyna-collection-field-added', function(e) {
                            var $li = e.target;
                            $li.data(data);
                            $li.find('input:text').eq(0).val(file.name);
                            data.context = $li;
                        });
                        $addButton.trigger('click');
                    });
                })
                .bind('fileuploadsubmit', function (e, data) { //e, data
                    var count = $form.data('uploadCount') || 0;
                    count++;
                    $submitButton.prop('disabled', true);
                    $form.data('uploadCount', count);
                })
                .bind('fileuploadalways', function () { // e, data
                    var count = $form.data('uploadCount') || 0;
                    count--;
                    $form.data('uploadCount', count);
                    if (0 >= count) {
                        $submitButton.prop('disabled', false);
                    }
                })
                .bind('fileuploaddone', function (e, data) {
                    var result = JSON.parse(data.result);
                    if (data.context) {
                        if (result.hasOwnProperty('upload_key')) {
                            data.context
                                .find('input[type=hidden]')
                                .val(result['upload_key']);
                            data.context
                                .find('.progress-bar')
                                .addClass('progress-bar-success');
                        } else {
                            data.context.addClass('has-error')
                                .find('input').prop('disabled', true);
                            data.context
                                .find('.progress-bar')
                                .addClass('progress-bar-danger');
                        }
                    }
                })
                .bind('fileuploadprogress', function (e, data) {
                    if (data.context && data._progress) {
                        var progress = parseInt(data._progress.loaded / data._progress.total * 100, 10);
                        data.context
                            .find('.progress-bar')
                            .css({width: progress + '%'})
                            .attr('aria-valuenow', progress);
                    }
                })
            ;

            $collection.on('ekyna-collection-field-removed', function(e) {
                if (e.target.data.abort) {
                    e.target.data.abort();
                }
            });

            /* Prevent form submission */
            $form.bind('submit', function(e) {
                var count = $form.data('uploadCount') || 0;
                if (0 < count) {
                    $submitButton.qtip({
                        content: 'Veuillez patienter pendant le téléchargement de vos fichiers&hellip;',
                        style: { classes: 'qtip-bootstrap' },
                        hide: { fixed: true, delay: 300 },
                        position: {
                            my: 'bottom center',
                            at: 'top center',
                            target: 'mouse',
                            adjust: {
                                mouse: false,
                                scroll: false
                            }
                        }
                    });
                    e.preventDefault();
                    return false;
                }
            });
        }
    };

    return {
        init: function ($element) {
            $element.each(function () {
                new MediaUploadWidget($(this)).init();
            });
        }
    };
});
