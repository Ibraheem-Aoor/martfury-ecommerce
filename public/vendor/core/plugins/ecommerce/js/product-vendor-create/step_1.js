var parent_id_select = $('#parent_id');
var sub_1_id_select = $('#sub_1_id');
var sub_2_id_select = $('#sub_2_id');
var get_children_route = parent_id_select.attr('data-route');
sub_1_id_select.hide();
sub_2_id_select.hide();

$(document).on('change', '#parent_id', function () {
    $.ajax({
        url: get_children_route,
        type: 'GET',
        data: { id: parent_id_select.val() },
        success: function (response) {
            if (response.status && response.categories.length != 0) {
                var html = ``;
                $.each(response.categories, function (key, item) {
                    html += `<option value="${item.id}">${item.name}</option>`;
                });
                sub_1_id_select.html('');
                sub_1_id_select.append(html);
                sub_1_id_select.show();
                sub_1_id_select.attr('required', 'required');
                sub_1_id_select.attr('name', 'sub_1_id');
            } else {
                sub_1_id_select.html('');
                sub_1_id_select.hide();
                sub_1_id_select.removeAttr('required');
                sub_1_id_select.removeAttr('name');
                sub_2_id_select.html('');
                sub_2_id_select.hide();
                sub_2_id_select.removeAttr('required');
                sub_2_id_select.removeAttr('name');
            }
        }, error: function (response) {

        },
    });
});

$(document).on('change', '#sub_1_id', function () {
    $.ajax({
        url: get_children_route,
        type: 'GET',
        data: { id: sub_1_id_select.val() },
        success: function (response) {
            if (response.status && response.categories.length != 0) {
                var html = ``;
                $.each(response.categories, function (key, item) {
                    html += `<option value="${item.id}">${item.name}</option>`;
                });
                sub_2_id_select.html('');
                sub_2_id_select.append(html);
                sub_2_id_select.show();
                sub_2_id_select.attr('required', 'required');
                sub_2_id_select.attr('name', 'sub_2_id');
            } else {
                sub_2_id_select.html('');
                sub_2_id_select.hide();
                sub_2_id_select.removeAttr('required');
                sub_2_id_select.removeAttr('name');
            }
        }, error: function (response) {

        },
    });
});

/**
 * Handling Form Submission Ajax
 */

$(document).on('submit', '#step_1_form', function (e) {
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
            if (responses.status == 422) {
                $.each(response.responseJSON.errors, function (key, item) {
                    toastr.error(item.message);
                });
            }

        }
    });
});
