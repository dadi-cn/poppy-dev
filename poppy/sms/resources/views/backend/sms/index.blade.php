@extends('py-mgr-page::backend.tpl.default')
@section('backend-main')
    <div class="layui-card-header">
        短信模板
        <div class="pull-right">
            <a href="{{route_url('py-sms:backend.sms.establish', null, ['_scope'=> $scope])}}" class="layui-btn layui-btn-sm J_iframe">
                创建模板
            </a>
            <a href="{{route_url('py-sms:backend.sms.store')}}" data-width="600" class="layui-btn layui-btn-sm J_iframe">
                短信设置
            </a>
        </div>
    </div>
    <div class="layui-card-body">
        {!! app('poppy.mgr-page.form')->scopes(\Poppy\Sms\Action\Sms::kvPlatform(), $scope) !!}
        <table class="layui-table">
            <tr>
                <th class="w108">平台</th>
                <th class="w108">类型</th>
                <th>短信内容/模版</th>
                <th>操作</th>
            </tr>
            @if (count($items))
                @foreach($items as $item)
                    <tr>
                        <td>{{ \Poppy\Sms\Action\Sms::kvPlatform($item['scope'])}}</td>
                        <td>{{ \Poppy\Sms\Action\Sms::kvType($item['type'])}}</td>
                        <td>{{$item['code']}}</td>
                        <td>
                            <a class="J_iframe" title="编辑"
                                href="{{route_url('py-sms:backend.sms.establish', [$item['scope'].':'.$item['type']])}}">
                                <i class="fa fa-edit text-info"></i>
                            </a>
                            <a title="删除" class="J_request"
                                data-confirm="确认删除 ?"
                                href="{{route('py-sms:backend.sms.destroy', [$item['scope'].':'.$item['type']])}}">
                                <i class="fa fa-times text-danger"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="4">
                        @include('py-mgr-page::backend.tpl._empty')
                    </td>
                </tr>
            @endif
        </table>
    </div>
@endsection