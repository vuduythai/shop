<div class="col-md-12">
    <div class="form-box-content">
        <ul class="nav nav-tabs">
            <li class="active nav-item">
                <a href="#general" class="nav-link" data-toggle="tab">
                    {{__('Backend.Lang::lang.general.general')}}
                </a>
            </li>
            <li class="nav-item">
                <a href="#password" class="nav-link" data-toggle="tab">
                    {{__('Backend.Lang::lang.backend_user.password')}}
                </a>
            </li>
            <li class="nav-item">
                <a href="#permission" class="nav-link" data-toggle="tab">
                    {{__('Backend.Lang::lang.general.permission')}}
                </a>
            </li>
        </ul>
        <div class="tab-content">

            <!-- TAB GENERAL -->
            <div class="tab-pane active" id="general">
                <div class="row">
                    <div class="col-md-6">
                        @include('Backend.View::layout.form', $form['name'])
                        @include('Backend.View::layout.form', $form['email'])
                        @include('Backend.View::layout.form', $form['status'])
                        @include('Backend.View::layout.form', $form['role_id'])
                    </div>
                </div>
            </div>

            <!-- TAB PASSWORD -->
            <div class="tab-pane" id="password">
                <div class="row">
                    <div class="col-md-6">
                        @include('Backend.View::layout.form', $form['password'])
                        @include('Backend.View::layout.form', $form['password_confirmation'])
                    </div>
                    @if ($form['id'] != 0)
                    <div class="col-md-6">
                        <h3>{{__('Backend.Lang::lang.backend_user.or_reset_password')}}</h3>
                        <button type="button" id="send-reset-password-email" attr-id="{{ $form['id'] }}"
                                attr-email="{{ $form['emailEdit'] }}" class="btn btn-blue">
                            {{__('Backend.Lang::lang.backend_user.send_reset_password_email')}}
                        </button>
                    </div>
                    @endif
                </div>
            </div>

            <!-- TAB PERMISSION -->
            <div class="tab-pane" id="permission">
                <div class="row">
                    <div class="col-md-12">
                        @foreach ($form['permission'] as $p)
                        <div class="permission-controller-label">
                            <h4>{{ $p['label'] }}</h4>
                        </div>

                        <div class="permission-resource">
                            <button type="button" class="check-all-control btn btn-default pull-left btn-xs"
                                    attr-controller="{{ $p['controller'] }}">
                                {{__('Backend.Lang::lang.general.check_all')}}
                            </button>
                            @foreach ($p['resource'] as $r)
                            @php
                            $checked = '';
                            if (array_key_exists($r['name'], $form['permissionEdit'])) {
                                if ($form['permissionEdit'][$r['name']] == $form['allow']) {
                                    $checked = 'checked="checked"';
                                }
                            }
                            @endphp
                            <div class="pretty p-default">
                                <input type="checkbox" name="{{ $r['name'] }}" class="checkbox-{{ $p['controller'] }}"
                                       value="{{ $form['allow'] }}" {{$checked}}/>
                                <div class="state p-info">
                                    <label>{{ $r['label'] }}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div><!-- /.tab-content -->
    </div>
</div>
