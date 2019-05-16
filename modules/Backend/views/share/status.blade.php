<td>
    @if ($fieldValue->status == \Modules\Backend\Core\System::STATUS_ACTIVE)
        <span class="enable"> {{ __('Backend.Lang::lang.general.enable') }}</span>
    @else
        <span class="disable"> {{ __('Backend.Lang::lang.general.disable') }}</span>
    @endif
</td>