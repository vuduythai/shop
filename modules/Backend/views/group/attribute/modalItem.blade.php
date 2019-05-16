<div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">{{ __('Backend.Lang::lang.attribute.property')}}</h4>
        </div>
        <div class="modal-body">

            {{Form::open(['url'=>'', 'id'=>'form-save-item'])}}
            <input type="hidden" id="property-type" name="type" value="{{ $type }}" />

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>{{ __('Backend.Lang::lang.field.name')}}</label>
                        <input type="text" class="form-control" name="name" value="{{ isset($item->name) ? $item->name : '' }}"
                               placeholder="{{ __('Backend.Lang::lang.field.name')}}" id="property-name"/>
                    </div>
                    @if ($type == \Modules\Backend\Models\Attribute::TYPE_COLOR)
                    <div class="form-group" id="option-value-div">
                        <label>{{ __('Backend.Lang::lang.field.value')}}</label>
                        <input type="color" class="form-control property_value" name="value" value="{{ isset($item->value) ? $item->value : '' }}"
                               id="property-value-color"/>
                    </div>
                    @else
                    <input type="hidden" name="value" value="" />
                    @endif
                </div>
            </div>
            {{Form::close()}}
        </div>
        <div class="modal-footer">
            <div class="form-group pull-left">
                <div class="btn btn-primary" id="save-item" attr-action="{{ $action }}"
                     attr-id="{{ isset($item->id) ? $item->id : 0 }}" attr-index="{{ $index }}">
                    {{ __('Backend.Lang::lang.action.save')}}
                </div>
            </div>
            <div class="btn btn-default pull-right" data-dismiss="modal">
                {{ __('Backend.Lang::lang.action.close')}}
            </div>
        </div>
    </div>

</div>