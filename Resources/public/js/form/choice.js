define("ekyna-media/form/choice",["jquery","routing","ekyna-modal","ekyna-media/browser","ekyna-media/templates"],function(a,b,c,d,e){"use strict";var f=function(b){this.$elem=a(b),this.defaults={types:[],controls:[]},this.config=a.extend({},this.defaults,this.$elem.data("config"))};return f.prototype={constructor:f,init:function(){var b=this;this.$elem.on("click",'.media-thumb [data-role="select"]',function(c){b.selectMedia(a(c.target).parents(".media-thumb").eq(0).data("media").folder_id)}),this.$elem.on("click",'.media-thumb [data-role="remove"]',function(){b.removeMedia()})},selectMedia:function(f){var g,h=this,i=new c;a(i).on("ekyna.modal.content",function(b){if("html"!=b.contentType)throw"Unexpected modal content type.";g=new d(b.content),a(g).bind("ekyna.media-browser.selection",function(b){if(b.hasOwnProperty("media")){var c=a(e["@EkynaMedia/Js/thumb.html.twig"].render({media:b.media,controls:h.config.controls,selector:!1}));c.data("media",b.media),h.$elem.find(".media-thumb").replaceWith(c),h.$elem.find("input").val(b.media.id)}i.getDialog().close()}),a(g).bind("ekyna.media-browser.media_delete",function(a){a.hasOwnProperty("media")&&a.media.id==h.$elem.find("input").val()&&h.removeMedia()})}),a(i).on("ekyna.modal.load_fail",function(){alert("Failed to load media browser.")}),a(i).on("ekyna.modal.shown",function(){g&&g.init({folderId:f})}),a(i).on("ekyna.modal.hide",function(){g&&(g=null)});var j={mode:"single_selection"};h.config.types.length>0&&(j.types=this.config.types),i.load({url:b.generate("ekyna_media_browser_admin_modal",j)})},removeMedia:function(){var b=a(this.$elem.data("empty-thumb"));this.$elem.find(".media-thumb").replaceWith(b),this.$elem.find("input").val("")}},{init:function(b){b.each(function(){new f(a(this)).init()})}}});