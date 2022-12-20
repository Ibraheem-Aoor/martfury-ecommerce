/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!**********************************************************************!*\
  !*** ./platform/plugins/ecommerce/resources/assets/js/currencies.js ***!
  \**********************************************************************/
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }
var Currencies = /*#__PURE__*/function () {
  function Currencies() {
    _classCallCheck(this, Currencies);
    this.template = $('#currency_template').html();
    this.totalItem = 0;
    this.deletedItems = [];
    this.initData();
    this.handleForm();
  }
  _createClass(Currencies, [{
    key: "initData",
    value: function initData() {
      var _self = this;
      var data = $.parseJSON($('#currencies').html());
      $.each(data, function (index, item) {
        var template = _self.template.replace(/__id__/gi, item.id).replace(/__position__/gi, item.order).replace(/__isPrefixSymbolChecked__/gi, item.is_prefix_symbol == 1 ? 'selected' : '').replace(/__notIsPrefixSymbolChecked__/gi, item.is_prefix_symbol == 0 ? 'selected' : '').replace(/__isDefaultChecked__/gi, item.is_default == 1 ? 'checked' : '').replace(/__title__/gi, item.title).replace(/__decimals__/gi, item.decimals).replace(/__exchangeRate__/gi, item.exchange_rate).replace(/__symbol__/gi, item.symbol);
        $('.swatches-container .swatches-list').append(template);
        _self.totalItem++;
      });
    }
  }, {
    key: "addNewAttribute",
    value: function addNewAttribute() {
      var _self = this;
      var template = _self.template.replace(/__id__/gi, 0).replace(/__position__/gi, _self.totalItem).replace(/__isPrefixSymbolChecked__/gi, '').replace(/__notIsPrefixSymbolChecked__/gi, '').replace(/__isDefaultChecked__/gi, _self.totalItem == 0 ? 'checked' : '').replace(/__title__/gi, '').replace(/__decimals__/gi, 0).replace(/__exchangeRate__/gi, 1).replace(/__symbol__/gi, '');
      $('.swatches-container .swatches-list').append(template);
      _self.totalItem++;
    }
  }, {
    key: "exportData",
    value: function exportData() {
      var data = [];
      $('.swatches-container .swatches-list li').each(function (index, item) {
        var $current = $(item);
        data.push({
          id: $current.data('id'),
          is_default: $current.find('[data-type=is_default] input[type=radio]').is(':checked') ? 1 : 0,
          order: $current.index(),
          title: $current.find('[data-type=title] input').val(),
          symbol: $current.find('[data-type=symbol] input').val(),
          decimals: $current.find('[data-type=decimals] input').val(),
          exchange_rate: $current.find('[data-type=exchange_rate] input').val(),
          is_prefix_symbol: $current.find('[data-type=is_prefix_symbol] select').val()
        });
      });
      return data;
    }
  }, {
    key: "handleForm",
    value: function handleForm() {
      var _self = this;
      $('.swatches-container .swatches-list').sortable();
      $('body').on('submit', '.main-setting-form', function () {
        var data = _self.exportData();
        $('#currencies').val(JSON.stringify(data));
        $('#deleted_currencies').val(JSON.stringify(_self.deletedItems));
      }).on('click', '.js-add-new-attribute', function (event) {
        event.preventDefault();
        _self.addNewAttribute();
      }).on('click', '.swatches-container .swatches-list li .remove-item a', function (event) {
        event.preventDefault();
        var $item = $(event.currentTarget).closest('li');
        _self.deletedItems.push($item.data('id'));
        $item.remove();
      });
    }
  }]);
  return Currencies;
}();
$(window).on('load', function () {
  new Currencies();
});
/******/ })()
;