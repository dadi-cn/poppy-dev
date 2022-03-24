<p class="layui-form-item mt10">
    {!! Form::label('headers', '附加Headers(json):') !!}
    <a href="{!! route_url('py-mgr-page:develop.api.field', [$guard, 'headers']) !!}"
       data-title="设置 Headers {!! $guard !!}"
       class="J_iframe pull-right layui-btn layui-btn-sm">设置 Headers</a>
    {!! Form::text('headers',$data['headers']??'', [
        'class' => 'layui-input J_calc mt3 layui-input-sm',
        'readonly'=> true,
    ]) !!}
</p>