<div class="layui-row">
	@foreach($fields as $field)
		<div class="layui-col-md{{ $field['width'] }}">
			{!! $field['element']->render() !!}
		</div>
	@endforeach
</div>