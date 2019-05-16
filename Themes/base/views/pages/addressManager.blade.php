@extends('layouts.main')

@section('title')
{{ Theme::lang('lang.user.address_manager') }}
@stop

@section('content')
<div class="container">
    <div class="row" id="user-address-row">
        @php
        $i = 1
        @endphp
        @foreach ($address as $row)
            <div class="col-md-4 user-address-div">
                <div class="user-extend-data">
                    <h3>Address {{$i}}
                        &nbsp;
                        <i class="fa fa-pencil-square-o edit-user-address" attr-id="{{ $row['id'] }}"></i>
                        &nbsp;
                        <i class="fa fa-trash-o delete-user-address" attr-id="{{ $row['id'] }}"></i>
                    </h3>
                    <div class="user-first-name">{{ Theme::lang('lang.user.first_name') }} : {{ $row['first_name'] }}</div>
                    <div class="user-last-name">{{ Theme::lang('lang.user.last_name') }} : {{ $row['last_name'] }}</div>
                    <div class="user-address"> {{ Theme::lang('lang.user.address') }} : {{ $row['address'] }}</div>
                    <div class="user-email">{{ Theme::lang('lang.user.email') }} : {{ $row['email'] }}</div>
                    <div class="user-phone">{{ Theme::lang('lang.user.phone') }} : {{ $row['phone'] }}</div>
                </div>
            </div>
            @php
            $i = $i + 1
            @endphp
        @endforeach
    </div>
    <div class="row">
        <div class="col-md-12">
            <button type="button" class="btn btn-black modal-add-user-address">
                {{ Theme::lang('lang.user.add_address') }}</button>
        </div>
    </div>
</div>
@stop
