define(
    'ekyna-media/thumb',
    ['require', 'jquery', 'routing', 'ekyna-modal', 'ekyna-media/player', 'fancybox'],
    function (require, $, Router, Modal, Player) {

    var initialized = false, modal;

    function show($media) {
        var data = $media.data('media');

        if (!data.hasOwnProperty('type')) {
            console.error('Type data is not set.');
            return;
        }

        if (data.type === 'file') {
            this.downloadMedia($media);
            return;
        }

        if (!data.hasOwnProperty('player')) {
            console.error('Type data is not set.');
            return;
        }

        var params = {
            src: data.player
            // maxWidth    : 1200,
            // //maxHeight   : 600,
            // fitToView   : false,
            // width       : '90%',
            // height      : '90%',
            // autoSize    : false,
            // closeClick  : false,
            // openEffect  : 'none',
            // closeEffect : 'none',
            // padding     : 0
        };
        if (data.type === 'image') {
            params.type = 'image';
        } else {
            params.type = 'ajax';

            params.beforeShow = function () {
                Player.init($('.fancybox-stage'));
            };
            params.beforeClose = function () {
                Player.destroy($('.fancybox-stage'));
            };
        }

        $.fancybox.open(params);
    }

    function download($media) {
        var data = $media.data('media');

        if (!data.hasOwnProperty('path')) {
            console.error('Path data is not set.');
            return;
        }

        window.open(Router.generate('ekyna_media_download', {'key': $media.data('media').path}), '_blank');
    }

    function browse($media) {
        var data = $media.data('media');

        if (!data.hasOwnProperty('folderId')) {
            console.error('Folder id data is not set.');
            return;
        }

        var browser;

        if (modal) {
            modal.hide();
        } else {
            modal = new Modal();
        }

        require(['ekyna-media/browser'], function(Browser) {
            $(modal).on('ekyna.modal.content', function (e) {
                if (e.contentType === 'html') {
                    browser = new Browser(e.content);
                } else {
                    throw "Unexpected modal content type.";
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
                modal = null;
            });

            modal.load({
                url: Router.generate('admin_ekyna_media_browser_modal', {
                    folderId: data.folderId
                })
            });
        });
    }

    return {
        init: function () {
            if (initialized) {
                return;
            }

            initialized = true;

            $(document)
                .on('click', '.media-thumb [data-role="show"]', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    show($(e.currentTarget).parents('.media-thumb'));

                    return false;
                })
                .on('click', '.media-thumb [data-role="download"]', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    download($(e.currentTarget).parents('.media-thumb'));

                    return false;
                })
                .on('click', '.media-thumb [data-role="browse"]', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    browse($(e.currentTarget).parents('.media-thumb'));

                    return false;
                });
        }
    };
});
