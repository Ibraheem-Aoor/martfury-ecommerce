(()=>{function e(e,t){for(var n=0;n<t.length;n++){var a=t[n];a.enumerable=a.enumerable||!1,a.configurable=!0,"value"in a&&(a.writable=!0),Object.defineProperty(e,a.key,a)}}var t=function(){function t(){!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,t)}var n,a;return n=t,(a=[{key:"init",value:function(){$(document).on("click",".btn-trigger-show-shipping-detail",(function(e){e.preventDefault(),$(e.currentTarget).closest(".table").find(".shipping-detail-information").find(".panel").toggleClass("hidden")})),$(document).on("click",".click-cancel",(function(e){e.preventDefault(),$(e.currentTarget).closest(".table").find(".shipping-detail-information").find(".panel").toggleClass("hidden")})),$(document).on("click",".btn-confirm-delete-region-item-modal-trigger",(function(e){e.preventDefault();var t=$("#confirm-delete-region-item-modal");t.find(".region-item-label").text($(e.currentTarget).data("name")),t.find("#confirm-delete-region-item-button").data("id",$(e.currentTarget).data("id")),t.modal("show")})),$(document).on("click","#confirm-delete-region-item-button",(function(e){e.preventDefault();var t=$(e.currentTarget);t.addClass("button-loading"),$.ajax({type:"DELETE",url:$("div[data-delete-region-item-url]").data("delete-region-item-url"),data:{id:t.data("id")},success:function(e){e.error?Botble.showError(e.message):($(".wrap-table-shipping-"+t.data("id")).remove(),Botble.showSuccess(e.message)),t.removeClass("button-loading"),$("#confirm-delete-region-item-modal").modal("hide")},error:function(e){Botble.handleError(e),t.removeClass("button-loading")}})})),$(document).on("click",".btn-confirm-delete-price-item-modal-trigger",(function(e){e.preventDefault();var t=$("#confirm-delete-price-item-modal");t.find(".region-price-item-label").text($(e.currentTarget).data("name")),t.find("#confirm-delete-price-item-button").data("id",$(e.currentTarget).data("id")),t.modal("show")})),$(document).on("click","#confirm-delete-price-item-button",(function(e){e.preventDefault();var t=$(e.currentTarget);t.addClass("button-loading"),$.ajax({type:"DELETE",url:$("div[data-delete-rule-item-url]").data("delete-rule-item-url"),data:{id:t.data("id")},success:function(e){e.error?Botble.showError(e.message):($(".box-table-shipping-item-"+t.data("id")).remove(),0===e.data.count&&$(".wrap-table-shipping-"+e.data.shipping_id).remove(),Botble.showSuccess(e.message)),t.removeClass("button-loading"),$("#confirm-delete-price-item-modal").modal("hide")},error:function(e){Botble.handleError(e),t.removeClass("button-loading")}})}));var e=function(e,t,n,a){$(document).find(".field-has-error").removeClass("field-has-error");var r=e;r.addClass("button-loading");var o=[];$.each(t.serializeArray(),(function(e,t){"from"!==t.name&&"to"!==t.name&&"price"!==t.name||t.value&&(t.value=parseFloat(t.value.replace(",","")).toFixed(2)),o[t.name]=t.value})),a&&(o.shipping_id=a),o=$.extend({},o),$.ajax({type:n,url:t.prop("action"),data:o,success:function(e){e.error?Botble.showError(e.message):(Botble.showSuccess(e.message),a&&e.data&&($(".wrap-table-shipping-"+a+" .pd-all-20.border-bottom").append(e.data),Botble.initResources())),a&&r.closest(".modal").modal("hide"),r.removeClass("button-loading")},error:function(e){Botble.handleError(e),r.removeClass("button-loading")}})};$(document).on("click",".btn-save-rule",(function(t){t.preventDefault(),e($(t.currentTarget),$(t.currentTarget).closest("form"),"PUT",null)})),$(document).on("change",".select-rule-type",(function(e){e.preventDefault();var t=$(e.currentTarget);t.closest(".box-table-shipping").find(".unit-item-price-label").toggleClass("hidden"),t.closest(".box-table-shipping").find(".unit-item-label").text($(e.currentTarget).find("option:selected").data("unit")),t.closest(".box-table-shipping").find(".rule-from-to-label").text($(e.currentTarget).find("option:selected").data("text"))})),$(document).on("keyup",".input-sync-item",(function(e){var t=$(e.currentTarget).val();t&&!isNaN(t)||(t=0),$(e.currentTarget).closest(".input-shipping-sync-wrapper").find($(e.currentTarget).data("target")).text(Botble.numberFormat(parseFloat(t),2))})),$(document).on("keyup",".input-sync-text-item",(function(e){$(e.currentTarget).closest(".input-shipping-sync-wrapper").find($(e.currentTarget).data("target")).text($(e.currentTarget).val())})),$(document).on("keyup",".input-to-value-field",(function(e){$(e.currentTarget).val()?($(".rule-to-value-wrap").removeClass("hidden"),$(".rule-to-value-missing").addClass("hidden")):($(".rule-to-value-wrap").addClass("hidden"),$(".rule-to-value-missing").removeClass("hidden"))})),$(document).on("click",".btn-add-shipping-rule-trigger",(function(e){e.preventDefault(),$("#add-shipping-rule-item-button").data("shipping-id",$(e.currentTarget).data("shipping-id")),$("#add-shipping-rule-item-modal input[name=name]").val(""),$("#add-shipping-rule-item-modal select").val("base_on_price"),$("#add-shipping-rule-item-modal input[name=from]").val("0"),$("#add-shipping-rule-item-modal input[name=to]").val(""),$("#add-shipping-rule-item-modal input[name=price]").val("0"),$("#add-shipping-rule-item-modal").modal("show")})),$(document).find(".select-country-search").select2({width:"100%",dropdownParent:$("#select-country-modal")}),$(document).on("click",".btn-select-country",(function(e){e.preventDefault(),$("#select-country-modal").modal("show")})),$(document).on("click","#add-shipping-region-button",(function(e){e.preventDefault();var t=$(e.currentTarget);t.addClass("button-loading");var n=t.closest(".modal-content").find("form");$.ajax({type:"POST",url:n.prop("action"),data:n.serialize(),success:function(e){e.error?Botble.showError(e.message):(Botble.showSuccess(e.message),$(".wrapper-content").load(window.location.href+" .wrapper-content > *")),t.removeClass("button-loading"),$("#select-country-modal").modal("hide")},error:function(e){Botble.handleError(e),t.removeClass("button-loading")}})})),$(document).on("click","#add-shipping-rule-item-button",(function(t){t.preventDefault(),e($(t.currentTarget),$(t.currentTarget).closest(".modal-content").find("form"),"POST",$(t.currentTarget).data("shipping-id"))})),$(document).on("keyup",".base-price-rule-item",(function(e){var t=$(e.currentTarget).val();t&&!isNaN(t)||(t=0),$.each($(document).find(".support-shipping .rule-adjustment-price-item"),(function(e,n){var a=$(n).closest("tr").find(".shipping-price-district").val();a&&!isNaN(a)||(a=0),$(n).text(Botble.numberFormat(parseFloat(t)+parseFloat(a)),2)}))}))}}])&&e(n.prototype,a),t}();$(document).ready((function(){(new t).init()}))})();
