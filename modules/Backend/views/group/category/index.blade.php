@extends('Backend.View::layout.main')

@section('title')
{{ ucfirst($controller) }}
@endsection

@section('content')
<input type="hidden" id="separate-string" value="{{ \Modules\Backend\Core\System::SEPARATE}}" />
<input type="hidden" id="controller-name" value="{{ $controller }}" />
<input type="hidden" id="parent-id" value="{{ $parentId }}" />
<?php $adminUrl = config('app.admin_url');?>
@include('Backend.View::share.breadCrumb')
{{Form::open(['url'=>route($controller.'.store'), 'class'=>'form_dynamic'])}}
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        <a href="{{ URL::to('/'.$adminUrl.'/'.$controller) }}"
                           class="btn btn-blue" id="create-root-category" attr-parent-id="0">
                            {{ __('Backend.Lang::lang.category.create_root_category') }}
                        </a>
                        <a href="javascript:void(0)" class="btn btn-default" id="create-sub-category"
                           attr-parent-id="">
                            {{ __('Backend.Lang::lang.category.create_sub_category') }}
                        </a>
                    </div>
                    <div class="col-md-6">
                        <div class="button-base-form pull-right">
                            <button type="submit" class="btn btn-primary btn_save_and_close"
                                attr-controller="{{ $controller }}" id="save-{{ $controller }}">
                                {{ __('Backend.Lang::lang.action.save') }}
                            </button>
                            <button class="btn btn-default btn-delete-category" id="button-delete" attr-id="0">
                                {{ __('Backend.Lang::lang.action.delete') }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="hr-10"></div>
                <div class="row">
                    <div class="col-md-3">
                        <div id="toggle-category-tree">
                            <a href="#" class="tree-toggle" data-action="collapsed">
                                {{ __('Backend.Lang::lang.category.collapsed_all') }}
                            </a> |
                            <a href="#" class="tree-toggle" data-action="expand">
                                {{ __('Backend.Lang::lang.category.expand_all') }}
                            </a>
                        </div>

                        <?php
                        $tree = Modules\Backend\Models\Category::categoryToJson($category);
                        $treeJson = json_encode($tree);
                        $parentIdArray = Modules\Backend\Models\Category::getParentIdArray($category);
                        $parentIdString = implode(\Modules\Backend\Core\System::SEPARATE, $parentIdArray);
                        $isRoot = \Modules\Backend\Models\Category::IS_ROOT;
                        if ($parentId != 0) {
                            $isRoot = \Modules\Backend\Models\Category::IS_SUB;
                        }
                        ?>
                        <div id="data-tree" style="display: none">{{ $treeJson }}</div>
                        <div id="parent-id-string" style="display: none">{{ $parentIdString }}</div>
                        <div id="category-tree">
                        </div>
                    </div>
                    <div class="col-md-9">

                        <input type="hidden" name="id" id="category-id"
                               value="{{!empty($form['id']) ? $form['id'] : 0}}" />
                        <input type="hidden" name="is_root" value="{{ $isRoot }}" />
                         <div class="row">
                             <div class="col-md-6">
                                 @include('Backend.View::layout.form', $form['name'])
                                 @include('Backend.View::layout.form', $form['description'])
                                 @include('Backend.View::layout.form', $form['status'])
                                 @if ($parentId != 0)
                                    @include('Backend.View::layout.form', $form['parent_id'])
                                 @endif
                                 @include('Backend.View::layout.form', $form['image'])
                             </div>
                             <div class="col-md-6">
                                 @include('Backend.View::layout.form', $form['slug'])
                                 @include('Backend.View::layout.form', $form['seo_title'])
                                 @include('Backend.View::layout.form', $form['seo_keyword'])
                                 @include('Backend.View::layout.form', $form['seo_description'])
                                 @include('Backend.View::layout.form', $form['is_homepage'])
                                 @include('Backend.View::layout.form', $form['num_display'])
                             </div>
                         </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
{{ Form::close() }}
@stop

@section('scripts_more')
<link rel="stylesheet" href="{{ asset('/vendor/jqtree/jqtree.css') }}">
<script src="{{ asset('/vendor/jqtree/jqtree.min.js')}}"></script>

@stop

