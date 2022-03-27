<div class="layui-card">
    @if ($title || $tools)
        <div class="layui-card-header">
            {!! $title !!}
            @if ($tools)
                <div class="pull-right">
                    @foreach($tools as $tool)
                        @if ($tool instanceof \Poppy\MgrPage\Classes\Form\Field)
                            {!! $tool->render() !!}
                        @else
                            {!! $tool !!}
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    @endif
    <div class="layui-card-body">
        {!! $content !!}
    </div>
</div>