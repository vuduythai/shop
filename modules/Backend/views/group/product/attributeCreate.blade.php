<textarea id="property-chosen" style="display: none">{{ $propertyChosen }}</textarea>
<table class="table table-bordered table-option-value">
    <thead>
    <tr>
        <th class="drag-option"><i class="fa fa-bars" aria-hidden="true"></i></th>
        <th><p>{{__('Backend.Lang::lang.field.name')}}</p></th>
        <th><p>{{__('Backend.Lang::lang.field.value')}}</p></th>
        <th>
            <div class="attribute-trash">
                <i class="fa fa-trash" aria-hidden="true"></i>
            </div>
        </th>
    </tr>
    </thead>
    <tbody id="product-attribute-tbody">
    @if (!empty($attribute))
    @foreach ($attribute as $row)
    <tr class="">
        <td width="5%" class="drag-option">
            <i class="fa fa-bars" aria-hidden="true"></i>
        </td>
        <td width="15%">
            <p>{{ $row['attribute']['name'] }}</p>
            @php
            $attributeId = $row['attribute']['id'];
            @endphp
        </td>
        <td width="75%">
            <div class="form-group">
            <input type="hidden" name="attribute_id[]" value="{{ $attributeId }}" />
            <input type="hidden" class="attribute_sort_order" name="attr_sort_order[]" value="0" />
            @if (!empty($row['property']))
                {{Form::select('property['.$attributeId.'][]', $row['property'], [],
                ['class'=>'attribute-select2 form-control', 'multiple'=>'multiple'])}}
            @else
                @php
                $value = '';
                if (isset($propertyEdit)) {
                    if (array_key_exists($attributeId, $propertyEdit)) {
                        $valueArray = $propertyEdit[$attributeId];
                        $value = $valueArray['value'];
                    }
                }
                @endphp
                <input type="text" name="property[{{$attributeId}}]" value="{{ $value }}" class="form-control" />
            @endif
            </div>
        </td>
        <td width="5%">
            <div class="remove-attribute attribute-trash">
                <i class="fa fa-trash" aria-hidden="true"></i>
            </div>
        </td>
    </tr>
    @endforeach
    @endif
    </tbody>
    <tfoot>
    <tr>
        <td colspan="3"></td>
        <td>
            <div class="attribute-plus">
                <i class="fa fa-plus-square" aria-hidden="true"></i>
            </div>
        </td>
    </tr>
    </tfoot>
</table>