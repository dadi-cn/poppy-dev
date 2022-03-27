@if(is_array($errorKey))
	@foreach($errorKey as $key => $col)
		@if(isset($errors) && $errors->has($col.$key))
			@foreach($errors->get($col.$key) as $message)
				<label class="layui-control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{$message}}</label><br/>
			@endforeach
		@endif
	@endforeach
@else
	@if(isset($errors) && $errors->has($errorKey))
		@foreach($errors->get($errorKey) as $message)
			<label class="layui-control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{$message}}</label><br/>
		@endforeach
	@endif
@endif