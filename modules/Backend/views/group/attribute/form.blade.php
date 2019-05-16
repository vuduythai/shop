<input type="hidden" id="controller-name" value="{{ $controller }}" />
<input type="hidden" id="item-factory-model-name" value="attribute_property" />
<input type="hidden" id="type-text" value="{{ \Modules\Backend\Models\Attribute::TYPE_TEXT }}" />
<input type="hidden" id="type-color" value="{{ \Modules\Backend\Models\Attribute::TYPE_COLOR }}" />
<textarea style="display: none" name="item_json_data" id="item-json-data"></textarea>

@extends('Backend.View::layout.one.form')

@section('oneFormField')
<div class="col-md-6">
    @include('Backend.View::layout.form', $form['name'])
    <div class="row">
        <div class="col-md-6">
            @include('Backend.View::layout.form', $form['is_filter'])
        </div>
        <div class="col-md-6">
            @include('Backend.View::layout.form', $form['is_display'])
        </div>
    </div>
</div>
<div class="col-md-6">
    @include('Backend.View::layout.form', $form['type'])
    @include('Backend.View::layout.form', $form['attribute_group_id'])
</div>
@endsection

@section('oneFormTableThead')
<th>{{ __('Backend.Lang::lang.field.name') }}</th>
<th>{{ __('Backend.Lang::lang.attribute.value_type') }}</th>
<th>{{ __('Backend.Lang::lang.field.value') }}</th>
@endsection
