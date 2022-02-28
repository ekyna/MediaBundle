define("ekyna-media/form/choice",["jquery","routing","ekyna-modal","ekyna-media/browser","ekyna-media/templates"],function(o,d,m,l,r){"use strict";function t(e){this.$elem=o(e),this.defaults={types:[],controls:[]},this.config=o.extend({},this.defaults,this.$elem.data("config"))}return t.prototype={constructor:t,init:function(){var t=this;this.$elem.on("click",'.media-thumb [data-role="select"]',function(e){t.selectMedia(o(e.target).parents(".media-thumb").eq(0).data("media").folder_id)}),this.$elem.on("click",'.media-thumb [data-role="remove"]',function(){t.removeMedia()})},selectMedia:function(e){var t,i=this,n=new m,a=(o(n).on("ekyna.modal.content",function(e){if("html"!==e.contentType)throw"Unexpected modal content type.";t=new l(e.content),o(t).bind("ekyna.media-browser.selection",function(e){var t;e.hasOwnProperty("media")&&((t=o(r["@EkynaMedia/Js/thumb.html.twig"].render({media:e.media,controls:i.config.controls,selector:!1}))).data("media",e.media),i.$elem.find(".media-thumb").replaceWith(t),i.$elem.find("input").val(e.media.id)),n.getDialog().close()}),o(t).bind("ekyna.media-browser.media_delete",function(e){e.hasOwnProperty("media")&&e.media.id===i.$elem.find("input").val()&&i.removeMedia()})}),o(n).on("ekyna.modal.load_fail",function(){alert("Failed to load media browser.")}),o(n).on("ekyna.modal.shown",function(){t&&t.init({folderId:e})}),o(n).on("ekyna.modal.hide",function(){t=t&&null}),{mode:"single_selection"});0<i.config.types.length&&(a.types=this.config.types),n.load({url:d.generate("admin_ekyna_media_browser_modal",a)})},removeMedia:function(){var e=o(this.$elem.data("empty-thumb"));this.$elem.find(".media-thumb").replaceWith(e),this.$elem.find("input").val("")}},{init:function(e){e.each(function(){new t(o(this)).init()})}}});