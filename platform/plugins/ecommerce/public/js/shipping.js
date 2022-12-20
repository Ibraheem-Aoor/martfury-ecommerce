/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!********************************************************************!*\
  !*** ./platform/plugins/ecommerce/resources/assets/js/shipping.js ***!
  \********************************************************************/
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }
var ShippingManagement = /*#__PURE__*/function () {
  function ShippingManagement() {
    _classCallCheck(this, ShippingManagement);
  }
  _createClass(ShippingManagement, [{
    key: "init",
    value: function init() {
      $(document).on('click', '.btn-trigger-show-shipping-detail', function (event) {
        event.preventDefault();
        $(event.currentTarget).closest('.table').find('.shipping-detail-information').find('.panel').toggleClass('hidden');
      });
      $(document).on('click', '.click-cancel', function (event) {
        event.preventDefault();
        $(event.currentTarget).closest('.table').find('.shipping-detail-information').find('.panel').toggleClass('hidden');
      });
      $(document).on('click', '.btn-confirm-delete-region-item-modal-trigger', function (event) {
        event.preventDefault();
        var $modal = $('#confirm-delete-region-item-modal');
        $modal.find('.region-item-label').text($(event.currentTarget).data('name'));
        $modal.find('#confirm-delete-region-item-button').data('id', $(event.currentTarget).data('id'));
        $modal.modal('show');
      });
      $(document).on('click', '#confirm-delete-region-item-button', function (event) {
        event.preventDefault();
        var _self = $(event.currentTarget);
        _self.addClass('button-loading');
        $.ajax({
          type: 'DELETE',
          url: $('div[data-delete-region-item-url]').data('delete-region-item-url'),
          data: {
            id: _self.data('id')
          },
          success: function success(res) {
            if (!res.error) {
              $('.wrap-table-shipping-' + _self.data('id')).remove();
              Botble.showSuccess(res.message);
            } else {
              Botble.showError(res.message);
            }
            _self.removeClass('button-loading');
            $('#confirm-delete-region-item-modal').modal('hide');
          },
          error: function error(_error) {
            Botble.handleError(_error);
            _self.removeClass('button-loading');
          }
        });
      });
      $(document).on('click', '.btn-confirm-delete-price-item-modal-trigger', function (event) {
        event.preventDefault();
        var $modal = $('#confirm-delete-price-item-modal');
        $modal.find('.region-price-item-label').text($(event.currentTarget).data('name'));
        $modal.find('#confirm-delete-price-item-button').data('id', $(event.currentTarget).data('id'));
        $modal.modal('show');
      });
      $(document).on('click', '#confirm-delete-price-item-button', function (event) {
        event.preventDefault();
        var _self = $(event.currentTarget);
        _self.addClass('button-loading');
        $.ajax({
          type: 'DELETE',
          url: $('div[data-delete-rule-item-url]').data('delete-rule-item-url'),
          data: {
            id: _self.data('id')
          },
          success: function success(res) {
            if (!res.error) {
              $('.box-table-shipping-item-' + _self.data('id')).remove();
              if (res.data.count === 0) {
                $('.wrap-table-shipping-' + res.data.shipping_id).remove();
              }
              Botble.showSuccess(res.message);
            } else {
              Botble.showError(res.message);
            }
            _self.removeClass('button-loading');
            $('#confirm-delete-price-item-modal').modal('hide');
          },
          error: function error(_error2) {
            Botble.handleError(_error2);
            _self.removeClass('button-loading');
          }
        });
      });
      var saveRuleItem = function saveRuleItem($this, $form, method, shippingId) {
        $(document).find('.field-has-error').removeClass('field-has-error');
        var _self = $this;
        _self.addClass('button-loading');
        var formData = [];
        $.each($form.serializeArray(), function (index, el) {
          if (el.name === 'from' || el.name === 'to' || el.name === 'price') {
            if (el.value) {
              el.value = parseFloat(el.value.replace(',', '')).toFixed(2);
            }
          }
          formData[el.name] = el.value;
        });
        if (shippingId) {
          formData['shipping_id'] = shippingId;
        }
        formData = $.extend({}, formData);
        $.ajax({
          type: method,
          url: $form.prop('action'),
          data: formData,
          success: function success(res) {
            if (!res.error) {
              Botble.showSuccess(res.message);
              if (shippingId && res.data) {
                $('.wrap-table-shipping-' + shippingId + ' .pd-all-20.border-bottom').append(res.data);
                Botble.initResources();
              }
            } else {
              Botble.showError(res.message);
            }
            if (shippingId) {
              _self.closest('.modal').modal('hide');
            }
            _self.removeClass('button-loading');
          },
          error: function error(_error3) {
            Botble.handleError(_error3);
            _self.removeClass('button-loading');
          }
        });
      };
      $(document).on('click', '.btn-save-rule', function (event) {
        event.preventDefault();
        saveRuleItem($(event.currentTarget), $(event.currentTarget).closest('form'), 'PUT', null);
      });
      $(document).on('change', '.select-rule-type', function (event) {
        event.preventDefault();
        var _self = $(event.currentTarget);
        _self.closest('.box-table-shipping').find('.unit-item-price-label').toggleClass('hidden');
        _self.closest('.box-table-shipping').find('.unit-item-label').text($(event.currentTarget).find('option:selected').data('unit'));
        _self.closest('.box-table-shipping').find('.rule-from-to-label').text($(event.currentTarget).find('option:selected').data('text'));
      });
      $(document).on('keyup', '.input-sync-item', function (event) {
        var number = $(event.currentTarget).val();
        if (!number || isNaN(number)) {
          number = 0;
        }
        $(event.currentTarget).closest('.input-shipping-sync-wrapper').find($(event.currentTarget).data('target')).text(Botble.numberFormat(parseFloat(number), 2));
      });
      $(document).on('keyup', '.input-sync-text-item', function (event) {
        $(event.currentTarget).closest('.input-shipping-sync-wrapper').find($(event.currentTarget).data('target')).text($(event.currentTarget).val());
      });
      $(document).on('keyup', '.input-to-value-field', function (event) {
        if ($(event.currentTarget).val()) {
          $('.rule-to-value-wrap').removeClass('hidden');
          $('.rule-to-value-missing').addClass('hidden');
        } else {
          $('.rule-to-value-wrap').addClass('hidden');
          $('.rule-to-value-missing').removeClass('hidden');
        }
      });
      $(document).on('click', '.btn-add-shipping-rule-trigger', function (event) {
        event.preventDefault();
        $('#add-shipping-rule-item-button').data('shipping-id', $(event.currentTarget).data('shipping-id'));
        $('#add-shipping-rule-item-modal input[name=name]').val('');
        $('#add-shipping-rule-item-modal select').val('base_on_price');
        $('#add-shipping-rule-item-modal input[name=from]').val('0');
        $('#add-shipping-rule-item-modal input[name=to]').val('');
        $('#add-shipping-rule-item-modal input[name=price]').val('0');
        $('#add-shipping-rule-item-modal').modal('show');
      });
      $(document).find('.select-country-search').select2({
        width: '100%',
        dropdownParent: $('#select-country-modal')
      });
      $(document).on('click', '.btn-select-country', function (event) {
        event.preventDefault();
        $('#select-country-modal').modal('show');
      });
      $(document).on('click', '#add-shipping-region-button', function (event) {
        event.preventDefault();
        var _self = $(event.currentTarget);
        _self.addClass('button-loading');
        var $form = _self.closest('.modal-content').find('form');
        $.ajax({
          type: 'POST',
          url: $form.prop('action'),
          data: $form.serialize(),
          success: function success(res) {
            if (!res.error) {
              Botble.showSuccess(res.message);
              $('.wrapper-content').load(window.location.href + ' .wrapper-content > *');
            } else {
              Botble.showError(res.message);
            }
            _self.removeClass('button-loading');
            $('#select-country-modal').modal('hide');
          },
          error: function error(_error4) {
            Botble.handleError(_error4);
            _self.removeClass('button-loading');
          }
        });
      });
      $(document).on('click', '#add-shipping-rule-item-button', function (event) {
        event.preventDefault();
        saveRuleItem($(event.currentTarget), $(event.currentTarget).closest('.modal-content').find('form'), 'POST', $(event.currentTarget).data('shipping-id'));
      });
      $(document).on('keyup', '.base-price-rule-item', function (event) {
        var _self = $(event.currentTarget);
        var basePrice = _self.val();
        if (!basePrice || isNaN(basePrice)) {
          basePrice = 0;
        }
        $.each($(document).find('.support-shipping .rule-adjustment-price-item'), function (index, item) {
          var adjustmentPrice = $(item).closest('tr').find('.shipping-price-district').val();
          if (!adjustmentPrice || isNaN(adjustmentPrice)) {
            adjustmentPrice = 0;
          }
          $(item).text(Botble.numberFormat(parseFloat(basePrice) + parseFloat(adjustmentPrice)), 2);
        });
      });
    }
  }]);
  return ShippingManagement;
}();
$(document).ready(function () {
  new ShippingManagement().init();
});
/******/ })()
;