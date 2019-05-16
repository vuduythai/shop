@extends('Install.View::layout.main')

@section('content')

<h3>1.{{ __('Install.Lang::lang.general.pre_install') }}</h3>
<p>{{ trans('Install.Lang::lang.msg.make_sure_php_ext') }}</p>
@foreach($extensionCheck['requirements'] as $type => $requirement)
<table class="table table-bordered table-requirement">
    <thead>
        <tr>
            <th>{{ trans('Install.Lang::lang.general.extensions') }}</th>
            <th>{{ trans('Install.Lang::lang.general.status') }}</th>
        </tr>
    </thead>
    <tbody>
    @if($type == 'php')
    <tr>
        <td>{{ ucfirst($type) }} >= {{ $checkPhpVersion['minimum'] }}</td>
        <td><i class="fa fa-{{ $checkPhpVersion['supported'] ? 'check-circle-o' : 'exclamation-circle' }}"></i></td>
    </tr>
    @endif

    @foreach($extensionCheck['requirements'][$type] as $extention => $enabled)
    <tr>
        <td>{{ $extention }}</td>
        <td>
            <i class="fa fa-{{ $enabled ? 'check-circle-o' : 'exclamation-circle' }}"></i>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
@endforeach

<p>{{ trans('Install.Lang::lang.msg.make_sure_set_permission') }}</p>
<table class="table table-bordered table-requirement">
    <thead>
    <tr>
        <th>{{ trans('Install.Lang::lang.general.directory') }}</th>
        <th>{{ trans('Install.Lang::lang.general.permission') }}</th>
        <th>{{ trans('Install.Lang::lang.general.status') }}</th>
    </tr>
    </thead>
    <tbody>
        @foreach($permissionCheck['permissions'] as $permission)
        <tr>
            <td>{{ $permission['folder'] }}</td>
            <td>{{ $permission['permission'] }}</td>
            <td>
                <i class="fa fa-{{ $permission['isSet'] ? 'check-circle-o' : 'exclamation-circle' }}"></i>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@if ( ! isset($permissionCheck['errors']) && !isset($extensionCheck['errors']) )
<div class="buttons">
    <a href="{{ route('install.configuration') }}" class="btn btn-primary">
        {{ trans('Install.Lang::lang.general.continue') }}
    </a>
</div>
@endif

@stop
