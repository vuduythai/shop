<div class="modal-dialog">
    {!! Form::open(['id'=>'form-variant']) !!}

    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">{{ __('Backend.Lang::lang.general.variant') }}</h4>
        </div>
        <div class="modal-body">
            <input type="hidden" name="property_string" value="{{ $property }}" />
            <div class="form-group">
                <label>{{ __('Backend.Lang::lang.product.price_variant') }}</label>
                <input type="number" name="price_variant" id="price_variant" class="form-control"
                       value="{{ isset($variant['price_variant']) ? $variant['price_variant'] : '' }}"/>
                <span class="form-comment">{{ __('Backend.Lang::lang.product.price_variant_comment') }}</span>
            </div>
            <div class="form-group">
                <label>{{ __('Backend.Lang::lang.product.qty_variant') }}</label>
                <input type="number" name="qty_variant" id="qty_variant" class="form-control"
                       value="{{ isset($variant['qty_variant']) ? $variant['qty_variant'] : '' }}"/>
                <span class="form-comment">{{ __('Backend.Lang::lang.product.qty_variant_comment') }}</span>
            </div>
            <div class="form-group" id="image-variant-form-group">
                <label>{{ __('Backend.Lang::lang.field.image') }}</label>
                <div class="variant-image">
                    @if (isset($variant['variant_image']))
                    <img src="@imageDisplay($variant['variant_image'])" class="image image-choose-ok">
                    <div class="overlay">
                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                    </div>
                    @else
                    <i class="fa fa-picture-o fa-lg" aria-hidden="true"></i>
                    @endif
                </div>
                <input type="hidden" name="variant_image" id="variant-image"
                       value="{{ isset($variant['variant_image']) ? $variant['variant_image'] : ''}}">
            </div>

            <div class="form-group" id="gallery-variant-form-group">
                <!-- GALLERY -->
                <label>{{ __('Backend.Lang::lang.product.gallery') }}</label>
                <div class="box-one">
                    <div class="box-one-body">
                        <div id="variant_gallery">
                            @if (isset($variant['variant_gallery']))
                                @php
                                $gallery = explode(\Modules\Backend\Core\System::SEPARATE, $variant['variant_gallery']);
                                $folderImage = \Modules\Backend\Core\System::FOLDER_IMAGE;
                                @endphp
                                @foreach ($gallery as $row)
                                <div class="image-gallery-outer">
                                    <img class="img-delete" src="{{ url('/') }}/modules/backend/assets/img/x.png">
                                    <input name="variant_gallery[]" value="{{ $row }}" type="hidden">
                                    <img class="image-gallery" src="{{ url($folderImage) }}/{{ $row }}">
                                </div>
                                @endforeach
                            @endif
                            <div class="variant-gallery-find">
                                <i class="fa fa-picture-o fa-lg" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END GALLERY -->
            </div>

            <input type="hidden" id="variant-id-update" value=""/>
            <div class="btn btn-large btn-primary"
                    id="button-save-variant" attr-index="{{ isset($variant) ? $index : 0}}">
                {{ __('Backend.Lang::lang.general.save') }}
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>