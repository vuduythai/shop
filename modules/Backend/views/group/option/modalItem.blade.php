<div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">{{ __('Backend.Lang::lang.option.value')}}</h4>
        </div>
        <div class="modal-body">
            {{Form::open(['url'=>'', 'id'=>'form-save-item'])}}

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ __('Backend.Lang::lang.field.name')}}</label>
                        <input type="text" class="form-control" name="name"
                               value="{{ isset($item->name) ? $item->name : '' }}"
                               placeholder="{{ __('Backend.Lang::lang.field.name')}}" required/>
                    </div>
                    <div class="form-group">
                        <label>{{ __('Backend.Lang::lang.field.type')}}</label>
                        <?php $type = \Modules\Backend\Core\System::getTypeFixPer();?>
                        <select name="type" class="select2 form-control">
                            @foreach ($type as $key => $value)
                            <?php  isset($item->type) ? $type = $item->type : $type = ''; ?>
                            @if ($type == $key)
                            <option value="{{ $key }}" selected="selected">{{ $value}}</option>
                            @else
                            <option value="{{ $key }}">{{ $value }}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ __('Backend.Lang::lang.option.price')}}</label>
                        <input type="number" class="form-control" name="price"
                               value="{{ isset($item->price) ? $item->price : 0 }}"
                               placeholder="{{ __('Backend.Lang::lang.option.price')}}" required/>
                    </div>
                </div>
            </div>
            {{Form::close()}}
        </div>
        <div class="modal-footer">
            <div class="form-group pull-left">
                <div class="btn btn-primary" id="save-item" attr-action="{{ $action }}"
                     attr-id="{{ $id }}" attr-index="{{ $index }}">
                    {{ __('Backend.Lang::lang.action.save')}}
                </div>
            </div>
            <div class="btn btn-default pull-right" data-dismiss="modal">
                {{ __('Backend.Lang::lang.action.close')}}
            </div>
        </div>
    </div>

</div>