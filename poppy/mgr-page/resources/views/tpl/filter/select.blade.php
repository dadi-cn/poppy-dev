<select class="{{ $class }}" name="{{$name}}" style="width: 100%;" lay-ignore>
    <option value="">{!! $label !!}</option>
    @foreach($options as $select => $option)
        <option value="{{$select}}" {{ (string)$select === (string)request($name, $value) ?'selected':'' }}>{{$option}}</option>
    @endforeach
</select>