
<div class="row row-cat-h1">
    <h1>{{ $categoryName }}</h1>
</div>
<div class="row row-sort">
    <div class="col-md-4">
        <select class="form-control" name="sort_by" id="product-sort-by">
            <option value="price-asc" {{$sortByString == 'price-asc' ? 'selected' : ''}}>{{ Theme::lang('lang.general.price_low_high') }} </option>
            <option value="price-desc" {{$sortByString == 'price-desc' ? 'selected' : ''}}>{{ Theme::lang('lang.general.price_high_low') }} </option>
        </select>
    </div>
    <div class="col-md-2"></div>
    <div class="col-md-6">
        <p class="pull-right show-number-result">Total {{$count}} results</p>
    </div>
</div>
<div class="hr-20"></div>
<div class="row row-product-list">
    @foreach ($products as $product)
    <div class="col-md-4 col-xs-6 product-col-cat">
        <div class="product-col-inside-2">
            <!-- product label -->
            {!! IHelpers::displayLabel($product['product_label'], $allLabel) !!}
            <!-- end product label -->
            <div class="product-col-type-2">
                <a href="{{ IHelpers::genSlug($product['slug']) }}">
                    <img src="{{ IHelpers::imageDisplaySlir('w230-h230', $product['image']) }}"
                         alt="Avatar" class="product-image-cat img-responsive">
                    <div class="overlay">
                    </div>
                </a>
                <div class="overlay-btn-cart {{$product['action_class']}}"
                     attr-name="{{$product['name']}}"
                     attr-qty="{{$product['qty']}}"
                     attr-qty-order="{{$product['qty_order']}}"
                     attr-image="{{$product['image']}}"
                     attr-price="{{$product['final_price']}}"
                     attr-origin-price="{{$product['final_price']}}"
                     attr-product-id="{{$product['id']}}"
                     attr-variant-id="0"
                     attr-slug="{{$product['slug']}}"
                     attr-weight="{{$product['weight']}}"
                     attr-weight-id="{{$product['weight_id']}}">
                    {{ $product['action_text'] }}
                </div>
            </div>
            <div class="product-col-text-2 text-center">
                <div class="product-text-name">
                    <a href="{{ IHelpers::genSlug($product['slug']) }}">
                    {{ ucfirst($product['name']) }}
                    </a>
                </div>
                <div class="product-text-price">
                    @if ($product['display_price_promotion'] == $const['yes'])
                        <span class="price-previous">@displayPriceAndCurrency($product['price'])</span>
                        @displayPriceAndCurrency($product['final_price'])
                    @else
                        @displayPriceAndCurrency($product['final_price'])
                    @endif
                </div>
                <div class="add-to-cart-mobile">
                    <div class="add-to-cart-btn {{$product['action_class']}}"
                         attr-name="{{$product['name']}}"
                         attr-qty="{{$product['qty']}}"
                         attr-qty-order="{{$product['qty_order']}}"
                         attr-image="{{$product['image']}}"
                         attr-price="{{$product['final_price']}}"
                         attr-origin-price="{{$product['final_price']}}"
                         attr-product-id="{{$product['id']}}"
                         attr-variant-id="0"
                         attr-slug="{{$product['slug']}}"
                         attr-weight="{{$product['weight']}}"
                         attr-weight-id="{{$product['weight_id']}}">
                         {{ $product['action_text'] }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="pagination">
    @if ($pages['currentPage'] != 1)
    <a href="javascript:void(0)" class="cat-pag" attr-page="1"> << </a>
    <a href="javascript:void(0)" class="cat-pag" attr-page="{{$pages['currentPage'] - 1}}"> < </a>
    @endif

    @if ($pages['totalPages'] != 1)
    @foreach ($pages['pages'] as $page)
    <a href="javascript:void(0)"
       class="{{ $pages['currentPage'] == $page ? 'active' : ''}} cat-pag"
       attr-page="{{$page}}">{{$page}}
    </a>
    @endforeach
    @endif

    @if ($pages['currentPage'] != $pages['totalPages'])
    <a href="javascript:void(0)" class="cat-pag" attr-page="{{ $pages['currentPage'] + 1 }}"> >> </a>
    <a href="javascript:void(0)" class="cat-pag" attr-page="{{ $pages['totalPages'] }}"> > </a>
    @endif
</div>
