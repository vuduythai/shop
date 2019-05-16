<div class="col-md-12">
    @include('Backend.View::layout.form', $form['name'])
</div>
<div id="mail_content_text" style="display: none">{{ $form['mailCss'] }} {{ $form['mailContentPreview'] }}</div>
<div class="col-md-6">
    @include('Backend.View::layout.form', $form['mail_content'])
</div>
<div class="col-md-6">
    <div class="form-group">
        <label>{{ __('Backend.Lang::lang.mail.mail_content_preview') }}</label>
        <div id="mail_content_preview"></div>
    </div>
</div>
