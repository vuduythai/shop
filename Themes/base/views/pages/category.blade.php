@extends('layouts.main')

@section('title')
{{ $categoryName }}
@stop

@section('content')

<input type="hidden" id="category_id" value="{{$category_id}}" />
<input type="hidden" id="is_display_price_slider" value="{{ $config['display_price_slider']  }}" />
<div id="filter_data_json" style="display: none;">{{ json_encode($filter) }}</div>
<div id="const_json" style="display: none">{{ json_encode($const) }}</div>

<div class="container">
    <div class="row" id="category-div">
        @include('partials.categoryDiv')
    </div>
</div>

@stop

@section('scripts')
<script src="{{ themes('js/category.js') }}"></script>
@stop