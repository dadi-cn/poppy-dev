@extends('py-mgr-page::tpl.develop')
@section('develop-main')
    <div class="row">
        <div class="col-sm-12">
            {!! Form::open(['class'=> 'layui-form pt15']) !!}
            <div class="layui-form-item">
                {!! $field !!} ( String )
            </div>
            <div class="layui-form-item">
                {!! Form::text('value', $value ?? null, ['id'=>$field, 'class'=>'layui-input']) !!}
            </div>
            <div class="layui-form-item">
                <button class="layui-btn J_submit" type="submit" id="submit">设置 {!! $field !!}</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection