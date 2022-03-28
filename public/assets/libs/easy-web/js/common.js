// 以下代码是配置layui扩展模块的目录，每个页面都需要引入
layui.config({
    base : '/assets/libs/easy-web/module/'
}).use(['admin'], function() {
    let admin = layui.admin;
    // 移除loading动画
    setTimeout(function() {
        admin.removeLoading();
    }, window === top ? 600 : 100);

});

// 获取当前项目的根路径，通过获取layui.js全路径截取assets之前的地址
function getProjectUrl() {
    var layuiDir = layui.cache.dir;
    if (!layuiDir) {
        var js = document.scripts, last = js.length - 1, src;
        for (var i = last; i > 0; i--) {
            if (js[i].readyState === 'interactive') {
                src = js[i].src;
                break;
            }
        }
        var jsPath = src || js[last].src;
        layuiDir   = jsPath.substring(0, jsPath.lastIndexOf('/') + 1);
    }
    return layuiDir.substring(0, layuiDir.indexOf('assets'));
}