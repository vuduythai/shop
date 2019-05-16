<input type="hidden" id="controller-name" value="{{ $controller }}" />
<input type="hidden" id="item-factory-model-name" value="option_value" />
<textarea style="display: none" name="item_json_data" id="item-json-data"></textarea>

@extends('Backend.View::layout.one.form')

@section('oneFormField')
<div class="col-md-12">
    @include('Backend.View::layout.form', $form['name'])
    @include('Backend.View::layout.form', $form['type'])
    @include('Backend.View::layout.form', $form['sort_order'])
</div>
@endsection

@section('oneFormTableThead')
    <th>{{ __('Backend.Lang::lang.field.name') }}</th>
    <th>{{ __('Backend.Lang::lang.field.type') }}</th>
    <th>{{ __('Backend.Lang::lang.option.price') }}</th>
@endsection
