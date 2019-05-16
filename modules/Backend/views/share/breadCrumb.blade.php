<?php $adminUrl = config('app.admin_url');?>
<!--  BREADCRUMB -->
<div class="row">
    <div class="col-md-12">
        <div class="box-breadcrumb-wrapper">
            <ul class="box-breadcrumb">
                <li>
                    <a href="{{ URL::to($adminUrl) }}">
                        <i class="fa fa-home breadcrumb-home" aria-hidden="true"></i>
                    </a>
                </li>
                <?php $arrayNotInSetting = ['product', 'category', 'order', 'setting']?>
                @if (!in_array($controller, $arrayNotInSetting))
                <li class="breadcrumb-subtract"></li>
                <li>
                    <a href="{{URL::to('/'.$adminUrl.'/setting')}}">{{__('Backend.Lang::lang.general.settings')}}</a>
                </li>
                @endif
                @if (empty($action))
                <li class="breadcrumb-subtract"></li>
                <li><a href="javascript:void(0)"> {{ __('Backend.Lang::lang.controller.'.$controller) }}</a></li>
                @else
                <li class="breadcrumb-subtract"></li>
                <li>
                    <a href="{{URL::to('/'.$adminUrl.'/'.$controller)}}">
                        {{ __('Backend.Lang::lang.controller.'.$controller) }}
                    </a>
                </li>
                <li class="breadcrumb-subtract"></li>
                <li><a href="javascript:void(0)">
                        {{ __('Backend.Lang::lang.action.'.$action) }}
                </a></li>
                @endif
            </ul>
        </div>
    </div>
</div>
<!-- END BREADCRUMB -->