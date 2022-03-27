<div class="{{$viewClass['form-group']}} {!! (isset($errors) && !$errors->has($errorKey)) ? '' : 'has-error' !!}">

	<div class="{{$viewClass['label']}}">
		<label for="{{$id}}" class="layui-form-auto-label {{$viewClass['label_element']}}">
			@include('py-mgr-page::tpl.form.help-tip')
			{{$label}}
		</label>
	</div>

	<div class="{{$viewClass['field']}} layui-field-radio-ctr">
		{!! !$inline ? '<div class="layui-field-radio-stack">' : '' !!}
		@foreach($options as $option => $label)
			<div class="layui-field-radio-item">
				{!! app('form')->radio(
				$name,
				$option,
				($option == old($column, $value)) || ($value === null && in_array($label, $checked, false)),
				array_merge($attributes , [
					'class' => 'layui-field-radio',
					'id' => $column.'-'.$option,
					'lay-ignore',
				])) !!}
				{!! app('form')->label($column.'-'.$option, $label, [
					'class' => 'layui-field-radio-label'
				]) !!}
			</div>
		@endforeach
		{!! !$inline ? '</div>' : '' !!}
		@include('py-mgr-page::tpl.form.help-block')
		@include('py-mgr-page::tpl.form.error')
	</div>
</div>