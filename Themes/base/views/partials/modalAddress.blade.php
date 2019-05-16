<div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">
                <span class="span-update-address">
                    {{ Theme::lang('lang.user.address') }}
                </span>
            </h4>
        </div>
        {{Form::open(['url'=>'/', 'id'=>'form-save-address'])}}
        @if (Auth::guard('users')->check())
        <input type="hidden" name="user_id" value="{{ Auth::guard('users')->user()->id }}" />
        @endif
        <input type="hidden" id="id_user_extend" name="id" value="{{ isset($address->id) ? $address->id : 0}}" />
        <input type="hidden" name="current_action" value="{{ $action }}" />
        <div class="modal-body">
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{ Theme::lang('lang.user.first_name') }}</label>
                    <input name="first_name" id="first_name" value="{{ isset($address->first_name) ? $address->first_name : ''}}"
                           type="text" class="form-control">
                </div>
                <div class="form-group">
                    <label>{{ Theme::lang('lang.user.last_name') }}</label>
                    <input name="last_name" id="last_name" value="{{ isset($address->last_name) ? $address->last_name : ''}}"
                           type="text" class="form-control">
                </div>
                <div class="form-group">
                    <label>{{ Theme::lang('lang.user.address') }}</label>
                    <input name="address" id="address" value="{{ isset($address->address) ? $address->address : ''}}"
                           type="text" class="form-control">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{ Theme::lang('lang.user.email') }}</label>
                    <input name="email" id="email" value="{{ isset($address->email) ? $address->email : ''}}"
                           type="text" class="form-control">
                </div>
                <div class="form-group">
                    <label>{{ Theme::lang('lang.user.phone') }}</label>
                    <input name="phone" id="phone" value="{{ isset($address->phone) ? $address->phone : ''}}"
                           type="number" class="form-control">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-black btn_save" id="address-btn"
                    attr-form-action="onSaveAddress"
                    attr-form="form-save-address">
                  {{ Theme::lang('lang.general.save') }}
            </button>
            <button type="button" class="btn btn-default" data-dismiss="modal">
                {{ Theme::lang('lang.general.close') }}
            </button>
        </div>
        {{ Form::close() }}
    </div>
</div>
