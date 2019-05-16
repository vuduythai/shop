<input type="hidden" id="controller-name" value="{{ $controller }}" />
<input type="hidden" id="item-factory-model-name" value="theme_image" />

<div class="col-md-12 row-item-form">
    <textarea name="items" id="many-items" style="display: none;"></textarea>
    @include('Backend.View::layout.form', $form['name'])
    @include('Backend.View::layout.form', $form['slug'])
    @include('Backend.View::layout.form', $form['description'])

    <!-- BUTTON HANDLE ITEM -->
    @php
    $item = 'image';
    @endphp
    <div id="btn-items">
        <div class="btn btn-default item-action" id="create-item">
            <i class="fa fa-file" aria-hidden="true"></i>
            {{ __('Backend.Lang::lang.theme.create_'.$form['itemName']) }}
        </div>
        <div class="btn btn-default item-action" id="attach-item">
            <i class="fa fa-plus" aria-hidden="true"></i>
            {{ __('Backend.Lang::lang.theme.attach_'.$form['itemName']) }}
        </div>
        <div class="btn btn-default item-action" id="remove-item">
            <i class="fa fa-minus" aria-hidden="true"></i>
            {{ __('Backend.Lang::lang.general.remove') }}
        </div>
        <div class="btn btn-default item-action" id="delete-item">
            <i class="fa fa-trash" aria-hidden="true"></i>
            {{ __('Backend.Lang::lang.general.delete') }}
        </div>
    </div>
    <!-- END BUTTON HANDLE ITEM-->
</div>
<div class="col-xs-12">
    <table class="table table-responsive table-bordered" id="table-item">
        <thead>
            <tr>
                <th class="item-checkbox-not-update">
                    <div class="pretty p-default p-smooth p-bigger">
                        <input type="checkbox" value="" id="check-all-items">
                        <div class="state p-primary">
                            <label></label>
                        </div>
                    </div>
                </th>
                <th>{{ __('Backend.Lang::lang.field.id') }}</th>
                <th>{{ __('Backend.Lang::lang.field.name') }}</th>
                <th>{{ __('Backend.Lang::lang.field.image') }}</th>
            </tr>
        </thead>
        <tbody id="div-append-item">
        </tbody>
    </table>
</div>


@section('scripts')
<script src="{{ asset('/modules/backend/assets/js/many.js')}}"></script>
@stop