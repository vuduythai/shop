<tr class="div-item" attr-id="{{ $item->id }}" id="div-item-id-{{ $item->id }}">
    <td class="item-checkbox-not-update">
        <div class="pretty p-default p-smooth p-bigger">
            <input type="checkbox" class="item-checkbox" value="{{ $item->id }}">
            <div class="state p-primary">
                <label></label>
            </div>
        </div>
    </td>
    <td><span>{{ $item->id }}</span></td>
    <td><span>{{ $item->name }}</span></td>
    <td><span>{{ \Modules\Backend\Core\System::displayTypeText($item->type) }}</span></td>
    <td><span>{{ $item->rate }}</span></td>
</tr>