@if (isset($data['current_params']) && $data['current_params'])
    @foreach($data['current_params'] as $param)
        @if (\Illuminate\Support\Str::startsWith($param->field, ':'))
			<?php continue; ?>
        @endif
        <div class="layui-form-item">
            <label for="field_{!! $param->field !!}">
                {!! $param->field !!}
                {!! ($param->optional ? '' : '<span style="color:red">*</span>') !!}
                ({!! strip_tags($param->type) !!})
            </label>
            &nbsp;&nbsp;
            <span>
                {!! strip_tags($param->description) !!}
                @if(isset($param->size))
                    { {!! $param->size !!} }
                @endif
                @if(isset($param->allowedValues))
                    { {!! \Poppy\Framework\Helper\ArrayHelper::combine($param->allowedValues) !!} }
                @endif
            </span>
            {!! Form::text($param->field, null, ['class' => 'layui-input layui-input-sm J_calc', 'id'=> 'field_'.$param->field]) !!}
        </div>
    @endforeach
@endif