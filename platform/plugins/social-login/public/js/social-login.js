/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!***************************************************************************!*\
  !*** ./platform/plugins/social-login/resources/assets/js/social-login.js ***!
  \***************************************************************************/
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }
var SocialLoginManagement = /*#__PURE__*/function () {
  function SocialLoginManagement() {
    _classCallCheck(this, SocialLoginManagement);
  }
  _createClass(SocialLoginManagement, [{
    key: "init",
    value: function init() {
      $('#social_login_enable').on('change', function (event) {
        if ($(event.currentTarget).prop('checked')) {
          $('.wrapper-list-social-login-options').show();
        } else {
          $('.wrapper-list-social-login-options').hide();
        }
      });
      $('.enable-social-login-option').on('change', function (event) {
        var _self = $(event.currentTarget);
        if (_self.prop('checked')) {
          _self.closest('.wrapper-content').find('.enable-social-login-option-wrapper').show();
          _self.closest('.form-group').removeClass('mb-0');
        } else {
          _self.closest('.wrapper-content').find('.enable-social-login-option-wrapper').hide();
          _self.closest('.form-group').addClass('mb-0');
        }
      });
    }
  }]);
  return SocialLoginManagement;
}();
$(document).ready(function () {
  new SocialLoginManagement().init();
});
/******/ })()
;