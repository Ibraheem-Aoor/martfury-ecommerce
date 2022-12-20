/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!****************************************************************************************!*\
  !*** ./platform/plugins/ecommerce/resources/assets/js/ecommerce-product-attributes.js ***!
  \****************************************************************************************/
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }
var EcommerceProductAttribute = /*#__PURE__*/function () {
  function EcommerceProductAttribute() {
    _classCallCheck(this, EcommerceProductAttribute);
    this.template = $('#product_attribute_template').html();
    this.totalItem = 0;
    this.deletedItems = [];
    this.handleChooseImage();
    this.handleForm();
    this.initSpectrum();
  }
  _createClass(EcommerceProductAttribute, [{
    key: "handleChooseImage",
    value: function handleChooseImage() {
      new RvMediaStandAlone('.image-box .btn-images', {
        filter: 'image',
        view_in: 'all_media',
        onSelectFiles: function onSelectFiles(files, $el) {
          var firstItem = _.first(files);
          if (firstItem) {
            $el.closest('.image-box').find('.image-data').val(firstItem.url);
            $el.closest('.image-box').find('.preview_image').attr('src', firstItem.thumb).show();
          }
        }
      });
    }
  }, {
    key: "initSpectrum",
    value: function initSpectrum() {
      $('.input-color-picker').each(function (index, item) {
        var $current = $(item);
        $current.spectrum({
          allowEmpty: true,
          color: $current.val() || 'rgb(51, 51, 51)',
          showInput: true,
          containerClassName: 'full-spectrum',
          showInitial: true,
          showSelectionPalette: false,
          showPalette: true,
          showAlpha: true,
          preferredFormat: 'hex',
          showButtons: false,
          palette: [["rgb(0, 0, 0)", "rgb(102, 102, 102)", "rgb(183, 183, 183)", "rgb(217, 217, 217)", "rgb(239, 239, 239)", "rgb(243, 243, 243)", "rgb(255, 255, 255)", "rgb(230, 184, 175)", "rgb(244, 204, 204)", "rgb(252, 229, 205)", "rgb(255, 242, 204)", "rgb(217, 234, 211)", "rgb(208, 224, 227)", "rgb(201, 218, 248)", "rgb(207, 226, 243)", "rgb(217, 210, 233)", "rgb(234, 209, 220)", "rgb(221, 126, 107)", "rgb(234, 153, 153)", "rgb(249, 203, 156)", "rgb(255, 229, 153)", "rgb(182, 215, 168)", "rgb(162, 196, 201)", "rgb(164, 194, 244)", "rgb(159, 197, 232)", "rgb(180, 167, 214)", "rgb(213, 166, 189)", "rgb(204, 65, 37)", "rgb(224, 102, 102)", "rgb(246, 178, 107)", "rgb(255, 217, 102)", "rgb(147, 196, 125)", "rgb(118, 165, 175)", "rgb(109, 158, 235)", "rgb(111, 168, 220)", "rgb(142, 124, 195)", "rgb(194, 123, 160)", "rgb(166, 28, 0)", "rgb(204, 0, 0)", "rgb(230, 145, 56)", "rgb(241, 194, 50)", "rgb(106, 168, 79)", "rgb(69, 129, 142)", "rgb(60, 120, 216)", "rgb(61, 133, 198)", "rgb(103, 78, 167)", "rgb(166, 77, 121)", "rgb(133, 32, 12)", "rgb(153, 0, 0)", "rgb(180, 95, 6)", "rgb(191, 144, 0)", "rgb(56, 118, 29)", "rgb(19, 79, 92)", "rgb(17, 85, 204)", "rgb(11, 83, 148)", "rgb(53, 28, 117)", "rgb(116, 27, 71)", "rgb(91, 15, 0)", "rgb(102, 0, 0)", "rgb(120, 63, 4)", "rgb(127, 96, 0)", "rgb(39, 78, 19)", "rgb(12, 52, 61)", "rgb(28, 69, 135)", "rgb(7, 55, 99)", "rgb(32, 18, 77)", "rgb(76, 17, 48)", "rgb(152, 0, 0)", "rgb(255, 0, 0)", "rgb(255, 153, 0)", "rgb(255, 255, 0)", "rgb(0, 255, 0)", "rgb(0, 255, 255)", "rgb(74, 134, 232)", "rgb(0, 0, 255)", "rgb(153, 0, 255)", "rgb(255, 0, 255)"]],
          change: function change(color) {
            $current.val(color.toRgbString());
          }
        });
      });
    }
  }, {
    key: "addNewAttribute",
    value: function addNewAttribute() {
      var _self = this;
      var template = _self.template.replace(/__id__/gi, 0).replace(/__position__/gi, 0).replace(/__checked__/gi, _self.totalItem == 0 ? 'checked' : '').replace(/__title__/gi, '').replace(/__slug__/gi, '').replace(/__color__/gi, '').replace(/__image__/gi, '');
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
          is_default: $current.find('input[type=radio]').is(':checked') ? 1 : 0,
          order: $current.index(),
          title: $current.find('.swatch-title input').val(),
          slug: $current.find('.swatch-slug input').val(),
          color: $current.find('.swatch-value input').val(),
          image: $current.find('.swatch-image input').val()
        });
      });
      return data;
    }
  }, {
    key: "handleForm",
    value: function handleForm() {
      var _self = this;
      $('.swatches-container .swatches-list').sortable();
      $('body').on('submit', '.update-attribute-set-form', function () {
        var data = _self.exportData();
        $('#attributes').val(JSON.stringify(data));
        $('#deleted_attributes').val(JSON.stringify(_self.deletedItems));
      }).on('click', '.js-add-new-attribute', function (event) {
        event.preventDefault();
        _self.addNewAttribute();
        _self.initSpectrum();
      }).on('click', '.swatches-container .swatches-list li .remove-item a', function (event) {
        event.preventDefault();
        var $item = $(event.currentTarget).closest('li');
        _self.deletedItems.push($item.data('id'));
        $item.remove();
      });
    }
  }]);
  return EcommerceProductAttribute;
}();
$(window).on('load', function () {
  new EcommerceProductAttribute();
});
/******/ })()
;