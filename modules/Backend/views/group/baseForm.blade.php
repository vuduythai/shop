@extends('Backend.View::layout.main')
<?php
$controllerNameConvert = ucfirst(str_replace('_', ' ', $controller));
?>
@section('title')
{{ $controllerNameConvert }}
@endsection

@section('content')
<input type="hidden" id="separate-string" value="{{ \Modules\Backend\Core\System::SEPARATE }}" />
<?php $adminUrl = config('app.admin_url');?>

@include('Backend.View::share.breadCrumb')

@if (session('msg'))
<div id="msg_display" style="display: none">{{session('msg')}}</div>
@endif

<div class="row">
    <div class="col-md-12">
        <div class="form-box-shadow">
            {{Form::open(['url'=>route($controller.'.store'), 'class'=>'form_dynamic'])}}
            <div class="form-box-header">
                <div class="row">
                    <div class="col-md-6">
                        <p>
                            {{ __('Backend.Lang::lang.action.'.$action) }}
                            {{ __('Backend.Lang::lang.controller.'.$controller) }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        @include('Backend.View::share.buttonSaveDynamic')
                    </div>
                </div>
            </div>
            <div class="form-box-content">

                <input type="hidden" name="id" value="{{!empty($form['id']) ? $form['id'] : 0}}" />
                <input type="hidden" id="controller-name" value="{{ $controller }}" />

                <div class="row">
                    @if (array_key_exists('template', $form))
                    <!-- if use own template, for example product, category -->
                    @include($form['template'])
                    @else
                    <div class="col-md-6">
                        @foreach ($form as $key => $value)
                        @if (is_array($value))
                        @if (array_key_exists('type', $value))
                        @include('Backend.View::layout.form', $value)
                        @endif
                        @endif
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            {{Form::close()}}
        </div>
    </div>
</div>



<!-- Some view need to include outside form, for example: modal product configurable, ... -->
@if (array_key_exists('include', $form))
@foreach ($form['include'] as $row)
@include($row, ['form'=>$form])
@endforeach
@endif

@stop

