<div class="layui-header">
    <div class="layui-logo" lay-href="{!! route('py-mgr-page:backend.home.cp') !!}">
        @if($logo)
            <img src="{!! $logo !!}" alt="{!! $name !!}">
        @else
            {!! $name ?: 'Poppy Mgr Page' !!}
        @endif
    </div>
    <ul class="layui-nav layui-layout-left">
        <li class="layui-nav-item layadmin-flexible" lay-unselect>
            <a href="javascript:" ew-event="flexible" title="侧边伸缩" class="J_ignore">
                <i class="layui-icon layui-icon-shrink-right" id="LAY_app_flexible"></i>
            </a>
        </li>
        <li class="layui-nav-item layui-hide-xs" lay-unselect>
            <a href="{!! url('/') !!}" target="_blank" title="前台" class="J_ignore">
                <i class="layui-icon layui-icon-website"></i>
            </a>
        </li>
        <li class="layui-nav-item" lay-unselect>
            <a href="javascript:" ew-event="refresh" title="刷新" class="J_ignore">
                <i class="layui-icon layui-icon-refresh-3"></i>
            </a>
        </li>
        @include('py-mgr-page::backend.tpl._header_search')
        {{--
        <li class="layui-nav-item layui-hide-xs" lay-unselect>
            <input type="text" placeholder="搜索..." autocomplete="off" class="layui-input layui-input-search"
                   lay-action="template/search.html?keywords=">
        </li>
        --}}
    </ul>
    <ul class="layui-nav layui-layout-right" data-pjax pjax-ctr="#main" style="padding-right: 4px;">
        <li class="layui-nav-item layui-hide-xs" lay-unselect>
            {!! sys_hook('poppy.mgr-page.html_top_nav') !!}
        </li>
        <li class="layui-nav-item layui-hide-xs" lay-unselect>
            <a href="#" ew-event="note" data-url="{!! route_url('py-mgr-page:backend.home.easy-web', ['note']) !!}" class="J_ignore">
                <i class="layui-icon layui-icon-note"></i>
            </a>
        </li>
        <li class="layui-nav-item layui-hide-xs" lay-unselect="">
            <a ew-event="theme" class="J_ignore"
                data-url="{!! route_url('py-mgr-page:backend.home.easy-web', ['theme'], ['host'=> $host]) !!}">
                <i class="layui-icon layui-icon-theme"></i>
            </a>
        </li>
        <li class="layui-nav-item layui-hide-xs" lay-unselect>
            <a ew-event="fullScreen" title="全屏" class="J_ignore">
                <i class="layui-icon layui-icon-screen-full"></i>
            </a>
        </li>
        <li class="layui-nav-item" lay-unselect style="min-width: 100px;">
            <a href="#" class="J_ignore">
                <cite>{{$_pam->username ?? ''}}</cite>
                <span class="layui-nav-more"></span>
            </a>
            <dl class="layui-nav-child">
                <dd><a ew-href="{!! route('py-mgr-page:backend.home.password') !!}" class="J_ignore">修改密码</a>
                </dd>
                <dd><a href="{!! route('py-mgr-page:backend.home.clear_cache') !!}" class="J_ignore J_request">清空缓存</a></dd>
                <dd style="text-align: center;">
                    <a href="#" ew-event="logout" data-url="{!! route('py-mgr-page:backend.home.logout') !!}" class="J_ignore">退出</a>
                </dd>
            </dl>
        </li>
    </ul>
</div>