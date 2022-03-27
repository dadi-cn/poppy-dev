<div class="{{$viewClass['form-group']}} {!! (isset($errors) && !$errors->has($errorKey)) ? '' : 'has-error' !!}">

	<div class="{{$viewClass['label']}}">
		<label for="{{$id}}" class="layui-form-auto-label {{$viewClass['label_element']}}">
			@include('py-mgr-page::tpl.form.help-tip')
			{{$label}}
		</label>
	</div>

	<div class="{{$viewClass['field']}}">
		<div class="layui-form-auto-field">
			@if($groups)
				{{-- todo  保留分组, 暂时不进行对接--}}
				@foreach($groups as $group)
					<optgroup label="{{ $group['label'] }}">
						@foreach($group['options'] as $select => $option)
							<option value="{{$select}}" {{ $select == old($column, $value) ?'selected':'' }}>{{$option}}</option>
						@endforeach
					</optgroup>
				@endforeach
			@endif

			{!! app('form')->select($name, $options, old($column, $value), $attributes) !!}
		</div>
		@include('py-mgr-page::tpl.form.help-block')
		@include('py-mgr-page::tpl.form.error')
	</div>
</div>
