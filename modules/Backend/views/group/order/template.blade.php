@extends('Backend.View::layout.main')

@section('title')
    Invoice Template
@endsection

@section('content')
@include('Backend.View::share.breadCrumb')

@if (session('msg'))
<div id="msg_display" attr-result="{{ session('result') }}"
     style="display: none">{{session('msg')}}</div>
@endif
<input type="hidden" id="controller-name" value="{{$controller}}/template" />

<div id="content" style="display: none">{{$css}}{{$template}}</div>
<div class="box box-default">
    <div class="box-body">
        {!! Form::open(['route' => 'order.invoice_save_template', 'method' => 'post', 'class'=>'form_dynamic']) !!}
        <div class="form-group col-md-6">
            <div id="template-invoice-header-title">
                <h3 class="pull-left">{{ __('Backend.Lang::lang.order.invoice_template') }}</h3>
                <input type="submit" class="btn btn-primary pull-right"
                       id="invoice-template-submit" attr-controller="{{ $controller }}"
                       value="{{ __('Backend.Lang::lang.general.submit') }}" />
            </div>
            <div id="invoice-template-css">
                <label>{{ __('Backend.Lang::lang.order.css') }}</label>
                <textarea name="invoice_css" id="invoice_css" class="form-control">{!! $css !!}</textarea>
                <div class="hr-10"></div>
                <label>{{ __('Backend.Lang::lang.order.template') }}</label>
                <textarea name="invoice_template" id="invoice_template" class="form-control">{!! $template !!}</textarea>
            </div>
        </div>
        <div id="preview" class='col-md-6'>
            <h3 id="preview-h3">{{ __('Backend.Lang::lang.order.preview') }}</h3>
            <div id="preview-inner"></div>
        </div>
        {!! Form::close() !!}
    </div>
</div>

<script>
    var content = document.getElementById('content').innerText;
    var viewHtml = document.getElementById('preview-inner');
    viewHtml.innerHTML = content ;
</script>

@stop