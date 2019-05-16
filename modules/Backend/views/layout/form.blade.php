<!-- TEXT, PASSWORD, NUMBER -->
@if ($type == 'text' || $type == 'password' || $type == 'number' || $type == 'color')
<div class="form-group" id="text-{{ $id }}">
    <label>
        {{$label}}
        @if (isset($is_required))
        <span class="is-required">*</span>
        @endif
    </label>
    <input type="{{$type}}" name="{{$name}}" value="{{$value}}" id="{{$id}}"
           class="{{$class}}" {!! isset($action) ? $action : '' !!} />
    @if (!empty($comment))
        <span class="form-comment">{{ $comment }}</span>
    @endif
</div>
@endif

<!-- TEXTAREA -->
@if ($type == 'textarea')
<div class="form-group" id="textarea-{{ $id }}">
    <label>
        {{$label}}
        @if (isset($is_required))
        <span class="is-required">*</span>
        @endif
    </label>
    <textarea name="{{$name}}" id="{{$id}}"
           class="{{$class}}" {!! isset($action) ? $action : '' !!} >{{$value}}</textarea>
    @if (!empty($comment))
    <span class="form-comment">{{ $comment }}</span>
    @endif
</div>
@endif

<!-- SELECT -->
@if ($type == 'select')
    <div class="form-group" id="select-{{ $id }}">
        <label>
            {{$label}}
            @if (isset($is_required))
            <span class="is-required">*</span>
            @endif
        </label>
        <?php $actionDo = '' ?>
        @if (isset($action))
            <?php $actionDo = $action ?>
        @endif
        {{Form::select($name, $data, $value, ['class'=>$class, 'id'=>$id, $actionDo])}}
        @if (!empty($comment))
        <span class="form-comment">{{ $comment }}</span>
        @endif
    </div>
@endif

<!-- SWITCH -->
<!-- just add css .custom-switch in sytle-admin.css -->
<!-- set value is 1, when check in Form/...Form.php : use isset($data->name) -->
@if ($type == 'switch')
<div class="form-group" id="switch-{{ $id }}">
    <label>
        {{$label}}
        @if (isset($is_required))
        <span class="is-required">*</span>
        @endif
    </label>
    <label class="custom-switch">
        <input type="checkbox" name="{{$name}}" id="{{ $id }}"
               value="1" {{$value == 1 ? 'checked' : ''}}/>
                    <span>
                        <span>{{ __('Backend.Lang::lang.general.on') }}</span>
                        <span>{{ __('Backend.Lang::lang.general.off') }}</span>
                    </span>
        <a class="slide-button"></a>
    </label>
    @if (!empty($comment))
    <span class="form-comment">{{ $comment }}</span>
    @endif
</div>
@endif

<!-- RADIO -->
<!-- https://lokesh-coder.github.io/pretty-checkbox/ -->
@if ($type == 'radio' || $type == 'checkbox')
<div class="form-group form-group-radio" id="radio-checkbox-{{ $id }}">
    <label>
        {{$label}}
        @if (isset($is_required))
        <span class="is-required">*</span>
        @endif
    </label>
    <br/>
    @foreach ($data as $k => $v)
    <div class="pretty p-default p-round">
        <input type="{{ $type }}" name="{{$name}}" value="{{$k}}"
               class="{{ $class }}" {{$value == $k ? 'checked' : ''}} />
        <div class="state p-success-o">
            <label>{{$v}}</label>
        </div>
    </div>
    @endforeach
    @if (!empty($comment))
    <br/>
    <span class="form-comment">{{ $comment }}</span>
    @endif
</div>
@endif

@if ($type == 'image')
<div class="form-group form-group-image-finder" attr-id="{{ $id }}">
    <label>{{ $label }}</label>
    <br/>
    @if (!empty($value))
        <div id="image-{{$id}}" class="image-outer has-image">
            <?php $folderImage = \Modules\Backend\Core\System::FOLDER_IMAGE?>
            <img src="{{URL::to($folderImage.$value)}}">
            <div class="overlay">
                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
            </div>
        </div>
    @else
        <div class="image-outer no-image" id="image-{{$id}}">
            <i class="fa fa-picture-o fa-lg" aria-hidden="true"></i>
        </div>
    @endif
    <input type="hidden" name="{{ $name }}" id="{{ $id }}" value="{{$value}}"/>
</div>

@section('image')
<script>
    $(document).ready(function() {
        $('.form-group-image-finder').each(function() {
            var params = {};
            params.id = $(this).attr('attr-id');
            params._token = $('#token_generate').text();
            var adminUrl = $('#admin_url').val();
            $.ajax({
                method: 'post',
                url: adminUrl+'/image/onLoadJs',
                data : params,
                dataType: 'html',
                success: function(res) {
                    $('#image-js-div').append(res);
                }
            });
        });
    });
</script>
@endsection

@endif