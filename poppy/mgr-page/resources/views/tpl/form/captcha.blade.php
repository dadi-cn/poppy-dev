<div class="{{$viewClass['form-group']}} {!! (isset($errors) && !$errors->has($errorKey)) ? '' : 'has-error' !!}">
	<div class="{{$viewClass['label']}}">
		<label for="{{$id}}" class="layui-form-auto-label {{$viewClass['label_element']}}">
			@include('py-mgr-page::tpl.form.help-tip')
			{{$label}}
		</label>
	</div>

	<div class="{{$viewClass['field']}}">
		<div class="layui-form-auto-field">
			<div class="layui-inline">
				{!! app('form')->text($name, $value, $attributes) !!}
			</div>
			<div class="layui-inline">
				<img alt="验证码" id="{{$column}}-captcha" src="{{ captcha_src() }}"
					style="height:34px;cursor: pointer;"
					title="Click to refresh"/>
			</div>
			<script>
			$('#{!! $column !!}-captcha').on('click', function() {
				$(this).attr('src', $(this).attr('src') + '?' + Math.random());
			});
			</script>
		</div>
		@include('py-mgr-page::tpl.form.help-block')
		@include('py-mgr-page::tpl.form.error')
	</div>
</div>