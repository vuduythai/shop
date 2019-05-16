<?php $routesTypeProduct = \Modules\Backend\Core\System::ROUTES_TYPE_PRODUCT;?>
<input type="hidden" id="routes_type" value="{{$routesTypeProduct}}" />
<input type="hidden" id="routes_id" name="routes_id"
       value="{{!empty($form['routes']->id) ? $form['routes']->id : 0}}" />

<div class="col-md-12">
<div class="form-box-content">
    <ul class="nav nav-tabs">
        <li class="active nav-item">
            <a href="#general" class="nav-link" data-toggle="tab">
                {{__('Backend.Lang::lang.general.general')}}
            </a>
        </li>
        <li class="nav-item">
            <a href="#price-tab" class="nav-link" data-toggle="tab">
                {{__('Backend.Lang::lang.general.price')}}
            </a>
        </li>
        <li class="nav-item">
            <a href="#inventory" class="nav-link" data-toggle="tab">
                {{__('Backend.Lang::lang.product.inventory')}}
            </a>
        </li>
        <li class="nav-item">
            <a href="#introduction" class="nav-link" data-toggle="tab">
                {{__('Backend.Lang::lang.general.introduction')}}
            </a>
        </li>
        <li class="nav-item">
            <a href="#advance" class="nav-link" data-toggle="tab">
                {{__('Backend.Lang::lang.general.advance')}}
            </a>
        </li>
        <li class="nav-item">
            <a href="#seo" class="nav-link" data-toggle="tab">
                {{__('Backend.Lang::lang.general.seo')}}
            </a>
        </li>
        <li class="nav-item">
            <a href="#gallery-tab" class="nav-link" data-toggle="tab">
                {{__('Backend.Lang::lang.general.gallery')}}
            </a>
        </li>
        <li class="nav-item">
            <a href="#attribute" class="nav-link" data-toggle="tab">
                {{__('Backend.Lang::lang.general.attribute')}}
            </a>
        </li>
        <li class="nav-item">
            <a href="#option" class="nav-link" data-toggle="tab">
                {{__('Backend.Lang::lang.general.option')}}
            </a>
        </li>
        <li class="nav-item">
            <a href="#variant" class="nav-link" data-toggle="tab">
                {{__('Backend.Lang::lang.general.variant')}}
            </a>
        </li>
    </ul>

    <div class="tab-content">

        <!-- TAB GENERAL -->
        <div class="tab-pane active" id="general">
            <div class="row">
                <div class="col-md-6">
                    @include('Backend.View::layout.form', $form['name'])
                    @include('Backend.View::layout.form', $form['price'])
                    @include('Backend.View::layout.form', $form['category[]'])
                    <input type="hidden" id="category-chosen" value="{{ $form['categoryChosen'] }}" />

                    <div class="row">
                        <div class="col-md-4">
                            @include('Backend.View::layout.form', $form['status'])
                        </div>
                        <div class="col-md-4">
                            @include('Backend.View::layout.form', $form['is_featured_product'])
                        </div>
                        <div class="col-md-4">
                            @include('Backend.View::layout.form', $form['is_new'])
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            @include('Backend.View::layout.form', $form['is_bestseller'])
                        </div>
                        <div class="col-md-4">
                            @include('Backend.View::layout.form', $form['is_on_sale'])
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    @include('Backend.View::layout.form', $form['slug'])

                    @include('Backend.View::layout.form', $form['category_default'])
                    @include('Backend.View::layout.form', $form['image'])
                </div>
            </div>
        </div>

        <!-- TAB PRICE -->
        <div class="tab-pane" id="price-tab">
            <div class="row">
                <div class="col-md-6">
                    @include('Backend.View::layout.form', $form['price_promotion'])
                    @include('Backend.View::layout.form', $form['price_promo_from'])
                    @include('Backend.View::layout.form', $form['price_promo_to'])
                </div>
            </div>
        </div>

        <!-- TAB INVENTORY -->
        <div class="tab-pane" id="inventory">
            <div class="row">
                <div class="col-md-6">
                    @include('Backend.View::layout.form', $form['sku'])
                    @include('Backend.View::layout.form', $form['is_in_stock'])
                    @include('Backend.View::layout.form', $form['qty'])
                </div>
            </div>
        </div>

        <!-- TAB INTRODUCTION -->
        <div class="tab-pane" id="introduction">
            <div class="row">
                <div class="col-md-12">
                    @include('Backend.View::layout.form', $form['short_intro'])
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    @include('Backend.View::layout.form', $form['full_intro'])
                </div>
            </div>
        </div>

        <!-- TAB ADVANCED -->
        <div class="tab-pane" id="advance">
            <div class="row">
                <div class="col-md-6">
                    @include('Backend.View::layout.form', $form['tax_class_id'])
                    @include('Backend.View::layout.form', $form['product_label[]'])
                    <input type="hidden" id="label-chosen" value="{{ $form['labelChosen'] }}" />
                    @include('Backend.View::layout.form', $form['length_id'])
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group form-dimensions">
                                <label>
                                    {{__('Backend.Lang::lang.product.length')}}
                                </label>
                                <input type="text" name="length" id="length" class="form-control"
                                       value="{{ isset($form['productData']) ? $form['productData']->length : 0 }}" />
                            </div>
                            <div class="form-group form-dimensions">
                                <label>
                                    {{__('Backend.Lang::lang.product.width')}}
                                </label>
                                <input type="text" name="width" id="width" class="form-control"
                                       value="{{ isset($form['productData']) ? $form['productData']->width : 0 }}">
                            </div>
                            <div class="form-group form-dimensions">
                                <label>
                                    {{__('Backend.Lang::lang.product.height')}}
                                </label>
                                <input type="text" name="height" id="height" class="form-control"
                                       value="{{ isset($form['productData']) ? $form['productData']->height : 0 }}">
                            </div>
                        </div>
                    </div>
                    @include('Backend.View::layout.form', $form['sort_order'])
                </div>
                <div class="col-md-6">
                    @include('Backend.View::layout.form', $form['weight_id'])
                    @include('Backend.View::layout.form', $form['weight'])
                    @include('Backend.View::layout.form', $form['brand_id'])
                    @include('Backend.View::layout.form', $form['tag'])
                </div>
            </div>
        </div>

        <!-- TAB SEO -->
        <div class="tab-pane" id="seo">
            <div class="row">
                <div class="col-md-12">
                    @include('Backend.View::layout.form', $form['seo_keyword'])
                    @include('Backend.View::layout.form', $form['seo_title'])
                    @include('Backend.View::layout.form', $form['seo_description'])
                </div>
            </div>
        </div>

        <!-- TAB GALLERY -->
        <div class="tab-pane" id="gallery-tab">
            <div class="row">
                <div class="col-md-12">
                    <div class="box-one">
                        <div class="box-one-header">
                            {{__('Backend.Lang::lang.field.image')}}
                        </div>
                        <div class="box-one-body">
                            <div id="product_gallery">
                                @if (!empty($form['gallery']))
                                    @php
                                    $gallery = explode(\Modules\Backend\Core\System::SEPARATE, $form['gallery']);
                                    $folderImage = \Modules\Backend\Core\System::FOLDER_IMAGE;
                                    @endphp
                                    @foreach ($gallery as $row)
                                    <div class="image-gallery-outer">
                                        <img class="img-delete" src="{{ url('/') }}/modules/backend/assets/img/x.png">
                                        <input name="product_gallery[]" value="{{ $row }}" type="hidden">
                                        <img class="image-gallery product-image" src="{{ url($folderImage) }}/{{ $row }}">
                                    </div>
                                    @endforeach
                                @endif
                                <div class="image-gallery-find">
                                    <i class="fa fa-picture-o fa-lg" aria-hidden="true"></i>
                                </div>
                            </div>
                            <span class="form-comment form-comment-product-gallery">
                                {{__('Backend.Lang::lang.comment.gallery_product_comment')}}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB ATTRIBUTE PROPERTY -->
        <div class="tab-pane" id="attribute">
            <div class="row">
                <div class="col-md-6">
                    @if ($form['id'] == 0)
                        @include('Backend.View::layout.form', $form['attribute_set_id'])
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-md-12" id="attribute-list">
                    @if (!empty($form['attributeEdit']))
                    @include('Backend.View::group.product.attributeCreate', $form['attributeEdit'])
                    @endif
                </div>
            </div>

        </div>

        <!-- TAB OPTION -->
        <div class="tab-pane" id="option">
            <div class="row">
                <div class="col-md-2" id="product-option-div">
                    <div id="option-type-json" style="display: none">{{ $form['optionType'] }}</div>
                    <div id="option-chosen">
                        @if (!empty($form['optionChosen']))
                            @foreach ($form['optionChosen']['option'] as $key => $value)
                            <input type="hidden" class="option-id-chosen"
                                   id="option-id-chosen-{{ $key }}" value="{{ $key }}" />
                            @endforeach
                        @endif
                    </div>
                    <ul id="prepend-option">
                        @if (!empty($form['optionChosen']))
                            @foreach ($form['optionChosen']['option'] as $key => $value)
                            <li class="product-option-li" id="product-option-li-{{ $key }}"
                                option-id="{{ $key }}">
                                <i class="fa fa-minus-circle" aria-hidden="true"></i>{{ $value }}
                            </li>
                            @endforeach
                        @endif
                    </ul>
                    <div class="form-group">
                        {{Form::select('option', $form['option'], [],
                        ['class'=>'select2 form-control', 'id'=>'option-choose-select'])}}
                    </div>
                </div>
                <div class="col-md-10" id="product-option-value-div">
                     <div id="option-value-delete" style="display: none"></div>
                     <div id="append-option-value">
                         @include('Backend.View::group.product.optionEdit', $form)
                     </div>
                </div>
            </div>
        </div>

        <!-- TAB VARIANT -->
        <div class="tab-pane" id="variant">
            <input type="hidden" id="const-no" value="{{ \Modules\Backend\Core\System::NO }}" />
            <div class="row">
                <div class="col-md-6" id="form-group-search-property">
                    <div class="form-group">
                        <label>{{__('Backend.Lang::lang.product.combine_property')}}</label>
                        <select id="property-search" name="variant_property[]" class="form-control select2" multiple>
                        </select>
                        <br/>
                        <span class="form-comment">{{__('Backend.Lang::lang.product.combine_property_comment')}}</span>
                    </div>
                </div>
                <div class="col-md-4">
                    @include('Backend.View::layout.form', $form['is_variant_change_image'])
                </div>
                <div class="col-md-2">
                    <div class="btn btn-primary" id="create-variant">
                        {{__('Backend.Lang::lang.product.create_variant')}}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-responsive table-bordered" id="table-item">
                        <thead>
                        <tr>
                            <th>{{ __('Backend.Lang::lang.product.variant') }}</th>
                            <th>{{ __('Backend.Lang::lang.product.price') }}</th>
                            <th>{{ __('Backend.Lang::lang.product.qty_title') }}</th>
                            <th><i class="fa fa-trash" aria-hidden="true"></i></th>
                        </tr>
                        </thead>
                        <tbody id="div-append-item">
                        <textarea name="id_update_old"
                                  style="display: none;">{{ $form['variantIdUpdateOld'] }}</textarea>
                        @if (!empty($form['variantEdit']))
                            @php
                            $i=1;
                            @endphp
                            @foreach ($form['variantEdit'] as $variant)
                                @php
                                $variant->id_update = $variant->id;
                                @endphp
                                <tr class="div-item" attr-index="{{ $i }}" id="variant-tr-{{ $i }}"
                                    attr-property-str="{{ $variant->property_string }}">
                                    <td>
                                        <textarea class="variant-json-data" id="variant-json-{{ $i }}"
                                                  name="variant[]" id-update="{{ $variant->id }}"
                                                  style="display: none">{{ json_encode($variant) }}</textarea>
                                        <span>{{ $variant->property_string_name }}</span>
                                    </td>
                                    <td><span class="price-variant">{{ $variant->price_variant }}</span></td>
                                    <td><span class="qty-variant">{{ $variant->qty_variant }}</span></td>
                                    <td class="remove-variant">
                                        <span><i class="fa fa-trash" aria-hidden="true"></i></span>
                                    </td>
                                </tr>
                            @php
                            $i++;
                            @endphp
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- /.tab-pane -->
    </div>
    <!-- /.tab-content -->
</div>
</div>

@section('scripts')
<script src="{{ asset('/vendor/js/moment.js') }}"></script>
<script src="{{ asset('/vendor/js/pikaday.js') }}"></script>
@stop

