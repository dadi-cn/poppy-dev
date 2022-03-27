<div class="box box-info">
	<div class="box-header with-border">
		<h3 class="box-title">{{ $form->title() }}</h3>

		<div class="box-tools">
			{!! $form->renderTools() !!}
		</div>
	</div>
	<!-- /.box-header -->

	<!-- form start -->
	{!! $form->open() !!}

	@if(!$tabObj->isEmpty())
		@include('admin::form.tab', compact('tabObj'))
	@else

		@if($form->hasRows())
			@foreach($form->getRows() as $row)
				{!! $row->render() !!}
			@endforeach
		@else
			@foreach($layout->columns() as $column)
				<div class="layui-col-md{{ $column->width() }}">
					@foreach($column->fields() as $field)
						{!! $field->render() !!}
					@endforeach
				</div>
			@endforeach
		@endif
	@endif

<!-- /.box-body -->

	{!! $form->renderFooter() !!}

	@foreach($form->getHiddenFields() as $field)
		{!! $field->render() !!}
	@endforeach

<!-- /.box-footer -->
	{!! $form->close() !!}
</div>

