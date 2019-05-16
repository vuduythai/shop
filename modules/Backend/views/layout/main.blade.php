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
    $favicon = \Modules\Backend\Core\System::getFavicon();
    @endphp
    <link rel="shortcut icon" href="@imageDisplay($favicon)" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('/vendor/css/mix_dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('/modules/backend/assets/css/main.css') }}">


<!--    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>-->
<!-- css of clockpicker and pikaday in mix_dashboard.css -->
<!--    <script src="{{ asset('/vendor/js/clockpicker.js') }}"></script>-->
<!--    <script src="{{ asset('/vendor/js/pikaday.js') }}"></script>-->

</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    <input type="hidden" id="admin_url" value="{{URL::to('/'.config('app.admin_url'))}}" />
    <input type="hidden" id="result-success" value="{{ \Modules\Backend\Core\System::SUCCESS}}" />
    <input type="hidden" id="result-fail" value="{{ \Modules\Backend\Core\System::FAIL}}" />
    <input type="hidden" id="action-create" value="{{ \Modules\Backend\Core\System::ACTION_CREATE}}" />
    <input type="hidden" id="action-update" value="{{ \Modules\Backend\Core\System::ACTION_UPDATE}}" />
    <input type="hidden" id="folder-image" value="{{ \Modules\Backend\Core\System::FOLDER_IMAGE}}" />
    <input type="hidden" id="wait-me-color" value="{{ \Modules\Backend\Core\System::WAIT_ME_COLOR}}" />
    <input type="hidden" id="wait-me-color" value="{{ \Modules\Backend\Core\System::WAIT_ME_COLOR}}" />


    <div id="token_generate" style="display: none;">{{csrf_token()}}</div>

    @if(Session::has('msg'))
    <div id="msg_display" style="display: none;">{{ Session::get('msg') }}</div>
    @endif

    <?php $adminUrl = config('app.admin_url');?>

    @include('Backend.View::layout.header', ['adminUrl'=>$adminUrl])
    @include('Backend.View::layout.sideBar', ['adminUrl'=>$adminUrl])

    <div class="content-wrapper">
        <section class="content">
        <!-- content -->
        @yield('content')
        </section>
    </div>

    <footer class="main-footer">
        <p class="pull-right">Admin Panel</p>&nbsp;
    </footer>

    <!-- div to fill modal - static -->
    <div id="divFillModal" class="modal fade"></div>

    <div id="msg_js" style="display: none;">{{$share['msg_js']}}</div>

    <script src="{{ asset('/vendor/js/mix.js') }}"></script>
    <script src="{{ asset('tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('/modules/backend/assets/js/backend.js') }}"></script>


<!-- add js by view -->
@yield('scripts')
@yield('scripts_more')

<!-- add js for form field type 'image' -->
<div id="image-js-div">
</div>
@yield('image')

</body>
</html>
