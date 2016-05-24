(function(root, factory) {
    "use strict";

    if (typeof module !== 'undefined' && module.exports) {
        module.exports = factory(require('jquery'), require('videojs'), require('swfobject'));
    } else if (typeof define === 'function' && define.amd) {
        define('ekyna-media-player', ['jquery', 'videojs', 'swfobject'], function(jQuery, videojs) {
            return factory(jQuery, videojs);
        });
    } else {
        root.EkynaMediaPlayer = factory(root.jQuery, root.videojs);
    }

}(this, function($, videojs) {
    "use strict";

    $('<link>')
        .attr('media', 'all')
        .attr('rel', 'stylesheet')
        .attr('href', '/bundles/ekynamedia/lib/videojs/video-js.css')
        .appendTo($('head'))
    ;
    //videojs.options.flash.swf = "/bundles/ekynamedia/lib/videojs/video-js.swf";

    window.VIDEOJS_NO_DYNAMIC_STYLE = true;

    swfobject.switchOffAutoHideShow();

    var MediaPlayer = function() {};

    MediaPlayer.prototype = {
        constructor: MediaPlayer,
        init: function ($container) {
            var that = this;

            $container = $container || $('body');

            var $videos = $container.find('.video-js');
            if (0 < $videos.length) {
                $videos.each(function() {
                    that.initVideo($(this));
                });
            }

            var $swfObjects = $container.find('.swf-object');
            if (0 < $swfObjects.length) {
                $swfObjects.each(function() {
                    that.initFlash($(this));
                });
            }
        },
        destroy: function($container) {
            var that = this;

            $container = $container || $('body');

            var $videos = $container.find('.video-js');
            if (0 < $videos.length) {
                $videos.each(function() {
                    that.destroyVideo($(this));
                });
            }
        },
        initVideo: function($element) {
            return videojs($element.attr('id'));
        },
        destroyVideo: function($element) {
            var video = videojs($element.attr('id'));
            video.dispose();
            return video;
        },
        initFlash: function($element) {
            swfobject.registerObject($element.attr('id'), "9.0.0", "/bundles/ekynamedia/lib/swfobject/expressInstall.swf");
        }
    };

    return new MediaPlayer();
}));
