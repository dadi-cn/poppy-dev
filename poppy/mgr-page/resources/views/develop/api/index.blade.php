@extends('py-mgr-page::tpl.develop')
@section('develop-main')
    @include('py-mgr-page::develop.api.nav')
    @if (!$data['file_exists'])
        <div class="layui-elem-quote">
            Api Doc 文件不存在, 请运行 <code>php artisan py-core:doc api</code> 来生成 Api 文档
        </div>
        <hr>
    @else
        @include('py-mgr-page::develop.api._pam')
        <div class="layui-row layui-col-space10" id="app">
            <div class="layui-col-md6 mt5">
                {{-- ajax 方式, 便于调试, 需要服务器配置跨域 --}}
                {!! Form::model($data['params'] ?? null,[
                    'url' => url($data['current']->url),
                    'method' => $data['current']->type,
                    'id'=> 'form_auto',
                    'class'=> 'layui-form'
                ]) !!}
                @include('py-mgr-page::develop.api._token')
                @include('py-mgr-page::develop.api._headers')
                @include('py-mgr-page::develop.api._certificate')
                @include('py-mgr-page::develop.api._params')

                <div class="layui-form-item">
                    {!! Form::button($data['current']->title, ['class' => 'layui-btn layui-btn-sm', 'type'=>'submit', 'id'=>'submit']) !!}
                </div>
                {!! Form::close() !!}
            </div>
            <div class="layui-col-md6 mt5">
                <div class="layui-elem-quote">{
                    @{{ requestType }}
                    }
                    @{{ url }}
                    <a class="pull-right fa fa-paragraph"
                       href="{!! $apidoc_url !!}/#api-{!! $data['current']->group !!}-{!! $data['current']->name !!}"
                       target="_blank"></a>
                    <p class="clearfix api-versions pt5">
                        @foreach($variables as $key => $item)
                            {!! Form::select($key, $item, null, [
                                'class' => 'J_variable',
                                'style' => 'display:inline-block',
                                'v-on:change' => 'changeVariable',
                                'placeholder' => '选择 '.$key,
                            ]) !!}
                        @endforeach
                    </p>
                </div>
                <div class="clearfix api-versions pt8" id="api_version">
                    @foreach($data['versions'] as $kv => $version)
                        <a @if ($kv === $data['version']) class="current"
                           @endif href="{!! route_url('', null, ['url' => $data['current']->url, 'method' => $data['current']->type, 'version' => $version]) !!}"> {{$version}} </a>
                    @endforeach
                </div>
                <pre id="J_result" style="display: none;color: #0a0a0a" class="layui-elem-quote layui-quote-nm mt8"></pre>
            </div>
        </div>
        <script>
        $(function () {
            let className = 'alert-warning alert-danger alert-info';
            let conf = Util.validateConfig({
                submitHandler: function (form) {
                    let $result = $('#J_result');
                    $result.text(
                        '进行中...'
                    ).css('color', 'grey');
                    $(form).ajaxSubmit({
                        beforeSend: function (request) {
                            let headerStr = '{!! $data['headers'] ?? '' !!}'
                            try {
                                let headers = JSON.parse(headerStr)
                                if (typeof headers == "object") {
                                    Object.keys(headers).forEach((key) => {
                                        request.setRequestHeader(key, headers[key])
                                    })
                                }
                            } catch (e) {
                            }

                            @if(isset($data['token']))
                            request.setRequestHeader("Authorization", "Bearer {!! $data['token'] !!}");
                            request.setRequestHeader("X-ACCESS-TOKEN", "{!! $data['token'] !!}");
                            @endif
                            @if(isset($data['version']))
                            request.setRequestHeader("X-APP-VERSION", "1.0.0");
                            @endif
                        },
                        success: function (data) {
                            let objData;
                            try {
                                objData = Util.toJson(data);
                            } catch (e) {
                                console.log($(form).serialize());
                                $result.text(
                                    '返回的不是标准的json 格式, 请求地址需要链接接访问 ' + "\n" + $(form).attr('action') + '?' + $(form).serialize()
                                ).show(300);
                                return;
                            }

                            let className;
                            if (objData.status === 0) {
                                className = 'alert-info'
                            } else {
                                className = 'alert-warning'
                            }
                            $result.text(
                                JSON.stringify(Util.toJson(data), null, '  ')
                            ).show(300).removeClass(className).addClass(className).css('color', '#000000');
                        },
                        error: function (data) {
                            $result
                                .text(data.responseText)
                                .show(300)
                                .removeClass(className).addClass('alert-danger');
                        }
                    });
                },
                @include('py-mgr-page::develop.api._validate')
            }, true);
            $('#form_auto').validate(conf);
        });
        </script>
        <script>
        new Vue({
            el: '#app',
            data: {
                requestType: '{!! $data['current']->type !!}',
                url: '{!! url($data['current']->url) !!}',
                url_origin: '{!! url($data['current']->url) !!}',
                variables: {}
            },
            methods: {
                changeVariable: function (e) {
                    const self = this;
                    let name = e.target.name;
                    this.variables[name] = e.target.value;
                    let url = this.url_origin;
                    Object.keys(this.variables).forEach(function (name) {
                        console.log(name);
                        url = url.replace(':' + name, self.variables[name])
                    });
                    this.url = url;
                    $('#form_auto').attr('action', this.url);
                }
            }
        });
        </script>
    @endif
@endsection
