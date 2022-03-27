<div class="{{$viewClass['form-group']}} {!! (isset($errors) && !$errors->has($errorKey)) ? '' : 'has-error' !!}">

	<label for="{{$id['lat']}}" class="{{$viewClass['label']}}">
		@include('py-mgr-page::tpl.form.help-tip')
		{{$label}}
	</label>

	<div class="{{$viewClass['field']}}">


		<div id="map_{{$id['lat'].$id['lng']}}" style="width: 100%;height: 300px"></div>
		<input type="hidden" id="{{$id['lat']}}" name="{{$name['lat']}}"
			value="{{ old($column['lat'], $value['lat']) }}" {!! $attributes !!} />
		<input type="hidden" id="{{$id['lng']}}" name="{{$name['lng']}}"
			value="{{ old($column['lng'], $value['lng']) }}" {!! $attributes !!} />
		@include('py-mgr-page::tpl.form.help-block')
		@include('py-mgr-page::tpl.form.error')
	</div>
</div>
