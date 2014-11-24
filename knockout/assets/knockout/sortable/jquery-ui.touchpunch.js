/*!
 * jQuery UI Touch Punch 0.2.3
 * Fixes for IE10+ touch-related events cherry-picked from https://github.com/tsmd/jquery-ui-touch-punch
 * Author: Ganesh Prasannah (exchequer598@gmail.com)
 *
 * Copyright 2011–2014, Dave Furfero
 * Dual licensed under the MIT or GPL Version 2 licenses.
 *
 * Depends:
 *  jquery.ui.widget.js
 *  jquery.ui.mouse.js
 */
!function(o){function t(o,t){if(!(!e&&o.originalEvent.touches.length>1||e&&!o.isPrimary)){o.preventDefault();var n=e?o.originalEvent:o.originalEvent.changedTouches[0],u=document.createEvent("MouseEvents");u.initMouseEvent(t,!0,!0,window,1,n.screenX,n.screenY,n.clientX,n.clientY,!1,!1,!1,!1,0,null),o.target.dispatchEvent(u)}}var e=window.navigator.pointerEnabled||window.navigator.msPointerEnabled;if(o.support.touch="ontouchend"in document||e,o.support.touch){var n,u=o.ui.mouse.prototype,r=u._mouseInit,c=u._mouseDestroy;u._touchStart=function(o){var u=this;n||!e&&!u._mouseCapture(o.originalEvent.changedTouches[0])||(n=!0,u._touchMoved=!1,t(o,"mouseover"),t(o,"mousemove"),t(o,"mousedown"))},u._touchMove=function(o){n&&(this._touchMoved=!0,t(o,"mousemove"))},u._touchEnd=function(o){n&&(t(o,"mouseup"),t(o,"mouseout"),this._touchMoved||t(o,"click"),n=!1)},u._mouseInit=function(){var t=this;t.element.bind(e?{pointerDown:o.proxy(t,"_touchStart"),pointerMove:o.proxy(t,"_touchMove"),pointerUp:o.proxy(t,"_touchEnd"),MSPointerDown:o.proxy(t,"_touchStart"),MSPointerMove:o.proxy(t,"_touchMove"),MSPointerUp:o.proxy(t,"_touchEnd")}:{touchstart:o.proxy(t,"_touchStart"),touchmove:o.proxy(t,"_touchMove"),touchend:o.proxy(t,"_touchEnd")}),r.call(t)},u._mouseDestroy=function(){var t=this;t.element.unbind({touchstart:o.proxy(t,"_touchStart"),touchmove:o.proxy(t,"_touchMove"),touchend:o.proxy(t,"_touchEnd")}),c.call(t)}}}(jQuery);