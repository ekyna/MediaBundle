define("ekyna-media/thumb",["require","jquery","routing","ekyna-modal","ekyna-media/player","fancybox"],function(t,o,r,i,a){let e=!1,d;function l(e){const t=e.data("media");t.hasOwnProperty("path")?window.open(r.generate("ekyna_media_download",{key:e.data("media").path}),"_blank"):console.error("Path data is not set.")}return{init:function(){e||(e=!0,o(document).on("click",'.media-thumb [data-role="show"]',function(t){t.preventDefault(),t.stopPropagation();{let e=(t=o(t.currentTarget).parents(".media-thumb")).data("media");if(e.hasOwnProperty("type"))if("file"!==e.type)if(e.hasOwnProperty("player")){const n={src:e.player};"image"===e.type||"svg"===e.type?n.type="image":(n.type="ajax",n.beforeShow=function(){a.init(o(".fancybox-stage"))},n.beforeClose=function(){a.destroy(o(".fancybox-stage"))}),o.fancybox.open(n)}else console.error("Type data is not set.");else l(t);else console.error("Type data is not set.")}return!1}).on("click",'.media-thumb [data-role="download"]',function(e){return e.preventDefault(),e.stopPropagation(),l(o(e.currentTarget).parents(".media-thumb")),!1}).on("click",'.media-thumb [data-role="browse"]',function(e){e.preventDefault(),e.stopPropagation();{const a=(e=o(e.currentTarget).parents(".media-thumb")).data("media");if(a.hasOwnProperty("folderId")){let n;d?d.hide():d=new i,t(["ekyna-media/browser"],function(t){o(d).on("ekyna.modal.content",function(e){if("html"!==e.contentType)throw"Unexpected modal content type.";n=new t(e.content)}),o(d).on("ekyna.modal.shown",function(){n&&n.init()}),o(d).on("ekyna.modal.hide",function(){n=n&&null,d=null}),d.load({url:r.generate("admin_ekyna_media_browser_modal",{folderId:a.folderId})})})}else console.error("Folder id data is not set.")}return!1}))}}});