<div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">{{ Theme::lang('lang.review.write_review') }}</h4>
        </div>
        <div class="modal-body">
            <form id="form-review-product" method="post">
                <input type="hidden" name="product_id" value="{{$productId}}" />
                @php
                $author = '';
                @endphp
                @if (Auth::guard('users')->check())
                    @php
                    $user = Auth::guard('users')->getUser();
                    $author = $user->first_name.' '.$user->last_name;
                    @endphp
                @endif
                <div class="form-group">
                    <label> {{  Theme::lang('lang.review.author') }}</label>
                    <input type="text" name="author" value="{{ $author }}" class="form-control" required=""/>
                </div>
                <div class="form-group">
                    <label> {{  Theme::lang('lang.review.content') }}</label>
                    <textarea name="content" class="form-control" required=""></textarea>
                </div>
                <div class="form-group">
                    <label>{{  Theme::lang('lang.review.rating') }}</label>
                    <div class="star_rating" id="star_rating">
                        <div class="row">
                            <div class="col-md-12">
                            @for ($i = 1; $i < 6; $i++)
                            <div class="review-rating-col">
                                <span class="span-rating">{{ $i }}</span>
                                <br/>
                                <div class="pretty p-default p-round">
                                    <input type="radio" name="rate" value="{{ $i }}"/>
                                    <div class="state p-success-o">
                                        <label></label>
                                    </div>
                                </div>
                            </div>
                            @endfor
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Captcha</label>
                    <input type="text" name="captcha" id="captcha" class="form-control" value="" />
                    <img src="" id="captcha-image"/>
                </div>
            </form>
            <div class="form-group">
                <button class="btn btn-primary" id="submit-review">
                    {{ Theme::lang('lang.general.submit') }}
                </button>
            </div>
        </div>
    </div>
</div>

