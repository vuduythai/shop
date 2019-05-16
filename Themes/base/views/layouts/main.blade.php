<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>
        @yield('title')
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
    $favicon = '';
    if (isset($share['favicon'])) {
        $favicon = $share['favicon'];
    }
    @endphp
    <link rel="shortcut icon" href="@imageDisplay($favicon)" type="image/x-icon">
    <meta name="title" content="{{ isset($seo['seo_title']) ? $seo['seo_title'] : '' }}">
    <meta name="keywords" content="{{ isset($seo['seo_keyword']) ? $seo['seo_keyword'] : '' }}">
    <meta name="description" content="{{ isset($seo['seo_description']) ? $seo['seo_description'] : '' }}">
    <link rel="stylesheet" href="{{ themes('css/mix.css') }}">
    <link rel="stylesheet" href="{{ themes('style.css') }}">
</head>

<body>
<input type="hidden" id="token_generate" value="{{ csrf_token() }}" />
<div id="msg_js" style="display: none">{{ $share['msg_js'] }}</div>
<input type="hidden" id="fail-val" value="{{ \Modules\Backend\Core\System::FAIL }}" />

<div class="wrap_header" id="wrap-header-top">
    <div id="top-header">
        <div class="container">
            <div class="row" id="top-header-row">
                <div class="col-md-4">
                    <li class="language">
                        <a href="#" class="">
                            Language
                            <i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="language_selection">
                            @foreach ($share['language'] as $lang)
                            <li><a href="{{ URL('/frontend-lang/'.$lang->code) }}">{{ $lang->name }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                </div>
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <ul id="header-ul" class="drop-down">
                        <li>
                            <div class="drop-down-text">
                                <p class="pull-right">{{ Theme::lang('lang.user.my_account') }}</p>
                            </div>
                            <ul class="drop-down-content">
                                @if (Session::get('user'))
                                    <li><a href="/user/order-manager">{{ Theme::lang('lang.user.order_manager') }}</a></li>
                                    <li><a href="/user/address-manager">{{ Theme::lang('lang.user.address_manager') }}</a></li>
                                    <li><a href="/user/change-password">{{ Theme::lang('lang.user.change_password') }}</a></li>
                                    <li><a href="/logout">{{ Theme::lang('lang.general.logout') }}</a></li>
                                @else
                                    <li><a href="/register">{{ Theme::lang('lang.general.register') }}</a></li>
                                    <li><a href="/login">{{ Theme::lang('lang.general.login') }}</a></li>
                                @endif

                            </ul>
                        </li>

                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div id="navbar">
        <div class="container">
            <div class="row" id="myNavbar">
                <div class="col-md-3">
                {!! isset($share['block']['mega-menu']) ? $share['block']['mega-menu'] : '' !!}
                </div>
                <div class="col-md-3" id="logo">
                    <a href="{{ URL::to('/') }}">
                        @php
                        $logo = '';
                        if (isset($share['logo'])) {
                            $logo = $share['logo'];
                        }
                        @endphp
                        <img src="@imageDisplay($logo)" />
                    </a>
                </div>
                <div class="col-md-5">
                    <div id="search-container">
                        {{Form::open(['url'=>'/search', 'method'=>'get', 'class'=>'form-search', 'id'=>'form-search'])}}
                            <input type="text" class="form-control" id="search-input"
                                   placeholder="Search.." name="key">
                            <button type="submit" id="submit-search" attr-action="onSearchValidate"
                                    attr-form-id="form-search">
                                <i class="fa fa-search"></i>
                            </button>
                        {{ Form::close() }}
                    </div>
                </div>
                <div class="col-md-1 cart-div">
                    <ul class="cart-not-collapse">
                        <li class="drop-down cart-dropdown-li">
                            <div class="cart-top"><span class="cart-count"><p>0</p></span>
                                <i class="fa fa-shopping-cart drop-down-text" aria-hidden="true"></i></div>
                            <div class="drop-down-content" id="cart-dropdown">

                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Menu Mobile -->



<div id="wrapper">
    <!-- End Menu Mobile -->
    {!! isset($share['block']['mobile-sidebar-menu']) ? $share['block']['mobile-sidebar-menu'] : '' !!}
    <div id="page-wrapper">

        @include('layouts.mobileHeader')

        @php
        $currentUrl = url()->current();
        $baseUrl = URL::to('/');
        @endphp

        @if ($currentUrl != $baseUrl)
        <div class="hr-20"></div>
        <div class="breadcrumbs">
            <div class="container">
                <div class="row row-breadcrumbs">
                    <ul>
                        <li><a href="/">{{ Theme::lang('lang.general.home') }}</a></li>
                        @if (Route::current()->getName() == 'slug')
                        {!! IHelpers::generateBreadCrumbsForProduct($breadcrumbArray) !!}
                        @else
                        {!! IHelpers::generateBreadCrumbs($breadcrumbName) !!}
                        @endif

                    </ul>
                </div>
            </div>
        </div>
        @endif

        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <!-- flash message notify -->
                    @if (Session::get('notify_flash_msg'))
                    <div id="notify_flash_msg" style="display: none;">{{ Session::get('notify_flash_msg') }}</div>
                    @endif
                    <!-- flash message div -->
                    @include('layouts.flashMessage')
                </div>
            </div>
        </div>
        @yield('content')

    </div> <!-- end #page-wrapper -->

    <div class="hr-40"></div>
    <!-- footer -->
    {!! isset($share['block']['footer']) ?  $share['block']['footer'] : '' !!}
    <!-- footer -->

</div> <!-- end #wrapper -->

@include('partials.modalConfirmCart')
<div id="divFillModal" class="modal fade"></div>
<script src="{{ themes('js/mix.js') }}"></script>
<script src="{{ themes('template.js') }}"></script>


@yield('scripts')

</body>
</html>
