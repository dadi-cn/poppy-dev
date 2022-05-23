@extends('py-mgr-page::tpl.develop')
@section('develop-main')
    <div class="row">
        <div class="col-sm-12">
            {!! Form::open([ 'id'=> 'form_auto','data-ajax'=>"true"]) !!}
            {!! Form::hidden('guard', $type) !!}
            <div class="layui-form-item">
                <label for="passport" class="validation">账号</label>
                ( String ) [passport]
                <input class="layui-input" name="passport" data-rule-required="true" type="text" id="passport">
            </div>
            <div class="layui-form-item">
                <label for="password">密码</label>
                ( String ) [password]
                <input class="layui-input" name="password" type="text" id="password">
            </div>
            <div class="layui-form-item">
                <label for="captcha">验证码 {!! Form::tip('后端可以设置用户绕过验证码设置') !!}</label>
                ( String ) [captcha]
                <input class="layui-input" name="captcha" type="text" id="captcha">
            </div>
            <div class="layui-form-item">
                <button class="layui-btn btn-sm J_validate" type="submit" id="submit">登录</button>
            </div>
            {!! Form::close() !!}
        </div>
        <pre id="J_result" style="display: none;color: #0a0a0a" class="layui-elem-quote layui-quote-nm mt8"></pre>
    </div>
    <script>
	$(function () {
		let conf = Util.validateConfig({
			submitHandler : function (form) {
				let $result = $('#J_result');
				$result.text(
					'进行中...'
				).css('color', 'grey');
				$(form).ajaxSubmit({
					beforeSend : function (request) {
						let headerStr = '{!! $headers ?? '' !!}'
						try {
							let headers = JSON.parse(headerStr);
							if (typeof headers == "object") {
								Object.keys(headers).forEach((key) => {
									request.setRequestHeader(key, headers[key])
								})
							}
						} catch (e) {
						}
					},
					success    : function (data) {
						let objData;
						try {
							objData = Util.toJson(data);
						} catch (e) {
							$result.text(
								'返回的不是标准的json 格式, 请求地址需要链接接访问 ' + "\n" + $(form).attr('action') + '?' + $(form).serialize()
							).show(300);
							return;
						}
						if (objData.status !== 0) {
							$result
								.text(objData.message)
								.show(300)
								.removeClass(className).addClass('alert-danger');
						}
						window.top.location.reload();
						window.close();
					},
					error      : function (data) {
						$result
							.text(data.responseText)
							.show(300)
							.removeClass(className).addClass('alert-danger');
					}
				});
			},
		}, true);
		$('#form_auto').validate(conf);
	});
    </script>
@endsection