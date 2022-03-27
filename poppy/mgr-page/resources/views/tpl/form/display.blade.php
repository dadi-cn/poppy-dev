<div class="{{$viewClass['form-group']}}">
	<div class="{{$viewClass['label']}}">
		<div class="layui-form-auto-label {{$viewClass['label_element']}}">
			@include('py-mgr-page::tpl.form.help-tip')
			{{$label}}
		</div>
	</div>
	<div class="{{$viewClass['field']}}">
		<div class="layui-form-auto-field">
			<div class="layui-field-display">
				{!! $value !!}&nbsp;
			</div>
		</div>
		@include('py-mgr-page::tpl.form.help-block')
	</div>
</div>