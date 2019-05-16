@foreach ($items as $row)
    @include('Backend.View::group.theme.appendItem', ['item'=>$row])
@endforeach