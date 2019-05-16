@if (!empty($form['optionChosen']))
@foreach ($form['optionChosen']['value'] as $key => $value)
<div id="div-option-{{ $key }}" class="option-value">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {{Form::select('option_id['.$key.'][option_type]', $form['optionTypeSelect'],
                $value[0]->option_type,
                ['class'=>'select2 form-control', 'id'=>'option-type-'.$key])}}
            </div>
        </div>
        <div class="col-md-5"></div>
        <div class="col-md-1">
            <div class="option-trash option-trash-parent remove-option"
                 attr-option-id="{{ $key }}">
                <i class="fa fa-trash" aria-hidden="true"></i>
            </div>
        </div>
    </div>
    <table class="table table-bordered table-option-value">
        <thead>
        <tr>
            <th class="drag-option"><i class="fa fa-bars" aria-hidden="true"></i></th>
            <th>{{ __('Backend.Lang::lang.field.name') }}</th>
            <th>{{ __('Backend.Lang::lang.field.price') }}</th>
            <th colspan="2">{{ __('Backend.Lang::lang.field.type') }}</th>
        </tr>
        </thead>
        <tbody id="tbody-option-value-{{ $key }}" class="tbody-option-value">
        @php
        $index = 1;
        @endphp
        @foreach ($value as $v)
        <tr class="">
            <td width="5%" class="drag-option">
                <i class="fa fa-bars" aria-hidden="true"></i>
                <input type="hidden" name="product_to_option_id_update[]" value="{{ $v->id }}" />
                <input type="hidden" name="option_id[{{$key}}][value][{{$index}}][id_update]" value="{{ $v->id }}" />
            </td>
            <td width="10%">
                <div class="form-group">
                    @if (array_key_exists($v->option_id, $form['optionChosen']['valueSelect']))
                    {{Form::select('option_id['.$key.'][value]['.$index.'][id]',
                    $form['optionChosen']['valueSelect'][$v->option_id], $v->value_id,
                    ['class'=>'select2 form-control',
                    'id'=>'option-value-type-'.$v->value_id])}}
                    @endif
                </div>
            </td>
            <td width="40%">
                <input type="number" class="form-control" value="{{ $v->value_price }}"
                       name="option_id[{{$key}}][value][{{$index}}][price]">
            </td>
            <td width="40%">
                <div class="form-group">
                    {{Form::select('option_id['.$key.'][value]['.$index.'][type]',
                    $form['optionValueTypeSelect'], $v->value_type,
                    ['class'=>'select2 form-control',
                    'id'=>'option-value-type-'.$v->value_id])}}
                    <input type="hidden" class="option_value_sort" value="{{ $v->sort_order }}"
                           name="option_id[{{ $key }}][value][{{$index}}][sort_order]"/>
                </div>
            </td>
            <td width="5%" class="not-cursor-grab">
                <div class="option-trash remove-option-value"
                     product-to-option={{$v->id}}">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </div>
            </td>
        </tr>
        @php
        $index++;
        @endphp
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td colspan="4"></td>
            <td>
                <div class="option-plus" attr-option-id="{{ $v->option_id }}">
                    <i class="fa fa-plus-square" aria-hidden="true"></i>
                </div>
                <div class="option-value-select-json" style="display: none">
                    @if (array_key_exists($v->option_id, $form['optionChosen']['valueSelect']))
                    {{ json_encode($form['optionChosen']['valueSelect'][$v->option_id]) }}
                    @endif
                </div>
            </td>
        </tr>
        </tfoot>
    </table>
</div>
@endforeach
@endif