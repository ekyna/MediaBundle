!function(e){"function"==typeof define&&define.amd?define(["jquery","jquery-ui/ui/widgets/draggable","jquery-ui/ui/widgets/droppable","./jquery.fancytree"],e):"object"==typeof module&&module.exports?(require("./jquery.fancytree"),module.exports=e(require("jquery"))):e(jQuery)}(function(f){"use strict";var t=!1,g="fancytree-drop-accept",u="fancytree-drop-after",c="fancytree-drop-before",v="fancytree-drop-reject";function h(e){return 0===e?"":0<e?"+"+e:""+e}function r(e){var r=e.options.dnd||null,n=e.options.glyph||null;r&&(t||(f.ui.plugin.add("draggable","connectToFancytree",{start:function(e,r){var t=f(this).data("ui-draggable")||f(this).data("draggable"),a=r.helper.data("ftSourceNode")||null;if(a)return t.offset.click.top=-2,t.offset.click.left=16,a.tree.ext.dnd._onDragEvent("start",a,null,e,r,t)},drag:function(e,r){var t,a=f(this).data("ui-draggable")||f(this).data("draggable"),n=r.helper.data("ftSourceNode")||null,o=r.helper.data("ftTargetNode")||null,d=f.ui.fancytree.getNode(e.target),l=n&&n.tree.options.dnd;e.target&&!d&&0<f(e.target).closest("div.fancytree-drag-helper,#fancytree-drop-marker").length?(n||o||f.ui.fancytree).debug("Drag event over helper: ignored."):(r.helper.data("ftTargetNode",d),l&&l.updateHelper&&(t=n.tree._makeHookContext(n,e,{otherNode:d,ui:r,draggable:a,dropMarker:f("#fancytree-drop-marker")}),l.updateHelper.call(n.tree,n,t)),o&&o!==d&&o.tree.ext.dnd._onDragEvent("leave",o,n,e,r,a),d&&d.tree.options.dnd.dragDrop&&(d===o||d.tree.ext.dnd._onDragEvent("enter",d,n,e,r,a),d.tree.ext.dnd._onDragEvent("over",d,n,e,r,a)))},stop:function(e,r){var t=f(this).data("ui-draggable")||f(this).data("draggable"),a=r.helper.data("ftSourceNode")||null,n=r.helper.data("ftTargetNode")||null,o="mouseup"===e.type&&1===e.which;o||(a||n||f.ui.fancytree).debug("Drag was cancelled"),n&&(o&&n.tree.ext.dnd._onDragEvent("drop",n,a,e,r,t),n.tree.ext.dnd._onDragEvent("leave",n,a,e,r,t)),a&&a.tree.ext.dnd._onDragEvent("stop",a,null,e,r,t)}}),t=!0)),r&&r.dragStart&&e.widget.element.draggable(f.extend({addClasses:!1,appendTo:e.$container,containment:!1,delay:0,distance:4,revert:!1,scroll:!0,scrollSpeed:7,scrollSensitivity:10,connectToFancytree:!0,helper:function(e){var r,t,a=f.ui.fancytree.getNode(e.target);return a?(t=a.tree.options.dnd,r=f(a.span),(r=f("<div class='fancytree-drag-helper'><span class='fancytree-drag-helper-img' /></div>").css({zIndex:3,position:"relative"}).append(r.find("span.fancytree-title").clone())).data("ftSourceNode",a),n&&r.find(".fancytree-drag-helper-img").addClass(n.map._addClass+" "+n.map.dragHelper),t.initHelper&&t.initHelper.call(a.tree,a,{node:a,tree:a.tree,originalEvent:e,ui:{helper:r}}),r):"<div>ERROR?: helper requested but sourceNode not found</div>"},start:function(e,r){return!!r.helper.data("ftSourceNode")}},e.options.dnd.draggable)),r&&r.dragDrop&&e.widget.element.droppable(f.extend({addClasses:!1,tolerance:"intersect",greedy:!1},e.options.dnd.droppable))}return f.ui.fancytree.registerExtension({name:"dnd",version:"2.38.1",options:{autoExpandMS:1e3,draggable:null,droppable:null,focusOnClick:!1,preventVoidMoves:!0,preventRecursiveMoves:!0,smartRevert:!0,dropMarkerOffsetX:-24,dropMarkerInsertOffsetX:-16,dragStart:null,dragStop:null,initHelper:null,updateHelper:null,dragEnter:null,dragOver:null,dragExpand:null,dragDrop:null,dragLeave:null},treeInit:function(t){var e=t.tree;this._superApply(arguments),e.options.dnd.dragStart&&e.$container.on("mousedown",function(e){var r;t.options.dnd.focusOnClick&&((r=f.ui.fancytree.getNode(e))&&r.debug("Re-enable focus that was prevented by jQuery UI draggable."),setTimeout(function(){f(e.target).closest(":tabbable").focus()},10))}),r(e)},_setDndStatus:function(e,r,t,a,n){var o,d="center",l=this._local,s=this.options.dnd,i=this.options.glyph,e=e?f(e.span):null,r=f(r.span),p=r.find("span.fancytree-title");if(l.$dropMarker||(l.$dropMarker=f("<div id='fancytree-drop-marker'></div>").hide().css({"z-index":1e3}).prependTo(f(this.$div).parent()),i&&l.$dropMarker.addClass(i.map._addClass+" "+i.map.dropMarker)),"after"===a||"before"===a||"over"===a){switch(o=s.dropMarkerOffsetX||0,a){case"before":d="top",o+=s.dropMarkerInsertOffsetX||0;break;case"after":d="bottom",o+=s.dropMarkerInsertOffsetX||0}i={my:"left"+h(o)+" center",at:"left "+d,of:p},this.options.rtl&&(i.my="right"+h(-o)+" center",i.at="right "+d),l.$dropMarker.toggleClass(u,"after"===a).toggleClass("fancytree-drop-over","over"===a).toggleClass(c,"before"===a).toggleClass("fancytree-rtl",!!this.options.rtl).show().position(f.ui.fancytree.fixPositionOptions(i))}else l.$dropMarker.hide();e&&e.toggleClass(g,!0===n).toggleClass(v,!1===n),r.toggleClass("fancytree-drop-target","after"===a||"before"===a||"over"===a).toggleClass(u,"after"===a).toggleClass(c,"before"===a).toggleClass(g,!0===n).toggleClass(v,!1===n),t.toggleClass(g,!0===n).toggleClass(v,!1===n)},_onDragEvent:function(p,e,r,g,t,a){var n,o,d,l=this.options.dnd,s=this._makeHookContext(e,g,{otherNode:r,ui:t,draggable:a}),i=null,u=this,c=f(e.span);switch(l.smartRevert&&(a.options.revert="invalid"),p){case"start":e.isStatusNode()?i=!1:l.dragStart&&(i=l.dragStart(e,s)),!1===i?(this.debug("tree.dragStart() cancelled"),t.helper.trigger("mouseup").hide()):(l.smartRevert&&(d=e[s.tree.nodeContainerAttrName].getBoundingClientRect(),n=f(a.options.appendTo)[0].getBoundingClientRect(),a.originalPosition.left=Math.max(0,d.left-n.left),a.originalPosition.top=Math.max(0,d.top-n.top)),c.addClass("fancytree-drag-source"),f(document).on("keydown.fancytree-dnd,mousedown.fancytree-dnd",function(e){("keydown"===e.type&&e.which===f.ui.keyCode.ESCAPE||"mousedown"===e.type)&&u.ext.dnd._cancelDrag()}));break;case"enter":i=!!(d=(!l.preventRecursiveMoves||!e.isDescendantOf(r))&&(l.dragEnter?l.dragEnter(e,s):null))&&(Array.isArray(d)?{over:0<=f.inArray("over",d),before:0<=f.inArray("before",d),after:0<=f.inArray("after",d)}:{over:!0===d||"over"===d,before:!0===d||"before"===d,after:!0===d||"after"===d}),t.helper.data("enterResponse",i);break;case"over":o=null,!1===(n=t.helper.data("enterResponse"))||("string"==typeof n?o=n:(d=c.offset(),d={x:(d={x:g.pageX-d.left,y:g.pageY-d.top}).x/c.width(),y:d.y/c.height()},n.after&&.75<d.y||!n.over&&n.after&&.5<d.y?o="after":n.before&&d.y<=.25||!n.over&&n.before&&d.y<=.5?o="before":n.over&&(o="over"),l.preventVoidMoves&&(e===r?(this.debug("    drop over source node prevented"),o=null):"before"===o&&r&&e===r.getNextSibling()?(this.debug("    drop after source node prevented"),o=null):"after"===o&&r&&e===r.getPrevSibling()?(this.debug("    drop before source node prevented"),o=null):"over"===o&&r&&r.parent===e&&r.isLastSibling()&&(this.debug("    drop last child over own parent prevented"),o=null)),t.helper.data("hitMode",o))),"before"===o||"after"===o||!l.autoExpandMS||!1===e.hasChildren()||e.expanded||l.dragExpand&&!1===l.dragExpand(e,s)||e.scheduleAction("expand",l.autoExpandMS),o&&l.dragOver&&(s.hitMode=o,i=l.dragOver(e,s)),d=!1!==i&&null!==o,l.smartRevert&&(a.options.revert=!d),this._local._setDndStatus(r,e,t.helper,o,d);break;case"drop":(o=t.helper.data("hitMode"))&&l.dragDrop&&(s.hitMode=o,l.dragDrop(e,s));break;case"leave":e.scheduleAction("cancel"),t.helper.data("enterResponse",null),t.helper.data("hitMode",null),this._local._setDndStatus(r,e,t.helper,"out",void 0),l.dragLeave&&l.dragLeave(e,s);break;case"stop":c.removeClass("fancytree-drag-source"),f(document).off(".fancytree-dnd"),l.dragStop&&l.dragStop(e,s);break;default:f.error("Unsupported drag event: "+p)}return i},_cancelDrag:function(){var e=f.ui.ddmanager.current;e&&e.cancel()}}),f.ui.fancytree});