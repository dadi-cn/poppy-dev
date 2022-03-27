<form action="{!! $action !!}" method="get" id="{{ $filter_id }}-form" class="layui-form layui-form-sm">
    <div class="layui-row layui-col-space5">
        @foreach($layout->columns() as $column)
            <div class="layui-col-md{{ $column->width() }}">
                @foreach($column->filters() as $filter)
                    {!! $filter->render() !!}
                @endforeach
            </div>
        @endforeach
        <div class="layui-col-md1">
            <button class="layui-btn layui-btn-info" id="{{ $filter_id }}-search"><i class="fa fa-search"></i></button>
            <button class="layui-btn layui-btn-warm" style="margin-left: 5px" id="{{ $filter_id }}-reload"><i class="fa fa-sync"></i></button>
            <button class="layui-btn layui-btn-primary" style="margin-left: 5px" id="{{ $filter_id }}-reset"><i class="fa fa-undo"></i></button>
        </div>
    </div>
</form>
