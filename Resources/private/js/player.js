define(['require', 'jquery'], function(require, $) {
    "use strict";

    function initVideos($videos) {
        if (0 === $videos.length) {
            return
        }

        if (0 === $('link#videojs-css').length) {
            $('<link>')
                .attr('id', 'videojs-css')
                .attr('media', 'all')
                .attr('rel', 'stylesheet')
                .attr('href', '/bundles/ekynamedia/lib/videojs/video-js.css')
                .appendTo($('head'));
        }

        require(['videojs'], function() {
            var vJsI = setInterval(function() {
                if (typeof window['videojs'] === 'undefined') {
                    return;
                }

                clearInterval(vJsI);

                $videos.each(function() {
                    videojs($(this).attr('id'))
                });
            }, 50);
        });
    }

    function destroyVideos($videos) {
        if (0 === $videos.length) {
            return
        }

        if (typeof window['videojs'] === 'undefined') {
            return;
        }

        $videos.each(function() {
            videojs($(this).attr('id')).dispose();
        });
    }

    function initFlashes($flashes) {
        if (0 === $flashes.length) {
            return
        }

        require(['swfobject'], function() {
            var swoI = setInterval(function() {
                if (typeof window['swfobject'] === 'undefined') {
                    return;
                }

                clearInterval(swoI);

                swfobject.switchOffAutoHideShow();

                $flashes.each(function () {
                    var id = $(this).attr('id');
                    if (id) {
                        swfobject.registerObject(id, "9.0.0", "/bundles/ekynamedia/lib/swfobject/expressInstall.swf");
                    }
                });
            }, 50);
        });
    }

    return {
        init: function ($container) {
            $container = $container || $('body');

            //initVideos($container.find('video.video-js'));

            initFlashes($container.find('object.swf-object'));
        },
        destroy: function($container) {
            $container = $container || $('body');

            //destroyVideos($container.find('video.video-js'));
        }
    };
});
