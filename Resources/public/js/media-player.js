define('ekyna-media-player', ['require', 'jquery'], function(require, $) {
    "use strict";

    var MediaPlayer = function() {};

    MediaPlayer.prototype = {
        constructor: MediaPlayer,
        init: function () {
            var that = this;

            var $videos = $('.video-js');
            if (0 < $videos.length) {
                $videos.each(function() {
                    that.initVideo($(this));
                });
            }

            var $swfObjects = $('.swf-object');
            if (0 < $swfObjects.length) {
                $swfObjects.each(function() {
                    that.initFlash($(this));
                });
            }
        },
        initVideo: function($element) {
            var that = this;
            if (typeof videojs == 'undefined') {
                $('<link>')
                    .attr('media', 'all')
                    .attr('rel', 'stylesheet')
                    .attr('href', '/bundles/ekynamedia/lib/videojs/video-js.min.css')
                    .appendTo($('head'))
                ;
                require(['videojs'], function () {
                    videojs.options.flash.swf = "/bundles/ekynamedia/lib/videojs/video-js.swf";
                    videojs($element.attr('id'));
                });
                return;
            }
            videojs($element.attr('id'));
        },
        initFlash: function($element) {
            var that = this;
            if (typeof swfobject == 'undefined') {
                require(['swfobject'], function() {
                    swfobject.switchOffAutoHideShow();
                    swfobject.registerObject($element.attr('id'), "9.0.0", "/bundles/ekynamedia/lib/swfobject/expressInstall.swf");
                });
                return;
            }
            swfobject.registerObject($element.attr('id'), "9.0.0", "/bundles/ekynamedia/lib/swfobject/expressInstall.swf");
        }
        // TODO fancybox (gallery)
    };

    return new MediaPlayer();
});
