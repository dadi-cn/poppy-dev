@if($group)
    <div class="layui-input-group layui-input-inline">
        <span class="layui-input-group-addon">
            {{$label}}
        </span>
        <div class="layui-row">
            <div class="layui-col-md4">
                {!! app('form')->select($id .'_group', collect($group)->pluck('label'), null, [
                    'lay-filter' => $id.'-lay-filter',
                    'lay-ignore',
                    'class' => 'layui-input-inline'
                ]) !!}
            </div>
            <div class="layui-col-md8">
                {!! app('form')->input($type, $name,  request($name, $value), [
                    'class' => 'J_tooltip layui-input layui-input-inline',
                    'title' => $label,
                    'placeholder' => $placeholder,
                ] ) !!}
            </div>
        </div>
    </div>
@else
    {!! app('form')->input($type, $name,  request($name, $value), [
        'class' => 'J_tooltip layui-input',
        'title' => $label,
        'placeholder' => $placeholder,
    ] ) !!}
@endif

