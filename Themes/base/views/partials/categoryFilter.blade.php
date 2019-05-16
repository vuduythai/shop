<div class="row row-cat-h1">
    <h1> {{ Theme::lang('lang.general.shop_by') }}</h1>
    <div class="" id="now-shopping-by">
    </div>
</div>

@if ($config['display_search'] == $const['enable'])
    <div class="filter-div">
        <div class="filter-header accordion">
            {{ Theme::lang('lang.general.search') }}
        </div>
        <div class="filter-body accordion-body">
            <input type="text" name="search_product" id="search_product" class="form-control" value="">
            <i class="fa fa-search" id="category-fa-search" aria-hidden="true"></i>
        </div>
    </div>
@endif

@if ($config['display_price_slider'] == $const['enable'])
    <div class="filter-div">
        <div class="filter-header accordion">
            {{ Theme::lang('lang.general.price') }}
        </div>
        <div class="filter-body accordion-body">
            <div class="range-slider">
                <input type="text" id="js-range-slider" value="" data-min="{{ $minMaxPrice['min'] }}"
                       data-max="{{ $minMaxPrice['max'] }}" data-from="{{ $priceRange[0] }}" data-to="{{ $priceRange[1] }}"/>
            </div>
        </div>
    </div>
@endif

<!-- BRAND -->
@if ($config['display_brand'] == $const['enable'])
    @if (!empty($brand))
        <div class="filter-div">
            <div class="filter-header accordion">
                {{ Theme::lang('lang.general.brand') }}
            </div>
            <div class="filter-body accordion-body">
                @foreach ($brand as $row)
                <img src="{{ IHelpers::imageDisplaySlir('w30-h20', $row['image']) }}" class="brand-image" attr-brand-id="{{ $row['id'] }}" attr-image="{{ $row['image'] }}"/>
                @endforeach
            </div>
        </div>
    @endif
@endif
<!-- END BRAND -->

<!-- FILTER -->
@foreach ($filter as $f)
<div class="filter-div">
    <div class="filter-header accordion">
        {{$f['attribute']['name']}}
    </div>
    <div class="filter-body accordion-body">
        <ul>
            @foreach ($f['property'] as $p)
                @if ($p['product_count'] != 0)
                    @if ($p['property_type'] == $const['property_type_color'])
                        <div class="property_color"
                            style="background-color: {{$p['property_value']}}" property-id="{{$p['property_id']}}"></div>
                    @else
                        <li class="property-filter" property-id="{{$p['property_id']}}">
                            <input type="checkbox" id="property-filter-checkbox-{{$p['property_id']}}"
                                   class="property-filter-checkbox" value="{{$p['property_id']}}"
                                    {{ in_array($p['property_id'], $filterChecked) ? 'checked' : ''}}/>
                            <a href="javascript:void(0);">{{$p['property_name']}}</a>
                            <span class="filter-counter">{{$p['product_count']}}</span>
                        </li>
                    @endif
                @endif
            @endforeach
        </ul>
    </div>
</div>
@endforeach
<!-- END FILTER -->

<!-- RATING -->
@if ($config['display_rating'] == $const['enable'])
    @if (!empty($reviews))
        <div class="filter-div">
            <div class="filter-header accordion">
                {{ Theme::lang('lang.general.rating') }}
            </div>
            <div class="filter-body accordion-body">
                <ul>
                    @foreach ($reviews as $re)
                    <li class="review-point" attr-point="{{$re['point']}}">
                        {!! IHelpers::displayReviewStar($re['point']) !!} ({{$re['count']}})
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
@endif
<!-- END RATING -->
