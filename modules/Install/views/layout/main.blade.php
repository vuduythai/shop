<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
        @yield('title')
    </title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    @php
    $favicon = '';
    if (isset($share['config']['favicon'])) {
    $favicon = $share['config']['favicon'];
    }
    @endphp
    <link rel="shortcut icon" href="@imageDisplay($favicon)" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('/vendor/css/mix_dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('/modules/install/assets/css/main.css') }}">

</head>
<body class="hold-transition skin-blue sidebar-mini">

<div id="token_generate" style="display: none;">{{csrf_token()}}</div>
<input type="hidden" id="result-fail" value="{{ \Modules\Backend\Core\System::FAIL }}" />
<div id="msg_js" style="display: none">{{ $share['msg_js'] }}</div>

@if(Session::has('msg'))
<div id="msg_display" style="display: none;">{{ Session::get('msg') }}</div>
@endif

<!-- content -->
<div class="container">
    <div class="row" id="content">
        <div class="col-md-3">
            <ul id="sidebar">
                <li class="{{ Request::is('install') ? 'active' : '' }}">
                    1.{{ __('Install.Lang::lang.general.pre_install') }}
                </li>
                <li class="{{ Request::is('install/configuration') ? 'active' : '' }}">
                    2.{{ __('Install.Lang::lang.general.configuration') }}
                </li>
                <li class="{{ Request::is('install/complete') ? 'active' : '' }}">
                    3.{{ __('Install.Lang::lang.general.complete') }}
                </li>
            </ul>
        </div>
        <div class="col-md-9">
            @yield('content')
        </div>
    </div>
</div>

<script src="{{ asset('/vendor/js/mix.js') }}"></script>
<script src="{{ asset('/modules/install/assets/js/install.js') }}"></script>

<!-- add js by view -->
@yield('scripts')

</body>
</html>
