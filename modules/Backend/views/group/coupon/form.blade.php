<input type="hidden" id="coupon_length_random" value="{{$form['coupon_length_random']}}" />
<input type="hidden" id="coupon_prefix" value="{{$form['coupon_prefix']}}" />
<div class="col-md-6">
    <div class="form-group" id="form-field-code">
        <div>
            <label>{{$form['code']['name']}}</label>
        </div>
        <input type="text" name="code" id="code-coupon-admin" class="form-control"
               value="{{$form['code']['value'] ? $form['code']['value'] : ''}}" />
        <i class="fa fa-refresh" id="refresh-coupon"></i>
    </div>
    @include('Backend.View::layout.form', $form['discount'])
    @include('Backend.View::layout.form', $form['start_date'])
    @include('Backend.View::layout.form', $form['num_uses'])
    <div class="form-group">
        <input type="hidden" id="category_update" value="{{ $form['categoryEditStr'] }}" />
        <label>{{ __('Backend.Lang::lang.coupon.category_search') }}</label>
        <!-- select multiple -->
        <select id="category-search" name="category[]" class="form-control select2" multiple>
            @if (!empty($form['categoryEdit']))
                @foreach ($form['categoryEdit'] as $row)
                    <option value="{{$row['id']}}">{{$row['name']}}</option>
                @endforeach
            @endif
        </select>
        <span class="form-comment">{{ __('Backend.Lang::lang.comment.apply_for_category') }}</span>
    </div>
    @include('Backend.View::layout.form', $form['status'])
</div>
<div class="col-md-6">
    @include('Backend.View::layout.form', $form['type'])
    @include('Backend.View::layout.form', $form['total'])
    @include('Backend.View::layout.form', $form['end_date'])
    @include('Backend.View::layout.form', $form['num_per_customer'])
    <div class="form-group">
        <input type="hidden" id="product_update" value="{{ $form['productEditStr'] }}" />
        <label>{{ __('Backend.Lang::lang.coupon.product_search') }}</label>
        <!-- select multiple -->
        <select id="product-search" name="product[]" class="form-control select2" multiple>
            @if (!empty($form['productEdit']))
                @foreach ($form['productEdit'] as $row)
                <option value="{{$row['id']}}">{{$row['name']}}</option>
                @endforeach
            @endif
        </select>
        <span class="form-comment">{{ __('Backend.Lang::lang.comment.apply_for_product') }}</span>
    </div>
    @include('Backend.View::layout.form', $form['logged'])
</div>

@section('scripts')
    <script src="{{ asset('/vendor/js/moment.js') }}"></script>
    <script src="{{ asset('/vendor/js/pikaday.js') }}"></script>
@stop
