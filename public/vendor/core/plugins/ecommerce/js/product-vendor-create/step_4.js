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
 * Handling Form Submission Ajax
 */
$(document).on('submit', '#step_4_form', function (e) {
    e.preventDefault();
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: $(this).serialize(),
        success: function (response) {
            if (response.status) {
                location.href = response.route;
            }
        }, error: function (response) {
            if (response.status == 422) {
                $.each(response.responseJSON.errors, function (key, item) {
                    toastr.error(item);
                });
            }

        }
    });
});
