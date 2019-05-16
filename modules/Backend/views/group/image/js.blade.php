<script>
    $(document).on('click', '#image-{!! $id !!}', function() {
        $.openKCFinder($('#image-{!! $id !!}'), '{!! $id !!}');
    });
</script>