<section class="col-md-4">
    <div class="box box-primary">
        <div class="box-body box-body-settings">
            @foreach ($menu as $row)
                @if (!empty($permission))
                    @if (array_key_exists($row['url'], $permission))
                        @if ($permission[$row['url']] == \Modules\Backend\Core\System::ALLOW)
                            <a href="{{URL::to($adminUrl.'/'.$row['url'])}}">
                                <span class="handle"><i class="fa {{$row['icon']}}"></i></span>
                                <span class="text">{{$row['text']}}</span>
                            </a>
                        @endif
                    @else
                        <a href="{{URL::to($adminUrl.'/'.$row['url'])}}">
                            <span class="handle"><i class="fa {{$row['icon']}}"></i></span>
                            <span class="text">{{$row['text']}}</span>
                        </a>
                    @endif
                @else
                    <a href="{{URL::to($adminUrl.'/'.$row['url'])}}">
                        <span class="handle"><i class="fa {{$row['icon']}}"></i></span>
                        <span class="text">{{$row['text']}}</span>
                    </a>
                @endif
            @endforeach
        </div>
    </div>
</section>