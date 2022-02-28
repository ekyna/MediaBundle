define("ekyna-media/form/upload",["jquery","jquery/fileupload","jquery/qtip"],function(r){"use strict";function t(e){this.$elem=r(e),this.defaults={},this.config=r.extend({},this.defaults,this.$elem.data("config"))}return t.prototype={constructor:t,init:function(){var e=this,t=e.$elem.find(".ekyna-media-upload-input").eq(0),o=e.$elem.find(".ekyna-collection").eq(0),a=o.find('[data-collection-role="add"]').eq(0).hide(),i=t.closest("form"),d=i.find("[type=submit]");0==d.length&&(d=i.closest(".modal-content").find("button#submit")),e.$elem.find(".file-input-button").on("click",function(e){e.preventDefault(),e.stopPropagation(),t.trigger("click")}),t.fileupload({dropZone:e.$elem.find(".ekyna-media-upload-drop-zone").eq(0)}).bind("fileuploadadd",function(e,n){r.each(n.files,function(e,t){o.one("ekyna-collection-field-added",function(e){e=e.target;e.data(n),e.find("input:text").eq(0).val(t.name),n.context=e}),a.trigger("click")})}).bind("fileuploadsubmit",function(e,t){var n=i.data("uploadCount")||0;n++,d.prop("disabled",!0),i.data("uploadCount",n)}).bind("fileuploadalways",function(){var e=i.data("uploadCount")||0;i.data("uploadCount",--e),e<=0&&d.prop("disabled",!1)}).bind("fileuploaddone",function(e,t){var n=JSON.parse(t.result);t.context&&(n.hasOwnProperty("upload_key")?(t.context.find("input[type=hidden]").val(n.upload_key),t.context.find(".progress-bar").addClass("progress-bar-success")):(t.context.addClass("has-error").find("input").prop("disabled",!0),t.context.find(".progress-bar").addClass("progress-bar-danger")))}).bind("fileuploadprogress",function(e,t){var n;t.context&&t._progress&&(n=parseInt(t._progress.loaded/t._progress.total*100,10),t.context.find(".progress-bar").css({width:n+"%"}).attr("aria-valuenow",n))}),o.on("ekyna-collection-field-removed",function(e){e.target.data.abort&&e.target.data.abort()}),i.bind("submit",function(e){if(0<(i.data("uploadCount")||0))return d.qtip({content:"Veuillez patienter pendant le téléchargement de vos fichiers&hellip;",style:{classes:"qtip-bootstrap"},hide:{fixed:!0,delay:300},position:{my:"bottom center",at:"top center",target:"mouse",adjust:{mouse:!1,scroll:!1}}}),e.preventDefault(),!1})}},{init:function(e){e.each(function(){new t(r(this)).init()})}}});