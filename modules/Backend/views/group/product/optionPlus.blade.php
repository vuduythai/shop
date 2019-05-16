<tr class="">
    <td width="5%" class="drag-option">
        <i class="fa fa-bars" aria-hidden="true"></i>
        <input type="hidden" name="option_id_plus[{{$optionId}}][option_id][]" value="{{ $optionId }}" />
        <input type="hidden" name="option_id_plus[{{$optionId}}][option_type][]" value="{{ $optionType }}" />
    </td>
    <td width="10%">
        <div class="form-group">
            {{Form::select('option_id_plus['.$optionId.'][id][]',
            $optionValueSelect, $firstKeyValue, ['class'=>'select2 form-control'])}}
        </div>
    </td>
    <td width="40%">
        <input type="number" class="form-control" value="" name="option_id_plus[{{$optionId}}][price][]"/>
    </td>
    <td width="40%">
        <div class="form-group">
            {{Form::select('option_id_plus['.$optionId.'][type][]',
            $optionValueTypeSelect, $firstKeyType, ['class'=>'select2 form-control'])}}
            <input type="hidden" class="option_value_sort" value="0"
                   name="option_id_plus[{{ $optionId }}][sort_order][]"/>
        </div>
    </td>
    <td width="5%" class="not-cursor-grab">
        <div class="option-trash remove-option-value" product-to-option="0">
            <i class="fa fa-trash" aria-hidden="true"></i>
        </div>
    </td>
</tr>