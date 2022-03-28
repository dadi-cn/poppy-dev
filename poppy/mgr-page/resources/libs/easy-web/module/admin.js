/** EasyWeb iframe v3.1.1 data:2019-03-24 License By http://easyweb.vip */

layui.define(['layer'], function (exports) {
    var $ = layui.jquery;
    var layer = layui.layer;
    var bodyDOM = '.layui-layout-admin>.layui-body';
    var tabDOM = bodyDOM + '>.layui-tab';
    var sideDOM = '.layui-layout-admin>.layui-side>.layui-side-scroll';
    var headerDOM = '.layui-layout-admin>.layui-header';
    var tabFilter = 'admin-pagetabs';
    var navFilter = 'admin-side-nav';
    var themeAdmin = 'theme-admin';  // 自带的主题

    var admin = {
        defaultTheme: 'theme-admin',  // 默认主题
        tableName: window.mgrHost,  // 存储表名
        // 设置侧栏折叠
        flexible: function (expand) {
            var isExapnd = $('.layui-layout-admin').hasClass('admin-nav-mini');
            if (isExapnd == !expand) {
                return;
            }
            if (expand) {
                $('.layui-layout-admin').removeClass('admin-nav-mini');
            } else {
                $('.layui-layout-admin').addClass('admin-nav-mini');
            }
            admin.removeNavHover();
        },
        // 设置导航栏选中
        activeNav: function (url) {
            if (!url) {
                url = window.location.pathname;
                url = url.substring(url.indexOf('/'));
            }
            if (url && url != '') {
                $(sideDOM + '>.layui-nav .layui-nav-item .layui-nav-child dd').removeClass('layui-this');
                $(sideDOM + '>.layui-nav .layui-nav-item').removeClass('layui-this');
                var $a = $(sideDOM + '>.layui-nav a[lay-href="' + url + '"]');
                if ($a && $a.length > 0) {
                    if ($(sideDOM + '>.layui-nav').attr('lay-accordion') == 'true') {
                        $(sideDOM + '>.layui-nav .layui-nav-itemed').removeClass('layui-nav-itemed');
                    }
                    $a.parent().addClass('layui-this');  // 选中当前
                    $a.parent('dd').parents('.layui-nav-child').parent().addClass('layui-nav-itemed');  // 展开所有父级
                    // 适配多系统模式
                    $('ul[lay-filter="' + navFilter + '"]').addClass('layui-hide');
                    var $aUl = $a.parents('.layui-nav');
                    $aUl.removeClass('layui-hide');
                    $(headerDOM + '>.layui-nav>.layui-nav-item').removeClass('layui-this');
                    $(headerDOM + '>.layui-nav>.layui-nav-item>a[nav-bind="' + $aUl.attr('nav-id') + '"]').parent().addClass('layui-this');
                    // 菜单超出屏幕自动滚动
                    var topBeyond = $a.offset().top + $a.outerHeight() + 30 - admin.getPageHeight();
                    var topDisparity = 50 + 65 - $a.offset().top;
                    if (topBeyond > 0) {
                        $(sideDOM).animate({'scrollTop': $(sideDOM).scrollTop() + topBeyond}, 100);
                    } else if (topDisparity > 0) {
                        $(sideDOM).animate({'scrollTop': $(sideDOM).scrollTop() - topDisparity}, 100);
                    }
                } else {
                    // console.warn(url + ' is not in left side');
                }
            } else {
                console.warn('active url is null');
            }
        },
        // 右侧弹出
        popupRight: function (param) {
            if (param.title == undefined) {
                param.title = false;
                param.closeBtn = false;
            }
            if (param.anim == undefined) {
                param.anim = 2;
            }
            if (param.fixed == undefined) {
                param.fixed = true;
            }
            param.isOutAnim = false;
            param.offset = 'r';
            param.shadeClose = true;
            param.area = '336px';
            param.skin = 'layui-layer-adminRight';
            param.move = false;
            return admin.open(param);
        },
        // 封装layer.open
        open: function (param) {
            if (!param.area) {
                param.area = (param.type == 2) ? ['360px', '300px'] : '360px';
            }
            if (!param.skin) {
                param.skin = 'layui-layer-admin';
            }
            if (!param.offset) {
                param.offset = '35px';
            }
            if (param.fixed == undefined) {
                param.fixed = false;
            }
            param.resize = param.resize != undefined ? param.resize : false;
            param.shade = param.shade != undefined ? param.shade : .1;
            var eCallBack = param.end;
            param.end = function () {
                layer.closeAll('tips');
                eCallBack && eCallBack();
            };
            return layer.open(param);
        },
        // 封装ajax请求，返回数据类型为json
        req: function (url, data, success, method) {
            admin.ajax({
                url: url,
                data: data,
                type: method,
                dataType: 'json',
                success: success
            });
        },
        // 封装ajax请求
        ajax: function (param) {
            var successCallback = param.success;
            param.success = function (result, status, xhr) {
                // 判断登录过期和没有权限
                var jsonRs;
                if ('json' == param.dataType.toLowerCase()) {
                    jsonRs = result;
                } else {
                    jsonRs = admin.parseJSON(result);
                }
                jsonRs && (jsonRs = result);
                if (admin.ajaxSuccessBefore(jsonRs, param.url) == false) {
                    return;
                }
                successCallback(result, status, xhr);
            };
            param.error = function (xhr) {
                param.success({code: xhr.status, msg: xhr.statusText});
            };
            param.beforeSend = function (xhr) {
                var headers = admin.getAjaxHeaders(param.url);
                for (var i = 0; i < headers.length; i++) {
                    xhr.setRequestHeader(headers[i].name, headers[i].value);
                }
            };
            $.ajax(param);
        },
        // ajax预处理
        ajaxSuccessBefore: function (res, requestUrl) {
            return true;
        },
        // ajax统一传递header
        getAjaxHeaders: function (requestUrl) {
            var headers = new Array();
            return headers;
        },
        // 判断是否为json
        parseJSON: function (str) {
            if (typeof str == 'string') {
                try {
                    var obj = JSON.parse(str);
                    if (typeof obj == 'object' && obj) {
                        return obj;
                    }
                } catch (e) {
                }
            }
        },
        // 显示加载动画
        showLoading: function (elem, type, opacity) {
            var loader = ['<div class="ball-loader"><span></span><span></span><span></span><span></span></div>', '<div class="rubik-loader"></div>'];
            if (!elem) {
                elem = 'body';
            }
            if (type == undefined) {
                type = 1;
            }
            $(elem).addClass('page-no-scroll');  // 禁用滚动条
            var $loading = $(elem).children('.page-loading');
            if ($loading.length <= 0) {
                $(elem).append('<div class="page-loading">' + loader[type - 1] + '</div>');
                $loading = $(elem).children('.page-loading');
            }
            opacity && $loading.css('background-color', 'rgba(255,255,255,' + opacity + ')');
            $loading.show();
        },
        // 移除加载动画
        removeLoading: function (elem, fade, del) {
            if (!elem) {
                elem = 'body';
            }
            if (fade == undefined) {
                fade = true;
            }
            var $loading = $(elem).children('.page-loading');
            if (del) {
                $loading.remove();
            } else {
                fade ? $loading.fadeOut() : $loading.hide();
            }
            $(elem).removeClass('page-no-scroll');
        },
	    // 缓存临时数据
	    putTempData: function (key, value) {
		    if (value != undefined && value != null) {
			    layui.sessionData(admin.tableName+'-tempData', {key: key, value: value});
		    } else {
			    layui.sessionData(admin.tableName+'-tempData', {key: key, remove: true});
		    }
	    },
	    // 获取缓存临时数据
	    getTempData: function (key) {
		    var tempData = layui.sessionData(admin.tableName+'-tempData');
		    if (tempData) {
			    return tempData[key];
		    } else {
			    return false;
		    }
	    },
        // 滑动选项卡
        rollPage: function (d) {
            var $tabTitle = $(tabDOM + '>.layui-tab-title');
            var left = $tabTitle.scrollLeft();
            if ('left' === d) {
                $tabTitle.animate({'scrollLeft': left - 120}, 100);
            } else if ('auto' === d) {
                var autoLeft = 0;
                $tabTitle.children("li").each(function () {
                    if ($(this).hasClass('layui-this')) {
                        return false;
                    } else {
                        autoLeft += $(this).outerWidth();
                    }
                });
                $tabTitle.animate({'scrollLeft': autoLeft - 120}, 100);
            } else {
                $tabTitle.animate({'scrollLeft': left + 120}, 100);
            }
        },
        // 刷新当前tab
        refresh: function (url) {
            var $iframe;
            if (!url) {
                $iframe = $(tabDOM + '>.layui-tab-content>.layui-tab-item.layui-show>.admin-iframe');
                if (!$iframe || $iframe.length <= 0) {
                    $iframe = $(bodyDOM + '>div>.admin-iframe');
                }
            } else {
                $iframe = $(tabDOM + '>.layui-tab-content>.layui-tab-item>.admin-iframe[lay-id="' + url + '"]');
                if (!$iframe || $iframe.length <= 0) {
                    $iframe = $(bodyDOM + '>.admin-iframe');
                }
            }
            if ($iframe && $iframe[0]) {
                let $reload = $($iframe[0].contentWindow.document.getElementById('filter-box-reload'));
                if ($reload.length){
                    let layerobj = $iframe[0].contentWindow.window.layer;
                    if (layerobj) {
                        layerobj.closeAll();
                    }
                    $reload.trigger('click');
                } else {
                    $iframe[0].contentWindow.location.reload(true);
                }
            } else {
                console.warn(url + ' is not found');
            }
        },
        // 关闭当前选项卡
        closeThisTabs: function (url) {
            admin.closeTabOperNav();
            var $title = $(tabDOM + '>.layui-tab-title');
            if (!url) {
                if ($title.find('li').first().hasClass('layui-this')) {
                    layer.msg('主页不能关闭', {icon: 2});
                    return;
                }
                $title.find('li.layui-this').find(".layui-tab-close").trigger("click");
            } else {
                if (url == $title.find('li').first().attr('lay-id')) {
                    layer.msg('主页不能关闭', {icon: 2});
                    return;
                }
                $title.find('li[lay-id="' + url + '"]').find(".layui-tab-close").trigger("click");
            }
        },
        // 关闭其他选项卡
        closeOtherTabs: function (url) {
            if (!url) {
                $(tabDOM + '>.layui-tab-title li:gt(0):not(.layui-this)').find('.layui-tab-close').trigger('click');
            } else {
                $(tabDOM + '>.layui-tab-title li:gt(0)').each(function () {
                    if (url != $(this).attr('lay-id')) {
                        $(this).find('.layui-tab-close').trigger('click');
                    }
                });
            }
            admin.closeTabOperNav();
        },
        // 关闭所有选项卡
        closeAllTabs: function () {
            $(tabDOM + '>.layui-tab-title li:gt(0)').find('.layui-tab-close').trigger('click');
            $(tabDOM + '>.layui-tab-title li:eq(0)').trigger('click');
            admin.closeTabOperNav();
        },
        // 关闭选项卡操作菜单
        closeTabOperNav: function () {
            $('.layui-icon-down .layui-nav .layui-nav-child').removeClass('layui-show');
        },
        // 设置主题
        changeTheme: function (theme) {
            if (theme) {
                layui.data(admin.tableName, {key: 'theme', value: theme});
                if (themeAdmin == theme) {
                    theme = undefined;
                }
            } else {
                layui.data(admin.tableName, {key: 'theme', remove: true});
            }
            admin.removeTheme(top);
            !theme || top.layui.link(admin.getThemeDir() + theme + '.css', theme);
            var ifs = top.window.frames;
            for (var i = 0; i < ifs.length; i++) {
                var tif = ifs[i];
                try {
                    admin.removeTheme(tif);
                } catch (e) {
                }
                if (theme && tif.layui) {
                    tif.layui.link(admin.getThemeDir() + theme + '.css', theme);
                }
                // iframe下还有iframe的情况
                var sifs = tif.frames;
                for (var j = 0; j < sifs.length; j++) {
                    var stif = sifs[j];
                    try {
                        admin.removeTheme(stif);
                    } catch (e) {
                    }
                    if (theme && stif.layui) {
                        stif.layui.link(admin.getThemeDir() + theme + '.css', theme);
                    }
                }
            }
        },
        // 移除主题
        removeTheme: function (w) {
            if (!w) {
                w = window;
            }
            if (w.layui) {
                var themeId = 'layuicss-theme';
                w.layui.jquery('link[id^="' + themeId + '"]').remove();
            }
        },
        // 获取主题目录
        getThemeDir: function () {
            return layui.cache.base + 'theme/';
        },
        // 关闭iframe所在的layer弹窗
        closeThisDialog: function () {
            parent.layer.close(parent.layer.getFrameIndex(window.name));
        },
        // 关闭所在的弹窗
        closeDialog: function (elem) {
            var id = $(elem).parents('.layui-layer').attr('id').substring(11);
            layer.close(id);
        },
        // 让当前的ifram弹层自适应高度
        iframeAuto: function () {
            parent.layer.iframeAuto(parent.layer.getFrameIndex(window.name));
        },
        // 获取浏览器高度
        getPageHeight: function () {
            return document.documentElement.clientHeight || document.body.clientHeight;
        },
        // 获取浏览器宽度
        getPageWidth: function () {
            return document.documentElement.clientWidth || document.body.clientWidth;
        },
        // 关闭导航菜单折叠悬浮效果
        removeNavHover: function () {
            $('.admin-nav-hover>.layui-nav-child').css({
                'top': 'auto',
                'max-height': 'none',
                'overflow': 'auto'
            });
            $('.admin-nav-hover').removeClass('admin-nav-hover');
        },
        // 自动计算导航菜单悬浮的样式
        setNavHoverCss: function ($that) {
            var $nav = $('.admin-nav-hover>.layui-nav-child');
            if ($that && $nav.length > 0) {
                var isOver = ($that.offset().top + $nav.outerHeight()) > window.innerHeight;  // 是否溢出屏幕
                if (isOver) {
                    var newTop = $that.offset().top - $nav.outerHeight() + $that.outerHeight();
                    if (newTop < 50) {
                        var pageHeight = admin.getPageHeight();
                        if ($that.offset().top < pageHeight / 2) {
                            $nav.css({
                                'top': '50px',
                                'max-height': pageHeight - 50 + 'px',
                                'overflow': 'auto'
                            });
                        } else {
                            $nav.css({
                                'top': $that.offset().top,
                                'max-height': pageHeight - $that.offset().top,
                                'overflow': 'auto'
                            });
                        }
                    } else {
                        $nav.css('top', newTop);
                    }
                } else {
                    $nav.css('top', $that.offset().top);
                }
                isHover = true;
            }
        }
    };

    // admin提供的事件
    admin.events = {
        // 折叠侧导航
        flexible: function (e) {
            var expand = $('.layui-layout-admin').hasClass('admin-nav-mini');
            admin.flexible(expand);
        },
        // 刷新主体部分
        refresh: function () {
            admin.refresh();
        },
        // 后退
        back: function () {
            history.back();
        },
        // 设置主题
        theme: function () {
            var url = $(this).attr('data-url');
            admin.popupRight({
                id: 'layer-theme',
                type: 2,
                content: url ? url : '/backend/system/easy-web/theme'
            });
        },
        // 打开便签
        note: function () {
            var url = $(this).attr('data-url');
            admin.popupRight({
                id: 'layer-note',
                title: '便签',
                type: 2,
                closeBtn: false,
                content: url ? url : '/backend/system/easy-web/note'
            });
        },
        // 打开消息
        message: function () {
            var url = $(this).attr('data-url');
            admin.popupRight({
                id: 'layer-notice',
                type: 2,
                content: url ? url : 'page/tpl/tpl-message.html'
            });
        },
        // 打开修改密码弹窗
        psw: function () {
            var url = $(this).attr('data-url');
            admin.open({
                id: 'pswForm',
                type: 2,
                title: '修改密码',
                shade: 0,
                content: url ? url : 'page/tpl/tpl-password.html'
            });
        },
        // 退出登录
        logout: function () {
            var url = $(this).attr('data-url');
            layer.confirm('确定要退出登录吗？', {
                title: '温馨提示',
                skin: 'layui-layer-admin'
            }, function () {
                location.replace(url ? url : '/');
            });
        },
        // 全屏
        fullScreen: function (e) {
            var ac = 'layui-icon-screen-full', ic = 'layui-icon-screen-restore';
            var ti = $(this).find('i');

            var isFullscreen = document.fullscreenElement || document.msFullscreenElement || document.mozFullScreenElement || document.webkitFullscreenElement || false;
            if (isFullscreen) {
                var efs = document.exitFullscreen || document.webkitExitFullscreen || document.mozCancelFullScreen || document.msExitFullscreen;
                if (efs) {
                    efs.call(document);
                } else if (window.ActiveXObject) {
                    var ws = new ActiveXObject('WScript.Shell');
                    ws && ws.SendKeys('{F11}');
                }
                ti.addClass(ac).removeClass(ic);
            } else {
                var el = document.documentElement;
                var rfs = el.requestFullscreen || el.webkitRequestFullscreen || el.mozRequestFullScreen || el.msRequestFullscreen;
                if (rfs) {
                    rfs.call(el);
                } else if (window.ActiveXObject) {
                    var ws = new ActiveXObject('WScript.Shell');
                    ws && ws.SendKeys('{F11}');
                }
                ti.addClass(ic).removeClass(ac);
            }
        },
        // 左滑动tab
        leftPage: function () {
            admin.rollPage("left");
        },
        // 右滑动tab
        rightPage: function () {
            admin.rollPage();
        },
        // 关闭当前选项卡
        closeThisTabs: function () {
            admin.closeThisTabs();
        },
        // 关闭其他选项卡
        closeOtherTabs: function () {
            admin.closeOtherTabs();
        },
        // 关闭所有选项卡
        closeAllTabs: function () {
            admin.closeAllTabs();
        },
        // 关闭当前iframe弹窗
        closeDialog: function () {
            admin.closeThisDialog();
        },
        // 关闭当前页面弹窗
        closePageDialog: function () {
            admin.closeDialog(this);
        }
    };

    // 所有ew-event
    $('body').on('click', '*[ew-event]', function () {
        var event = $(this).attr('ew-event');
        var te = admin.events[event];
        te && te.call(this, $(this));
    });

    // 所有ew-href处理
    $('body').on('click', '*[ew-href]', function () {
        var url = $(this).attr('ew-href');
        var title = $(this).text();
        top.layui.index.openTab({
            title: title,
            url: url
        });
    });

    // 所有lay-tips处理
    $('body').on('mouseenter', '*[lay-tips]', function () {
        var tipText = $(this).attr('lay-tips');
        var dt = $(this).attr('lay-direction');
        var bgColor = $(this).attr('lay-bg');
        layer.tips(tipText, this, {tips: [dt || 3, bgColor || '#333333'], time: -1});
    }).on('mouseleave', '*[lay-tips]', function () {
        layer.closeAll('tips');
    });

    // 侧导航折叠状态下鼠标经过显示提示
    var isHover = false;
    $('body').on('mouseenter', '.layui-layout-admin.admin-nav-mini .layui-side .layui-nav .layui-nav-item>a', function () {
        if (admin.getPageWidth() > 750) {
            var $that = $(this);
            $('.admin-nav-hover>.layui-nav-child').css('top', 'auto');
            $('.admin-nav-hover').removeClass('admin-nav-hover');
            $that.parent().addClass('admin-nav-hover');
            var $nav = $('.admin-nav-hover>.layui-nav-child');
            if ($nav.length > 0) {
                admin.setNavHoverCss($that);
            } else {
                var tipText = $that.find('cite').text();
                layer.tips(tipText, $that, {tips: [2, '#333333'], time: -1});
            }
        }
    }).on('mouseleave', '.layui-layout-admin.admin-nav-mini .layui-side .layui-nav .layui-nav-item>a', function () {
        layer.closeAll('tips');
    });

    // 鼠标离开侧导航关闭折叠浮窗
    $('body').on('mouseleave', '.layui-layout-admin.admin-nav-mini .layui-side', function () {
        isHover = false;
        setTimeout(function () {
            if (!isHover) {
                admin.removeNavHover();
            }
        }, 500);
    });

    $('body').on('mouseenter', '.layui-layout-admin.admin-nav-mini .layui-side .layui-nav .layui-nav-item.admin-nav-hover .layui-nav-child', function () {
        isHover = true;
    });

    // 帮助鼠标右键菜单完成点击空白关闭的功能
    if (!layui.contextMenu) {
        $(document).off('click.ctxMenu').on('click.ctxMenu', function () {
            var ifs = top.window.frames;
            for (var i = 0; i < ifs.length; i++) {
                var tif = ifs[i];
                try {
                    tif.layui.jquery('.ctxMenu').remove();
                } catch (e) {
                }
            }
            top.layui.jquery('.ctxMenu').remove();
        });
    }

    // 加载缓存的主题
    var cacheSetting = layui.data(admin.tableName);
    if (cacheSetting && cacheSetting.theme) {
        (cacheSetting.theme == themeAdmin) || layui.link(admin.getThemeDir() + cacheSetting.theme + '.css', cacheSetting.theme);
    } else if (themeAdmin != admin.defaultTheme) {
        layui.link(admin.getThemeDir() + admin.defaultTheme + '.css', admin.defaultTheme);
    }

    exports('admin', admin);
});
