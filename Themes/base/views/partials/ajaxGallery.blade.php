<img class="xzoom" id="xzoom-big-image"
     src="{{ IHelpers::imageDisplaySlir('w555-h450', $bigImage) }}"
     xoriginal="@imageDisplay($bigImage)" />
@if (!empty($gallery))
<ul class="xzoom-thumbs" id="detail-product-thumb">
    <li>
        <a href="@imageDisplay($bigImage)">
            <img class="xzoom xzoom-thumbnail"
                 src="{{ IHelpers::imageDisplaySlir('w555-h450', $bigImage) }}">
        </a>
    </li>
    @foreach ($gallery as $image)
    <li>
        <a href="@imageDisplay($image)">
            <img class="xzoom xzoom-thumbnail"
                 src="{{ IHelpers::imageDisplaySlir('w555-h450', $image) }}">
        </a>
    </li>
    @endforeach
</ul>
@endif