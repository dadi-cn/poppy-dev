<div class="{{$viewClass['form-group']}} {!! (isset($errors) && !$errors->has($errorKey)) ? '' : 'has-error' !!}">

	<div class="{{$viewClass['label']}}">
		<label for="{{$id}}" class="layui-form-auto-label {{$viewClass['label_element']}}">
			@include('py-mgr-page::tpl.form.help-tip')
			{{$label}}
		</label>
	</div>

	<div class="{{$viewClass['field']}}">

		<div class="input-group" style="width: 150px">
			<input type="text" id="{{$id}}" name="{{$name}}" value="{{ old($column, $value) }}" class="form-control {{$class}}"
				placeholder="0" style="text-align:right;" {!! $attributes !!} />
			<span class="input-group-addon">%</span>
		</div>
		@include('py-mgr-page::tpl.form.help-block')
		@include('py-mgr-page::tpl.form.error')
	</div>
</div>
