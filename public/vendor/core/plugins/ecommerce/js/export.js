/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!******************************************************************!*\
  !*** ./platform/plugins/ecommerce/resources/assets/js/export.js ***!
  \******************************************************************/
$(function () {
  var isExporting = false;
  $(document).on('click', '.btn-export-data', function (event) {
    event.preventDefault();
    if (isExporting) {
      return;
    }
    var $this = $(event.currentTarget);
    var $content = $this.html();
    $.ajax({
      url: $this.attr('href'),
      method: 'POST',
      xhrFields: {
        responseType: 'blob'
      },
      beforeSend: function beforeSend() {
        $this.html($this.data('loading-text'));
        $this.attr('disabled', 'true');
        isExporting = true;
      },
      success: function success(data) {
        var a = document.createElement('a');
        var url = window.URL.createObjectURL(data);
        a.href = url;
        a.download = $this.data('filename');
        document.body.append(a);
        a.click();
        a.remove();
        window.URL.revokeObjectURL(url);
      },
      error: function error(data) {
        Botble.handleError(data);
      },
      complete: function complete() {
        setTimeout(function () {
          $this.html($content);
          $this.removeAttr('disabled');
          isExporting = false;
        }, 2000);
      }
    });
  });
});
/******/ })()
;