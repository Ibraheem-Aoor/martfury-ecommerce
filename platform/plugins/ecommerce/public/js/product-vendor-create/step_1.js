var get_children_route = $('input[name="child_categories_route"]').val();
var categories_count = 2;
function getChildrenCategories(src) {
  src.nextAll('.child-category').remove();
  $.ajax({
    url: get_children_route,
    type: 'GET',
    data: { id: src.val() },
    success: function (response) {
      if (response.status && response.categories.length != 0) {
        var html = `<select name="categories[]" onchange="(getChildrenCategories($(this)));" class="child-category form-control"> <option selected>--SELECT ONE--</option>`;
        $.each(response.categories, function (key, item) {
          html += `<option value="${item.id}">${item.name}</option>`;
        });
        html += `</select>`;
        src.after(html);
      } else {
        // silent
      }
    }, error: function (response) {
      $.each(response.responseJSON.errors, function (key, item) {
        toastr.error(item.message);
      });
    },
  });
}


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
      if (response.status == 422) {
        $.each(response.responseJSON.errors, function (key, item) {
          toastr.error(item.message);
        });
      }

    }
  });
});
