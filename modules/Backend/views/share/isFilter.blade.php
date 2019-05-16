<td>
    @if ($fieldValue->is_filter == \Modules\Backend\Models\Attribute::IS_FILTER)
    <span class="enable"> {{ __('Backend.Lang::lang.general.yes') }}</span>
    @else
    <span class="disable"> {{ __('Backend.Lang::lang.general.no') }}</span>
    @endif
</td>