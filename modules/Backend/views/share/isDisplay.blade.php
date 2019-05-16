<td>
    @if ($fieldValue->is_display == \Modules\Backend\Models\Attribute::IS_FOR_DISPLAY)
    <span class="enable"> {{ __('Backend.Lang::lang.general.yes') }}</span>
    @else
    <span class="disable"> {{ __('Backend.Lang::lang.general.no') }}</span>
    @endif
</td>