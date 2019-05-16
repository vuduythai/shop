<?php
$user = \Illuminate\Support\Facades\Session::get('admin');
$permission = json_decode($user['permission'], true);
$permission = \Modules\Backend\Core\AclResource::convertResourceForMenu($permission);
$menu = [
    ['url'=>'/', 'icon'=>'fa-dashboard', 'text'=>__('Backend.Lang::lang.general.dashboard')],
    ['url'=>'product', 'icon'=>'fa-shopping-bag', 'text'=>__('Backend.Lang::lang.general.product')],
    ['url'=>'category', 'icon'=>'fa-sitemap', 'text'=>__('Backend.Lang::lang.general.category')],
    ['url'=>'order', 'icon'=>'fa-shopping-basket', 'text'=>__('Backend.Lang::lang.general.order')],
    ['url'=>'setting', 'icon'=>'fa-cog', 'text'=>__('Backend.Lang::lang.general.setting')]
]
?>
<aside class="main-sidebar">
    <!-- sidebar -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">

        </div>
        <!-- sidebar menu-->
        <ul class="sidebar-menu" data-widget="tree">
            @foreach ($menu as $row)
                @if (!empty($permission))
                    @if (array_key_exists($row['url'], $permission))
                        @if ($permission[$row['url']] == \Modules\Backend\Core\System::ALLOW)
                            <li class="">
                                <a href="{{URL::to($adminUrl.'/'.$row['url'])}}"><i class="fa {{$row['icon']}}"></i>
                                    <span>{{$row['text']}}</span></a>
                            </li>
                        @endif
                    @else
                        <li class="">
                            <a href="{{URL::to($adminUrl.'/'.$row['url'])}}"><i class="fa {{$row['icon']}}"></i>
                                <span>{{$row['text']}}</span></a>
                        </li>
                    @endif
                @else
                <li class="">
                    <a href="{{URL::to($adminUrl.'/'.$row['url'])}}"><i class="fa {{$row['icon']}}"></i>
                        <span>{{$row['text']}}</span></a>
                </li>
                @endif
            @endforeach
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>