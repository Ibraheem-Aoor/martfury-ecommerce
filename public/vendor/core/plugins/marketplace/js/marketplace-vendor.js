/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!********************************************************************************!*\
  !*** ./platform/plugins/marketplace/resources/assets/js/marketplace-vendor.js ***!
  \********************************************************************************/
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }
var MarketplaceVendorManagement = /*#__PURE__*/function () {
  function MarketplaceVendorManagement() {
    _classCallCheck(this, MarketplaceVendorManagement);
  }
  _createClass(MarketplaceVendorManagement, [{
    key: "init",
    value: function init() {
      $(document).on('click', '.approve-vendor-for-selling-button', function (event) {
        event.preventDefault();
        $('#confirm-approve-vendor-for-selling-button').data('action', $(event.currentTarget).prop('href'));
        $('#approve-vendor-for-selling-modal').modal('show');
      });
      $(document).on('click', '#confirm-approve-vendor-for-selling-button', function (event) {
        event.preventDefault();
        var _self = $(event.currentTarget);
        _self.addClass('button-loading');
        $.ajax({
          type: 'POST',
          cache: false,
          url: _self.data('action'),
          success: function success(res) {
            if (!res.error) {
              Botble.showSuccess(res.message);
              window.location.href = route('marketplace.unverified-vendors.index');
            } else {
              Botble.showError(res.message);
            }
            _self.removeClass('button-loading');
            _self.closest('.modal').modal('hide');
          },
          error: function error(res) {
            Botble.handleError(res);
            _self.removeClass('button-loading');
          }
        });
      });
    }
  }]);
  return MarketplaceVendorManagement;
}();
$(document).ready(function () {
  new MarketplaceVendorManagement().init();
});
/******/ })()
;