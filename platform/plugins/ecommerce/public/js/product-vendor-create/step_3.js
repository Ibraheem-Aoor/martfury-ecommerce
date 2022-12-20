


/**
 * Handling Form Submission Ajax
 */
$(document).on('submit', '#step_3_form', function (e) {
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
