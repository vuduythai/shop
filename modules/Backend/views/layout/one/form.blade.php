@yield('oneFormField')

<div class="col-md-12 btn-one-item">
    <!-- BUTTON HANDLE ITEM -->
    <div class="btn btn-default item-action" id="create-item">
        <i class="fa fa-file" aria-hidden="true"></i>
        {{ __('Backend.Lang::lang.'.$controller.'.create_'.$form['itemName']) }}
    </div>
    <div class="btn btn-default item-action" id="delete-item">
        <i class="fa fa-trash" aria-hidden="true"></i>
        {{ __('Backend.Lang::lang.general.delete') }}
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
            @yield('oneFormTableThead')
        </tr>
        </thead>
        <tbody id="div-append-item">
        </tbody>
    </table>
</div>


@section('scripts')
<script src="{{ asset('/modules/backend/assets/js/one.js')}}"></script>
@stop