!function(t){"use strict";"function"==typeof define&&define.amd?define(["jquery"],t):t(jQuery)}(function(x){"use strict";x.ui=x.ui||{};x.ui.version="1.13.0";var h,o,W,C,n,s,l,r,a,i,c=0,f=Array.prototype.hasOwnProperty,u=Array.prototype.slice;x.cleanData=x.cleanData||(h=x.cleanData,function(t){for(var e,i,o=0;null!=(i=t[o]);o++)(e=x._data(i,"events"))&&e.remove&&x(i).triggerHandler("remove");h(t)}),x.widget=x.widget||function(t,i,e){var o,n,s,l={},r=t.split(".")[0],a=r+"-"+(t=t.split(".")[1]);return e||(e=i,i=x.Widget),Array.isArray(e)&&(e=x.extend.apply(null,[{}].concat(e))),x.expr.pseudos[a.toLowerCase()]=function(t){return!!x.data(t,a)},x[r]=x[r]||{},o=x[r][t],n=x[r][t]=function(t,e){if(!this._createWidget)return new n(t,e);arguments.length&&this._createWidget(t,e)},x.extend(n,o,{version:e.version,_proto:x.extend({},e),_childConstructors:[]}),(s=new i).options=x.widget.extend({},s.options),x.each(e,function(e,o){function n(){return i.prototype[e].apply(this,arguments)}function s(t){return i.prototype[e].apply(this,t)}l[e]="function"==typeof o?function(){var t,e=this._super,i=this._superApply;return this._super=n,this._superApply=s,t=o.apply(this,arguments),this._super=e,this._superApply=i,t}:o}),n.prototype=x.widget.extend(s,{widgetEventPrefix:o&&s.widgetEventPrefix||t},l,{constructor:n,namespace:r,widgetName:t,widgetFullName:a}),o?(x.each(o._childConstructors,function(t,e){var i=e.prototype;x.widget(i.namespace+"."+i.widgetName,n,e._proto)}),delete o._childConstructors):i._childConstructors.push(n),x.widget.bridge(t,n),n},x.widget.extend=function(t){for(var e,i,o=u.call(arguments,1),n=0,s=o.length;n<s;n++)for(e in o[n])i=o[n][e],f.call(o[n],e)&&void 0!==i&&(x.isPlainObject(i)?t[e]=x.isPlainObject(t[e])?x.widget.extend({},t[e],i):x.widget.extend({},i):t[e]=i);return t},x.widget.bridge=function(s,e){var l=e.prototype.widgetFullName||s;x.fn[s]=function(i){var t="string"==typeof i,o=u.call(arguments,1),n=this;return t?this.length||"instance"!==i?this.each(function(){var t,e=x.data(this,l);return"instance"===i?(n=e,!1):e?"function"!=typeof e[i]||"_"===i.charAt(0)?x.error("no such method '"+i+"' for "+s+" widget instance"):(t=e[i].apply(e,o))!==e&&void 0!==t?(n=t&&t.jquery?n.pushStack(t.get()):t,!1):void 0:x.error("cannot call methods on "+s+" prior to initialization; attempted to call method '"+i+"'")}):n=void 0:(o.length&&(i=x.widget.extend.apply(null,[i].concat(o))),this.each(function(){var t=x.data(this,l);t?(t.option(i||{}),t._init&&t._init()):x.data(this,l,new e(i,this))})),n}},x.Widget=x.Widget||function(){},x.Widget._childConstructors=[],x.Widget.prototype={widgetName:"widget",widgetEventPrefix:"",defaultElement:"<div>",options:{classes:{},disabled:!1,create:null},_createWidget:function(t,e){e=x(e||this.defaultElement||this)[0],this.element=x(e),this.uuid=c++,this.eventNamespace="."+this.widgetName+this.uuid,this.bindings=x(),this.hoverable=x(),this.focusable=x(),this.classesElementLookup={},e!==this&&(x.data(e,this.widgetFullName,this),this._on(!0,this.element,{remove:function(t){t.target===e&&this.destroy()}}),this.document=x(e.style?e.ownerDocument:e.document||e),this.window=x(this.document[0].defaultView||this.document[0].parentWindow)),this.options=x.widget.extend({},this.options,this._getCreateOptions(),t),this._create(),this.options.disabled&&this._setOptionDisabled(this.options.disabled),this._trigger("create",null,this._getCreateEventData()),this._init()},_getCreateOptions:function(){return{}},_getCreateEventData:x.noop,_create:x.noop,_init:x.noop,destroy:function(){var i=this;this._destroy(),x.each(this.classesElementLookup,function(t,e){i._removeClass(e,t)}),this.element.off(this.eventNamespace).removeData(this.widgetFullName),this.widget().off(this.eventNamespace).removeAttr("aria-disabled"),this.bindings.off(this.eventNamespace)},_destroy:x.noop,widget:function(){return this.element},option:function(t,e){var i,o,n,s=t;if(0===arguments.length)return x.widget.extend({},this.options);if("string"==typeof t)if(s={},t=(i=t.split(".")).shift(),i.length){for(o=s[t]=x.widget.extend({},this.options[t]),n=0;n<i.length-1;n++)o[i[n]]=o[i[n]]||{},o=o[i[n]];if(t=i.pop(),1===arguments.length)return void 0===o[t]?null:o[t];o[t]=e}else{if(1===arguments.length)return void 0===this.options[t]?null:this.options[t];s[t]=e}return this._setOptions(s),this},_setOptions:function(t){for(var e in t)this._setOption(e,t[e]);return this},_setOption:function(t,e){return"classes"===t&&this._setOptionClasses(e),this.options[t]=e,"disabled"===t&&this._setOptionDisabled(e),this},_setOptionClasses:function(t){var e,i,o;for(e in t)o=this.classesElementLookup[e],t[e]!==this.options.classes[e]&&o&&o.length&&(i=x(o.get()),this._removeClass(o,e),i.addClass(this._classes({element:i,keys:e,classes:t,add:!0})))},_setOptionDisabled:function(t){this._toggleClass(this.widget(),this.widgetFullName+"-disabled",null,!!t),t&&(this._removeClass(this.hoverable,null,"ui-state-hover"),this._removeClass(this.focusable,null,"ui-state-focus"))},enable:function(){return this._setOptions({disabled:!1})},disable:function(){return this._setOptions({disabled:!0})},_classes:function(n){var s=[],l=this;function t(t,e){for(var i,o=0;o<t.length;o++)i=l.classesElementLookup[t[o]]||x(),i=n.add?(n.element.each(function(t,e){x.map(l.classesElementLookup,function(t){return t}).some(function(t){return t.is(e)})||l._on(x(e),{remove:"_untrackClassesElement"})}),x(x.uniqueSort(i.get().concat(n.element.get())))):x(i.not(n.element).get()),l.classesElementLookup[t[o]]=i,s.push(t[o]),e&&n.classes[t[o]]&&s.push(n.classes[t[o]])}return(n=x.extend({element:this.element,classes:this.options.classes||{}},n)).keys&&t(n.keys.match(/\S+/g)||[],!0),n.extra&&t(n.extra.match(/\S+/g)||[]),s.join(" ")},_untrackClassesElement:function(i){var o=this;x.each(o.classesElementLookup,function(t,e){-1!==x.inArray(i.target,e)&&(o.classesElementLookup[t]=x(e.not(i.target).get()))}),this._off(x(i.target))},_removeClass:function(t,e,i){return this._toggleClass(t,e,i,!1)},_addClass:function(t,e,i){return this._toggleClass(t,e,i,!0)},_toggleClass:function(t,e,i,o){var n="string"==typeof t||null===t,e={extra:n?e:i,keys:n?t:e,element:n?this.element:t,add:o="boolean"==typeof o?o:i};return e.element.toggleClass(this._classes(e),o),this},_on:function(n,s,t){var l,r=this;"boolean"!=typeof n&&(t=s,s=n,n=!1),t?(s=l=x(s),this.bindings=this.bindings.add(s)):(t=s,s=this.element,l=this.widget()),x.each(t,function(t,e){function i(){if(n||!0!==r.options.disabled&&!x(this).hasClass("ui-state-disabled"))return("string"==typeof e?r[e]:e).apply(r,arguments)}"string"!=typeof e&&(i.guid=e.guid=e.guid||i.guid||x.guid++);var t=t.match(/^([\w:-]*)\s*(.*)$/),o=t[1]+r.eventNamespace,t=t[2];t?l.on(o,t,i):s.on(o,i)})},_off:function(t,e){e=(e||"").split(" ").join(this.eventNamespace+" ")+this.eventNamespace,t.off(e),this.bindings=x(this.bindings.not(t).get()),this.focusable=x(this.focusable.not(t).get()),this.hoverable=x(this.hoverable.not(t).get())},_delay:function(t,e){var i=this;return setTimeout(function(){return("string"==typeof t?i[t]:t).apply(i,arguments)},e||0)},_hoverable:function(t){this.hoverable=this.hoverable.add(t),this._on(t,{mouseenter:function(t){this._addClass(x(t.currentTarget),null,"ui-state-hover")},mouseleave:function(t){this._removeClass(x(t.currentTarget),null,"ui-state-hover")}})},_focusable:function(t){this.focusable=this.focusable.add(t),this._on(t,{focusin:function(t){this._addClass(x(t.currentTarget),null,"ui-state-focus")},focusout:function(t){this._removeClass(x(t.currentTarget),null,"ui-state-focus")}})},_trigger:function(t,e,i){var o,n,s=this.options[t];if(i=i||{},(e=x.Event(e)).type=(t===this.widgetEventPrefix?t:this.widgetEventPrefix+t).toLowerCase(),e.target=this.element[0],n=e.originalEvent)for(o in n)o in e||(e[o]=n[o]);return this.element.trigger(e,i),!("function"==typeof s&&!1===s.apply(this.element[0],[e].concat(i))||e.isDefaultPrevented())}},x.each({show:"fadeIn",hide:"fadeOut"},function(s,l){x.Widget.prototype["_"+s]=function(e,t,i){var o,n=(t="string"==typeof t?{effect:t}:t)?!0!==t&&"number"!=typeof t&&t.effect||l:s;"number"==typeof(t=t||{})?t={duration:t}:!0===t&&(t={}),o=!x.isEmptyObject(t),t.complete=i,t.delay&&e.delay(t.delay),o&&x.effects&&x.effects.effect[n]?e[s](t):n!==s&&e[n]?e[n](t.duration,t.easing,i):e.queue(function(t){x(this)[s](),i&&i.call(e[0]),t()})}}),x.widget;function P(t,e,i){return[parseFloat(t[0])*(a.test(t[0])?e/100:1),parseFloat(t[1])*(a.test(t[1])?i/100:1)]}function E(t,e){return parseInt(x.css(t,e),10)||0}function L(t){return null!=t&&t===t.window}W=Math.max,C=Math.abs,n=/left|center|right/,s=/top|center|bottom/,l=/[\+\-]\d+(\.[\d]+)?%?/,r=/^\w+/,a=/%$/,i=x.fn.position,x.position=x.position||{scrollbarWidth:function(){if(void 0!==o)return o;var t,e=x("<div style='display:block;position:absolute;width:200px;height:200px;overflow:hidden;'><div style='height:300px;width:auto;'></div></div>"),i=e.children()[0];return x("body").append(e),t=i.offsetWidth,e.css("overflow","scroll"),t===(i=i.offsetWidth)&&(i=e[0].clientWidth),e.remove(),o=t-i},getScrollInfo:function(t){var e=t.isWindow||t.isDocument?"":t.element.css("overflow-x"),i=t.isWindow||t.isDocument?"":t.element.css("overflow-y"),e="scroll"===e||"auto"===e&&t.width<t.element[0].scrollWidth;return{width:"scroll"===i||"auto"===i&&t.height<t.element[0].scrollHeight?x.position.scrollbarWidth():0,height:e?x.position.scrollbarWidth():0}},getWithinInfo:function(t){var e=x(t||window),i=L(e[0]),o=!!e[0]&&9===e[0].nodeType;return{element:e,isWindow:i,isDocument:o,offset:!i&&!o?x(t).offset():{left:0,top:0},scrollLeft:e.scrollLeft(),scrollTop:e.scrollTop(),width:e.outerWidth(),height:e.outerHeight()}}},x.fn.position=function(f){if(!f||!f.of)return i.apply(this,arguments);var u,d,p,g,m,t,v="string"==typeof(f=x.extend({},f)).of?x(document).find(f.of):x(f.of),y=x.position.getWithinInfo(f.within),_=x.position.getScrollInfo(y),w=(f.collision||"flip").split(" "),b={},e=9===(e=(t=v)[0]).nodeType?{width:t.width(),height:t.height(),offset:{top:0,left:0}}:L(e)?{width:t.width(),height:t.height(),offset:{top:t.scrollTop(),left:t.scrollLeft()}}:e.preventDefault?{width:0,height:0,offset:{top:e.pageY,left:e.pageX}}:{width:t.outerWidth(),height:t.outerHeight(),offset:t.offset()};return v[0].preventDefault&&(f.at="left top"),d=e.width,p=e.height,m=x.extend({},g=e.offset),x.each(["my","at"],function(){var t,e,i=(f[this]||"").split(" ");(i=1===i.length?n.test(i[0])?i.concat(["center"]):s.test(i[0])?["center"].concat(i):["center","center"]:i)[0]=n.test(i[0])?i[0]:"center",i[1]=s.test(i[1])?i[1]:"center",t=l.exec(i[0]),e=l.exec(i[1]),b[this]=[t?t[0]:0,e?e[0]:0],f[this]=[r.exec(i[0])[0],r.exec(i[1])[0]]}),1===w.length&&(w[1]=w[0]),"right"===f.at[0]?m.left+=d:"center"===f.at[0]&&(m.left+=d/2),"bottom"===f.at[1]?m.top+=p:"center"===f.at[1]&&(m.top+=p/2),u=P(b.at,d,p),m.left+=u[0],m.top+=u[1],this.each(function(){var i,t,l=x(this),r=l.outerWidth(),a=l.outerHeight(),e=E(this,"marginLeft"),o=E(this,"marginTop"),n=r+e+E(this,"marginRight")+_.width,c=a+o+E(this,"marginBottom")+_.height,h=x.extend({},m),s=P(b.my,l.outerWidth(),l.outerHeight());"right"===f.my[0]?h.left-=r:"center"===f.my[0]&&(h.left-=r/2),"bottom"===f.my[1]?h.top-=a:"center"===f.my[1]&&(h.top-=a/2),h.left+=s[0],h.top+=s[1],i={marginLeft:e,marginTop:o},x.each(["left","top"],function(t,e){x.ui.position[w[t]]&&x.ui.position[w[t]][e](h,{targetWidth:d,targetHeight:p,elemWidth:r,elemHeight:a,collisionPosition:i,collisionWidth:n,collisionHeight:c,offset:[u[0]+s[0],u[1]+s[1]],my:f.my,at:f.at,within:y,elem:l})}),f.using&&(t=function(t){var e=g.left-h.left,i=e+d-r,o=g.top-h.top,n=o+p-a,s={target:{element:v,left:g.left,top:g.top,width:d,height:p},element:{element:l,left:h.left,top:h.top,width:r,height:a},horizontal:i<0?"left":0<e?"right":"center",vertical:n<0?"top":0<o?"bottom":"middle"};d<r&&C(e+i)<d&&(s.horizontal="center"),p<a&&C(o+n)<p&&(s.vertical="middle"),W(C(e),C(i))>W(C(o),C(n))?s.important="horizontal":s.important="vertical",f.using.call(this,t,s)}),l.offset(x.extend(h,{using:t}))})},x.ui.position={fit:{left:function(t,e){var i,o=e.within,n=o.isWindow?o.scrollLeft:o.offset.left,o=o.width,s=t.left-e.collisionPosition.marginLeft,l=n-s,r=s+e.collisionWidth-o-n;e.collisionWidth>o?0<l&&r<=0?(i=t.left+l+e.collisionWidth-o-n,t.left+=l-i):t.left=!(0<r&&l<=0)&&r<l?n+o-e.collisionWidth:n:0<l?t.left+=l:0<r?t.left-=r:t.left=W(t.left-s,t.left)},top:function(t,e){var i,o=e.within,o=o.isWindow?o.scrollTop:o.offset.top,n=e.within.height,s=t.top-e.collisionPosition.marginTop,l=o-s,r=s+e.collisionHeight-n-o;e.collisionHeight>n?0<l&&r<=0?(i=t.top+l+e.collisionHeight-n-o,t.top+=l-i):t.top=!(0<r&&l<=0)&&r<l?o+n-e.collisionHeight:o:0<l?t.top+=l:0<r?t.top-=r:t.top=W(t.top-s,t.top)}},flip:{left:function(t,e){var i=e.within,o=i.offset.left+i.scrollLeft,n=i.width,i=i.isWindow?i.scrollLeft:i.offset.left,s=t.left-e.collisionPosition.marginLeft,l=s-i,s=s+e.collisionWidth-n-i,r="left"===e.my[0]?-e.elemWidth:"right"===e.my[0]?e.elemWidth:0,a="left"===e.at[0]?e.targetWidth:"right"===e.at[0]?-e.targetWidth:0,h=-2*e.offset[0];l<0?((n=t.left+r+a+h+e.collisionWidth-n-o)<0||n<C(l))&&(t.left+=r+a+h):0<s&&(0<(o=t.left-e.collisionPosition.marginLeft+r+a+h-i)||C(o)<s)&&(t.left+=r+a+h)},top:function(t,e){var i=e.within,o=i.offset.top+i.scrollTop,n=i.height,i=i.isWindow?i.scrollTop:i.offset.top,s=t.top-e.collisionPosition.marginTop,l=s-i,s=s+e.collisionHeight-n-i,r="top"===e.my[1]?-e.elemHeight:"bottom"===e.my[1]?e.elemHeight:0,a="top"===e.at[1]?e.targetHeight:"bottom"===e.at[1]?-e.targetHeight:0,h=-2*e.offset[1];l<0?((n=t.top+r+a+h+e.collisionHeight-n-o)<0||n<C(l))&&(t.top+=r+a+h):0<s&&(0<(o=t.top-e.collisionPosition.marginTop+r+a+h-i)||C(o)<s)&&(t.top+=r+a+h)}},flipfit:{left:function(){x.ui.position.flip.left.apply(this,arguments),x.ui.position.fit.left.apply(this,arguments)},top:function(){x.ui.position.flip.top.apply(this,arguments),x.ui.position.fit.top.apply(this,arguments)}}};var e,d,t;x.ui.position,x.expr.pseudos||(x.expr.pseudos=x.expr[":"]),x.uniqueSort||(x.uniqueSort=x.unique),x.escapeSelector||(e=/([\0-\x1f\x7f]|^-?\d)|^-$|[^\x80-\uFFFF\w-]/g,d=function(t,e){return e?"\0"===t?"�":t.slice(0,-1)+"\\"+t.charCodeAt(t.length-1).toString(16)+" ":"\\"+t},x.escapeSelector=function(t){return(t+"").replace(e,d)}),x.fn.even&&x.fn.odd||x.fn.extend({even:function(){return this.filter(function(t){return t%2==0})},odd:function(){return this.filter(function(t){return t%2==1})}}),x.ui.keyCode={BACKSPACE:8,COMMA:188,DELETE:46,DOWN:40,END:35,ENTER:13,ESCAPE:27,HOME:36,LEFT:37,PAGE_DOWN:34,PAGE_UP:33,PERIOD:190,RIGHT:39,SPACE:32,TAB:9,UP:38},x.fn.scrollParent=function(t){var e=this.css("position"),i="absolute"===e,o=t?/(auto|scroll|hidden)/:/(auto|scroll)/,t=this.parents().filter(function(){var t=x(this);return(!i||"static"!==t.css("position"))&&o.test(t.css("overflow")+t.css("overflow-y")+t.css("overflow-x"))}).eq(0);return"fixed"!==e&&t.length?t:x(this[0].ownerDocument||document)},x.fn.extend({uniqueId:(t=0,function(){return this.each(function(){this.id||(this.id="ui-id-"+ ++t)})}),removeUniqueId:function(){return this.each(function(){/^ui-id-\d+$/.test(this.id)&&x(this).removeAttr("id")})}})});