@extends('Install.View::layout.main')

@section('content')

<h3>2.{{ __('Install.Lang::lang.general.configuration') }}</h3>
{{Form::open(['url'=>'/', 'class'=>'form_dynamic'])}}
<p>{{ trans('Install.Lang::lang.msg.enter_db') }}</p>
<table class="table-configuration">
    <tbody>
        <tr>
            <td>{{ trans('Install.Lang::lang.db.host') }} <span class="required">*</span></td>
            <td><input type="text" name="db_host" class="form-control" value="127.0.0.1"/></td>
        </tr>
        <tr>
            <td>{{ trans('Install.Lang::lang.db.port') }} <span class="required">*</span></td>
            <td><input type="text" name="db_port" class="form-control" value="3306"/></td>
        </tr>
        <tr>
            <td>{{ trans('Install.Lang::lang.db.username') }} <span class="required">*</span></td>
            <td><input type="text" name="db_username" class="form-control"/></td>
        </tr>
        <tr>
            <td>{{ trans('Install.Lang::lang.db.password') }} <span class="required">*</span></td>
            <td><input type="password" name="db_password" class="form-control"/></td>
        </tr>
        <tr>
            <td>{{ trans('Install.Lang::lang.db.db_name') }} <span class="required">*</span></td>
            <td><input type="text" name="db_database" class="form-control"/></td>
        </tr>
    </tbody>
</table>

<p>{{ trans('Install.Lang::lang.msg.enter_admin') }}</p>
<table class="table-configuration">
    <tbody>
    <tr>
        <td>{{ trans('Install.Lang::lang.admin.name') }} <span class="required">*</span></td>
        <td><input type="text" name="name" class="form-control"/></td>
    </tr>
    <tr>
        <td>{{ trans('Install.Lang::lang.admin.email') }} <span class="required">*</span></td>
        <td><input type="text" name="email" class="form-control"/></td>
    </tr>
    <tr>
        <td>{{ trans('Install.Lang::lang.admin.password') }} <span class="required">*</span></td>
        <td><input type="password" name="password" class="form-control"/></td>
    </tr>
    <tr>
        <td>{{ trans('Install.Lang::lang.admin.password_confirmation') }} <span class="required">*</span></td>
        <td><input type="password" name="password_confirmation" class="form-control"/></td>
    </tr>
    </tbody>
</table>

<p>{{ trans('Install.Lang::lang.msg.enter_other_info') }}</p>
<table class="table-configuration">
    <tbody>
        <tr>
            <td>{{ trans('Install.Lang::lang.general.admin_url') }} <span class="required">*</span></td>
            <td><input type="text" name="admin_url" class="form-control" value="admin"/></td>
        </tr>
        <tr>
            <td>{{ trans('Install.Lang::lang.general.install_db_data_sample') }}<span class="required">*</span></td>
            <td>
                <div class="pretty p-default p-round">
                    <input type="radio" name="install_db_data_sample"
                           value="{{ \Modules\Install\Facades\Configuration::SAVE_SAMPLE_DATA }}" checked="checked"/>
                    <div class="state p-success-o">
                        <label>{{ trans('Install.Lang::lang.general.yes') }}</label>
                    </div>
                </div>
                <div class="pretty p-default p-round">
                    <input type="radio" name="install_db_data_sample"
                        value="{{  \Modules\Install\Facades\Configuration::NOT_SAVE_SAMPLE_DATA }}"/>
                    <div class="state p-success-o">
                        <label>{{ trans('Install.Lang::lang.general.no') }}</label>
                    </div>
                </div>
            </td>
        </tr>
    </tbody>
</table>
<button type="submit" class="btn btn-primary" id="save-configuration">
    {{ trans('Install.Lang::lang.general.install') }}
</button>
{{ Form::close() }}
@stop
