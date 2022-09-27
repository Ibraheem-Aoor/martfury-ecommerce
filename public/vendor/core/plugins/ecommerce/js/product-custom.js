

/**
 * Vednor product create
 * Refund Policy Section
 */
var refund_details_div = $('#refund-details-div');
refund_details_div.hide();
$(document).on('click', '#is_refunded_true', function () {
    refund_details_div.show();
    $('#refund-details').attr('required', 'required');
    $('#refund-details').attr('name', 'refund_details');
});
$(document).on('click', '#is_refunded_false', function () {
    refund_details_div.hide();
    $('#refund-details').removeAttr('required');
    $('#refund-details').removeAttr('name');
});




/**
 * Vednor product create
 * Refund Guarntee Section
 */
var guarntee_details_div = $('#guanrtee-details-div');
guarntee_details_div.hide();
$(document).on('click', '#is_guaranteed_true', function () {
    guarntee_details_div.show();
    $('#guarntee-details').attr('required', 'required');
    $('#guarntee-details').attr('name', 'guarantee');
});
$(document).on('click', '#is_guaranteed_false', function () {
    guarntee_details_div.hide();
    $('#guarntee-details').removeAttr('required');
    $('#guarntee-details').removeAttr('name');

});




/**
 * Vendor Product Edit 
 */
is_guaranteed ? $('#is_guaranteed_true').click() : $('#is_guaranteed_false').click();
is_refunded ? $('#is_refunded_true').click() : $('#is_refunded_false').click();
