<span class="header-filter-switch" id="{!! $filter_id !!}">
    <i class="fa fa-caret-down" id="{!! $filter_id !!}-icon"></i>
</span>
<script>
$(function() {
    $('#{!! $filter_id !!}').on('click', function() {
        let $filterIcon = $('#{!! $filter_id !!}-icon');
        let $filterForm = $('#{!! $filter_id !!}-form');
        let display     = $filterForm.css('display');
        $filterForm.fadeToggle();
        if (display !== 'none') {
            $filterIcon.removeClass('fa-caret-down').addClass('fa-caret-right')
        } else {
            $filterIcon.removeClass('fa-caret-right').addClass('fa-caret-down');
        }
    })
})
</script>