@if (data_get($definition, 'sign_certificate'))
    <div class="alert alert-warning">
        @foreach(data_get($definition, 'sign_certificate') as $field)
            <div class="layui-form-item">
                <p class="pr5 pb2">
                    {!! Form::label($field['name'], strip_tags($field['title'])) !!}
                    {!! (($field['is_required']??'N') == 'N' ? '' : '<span style="color:red">*</span>') !!}
                    (
                    {!! strip_tags($field['type']) !!}
                    [{!! $field['name'] !!}]
                    )
                    <a href="{!! route_url('py-mgr-page:develop.api.field', [$guard,$field['name']]) !!}"
                        data-title="设置 {!! $field['title'] !!}" class="J_iframe pull-right">
                        <i class="layui-icon layui-icon-set" style="font-size: 30px;"></i>
                    </a>
                </p>
                {!! Form::text($field['name'], Session::get('dev#' . $guard . '#' . $field['name']) ?: ($field['default']??''), [
                    'class' => 'layui-input layui-input-sm J_calc', 'readonly'=> 'readonly'
                ]) !!}
            </div>
        @endforeach
    </div>
@endif