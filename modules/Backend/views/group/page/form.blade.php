<div class="col-md-12">
    <div class="form-box-content">
        <ul class="nav nav-tabs">
            <li class="active nav-item">
                <a href="#general" class="nav-link" data-toggle="tab">
                    {{__('Backend.Lang::lang.general.general')}}
                </a>
            </li>
            <li class="nav-item">
                <a href="#seo" class="nav-link" data-toggle="tab">
                    {{__('Backend.Lang::lang.general.seo')}}
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
                    <div class="col-md-6">
                        @include('Backend.View::layout.form', $form['slug'])
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        @include('Backend.View::layout.form', $form['body'])
                    </div>
                </div>
            </div>

            <!-- TAB SEO -->
            <div class="tab-pane" id="seo">
                <div class="row">
                    <div class="col-md-12">
                        @include('Backend.View::layout.form', $form['seo_title'])
                        @include('Backend.View::layout.form', $form['seo_keyword'])
                        @include('Backend.View::layout.form', $form['seo_description'])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>