<div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">{{ __('Backend.Lang::lang.field.image')}}</h4>
        </div>
        <div class="modal-body">
            {{Form::open(['url'=>'', 'id'=>'form-save-item'])}}

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ __('Backend.Lang::lang.field.name')}}</label>
                        <input type="text" class="form-control" name="name" value="{{ isset($item->name) ? $item->name : '' }}"
                               placeholder="{{ __('Backend.Lang::lang.field.name')}}" required/>
                    </div>
                    <div class="form-group">
                        <label>{{ __('Backend.Lang::lang.field.link')}}</label>
                        <input type="text" class="form-control" name="link" value="{{ isset($item->link) ? $item->link : '' }}"
                               placeholder="{{ __('Backend.Lang::lang.field.link')}}"/>
                    </div>
                    <div class="form-group">
                        <label>{{ __('Backend.Lang::lang.field.image')}}</label>
                        <div class="image-finder">
                            @php
                            $folderImage = \Modules\Backend\Core\System::FOLDER_IMAGE;
                            @endphp
                            @if (isset($item->image))
                                @if ($item->image != '')
                                <img src="{{ $folderImage.$item->image }}" class="image-choose-ok"/>
                                <div class="overlay">
                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                </div>
                                @else
                                 <i class="fa fa-picture-o fa-lg" aria-hidden="true"></i>
                                @endif
                            @else
                                 <i class="fa fa-picture-o fa-lg" aria-hidden="true"></i>
                            @endif
                        </div>
                        <input type="hidden" name="image" id="item-hidden"
                               value="{{ isset($item->image) ? $item->image : '' }}"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ __('Backend.Lang::lang.field.title')}}</label>
                        <input type="text" class="form-control" name="title" value="{{ isset($item->title) ? $item->title : '' }}"
                               placeholder="{{ __('Backend.Lang::lang.field.title')}}"/>
                    </div>
                    <div class="from-group">
                        <label>{{ __('Backend.Lang::lang.field.alt')}}</label>
                        <input type="text" class="form-control" name="alt" value="{{ isset($item->alt) ? $item->alt : '' }}"
                               placeholder="{{ __('Backend.Lang::lang.field.alt')}}"/>
                    </div>
                    <br/>
                    <div class="form-group">
                        <label>{{ __('Backend.Lang::lang.field.description')}}</label>
                        <textarea name="description" class="form-control"
                                  placeholder="{{ __('Backend.Lang::lang.field.description')}}"
                            >{{ isset($item->description) ? $item->description : '' }}</textarea>
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