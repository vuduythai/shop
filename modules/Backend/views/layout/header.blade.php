<?php
$cache = \Modules\Backend\Core\AppModel::getCacheStatus();
$notify = \Modules\Backend\Core\System::CACHE_NOT_NEED_DELETE;
if ($cache == \Modules\Backend\Core\System::CACHE_NEED_DELETE) {
    $notify = \Modules\Backend\Core\System::CACHE_NEED_DELETE;
}
?>

<header class="main-header">
    <!-- Logo -->
    <div class="logo">
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg">{{env('APP_NAME', 'Ideas Shop')}}</span>
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
    </div>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">

        <?php
        $loginUser = \Illuminate\Support\Facades\Session::get('admin');
        $email = $loginUser['email'];
        $avatar = $loginUser['avatar'];
        $agencyId = $loginUser['id'];
        ?>
        <div class="navbar-custom-menu">
            @if ($notify == \Modules\Backend\Core\System::CACHE_NEED_DELETE)
            <ul class="nav navbar-nav">
                <li class="dropdown tasks-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-flag-o"></i>
                        <span class="label label-danger">1</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="{{URL::to($adminUrl.'/dashboard/clear-cache')}}">
                                <span class="text-red">
                                    {{ __('Backend.Lang::lang.general.cache_need_delete') }}.
                                    {{ __('Backend.Lang::lang.general.click_here') }}
                                </span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            @endif

            <div class="nav navbar-nav" id="user-navbar">
                <li class="top-nav-menu" id="language-admin-change">
                    <div class="dropdown" id="language-dropdown">
                        <div class="dropdown-toggle" data-toggle="dropdown">
                            {{ __('Backend.Lang::lang.general.language') }}
                            <span class="caret"></span>
                        </div>
                        <ul class="dropdown-menu">
                            <?php  $language = \Modules\Backend\Core\System::getLanguage() ?>
                            @foreach ($language as $lang)
                            <li><a href="{{ URL('/backend-lang/'.$lang->code) }}">{{ $lang->name }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </li>
                <li class="top-nav-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="user-dropdown-toggle">
                        <i class="fa fa-user-circle-o"></i>
                        <p class="login-user-name">{{ $loginUser['name'] }}</p>
                    </a>
                </li>
                <li class="top-nav-menu pull-right" id="user-logout"">
                    <a href="{{ URL::to('/'.config('app.admin_url').'/logout') }}">
                        <i class="fa fa-sign-out" title="{{ __('Backend.Lang::lang.general.logout') }}"></i></a>
                </li>
            </div>
        </div>
    </nav>
</header>