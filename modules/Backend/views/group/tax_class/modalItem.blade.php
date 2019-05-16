<div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">{{ __('Backend.Lang::lang.tax_class.tax_rate')}}</h4>
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
                        <label>{{ __('Backend.Lang::lang.field.geo_zone_id')}}</label>
                        <?php $geo = \Modules\Backend\Models\TaxRate::getGeo();?>
                        <select name="geo_zone_id" class="select2 form-control">
                            @foreach ($geo as $row)
                            <?php  isset($item->geo_zone_id) ? $geoZoneId = $item->geo_zone_id : $geoZoneId = ''; ?>
                            @if ($geoZoneId == $row->id)
                            <option value="{{ $row->id }}" selected="selected">{{ $row->name }}</option>
                            @else
                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
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

                    <div class="form-group">
                        <label>{{ __('Backend.Lang::lang.field.rate')}}</label>
                        <input type="number" class="form-control" name="rate"
                               value="{{ isset($item->rate) ? $item->rate : 0 }}"
                               placeholder="{{ __('Backend.Lang::lang.field.rate')}}"/>
                    </div>

                </div>
            </div>
            {{Form::close()}}
        </div>
        <div class="modal-footer">
            <div class="form-group pull-left">
                <div class="btn btn-primary" id="save-item" attr-action="{{ $action }}" attr-id="{{ $id }}">
                    {{ __('Backend.Lang::lang.action.save')}}
                </div>
            </div>
            <div class="btn btn-default pull-right" data-dismiss="modal">
                {{ __('Backend.Lang::lang.action.close')}}
            </div>
        </div>
    </div>

</div>