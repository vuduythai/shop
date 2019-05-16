<!-- Header Mobile in page wrapper-->
{{Form::open(['url'=>'/search', 'method'=>'get', 'class'=>'form-search', 'id'=>'form-search-mobile'])}}
<div id="header-mobile">
    <div class="wrap_header_mobile">
        <div class="btn-show-menu-mobile hamburger hamburger--squeeze">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
        </div>
    </div>
    <div id="logo-mobile">
        @php
        $logo = '';
        if (isset($share['logo'])) {
            $logo = $share['logo'];
        }
        @endphp
        <img src="@imageDisplay($logo)"/>
    </div>
    <div id="search-mobile">
        <i class="fa fa-search search-top-head-mobile" aria-hidden="true"></i>
    </div>
    <div id="cart-mobile-div">
        <ul id="cart-mobile">
            <li class="drop-down cart-dropdown-li">
                <a href="{{ URL::to('/cart') }}">
                    <div class="cart-top"><span class="cart-count"><p>0</p></span>
                        <i class="fa fa-shopping-cart drop-down-text" aria-hidden="true"></i></div>
                </a>
            </li>
        </ul>
    </div>
</div>
{{ Form::close() }}

