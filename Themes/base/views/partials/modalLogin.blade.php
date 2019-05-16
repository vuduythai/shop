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
            {{Form::open(['url'=>'/', 'id'=>'form-login-in-checkout'])}}
            <input type="hidden" name="redirect_url" value="/checkout" />
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>{{ Theme::lang('lang.user.email') }}</label>
                        <input name="email" id="email" type="text" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>{{ Theme::lang('lang.user.password') }}</label>
                        <input type="password" name="password" id="password" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-black btn_save"
                        attr-form-action="onLogin"
                        attr-form="form-login-in-checkout">
                    {{ Theme::lang('lang.general.submit') }}
                </button>
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    {{ Theme::lang('lang.general.close') }}
                </button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
