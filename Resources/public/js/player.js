define(["require","jquery"],function(a,b){"use strict";function c(c){0!==c.length&&a(["swfobject"],function(){var a=setInterval(function(){"undefined"!=typeof window.swfobject&&(clearInterval(a),swfobject.switchOffAutoHideShow(),c.each(function(){var a=b(this).attr("id");a&&swfobject.registerObject(a,"9.0.0",document.documentElement.getAttribute("data-asset-base-url")+"/bundles/ekynamedia/lib/swfobject/expressInstall.swf")}))},50)})}return{init:function(a){a=a||b("body"),c(a.find("object.swf-object"))},destroy:function(a){a=a||b("body")}}});