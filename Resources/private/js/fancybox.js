define(['fancybox'], function() {
    const ID = 'media-fancybox-stylesheet';

    if (document.getElementById(ID)) {
        return;
    }

    var stylesheet = document.createElement('link');
    stylesheet.id = ID;
    stylesheet.href = document.documentElement.getAttribute('data-asset-base-url') + '/bundles/ekynamedia/lib/fancybox/jquery.fancybox.css';
    stylesheet.media = 'screen';
    stylesheet.rel = 'stylesheet';
    stylesheet.type = 'text/css';
    document.head.appendChild(stylesheet);
});
