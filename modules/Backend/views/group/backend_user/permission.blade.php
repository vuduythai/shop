<div class="row">
    <div class="col-md-12">
        @foreach ($permission as $p)
        <div class="permission-controller-label">
            <h4>{{ $p['label'] }}</h4>
        </div>

        <div class="permission-resource">
            <button type="button" class="check-all-control btn btn-default pull-left btn-xs"
                    attr-controller="{{ $p['controller'] }}">
                {{__('Backend.Lang::lang.general.check_all')}}
            </button>
            @foreach ($p['resource'] as $r)
            @php
            $checked = '';
            if (array_key_exists($r['name'], $permissionByRole)) {
                if ($permissionByRole[$r['name']] == $allow) {
                    $checked = 'checked="checked"';
                }
            }
            @endphp
            <div class="pretty p-default">
                <input type="checkbox" name="{{ $r['name'] }}" class="checkbox-{{ $p['controller'] }}"
                       value="{{ $allow }}" {{$checked}}/>
                <div class="state p-info">
                    <label>{{ $r['label'] }}</label>
                </div>
            </div>
            @endforeach
        </div>
        @endforeach
    </div>
</div>