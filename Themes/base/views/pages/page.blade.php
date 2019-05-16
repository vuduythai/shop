@extends('layouts.main')

@section('title')
@if (!empty($page))
{{ $page['name'] }}
@endif
@stop

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12">
            @if (!empty($page))
                <h3>{{ $page['name'] }}</h3>
                {!! $page['body'] !!}
            @endif
        </div>
    </div>
</div>

@stop
