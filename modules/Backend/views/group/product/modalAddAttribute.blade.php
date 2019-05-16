<div class="modal-dialog">
    {!! Form::open(['id'=>'form-variant']) !!}
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">{{ __('Backend.Lang::lang.product.add_attribute') }}</h4>
        </div>
        <div class="modal-body">
             <div class="form-group">
                 <label>{{ __('Backend.Lang::lang.product.choose_attribute') }}</label>
                 {{Form::select('attribute_add', $attribute, '', ['class'=>'form-control select2', 'id'=>'add-attribute-select'])}}
             </div>
            <div class="btn btn-large btn-primary" id="btn-add-attribute">
                {{ __('Backend.Lang::lang.product.add') }}
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>