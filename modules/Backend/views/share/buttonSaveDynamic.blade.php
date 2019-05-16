<div class="button-base-form pull-right">
    <button type="submit" class="btn btn-primary btn_save"
            attr-controller="{{ $controller }}" id="save-{{$controller}}">
        {{ __('Backend.Lang::lang.action.save') }}
    </button>
    <button type="submit" class="btn btn-primary btn_save_and_close"
            attr-controller="{{ $controller }}" id="save-and-close-{{$controller}}">
        {{ __('Backend.Lang::lang.action.save_and_close') }}
    </button>
    <a href="{{ Url::to('/'.config('app.admin_url').'/'.$controller.'') }}">
        {{ __('Backend.Lang::lang.action.close') }}
    </a>
</div>