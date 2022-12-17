@foreach ($paynl_payment_methods as $metohd)
    <li class="list-group-item">
        <input class="magic-radio js_payment_method" type="radio" name="payment_method" id="payment_ideal"
            value="{{ @$method['id'] }}" data-bs-toggle="collapse" data-bs-target=".payment_ideal_wrap"
            data-parent=".list_payment_method">
        <label for="payment_ideal" class="text-start">
            <img src="{{ asset('payment-images-master/' . @$method['brand']['image']) }}" width="100" alt="">
            {{ @$method['brand']['name'] }}</label>
        <div class="payment_ideal_wrap payment_collapse_wrap show" style="padding: 15px 0;" id="ideal-root">
            <p>
                {{ @$method['brand']['public_description'] }}
            </p>
            @isset($method['banks'])
                <select name="method_bank" class="form-control">
                    <option value="">--SELECT BANK--</option>
                    @foreach (@$method['banks'] as $bank)
                    <option value="{{@$bank['id']}}">{{@$bank['visibleName']}}</option>
                    @endforeach
                </select>
            @endisset
        </div>
    </li>
@endif
