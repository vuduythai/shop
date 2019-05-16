<?php  $i = 1;?>
@foreach ($items as $row)
    @include('Backend.View::group.attribute.appendItem', ['item'=>$row, 'index'=>$i, 'itemJson'=>json_encode($row)])
    <?php  $i++;?>
@endforeach