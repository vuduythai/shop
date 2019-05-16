@extends('Backend.View::layout.main')

<?php
//convert name, for example : role_permission => role permission
$controllerNameConvert = ucfirst(str_replace('_', ' ', $controller));
?>
@section('title')
{{ ucfirst($controllerNameConvert) }}
@endsection

@section('content')
<input type="hidden" id="separate-string" value="{{ \Modules\Backend\Core\System::SEPARATE }}" />
<script>
    function toggle(source) {
        var checkboxes = document.getElementsByName('checkbox-record');
        for(var i=0, n = checkboxes.length;i<n;i++) {
            checkboxes[i].checked = source.checked;
        }
    }
</script>

@if (session('msg'))
<div id="msg_display" style="display: none">{{session('msg')}}</div>
@endif

<input type="hidden" id="controller-name" value="{{ $controller }}" />

<?php $adminUrl = config('app.admin_url');?>
@include('Backend.View::share.breadCrumb')

<!-- LIST -->
<div class="row">
    <div class="col-md-12">
        <div class="box list-box">
            <div class="box-header list-box-header">
                <div class="button-in-list pull-left">
                    <?php
                    $arrayControllerNotHaveCreateAction = ['role_permission', 'order', 'review', 'mail'];
                    ?>
                    @if (!in_array($controller, $arrayControllerNotHaveCreateAction))
                    <a href="{{route($controller.'.create')}}" class="btn btn-primary btn-blue"
                       id="button-create">
                        {{__('Backend.Lang::lang.action.create')}}
                    </a>
                    @endif
                    <!-- Add some more button, for example: category reorder, ... -->
                    @if (isset($rs['button']))
                        @foreach ($rs['button'] as $row)
                            @include($row, ['controller'=>$controller])
                        @endforeach
                    @endif

                    <?php
                    $arrayControllerNotHaveDeleteAction = ['order', 'mail'];
                    ?>
                    @if (!in_array($controller, $arrayControllerNotHaveDeleteAction))
                    <button class="btn btn-default" id="button-delete" attr-id="0">
                        {{__('Backend.Lang::lang.action.delete')}}
                    </button>
                    @endif
                </div>

                <div class="form-field-in-list pull-right">
                    <!-- form default filter -->
                    {{Form::open(['method'=>'GET', 'url'=>$adminUrl.'/'.$controller,
                    'class'=>'', 'id'=>'form-filter-search'])}}
                        <div class="form-group pull-right">
                            <input type="text" name="key"
                                   value="{{!empty($params['key']) ? $params['key'] : ''}}"
                                   class="form-control search-filter"
                                   placeholder="{{__('Backend.Lang::lang.general.type_two_letters')}}">
                        </div>
                        @if (array_key_exists('filter_template', $rs))
                        <!-- add more filter field -->
                        @include($rs['filter_template'])
                        @endif
                    {{Form::close()}}
                </div>
            </div>

            <div class="box-body list-box-body">
                <div class="row">
                </div>
                <!-- /.box-header -->
                <table class="table table-bordered base-list">
                    <thead>
                    <th class="checkbox-td-list" width="5%">
                        <div class="pretty p-default p-smooth p-bigger">
                            <input type="checkbox" id="check-all-record" onclick="toggle(this)">
                            <div class="state p-primary">
                                <label></label>
                            </div>
                        </div>
                    </th>
                    @foreach ($rs['field'] as $row)
                    <th>{{$row['name']}}</th>
                    @endforeach
                    </thead>
                    <tbody>
                    @if (!@empty($data))
                    @foreach ($data as $row)
                    <tr attr-url-edit="{{URL::to('/'.$adminUrl.'/'.$controller.'/'.$row->id.'/edit')}}">
                        <td class="checkbox-td-list">
                            <div class="pretty p-default p-smooth p-bigger">
                                <input type="checkbox" name="checkbox-record"
                                       class="checkbox-record" value="{{$row->id}}" />
                                <div class="state p-primary">
                                    <label></label>
                                </div>
                            </div>
                        </td>
                        @foreach ($rs['field'] as $f)
                        @if (array_key_exists('relation', $f))
                            <?php
                            $relationArray = explode(',', $f['relation']);
                            $relation = $relationArray[0];//for php version > 7
                            $relationName = $relationArray[1];
                            //don't use $row->$relationArray[0]->$relationArray[1] if php >= 7.0
                            ?>
                            @if (!empty($row->$relation->$relationName))
                                @if (isset($relationArray[2]))
                                    <!-- in case of controller 'order' => list order -->
                                    <?php $color = $relationArray[2] ?>
                                    <td>
                                        <span class="relation-color"
                                              style="background-color: {{$row->$relation->$color}}">
                                            {{$row->$relation->$relationName}}
                                        </span>
                                    </td>
                                @else
                                    <td>{{$row->$relation->$relationName}}</td>
                                @endif
                            @else
                            <td></td>
                            @endif
                        @elseif (array_key_exists('partial', $f))
                            <?php $columnName = $f['column']; //for php version > 7?>
                            @include($f['partial'], ['fieldValue'=>$row])
                        @else
                            <?php
                            $columnName = $f['column']; //for php version > 7
                            $class = '';
                            if ($columnName == 'name') {
                                $class = 'row-td-name';
                            }
                            ?>
                            <td class="{{ $class }}">{{$row->$columnName}}</td>
                        @endif
                        @endforeach
                    </tr>
                    @endforeach
                    @endif
                    </tbody>
                </table>
                <!-- /.box-body -->
            </div>
            <div class="box-footer list-pagination">
                {{$rs['data']->appends($params)->links()}}
            </div>
        </div>
    </div>
</div>

@stop
