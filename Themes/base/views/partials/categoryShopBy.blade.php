@inject('frontend', 'Modules\Frontend\Classes\Frontend')

<ul id="shop-by-now-ul">
    @if (!empty($filter))
        @foreach ($filter as $row)
            <li class="remove-shop-by-item" attr-remove="filter"
                attr-id="{{ $row['propertyId'] }}">
                <i class="fa fa-times" aria-hidden="true"></i>
                {{ $row['attributeName'] }} : {{ $row['propertyName'] }}
            </li>
        @endforeach
    @endif

    @if (!empty($reviews))
    <li class="remove-shop-by-item" attr-remove="reviews">
        <i class="fa fa-times" aria-hidden="true"></i>
        {{ Theme::lang('lang.general.rating') }} : {{ $reviews }}
    </li>
    @endif

    @if (!empty($brand))
    <li class="remove-shop-by-item" attr-remove="brand">
        <i class="fa fa-times" aria-hidden="true"></i> {{ Theme::lang('lang.general.brand') }} :
        <img src="{{ IHelpers::imageDisplaySlir('w30-h20', $brand) }}" class="brand-shop-by" />
    </li>
    @endif

    @if (!empty($price_range))
    <li class="remove-shop-by-item" attr-remove="price_range">
        <i class="fa fa-times" aria-hidden="true"></i>
        {{ Theme::lang('lang.general.price_range') }} : {{ $price_range }}
    </li>
    @endif

    @if (!empty($key))
    <li class="remove-shop-by-item" attr-remove="key">
        <i class="fa fa-times" aria-hidden="true"></i>
        {{ Theme::lang('lang.general.key_search') }} : {{ $key}}
    </li>
    @endif

    @if ($hidden_clear_all == $disable)
    <!-- remove all -->
    <li class="remove-shop-by-item">
        <a href="javascript:void(0)" id="clear-all">
            {{ Theme::lang('lang.general.clear_all') }}
        </a>
    </li>
    @endif

</ul>