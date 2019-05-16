@if (!empty($review))
    @foreach ($review['data'] as $re)
    <div class="review-div">
        <div class="review-author">{{$re['author']}}</div>
        <div class="review-star-rating">
            {!! IHelpers::displayReviewStar($re['rate']) !!}
        </div>
        <div class="review-created-at">{{$re['created_at']}}</div>
        <div class="review-content">{{$re['content']}}</div>
    </div>
    <hr/>
    @endforeach
    <div class="pagination" id="pagination-review">
        @if ($review['current_page'] != 1)
        <a href="javascript:void(0)" class="review-pag" attr-page="1"> << </a>
        <a href="javascript:void(0)" class="review-pag" attr-page="{{$review['current_page'] - 1}}"> < </a>
        @endif

        @if ($review['last_page'] != 1)
        @for ($i = 1; $i <= $review['last_page'] ; $i++)
        <a href="javascript:void(0)"
           class="{{ $review['current_page'] == $i ? 'active' : ''}} review-pag"
           attr-page="{{$i}}">{{$i}}
        </a>
        @endfor
        @endif

        @if ($review['current_page'] != $review['last_page'])
        <a href="javascript:void(0)" class="review-pag" attr-page="{{ $review['current_page'] + 1 }}"> >> </a>
        <a href="javascript:void(0)" class="review-pag" attr-page="{{ $review['last_page'] }}"> > </a>
        @endif
    </div>
@endif