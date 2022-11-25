
/**
 * @var base_price
 */
var base_price = $('input[name="price"]').val();
console.log(base_price);

$(document).on('click', '.add-price-per-qty', function () {
    var html = addNewRecored();
    $(this).parent().parent().after(html);
});
$(document).on('click', '.remove-price-per-qty', function () {
    var html = addNewRecored();
    $(this).parent().parent().remove();
});




function addNewRecored() {
    return `<tr>
    <td>
        <input type="number" name="ppq['sale_price'][]" class="form-control">
    </td>
    <td>
        <input type="number" name="ppq['sale_quantity'][]" class="form-control">
    </td>
    <td>
        <input type="number" name="ppq['sale_rate'][]" class="form-control">
    </td>
    <td>
        <button type="button" class="btn btn-sm btn-primary add-price-per-qty"><i
                class="fa fa-plus"></i></button>
        <button type="button" class="btn btn-sm btn-danger remove-price-per-qty"><i
                class="fa fa-trash"></i></button>
    </td>
</tr>`;
}
