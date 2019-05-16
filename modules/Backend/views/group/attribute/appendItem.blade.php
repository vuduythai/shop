<tr class="div-item" attr-id="{{ $item->id }}" id="div-item-index-{{ $index }}" attr-index="{{ $index }}">
    <td class="item-json-data" style="display:none;">{{ $itemJson }}</td>
    <td class="item-checkbox-not-update">
        <div class="pretty p-default p-smooth p-bigger">
            <input type="checkbox" class="item-checkbox" value="{{ $item->id }}" attr-index="{{ $index }}">
            <div class="state p-primary">
                <label></label>
            </div>
        </div>
    </td>
    <td><span>{{ $item->name }}</span></td>
    <td><span>{{ \Modules\Backend\Models\Attribute::displayTypeText($item->type) }}</span></td>
    <td>
        <span>
             {!! \Modules\Backend\Models\Attribute::displayPropertyValue($item->value, $item->type) !!}
        </span>
    </td>
</tr>