<div class="col-md-12">
    <div class="form-box-content">
        <ul class="nav nav-tabs">
            <li class="active nav-item">
                <a href="#general" class="nav-link" data-toggle="tab">
                    {{__('Backend.Lang::lang.general.general')}}
                </a>
            </li>
            <li class="nav-item">
                <a href="#attribute-set" class="nav-link" data-toggle="tab">
                    {{__('Backend.Lang::lang.general.attribute_set')}}
                </a>
            </li>
        </ul>

        <div class="tab-content">

            <!-- TAB GENERAL -->
            <div class="tab-pane active" id="general">
                <div class="row">
                    <div class="col-md-6">
                        @include('Backend.View::layout.form', $form['name'])
                    </div>
                </div>
            </div>

            <!-- TAB ATTRIBUTE SET -->
            <div class="tab-pane" id="attribute-set">
                <span class="form-comment">
                    {{ __('Backend.Lang::lang.attribute_set.drag_and_drop_attribute') }}
                </span>
                <div class="row">
                    <div class="col-md-6">
                        <div class="attribute-list">
                            <div class="attribute-list-header">
                                {{ __('Backend.Lang::lang.attribute_set.default_list') }}
                            </div>
                            <div id="attribute-default-list" class="attribute-list-group col">
                                @foreach ($form['attribute'] as $row)
                                <div class="list-group-item">
                                    <input type="hidden" class="drag_attr_id" value="{{ $row->id }}" />
                                    {{ $row->name }}
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="attribute-list">
                            <div class="attribute-list-header">
                                {{ __('Backend.Lang::lang.attribute_set.set_list') }}
                            </div>
                            <div id="attribute-set-list" class="attribute-list-group col">
                                @if (!empty($form['attributeChosen']))
                                    @foreach ($form['attributeChosen'] as $row)
                                        <div class="list-group-item">
                                            <input type="hidden" class="drag_attr_id" name="drag_attr_id[]" value="{{ $row->id }}" />
                                            {{ $row->name }}
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



