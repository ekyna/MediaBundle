define(['jquery', 'fancybox'], function($) {
    $('<link>')
        .attr('media', 'all')
        .attr('rel', 'stylesheet')
        .attr('href', '/bundles/ekynamedia/lib/fancybox/jquery.fancybox.css')
        .appendTo($('head'));
});
