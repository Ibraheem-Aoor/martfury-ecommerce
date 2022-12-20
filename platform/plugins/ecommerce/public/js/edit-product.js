/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!************************************************************************!*\
  !*** ./platform/plugins/ecommerce/resources/assets/js/edit-product.js ***!
  \************************************************************************/
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }
var EcommerceProduct = /*#__PURE__*/function () {
  function EcommerceProduct() {
    _classCallCheck(this, EcommerceProduct);
    this.$body = $('body');
    this.initElements();
    this.handleEvents();
    this.handleChangeSaleType();
    this.handleShipping();
    this.handleStorehouse();
    this.handleModifyAttributeSets();
    this.handleAddVariations();
    this.handleDeleteVariations();
  }
  _createClass(EcommerceProduct, [{
    key: "handleEvents",
    value: function handleEvents() {
      var _self = this;
      _self.$body.on('click', '.select-all', function (event) {
        event.preventDefault();
        var $select = $($(event.currentTarget).attr('href'));
        $select.find('option').attr('selected', true);
        $select.trigger('change');
      });
      _self.$body.on('click', '.deselect-all', function (event) {
        event.preventDefault();
        var $select = $($(event.currentTarget).attr('href'));
        $select.find('option').removeAttr('selected');
        $select.trigger('change');
      });
      _self.$body.on('change', '#attribute_sets', function (event) {
        var $groupContainer = $('#attribute_set_group');
        var value = $(event.currentTarget).val();
        $groupContainer.find('.panel').hide();
        if (value) {
          _.forEach(value, function (value) {
            $groupContainer.find('.panel[data-id="' + value + '"]').show();
          });
        }
        $('.select2-select').select2();
      });
      $('#attribute_sets').trigger('change');
      _self.$body.on('change', '.is-variation-default input', function (event) {
        var $current = $(event.currentTarget);
        var isChecked = $current.is(':checked');
        $('.is-variation-default input').prop('checked', false);
        if (isChecked) {
          $current.prop('checked', true);
        }
      });
      $(document).on('change', '.table-check-all', function (event) {
        var $current = $(event.currentTarget);
        var set = $current.attr('data-set');
        var checked = $current.prop('checked');
        $(set).each(function (index, el) {
          if (checked) {
            $(el).prop('checked', true);
            $('.btn-trigger-delete-selected-variations').show();
          } else {
            $(el).prop('checked', false);
            $('.btn-trigger-delete-selected-variations').hide();
          }
        });
      });
      $(document).on('change', '.checkboxes', function (event) {
        var $current = $(event.currentTarget);
        var $table = $current.closest('.table-hover-variants');
        var ids = [];
        $table.find('.checkboxes:checked').each(function (i, el) {
          ids[i] = $(el).val();
        });
        if (ids.length > 0) {
          $('.btn-trigger-delete-selected-variations').show();
        } else {
          $('.btn-trigger-delete-selected-variations').hide();
        }
        if (ids.length !== $table.find('.checkboxes').length) {
          $table.find('.table-check-all').prop('checked', false);
        } else {
          $table.find('.table-check-all').prop('checked', true);
        }
      });
      $(document).on('click', '.btn-trigger-delete-selected-variations', function (event) {
        event.preventDefault();
        var $current = $(event.currentTarget);
        var ids = [];
        $('.table-hover-variants').find('.checkboxes:checked').each(function (i, el) {
          ids[i] = $(el).val();
        });
        if (ids.length === 0) {
          Botble.showError(BotbleVariables.languages.tables.please_select_record);
          return false;
        }
        $('#delete-selected-variations-button').data('href', $current.data('target'));
        $('#delete-variations-modal').modal('show');
      });
      $('#delete-selected-variations-button').off('click').on('click', function (event) {
        event.preventDefault();
        var $current = $(event.currentTarget);
        $current.addClass('button-loading');
        var $table = $('.table-hover-variants');
        var ids = [];
        $table.find('.checkboxes:checked').each(function (i, el) {
          ids[i] = $(el).val();
        });
        $.ajax({
          url: $current.data('href'),
          type: 'DELETE',
          data: {
            ids: ids
          },
          success: function success(data) {
            if (data.error) {
              Botble.showError(data.message);
            } else {
              Botble.showSuccess(data.message);
            }
            $('.btn-trigger-delete-selected-variations').hide();
            $table.find('.table-check-all').prop('checked', false);
            $current.closest('.modal').modal('hide');
            $current.removeClass('button-loading');
            if ($table.find('tbody tr').length === ids.length) {
              $('#main-manage-product-type').load(window.location.href + ' #main-manage-product-type > *', function () {
                _self.initElements();
                _self.handleEvents();
              });
            } else {
              ids.forEach(function (id) {
                $table.find('#variation-id-' + id).fadeOut(400).remove();
              });
            }
          },
          error: function error(data) {
            Botble.handleError(data);
            $current.removeClass('button-loading');
          }
        });
      });
    }
  }, {
    key: "initElements",
    value: function initElements() {
      $('.select2-select').select2();
      $('.form-date-time').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
        toolbarPlacement: 'bottom',
        showTodayButton: true,
        stepping: 1
      });
      $('#attribute_set_group .panel-collapse').on('shown.bs.collapse', function () {
        $('.select2-select').select2();
      });
      $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
        $('.select2-select').select2();
      });
    }
  }, {
    key: "handleChangeSaleType",
    value: function handleChangeSaleType() {
      var _self = this;
      _self.$body.on('click', '.turn-on-schedule', function (event) {
        event.preventDefault();
        var $current = $(event.currentTarget);
        var $group = $current.closest('.price-group');
        $current.addClass('hidden');
        $group.find('.turn-off-schedule').removeClass('hidden');
        $group.find('.detect-schedule').val(1);
        $group.find('.scheduled-time').removeClass('hidden');
      });
      _self.$body.on('click', '.turn-off-schedule', function (event) {
        event.preventDefault();
        var $current = $(event.currentTarget);
        var $group = $current.closest('.price-group');
        $current.addClass('hidden');
        $group.find('.turn-on-schedule').removeClass('hidden');
        $group.find('.detect-schedule').val(0);
        $group.find('.scheduled-time').addClass('hidden');
      });
    }
  }, {
    key: "handleStorehouse",
    value: function handleStorehouse() {
      var _self = this;
      _self.$body.on('click', 'input.storehouse-management-status', function (event) {
        var $storehouseInfo = $('.storehouse-info');
        if ($(event.currentTarget).prop('checked') === true) {
          $storehouseInfo.removeClass('hidden');
          $('.stock-status-wrapper').addClass('hidden');
        } else {
          $storehouseInfo.addClass('hidden');
          $('.stock-status-wrapper').removeClass('hidden');
        }
      });
    }
  }, {
    key: "handleShipping",
    value: function handleShipping() {
      var _self = this;
      _self.$body.on('click', '.change-measurement .dropdown-menu a', function (event) {
        event.preventDefault();
        var $current = $(event.currentTarget);
        var $parent = $current.closest('.change-measurement');
        var $input = $parent.find('input[type=hidden]');
        $input.val($current.attr('data-alias'));
        $parent.find('.dropdown-toggle .alias').html($current.attr('data-alias'));
      });
    }
  }, {
    key: "handleModifyAttributeSets",
    value: function handleModifyAttributeSets() {
      var _self = this;
      _self.$body.on('click', '#store-related-attributes-button', function (event) {
        event.preventDefault();
        var $current = $(event.currentTarget);
        var attributeSets = [];
        $current.closest('.modal-content').find('.attribute-set-item:checked').each(function (index, item) {
          attributeSets[index] = $(item).val();
        });
        $.ajax({
          url: $current.data('target'),
          type: 'POST',
          data: {
            attribute_sets: attributeSets
          },
          beforeSend: function beforeSend() {
            $current.addClass('button-loading');
          },
          success: function success(res) {
            if (res.error) {
              Botble.showError(res.message);
            } else {
              Botble.showSuccess(res.message);
              $('#main-manage-product-type').load(window.location.href + ' #main-manage-product-type > *', function () {
                _self.initElements();
                _self.handleEvents();
              });
              $('#select-attribute-sets-modal').modal('hide');
            }
            $current.removeClass('button-loading');
          },
          complete: function complete() {
            $current.removeClass('button-loading');
          },
          error: function error(data) {
            Botble.handleError(data);
            $current.removeClass('button-loading');
          }
        });
      });
    }
  }, {
    key: "handleAddVariations",
    value: function handleAddVariations() {
      var _self = this;
      var createOrUpdateVariation = function createOrUpdateVariation($current) {
        var formData = $current.closest('.modal-content').find('.variation-form-wrapper').find('select,textarea,input').serialize();
        $.ajax({
          url: $current.data('target'),
          type: 'POST',
          data: formData,
          beforeSend: function beforeSend() {
            $current.addClass('button-loading');
          },
          success: function success(res) {
            if (res.error) {
              Botble.showError(res.message);
            } else {
              Botble.showSuccess(res.message);
              $current.closest('.modal.fade').modal('hide');
              $('#product-variations-wrapper').load(window.location.href + ' #product-variations-wrapper > *', function () {
                _self.initElements();
                _self.handleEvents();
              });
              $current.closest('.modal-content').find('.variation-form-wrapper').remove();
            }
            $current.removeClass('button-loading');
          },
          complete: function complete() {
            $current.removeClass('button-loading');
          },
          error: function error(data) {
            Botble.handleError(data);
            $current.removeClass('button-loading');
          }
        });
      };
      _self.$body.on('click', '#store-product-variation-button', function (event) {
        event.preventDefault();
        createOrUpdateVariation($(event.currentTarget));
      });
      _self.$body.on('click', '#update-product-variation-button', function (event) {
        event.preventDefault();
        createOrUpdateVariation($(event.currentTarget));
      });
      $('#add-new-product-variation-modal').on('hidden.bs.modal', function (e) {
        $(this).find('.modal-content .variation-form-wrapper').remove();
      });
      $('#edit-product-variation-modal').on('hidden.bs.modal', function (e) {
        $(this).find('.modal-content .variation-form-wrapper').remove();
      });
      _self.$body.on('click', '#generate-all-versions-button', function (event) {
        event.preventDefault();
        var $current = $(event.currentTarget);
        $.ajax({
          url: $current.data('target'),
          type: 'POST',
          beforeSend: function beforeSend() {
            $current.addClass('button-loading');
          },
          success: function success(res) {
            if (res.error) {
              Botble.showError(res.message);
            } else {
              Botble.showSuccess(res.message);
              $('#generate-all-versions-modal').modal('hide');
              $('#product-variations-wrapper').load(window.location.href + ' #product-variations-wrapper > *', function () {
                _self.initElements();
                _self.handleEvents();
              });
            }
            $current.removeClass('button-loading');
          },
          complete: function complete() {
            $current.removeClass('button-loading');
          },
          error: function error(data) {
            Botble.handleError(data);
            $current.removeClass('button-loading');
          }
        });
      });
      $(document).on('click', '.btn-trigger-add-new-product-variation', function (event) {
        event.preventDefault();
        var $current = $(event.currentTarget);
        $('#add-new-product-variation-modal .modal-body .loading-spinner').show();
        $('#add-new-product-variation-modal .modal-body .variation-form-wrapper').remove();
        $('#add-new-product-variation-modal').modal('show');
        $.ajax({
          url: $current.data('load-form'),
          type: 'GET',
          success: function success(res) {
            if (res.error) {
              Botble.showError(res.message);
            } else {
              $('#add-new-product-variation-modal .modal-body .loading-spinner').hide();
              $('#add-new-product-variation-modal .modal-body').append(res.data);
              _self.initElements();
              Botble.initResources();
              $('#store-product-variation-button').data('target', $current.data('target'));
              $('.list-gallery-media-images').each(function (index, item) {
                var $current = $(item);
                if ($current.data('ui-sortable')) {
                  $current.sortable('destroy');
                }
                $current.sortable();
              });
            }
          },
          error: function error(data) {
            Botble.handleError(data);
          }
        });
      });
      $(document).on('click', '.btn-trigger-edit-product-version', function (event) {
        event.preventDefault();
        $('#update-product-variation-button').data('target', $(event.currentTarget).data('target'));
        var $current = $(event.currentTarget);
        $('#edit-product-variation-modal .modal-body .loading-spinner').show();
        $('#edit-product-variation-modal .modal-body .variation-form-wrapper').remove();
        $('#edit-product-variation-modal').modal('show');
        $.ajax({
          url: $current.data('load-form'),
          type: 'GET',
          success: function success(res) {
            if (res.error) {
              Botble.showError(res.message);
            } else {
              $('#edit-product-variation-modal .modal-body .loading-spinner').hide();
              $('#edit-product-variation-modal .modal-body').append(res.data);
              _self.initElements();
              Botble.initResources();
              $('.list-gallery-media-images').each(function (index, item) {
                var $current = $(item);
                if ($current.data('ui-sortable')) {
                  $current.sortable('destroy');
                }
                $current.sortable();
              });
            }
          },
          error: function error(data) {
            Botble.handleError(data);
          }
        });
      });
      _self.$body.on('click', '.btn-trigger-add-attribute-to-simple-product', function (event) {
        event.preventDefault();
        var $current = $(event.currentTarget);
        var addedAttributes = [];
        var addedAttributeSets = [];
        $.each($('.list-product-attribute-wrap-detail .product-attribute-set-item'), function (index, el) {
          if (!$(el).hasClass('hidden')) {
            if ($(el).find('.product-select-attribute-item-value').val() !== '') {
              addedAttributes.push($(el).find('.product-select-attribute-item-value').val());
              addedAttributeSets.push($(el).find('.product-select-attribute-item-value').data('set-id'));
            }
          }
        });
        if (addedAttributes.length) {
          $.ajax({
            url: $current.data('target'),
            type: 'POST',
            data: {
              added_attributes: addedAttributes,
              added_attribute_sets: addedAttributeSets
            },
            beforeSend: function beforeSend() {
              $current.addClass('button-loading');
            },
            success: function success(res) {
              if (res.error) {
                Botble.showError(res.message);
              } else {
                $('#main-manage-product-type').load(window.location.href + ' #main-manage-product-type > *', function () {
                  _self.initElements();
                  _self.handleEvents();
                });
                $('#confirm-delete-version-modal').modal('hide');
                Botble.showSuccess(res.message);
              }
              $current.removeClass('button-loading');
            },
            complete: function complete() {
              $current.removeClass('button-loading');
            },
            error: function error(data) {
              $current.removeClass('button-loading');
              Botble.handleError(data);
            }
          });
        }
      });
    }
  }, {
    key: "handleDeleteVariations",
    value: function handleDeleteVariations() {
      var _self = this;
      $(document).on('click', '.btn-trigger-delete-version', function (event) {
        event.preventDefault();
        $('#delete-version-button').data('target', $(event.currentTarget).data('target')).data('id', $(event.currentTarget).data('id'));
        $('#confirm-delete-version-modal').modal('show');
      });
      _self.$body.on('click', '#delete-version-button', function (event) {
        event.preventDefault();
        var $current = $(event.currentTarget);
        $.ajax({
          url: $current.data('target'),
          type: 'POST',
          beforeSend: function beforeSend() {
            $current.addClass('button-loading');
          },
          success: function success(res) {
            if (res.error) {
              Botble.showError(res.message);
            } else {
              var $table = $('.table-hover-variants');
              if ($table.find('tbody tr').length === 1) {
                $('#main-manage-product-type').load(window.location.href + ' #main-manage-product-type > *', function () {
                  _self.initElements();
                  _self.handleEvents();
                });
              } else {
                $table.find('#variation-id-' + $current.data('id')).fadeOut(400).remove();
              }
              $('#confirm-delete-version-modal').modal('hide');
              Botble.showSuccess(res.message);
            }
            $current.removeClass('button-loading');
          },
          complete: function complete() {
            $current.removeClass('button-loading');
          },
          error: function error(data) {
            $current.removeClass('button-loading');
            Botble.handleError(data);
          }
        });
      });
    }
  }]);
  return EcommerceProduct;
}();
$(window).on('load', function () {
  new EcommerceProduct();
  $('body').on('click', '.list-gallery-media-images .btn_remove_image', function (event) {
    event.preventDefault();
    $(event.currentTarget).closest('li').remove();
  });
  $(document).on('click', '.btn-trigger-select-product-attributes', function (event) {
    event.preventDefault();
    $('#store-related-attributes-button').data('target', $(event.currentTarget).data('target'));
    $('#select-attribute-sets-modal').modal('show');
  });
  $(document).on('click', '.btn-trigger-generate-all-versions', function (event) {
    event.preventDefault();
    $('#generate-all-versions-button').data('target', $(event.currentTarget).data('target'));
    $('#generate-all-versions-modal').modal('show');
  });
  $(document).on('click', '.btn-trigger-add-attribute', function (event) {
    event.preventDefault();
    $('.list-product-attribute-wrap').toggleClass('hidden');
    $(event.currentTarget).toggleClass('adding_attribute_enable');
    if ($(event.currentTarget).hasClass('adding_attribute_enable')) {
      $('#is_added_attributes').val(1);
    } else {
      $('#is_added_attributes').val(0);
    }
    var toggleText = $(event.currentTarget).data('toggle-text');
    $(event.currentTarget).data('toggle-text', $(event.currentTarget).text());
    $(event.currentTarget).text(toggleText);
  });
  $(document).on('change', '.product-select-attribute-item', function () {
    var selectedItems = [];
    $.each($('.product-select-attribute-item'), function (index, el) {
      if ($(el).val() !== '') {
        selectedItems.push(index);
      }
    });
    if (selectedItems.length) {
      $('.btn-trigger-add-attribute-to-simple-product').removeClass('hidden');
    } else {
      $('.btn-trigger-add-attribute-to-simple-product').addClass('hidden');
    }
  });
  var handleChangeAttributeSet = function handleChangeAttributeSet() {
    $.each($('.product-attribute-set-item:visible .product-select-attribute-item option'), function (index, el) {
      if ($(el).prop('value') !== $(el).closest('select').val()) {
        if ($('.list-product-attribute-wrap-detail .product-select-attribute-item-value-id-' + $(el).prop('value')).length === 0) {
          $(el).prop('disabled', false);
        } else {
          $(el).prop('disabled', true);
        }
      }
    });
  };
  $(document).on('change', '.product-select-attribute-item', function (event) {
    $(event.currentTarget).closest('.product-attribute-set-item').find('.product-select-attribute-item-value-wrap').html($('.list-product-attribute-values-wrap .product-select-attribute-item-value-wrap-' + $(event.currentTarget).val()).html());
    $(event.currentTarget).closest('.product-attribute-set-item').find('.product-select-attribute-item-value-id-' + $(event.currentTarget).val()).prop('name', 'added_attributes[' + $(event.currentTarget).val() + ']');
    handleChangeAttributeSet();
  });
  $(document).on('click', '.btn-trigger-add-attribute-item', function (event) {
    event.preventDefault();
    var $template = $('.list-product-attribute-values-wrap .product-select-attribute-item-template');
    var selectedValue = null;
    $.each($('.product-attribute-set-item:visible .product-select-attribute-item option'), function (index, el) {
      if ($(el).prop('value') !== $(el).closest('select').val() && $(el).prop('disabled') === false) {
        $template.find('.product-select-attribute-item-value-wrap').html($('.list-product-attribute-values-wrap .product-select-attribute-item-value-wrap-' + $(el).prop('value')).html());
        selectedValue = $(el).prop('value');
      }
    });
    var $listDetailWrap = $('.list-product-attribute-wrap-detail');
    $listDetailWrap.append($template.html());
    $listDetailWrap.find('.product-attribute-set-item:last-child .product-select-attribute-item').val(selectedValue);
    $listDetailWrap.find('.product-select-attribute-item-value-id-' + selectedValue).prop('name', 'added_attributes[' + selectedValue + ']');
    if ($listDetailWrap.find('.product-attribute-set-item').length === $('.list-product-attribute-values-wrap .product-select-attribute-item-wrap-template').length) {
      $(event.currentTarget).addClass('hidden');
    }
    $('.product-set-item-delete-action').removeClass('hidden');
    handleChangeAttributeSet();
  });
  $(document).on('click', '.product-set-item-delete-action a', function (event) {
    event.preventDefault();
    $(event.currentTarget).closest('.product-attribute-set-item').remove();
    var $listProductAttributeWrap = $('.list-product-attribute-wrap-detail');
    if ($listProductAttributeWrap.find('.product-attribute-set-item').length < 2) {
      $('.product-set-item-delete-action').addClass('hidden');
    }
    if ($listProductAttributeWrap.find('.product-attribute-set-item').length < $('.list-product-attribute-values-wrap .product-select-attribute-item-wrap-template').length) {
      $('.btn-trigger-add-attribute-item').removeClass('hidden');
    }
    handleChangeAttributeSet();
  });
  if (typeof RvMediaStandAlone != 'undefined') {
    new RvMediaStandAlone('.images-wrapper .btn-trigger-edit-product-image', {
      filter: 'image',
      view_in: 'all_media',
      onSelectFiles: function onSelectFiles(files, $el) {
        var firstItem = _.first(files);
        var $currentBox = $el.closest('.product-image-item-handler').find('.image-box');
        var $currentBoxList = $el.closest('.list-gallery-media-images');
        $currentBox.find('.image-data').val(firstItem.url);
        $currentBox.find('.preview_image').attr('src', firstItem.thumb).show();
        _.forEach(files, function (file, index) {
          if (!index) {
            return;
          }
          var template = $(document).find('#product_select_image_template').html();
          var imageBox = template.replace(/__name__/gi, $currentBox.find('.image-data').attr('name'));
          var $template = $('<li class="product-image-item-handler">' + imageBox + '</li>');
          $template.find('.image-data').val(file.url);
          $template.find('.preview_image').attr('src', file.thumb).show();
          $currentBoxList.append($template);
        });
      }
    });
  }
  $(document).on('click', '.btn-trigger-remove-product-image', function (event) {
    event.preventDefault();
    $(event.currentTarget).closest('.product-image-item-handler').remove();
    if ($('.list-gallery-media-images').find('.product-image-item-handler').length === 0) {
      $('.default-placeholder-product-image').removeClass('hidden');
    }
  });
  $(document).on('click', '.list-search-data .selectable-item', function (event) {
    event.preventDefault();
    var _self = $(event.currentTarget);
    var $input = _self.closest('.form-group').find('input[type=hidden]');
    var existedValues = $input.val().split(',');
    $.each(existedValues, function (index, el) {
      existedValues[index] = parseInt(el);
    });
    if ($.inArray(_self.data('id'), existedValues) < 0) {
      if ($input.val()) {
        $input.val($input.val() + ',' + _self.data('id'));
      } else {
        $input.val(_self.data('id'));
      }
      var template = $(document).find('#selected_product_list_template').html();
      var productItem = template.replace(/__name__/gi, _self.data('name')).replace(/__id__/gi, _self.data('id')).replace(/__url__/gi, _self.data('url')).replace(/__image__/gi, _self.data('image')).replace(/__attributes__/gi, _self.find('a span').text());
      _self.closest('.form-group').find('.list-selected-products').removeClass('hidden');
      _self.closest('.form-group').find('.list-selected-products table tbody').append(productItem);
    }
    _self.closest('.panel').addClass('hidden');
  });
  $(document).on('click', '.textbox-advancesearch', function (event) {
    var _self = $(event.currentTarget);
    var $formBody = _self.closest('.box-search-advance').find('.panel');
    $formBody.removeClass('hidden');
    $formBody.addClass('active');
    if ($formBody.find('.panel-body').length === 0) {
      Botble.blockUI({
        target: $formBody,
        iconOnly: true,
        overlayColor: 'none'
      });
      $.ajax({
        url: _self.data('target'),
        type: 'GET',
        success: function success(res) {
          if (res.error) {
            Botble.showError(res.message);
          } else {
            $formBody.html(res.data);
            Botble.unblockUI($formBody);
          }
        },
        error: function error(data) {
          Botble.handleError(data);
          Botble.unblockUI($formBody);
        }
      });
    }
  });
  $(document).on('keyup', '.textbox-advancesearch', function (event) {
    var _self = $(event.currentTarget);
    var $formBody = _self.closest('.box-search-advance').find('.panel');
    setTimeout(function () {
      Botble.blockUI({
        target: $formBody,
        iconOnly: true,
        overlayColor: 'none'
      });
      $.ajax({
        url: _self.data('target') + '&keyword=' + _self.val(),
        type: 'GET',
        success: function success(res) {
          if (res.error) {
            Botble.showError(res.message);
          } else {
            $formBody.html(res.data);
            Botble.unblockUI($formBody);
          }
        },
        error: function error(data) {
          Botble.handleError(data);
          Botble.unblockUI($formBody);
        }
      });
    }, 500);
  });
  $(document).on('click', '.box-search-advance .page-link', function (event) {
    event.preventDefault();
    var _self = $(event.currentTarget);
    if (!_self.closest('.page-item').hasClass('disabled') && _self.prop('href')) {
      var $formBody = _self.closest('.box-search-advance').find('.panel');
      Botble.blockUI({
        target: $formBody,
        iconOnly: true,
        overlayColor: 'none'
      });
      $.ajax({
        url: _self.prop('href') + '&keyword=' + _self.val(),
        type: 'GET',
        success: function success(res) {
          if (res.error) {
            Botble.showError(res.message);
          } else {
            $formBody.html(res.data);
            Botble.unblockUI($formBody);
          }
        },
        error: function error(data) {
          Botble.handleError(data);
          Botble.unblockUI($formBody);
        }
      });
    }
  });
  $(document).on('click', 'body', function (e) {
    var container = $('.box-search-advance');
    if (!container.is(e.target) && container.has(e.target).length === 0) {
      container.find('.panel').addClass('hidden');
    }
  });
  $(document).on('click', '.btn-trigger-remove-selected-product', function (event) {
    event.preventDefault();
    var $input = $(event.currentTarget).closest('.form-group').find('input[type=hidden]');
    var existedValues = $input.val().split(',');
    $.each(existedValues, function (index, el) {
      el = el.trim();
      if (!_.isEmpty(el)) {
        existedValues[index] = parseInt(el);
      }
    });
    var index = existedValues.indexOf($(event.currentTarget).data('id'));
    if (index > -1) {
      existedValues.splice(index, 1);
    }
    $input.val(existedValues.join(','));
    if ($(event.currentTarget).closest('tbody').find('tr').length < 2) {
      $(event.currentTarget).closest('.list-selected-products').addClass('hidden');
    }
    $(event.currentTarget).closest('tr').remove();
  });
  var loadRelationBoxes = function loadRelationBoxes() {
    var $wrapBody = $('.wrap-relation-product');
    if ($wrapBody.length) {
      Botble.blockUI({
        target: $wrapBody,
        iconOnly: true,
        overlayColor: 'none'
      });
      $.ajax({
        url: $wrapBody.data('target'),
        type: 'GET',
        success: function success(res) {
          if (res.error) {
            Botble.showError(res.message);
          } else {
            $wrapBody.html(res.data);
            Botble.unblockUI($wrapBody);
          }
        },
        error: function error(data) {
          Botble.handleError(data);
          Botble.unblockUI($wrapBody);
        }
      });
    }
  };
  $(document).ready(function () {
    loadRelationBoxes();
  });
});
/******/ })()
;