<!-- form filter -->
<div class="form-group pull-right">
    {{Form::select('product_type', $rs['filter']['type'], '', ['class'=>'form-control select2 select-filter'])}}
</div>
<div class="form-group pull-right">
    {{Form::select('category', $rs['filter']['category'], '', ['class'=>'form-control select2 select-filter'])}}
</div>
