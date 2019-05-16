<tr class="">
    <td width="5%" class="drag-option">
        <i class="fa fa-bars" aria-hidden="true"></i>
    </td>
    <td width="15%">
        <p>{{ $attributeName }}</p>
    </td>
    <td width="75%">
        <div class="form-group">
            <input type="hidden" name="attribute_id[]" value="{{ $attributeId }}" />
            <input type="hidden" class="attribute_sort_order" name="attr_sort_order[]" value="0" />
            @if (!empty($property))
                {{Form::select('property['.$attributeId.'][]', $property, [],
                ['class'=>'attribute-select2 form-control', 'multiple'=>'multiple'])}}
            @else
            <input type="text" name="property[{{$attributeId}}]" class="form-control" />
            @endif
        </div>
    </td>
    <td width="5%">
        <div class="remove-attribute attribute-trash">
            <i class="fa fa-trash" aria-hidden="true"></i>
        </div>
    </td>
</tr>