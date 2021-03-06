<div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">{{ __('Backend.Lang::lang.theme.choose_image')}}</h4>

        </div>
        <div class="modal-body">
            <table class="table table-bordered">
                <tbody>
                <tr>
                    <th>
                        <div class="pretty p-default p-smooth p-bigger">
                            <input type="checkbox" id="attach-all-items-checkbox" value="">
                            <div class="state p-primary">
                                <label></label>
                            </div>
                        </div>
                    </th>
                    <th>{{ __('Backend.Lang::lang.field.id')}}</th>
                    <th>{{ __('Backend.Lang::lang.field.name')}}</th>
                    <th>{{ __('Backend.Lang::lang.field.image')}}</th>
                </tr>
                @foreach ($items as $row)
                <tr>
                    <td>
                        <div class="pretty p-default p-smooth p-bigger">
                            <input type="checkbox" class="attach-item-checkbox" value="{{ $row->id }}">
                            <div class="state p-primary">
                                <label></label>
                            </div>
                        </div>
                    </td>
                    <td>{{ $row->id }}</td>
                    <td>{{ $row->name }}</td>
                    <td>
                        @php
                        $folderImage = \Modules\Backend\Core\System::FOLDER_IMAGE;
                        @endphp
                        @if ($row->image != '')
                        <img src="{{ $folderImage.$row->image }}" class="image-mini-modal"/>
                        @else
                        <img src="{{ '/modules/backend/assets/img/no_image.jpg'}}" class="image-mini-modal"/>
                        @endif
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="modal-footer">
            <div class="pull-left">{{ $items->links() }}</div>
            <div class="pull-right modal-add-item-btn">
                <div class="btn btn-primary" id="choose-item-btn">
                    {{ __('Backend.Lang::lang.general.attach')}}
                </div>
                <div class="btn btn-default" data-dismiss="modal">
                    {{ __('Backend.Lang::lang.action.close')}}
                </div>
            </div>
        </div>
    </div>

</div>