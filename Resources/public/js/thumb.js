define("ekyna-media/thumb",["require","jquery","routing","ekyna-modal","ekyna-media/player","fancybox"],function(a,b,c,d,e){function f(a){var c=a.data("media");if(!c.hasOwnProperty("type"))return void console.error("Type data is not set.");if("file"===c.type)return void this.downloadMedia(a);if(!c.hasOwnProperty("player"))return void console.error("Type data is not set.");var d={src:c.player};"image"===c.type?d.type="image":(d.type="ajax",d.beforeShow=function(){e.init(b(".fancybox-stage"))},d.beforeClose=function(){e.destroy(b(".fancybox-stage"))}),b.fancybox.open(d)}function g(a){var b=a.data("media");return b.hasOwnProperty("path")?void window.open(c.generate("ekyna_media_download",{key:a.data("media").path}),"_blank"):void console.error("Path data is not set.")}function h(e){var f=e.data("media");if(!f.hasOwnProperty("folderId"))return void console.error("Folder id data is not set.");var g;i?i.hide():i=new d,a(["ekyna-media/browser"],function(a){b(i).on("ekyna.modal.content",function(b){if("html"!==b.contentType)throw"Unexpected modal content type.";g=new a(b.content)}),b(i).on("ekyna.modal.shown",function(){g&&g.init()}),b(i).on("ekyna.modal.hide",function(){g&&(g=null),i=null}),i.load({url:c.generate("ekyna_media_browser_admin_modal",{folderId:f.folderId})})})}var i,j=!1;return{init:function(){j||(j=!0,b(document).on("click",'.media-thumb [data-role="show"]',function(a){return a.preventDefault(),a.stopPropagation(),f(b(a.currentTarget).parents(".media-thumb")),!1}).on("click",'.media-thumb [data-role="download"]',function(a){return a.preventDefault(),a.stopPropagation(),g(b(a.currentTarget).parents(".media-thumb")),!1}).on("click",'.media-thumb [data-role="browse"]',function(a){return a.preventDefault(),a.stopPropagation(),h(b(a.currentTarget).parents(".media-thumb")),!1}))}}});