!function(t){function n(e){if(o[e])return o[e].exports;var r=o[e]={i:e,l:!1,exports:{}};return t[e].call(r.exports,r,r.exports,n),r.l=!0,r.exports}var o={};return n.m=t,n.c=o,n.i=function(t){return t},n.d=function(t,o,e){n.o(t,o)||Object.defineProperty(t,o,{configurable:!1,enumerable:!0,get:e})},n.n=function(t){var o=t&&t.__esModule?function(){return t.default}:function(){return t};return n.d(o,"a",o),o},n.o=function(t,n){return Object.prototype.hasOwnProperty.call(t,n)},n.p="",n(n.s=984)}({297:function(t,n){t.exports=jQuery},456:function(t,n,o){"use strict";function e(t){return t&&t.__esModule?t:{default:t}}var r="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},i=o(297),u=e(i);(0,u.default)(document).ready(function(t){function n(o,e){if(Array.isArray(o)&&o[e]){var i=o[e];if("string"!=typeof i.target||"string"!=typeof i.id||"object"!==r(i.options)||"string"!=typeof i.options.content)return void n(o,e+1);var u=t.extend(i.options,{close:function(){t.post(ajaxurl,{pointer:i.id,action:"dismiss-wp-pointer"}),n(o,e+1)}});t(i.target).pointer(u).pointer("open")}}n(wcSevicesAdminPointers,0)})},984:function(t,n,o){t.exports=o(456)}});