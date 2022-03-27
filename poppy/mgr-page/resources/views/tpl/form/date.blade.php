<div class="{{$viewClass['form-group']}} {!! (isset($errors) && $errors->has($errorKey)) ? 'has-error' : ''  !!}">
    <div class="{{$viewClass['label']}}">
        <label for="{{$id}}" class="layui-form-auto-label {{$viewClass['label_element']}}">
            @include('py-mgr-page::tpl.form.help-tip')
            {{$label}}
        </label>
    </div>
    <div class="{{$viewClass['field']}}">
        <div class="layui-form-auto-field">
            <div class="layui-inline">
                {!! app('poppy.mgr-page.form')->datePicker($name, old($column, $value), $attributes + [
                    'id' => $id,
                    'placeholder' => $placeholder,
                ] + $options) !!}
            </div>
        </div>
        @include('py-mgr-page::tpl.form.help-block')
        @include('py-mgr-page::tpl.form.error')
    </div>
</div>
