<tr class="div-item div-item-have-big-image" attr-id="{{ $item->id }}" id="div-item-id-{{ $item->id }}">
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
    <td>
        @if ($item->image != '')
        <img src="{{ \Modules\Backend\Core\System::FOLDER_IMAGE.$item->image }}" class="image-mini"/>
        @else
        <img src="{{ '/modules/backend/assets/img/no_image.jpg'}}" class="image-mini"/>
        @endif
    </td>
</tr>