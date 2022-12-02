
/**
 * @var base_price
 */
var counters_array = [];
$(document).on('click', '.add-price-per-qty', function () {
    // The Counter must be greater than the max exisiting index.
    $('.ppq-table  tr td:first-child input').each(function () {
        counters_array.push($(this).attr('name').charAt(4));
    });
    var counter = counters_array.reduce((a, b) => Math.max(a, b), -Infinity);
    if (counter == NaN || counter == -Infinity) {
        counter = 1;
    }
    var html = addNewRecored(++counter);
    $(this).parent().parent().after(html);
});
$(document).on('click', '.remove-price-per-qty', function () {
    $(this).parent().parent().remove();
});



function addNewRecored(i) {
    return `<tr>
    <td>
        <input type="number" name="ppq[`+ i + `][quantity]" class="form-control" required>
    </td>
    <td>
        <input type="number" name="ppq[`+ i + `][sale_price]" class="form-control" onkeyup="calcSaleRate($(this));" required>
    </td>
    <td>
        <input type="number" name="total" readonly class="form-control" required>
    </td>
    <td>
        <input type="number" name="ppq[`+ i + `][sale_rate]" class="form-control" placeholder="%" readonly required>
    </td>
    <td class="d-flex">
        <button style="font-size: 8px !important;" type="button" class="btn btn-sm btn-primary add-price-per-qty"><i
                class="fa fa-plus"></i></button>
        <button style="font-size: 8px !important;" type="button" class="btn btn-sm btn-danger remove-price-per-qty"><i
                class="fa fa-trash"></i></button>
    </td>
</tr>`;
}


/**
 * Calculate the sale rate
 * @returns double
 */

function calcSaleRate(sale_price_input) {

    var base_price = $('input[name="price"]').val();
    var quantity = sale_price_input.parent().parent().find('td:first-child input').val();
    var sale_price = sale_price_input.val();
    // Set the total
    sale_price_input.parent().parent().find('td:nth-child(3) input').val(quantity * sale_price);
    // set the sale rate
    var sale_rate = 1 - (sale_price / base_price);
    sale_rate = (sale_rate.toFixed(2) * 100).toFixed(2);
    if(sale_rate == NaN || sale_rate == -Infinity)
    {
        sale_rate = 0;
    }
    sale_price_input.parent().parent().find('td:nth-child(4) input').val(parseInt(sale_rate));
}
