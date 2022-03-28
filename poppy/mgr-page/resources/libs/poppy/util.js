/**
 * Poppy 的核心函数类 (全局)
 */
if (typeof jQuery === 'undefined') {
    alert('You need import jquery before poppy global util');
}

if (typeof Util !== 'object') {
    Util = {};
}


( function($) {
    if (typeof $.validator !== 'undefined') {

        $.validator.addMethod("mobile", function(phone_number, element) {
            return this.optional(element) || Util.isMobile(phone_number);
        }, "Please specify a valid mobile number");

        $.validator.addMethod("email", function(mail, element) {
            mail = mail.replace(/\(|\)|\s+|-/g, "");
            return this.optional(element) || Util.isEmail(mail);
        }, "Please specify a valid email address");

        $.validator.addMethod("qq", function(qq_number, element) {
            qq_number = qq_number.replace(/\(|\)|\s+|-/g, "");
            return this.optional(element) || qq_number.length > 4 &&
                qq_number.match(/^[1-9]\d{3,10}$/);
        }, "Please specify a valid qq number");

        // 中国电话号码的验证
        $.validator.addMethod("phone", function(value, element) {
            return this.optional(element) || /^(([0\+]\d{2,3}-?)?(0\d{2,3})-?)?(\d{7,8})(-(\d{3,}))?$/.test(value);
        }, "Please specify a valid phone number.");

        $.validator.addMethod("ipv4", function(value, element) {
            return this.optional(element)
                ||
                /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(value);
        }, "Please input a valid ipv4 address.");

        // 中国电话号码和手机的验证
        $.validator.addMethod("phone_mobile", function(value, element) {
            let phone_number = value.replace(/\(|\)|\s+|-/g, "");
            return ( this.optional(element) || /^(([0\+]\d{2,3}-?)?(0\d{2,3})-?)?(\d{7,8})(-(\d{3,}))?$/.test(value) )
                ||
                ( this.optional(element) || Util.isMobile(phone_number) );
        }, "Please specify a valid phone number.");

        // 中文身份证验证
        $.validator.addMethod("chId", function(chId, element) {
            let iW   = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2, 1];
            let iSum = 0;
            let iC, iVal;
            for (let i = 0; i < 17; i++) {
                iC   = chId.charAt(i);
                iVal = parseInt(iC, 10);
                iSum += iVal * iW[i];
            }
            let iJYM = iSum % 11;
            let sJYM = '';
            if (iJYM === 0) sJYM = "1";
            else if (iJYM === 1) sJYM = "0";
            else if (iJYM === 2) sJYM = "x";
            else if (iJYM === 3) sJYM = "9";
            else if (iJYM === 4) sJYM = "8";
            else if (iJYM === 5) sJYM = "7";
            else if (iJYM === 6) sJYM = "6";
            else if (iJYM === 7) sJYM = "5";
            else if (iJYM === 8) sJYM = "4";
            else if (iJYM === 9) sJYM = "3";
            else if (iJYM === 10) sJYM = "2";
            let cCheck = chId.charAt(17).toLowerCase();
            return sJYM && cCheck == sJYM;
        }, "Please specify a valid chinese id");

        // 不允许含有空格
        $.validator.addMethod("noSpace", function(value, element) {
            return !/\s+/.test(value);
        }, "Please do not insert space");

        /* 小数验证，小数点位数按照max参数的小数点位数进行判断
         * 不能为空、只能输入数字 */
        $.validator.addMethod("decimal", function(value, element, params) {
            if (!value) {
                return true;
            }
            if (isNaN(params[0])) {
                return false;
            }
            if (isNaN(params[1])) {
                return false;
            }
            if (isNaN(params[2])) {
                return false;
            }
            if (isNaN(value)) {
                return false;
            }
            if (typeof ( value ) == undefined || value == "") {
                return false;
            }
            let min     = Number(params[0]);
            let max     = Number(params[1]);
            let testVal = Number(value);
            if (typeof ( params[2] ) == undefined || params[2] == 0) {
                let regX = /^\d+$/;
            } else {
                let regxStr = "^\\d+(\\.\\d{1," + params[2] + "})?$";
                let regX    = new RegExp(regxStr);
            }
            return this.optional(element) || ( regX.test(value) && testVal >= min && testVal <= max );
        }, $.validator.format("请正确输入在{0}到{1}之间，最多只保留小数点后{2}的数值"));

        $.validator.addMethod(
            "regex",
            function(value, element, regexp) {
                let re = new RegExp(regexp);
                return this.optional(element) || re.test(value);
            },
            "Please check your input."
        );

        $.validator.addMethod("alphanumeric", function(value, element) {
            return this.optional(element) || /^[\w.]+$/i.test(value);
        }, "Letters, numbers, and underscores only please");

        $.validator.addMethod("alpha", function(value, element) {
            return this.optional(element) || /^[a-zA-Z]+$/i.test(value);
        }, "Letters, numbers, and underscores only please");

        $.validator.addMethod("alpha_dash", function(value, element) {
            return this.optional(element) || /^[a-zA-Z_]+$/i.test(value);
        }, "Letters, numbers, and underscores only please");

        $.extend($.validator.messages, {
            required : "必须填写",
            remote : "请修正此栏位",
            email : "请输入有效的电子邮件",
            qq : '请输入正确的QQ号',
            mobile : '请输入正确的手机号',
            phoneZh : '请输入正确的固定电话号码',
            phoneAmobile : '请输入正确的固话或者手机号',
            url : "请输入有效的网址",
            date : "请输入有效的日期",
            dateISO : "请输入有效的日期 (YYYY-MM-DD)",
            number : "请输入正确的数字",
            digits : "只可输入数字",
            creditcard : "请输入有效的信用卡号码",
            equalTo : "你的输入不相同",
            extension : "请输入有效的后缀",
            maxlength : $.validator.format("最多 {0} 个字"),
            minlength : $.validator.format("最少 {0} 个字"),
            eqlength : $.validator.format("请输入 {0} 长度的字符!"),
            rangelength : $.validator.format("请输入长度为 {0} 至 {1} 之间的字串"),
            range : $.validator.format("请输入 {0} 至 {1} 之间的数值"),
            max : $.validator.format("请输入不大于 {0} 的数值"),
            min : $.validator.format("请输入不小于 {0} 的数值"),
            ipv4 : '请输入正确的IP地址',
            chId : '请输入正确的身份证信息',
            noSpace : '请不要在此输入空格',
            alpha : '请输入字母',
            alpha_dash : '请输入字母或下划线',
            alphanumeric : '请输入字母, 数字, 下划线的组合!',
            decimal : '请正确输入在{0}到{1}之间，最多只保留小数点后{2}的数值',
            step : $.validator.format("请输入 {0} 的整数倍值"),
            regex : '请检查输入是否符合规则'
        });
    }
} )(jQuery);


( function() {
    'use strict';

    /**
     * 点击加入收藏
     * @param id
     */
    Util.addFav = function(id) {
        $(id).on('click', function() {
            if (document.all) {
                try {
                    window.external.addFavorite(window.location.href, document.title);
                } catch (e) {
                    alert("加入收藏失败，请使用Ctrl+D进行添加");
                }
            } else if (window.sidebar) {
                window.sidebar.addPanel(document.title, window.location.href, "");
            } else {
                alert("加入收藏失败，请使用Ctrl+D进行添加");
            }
        })
    };


    /**
     * 返回浏览器的版本和ie的判定
     * @returns {{version: *, safari: boolean, opera: boolean, msie: boolean, mozilla: boolean, is_ie8: boolean, is_ie9: boolean, is_ie10: boolean, is_rtl: boolean}}
     */
    Util.browser = function() {
        let userAgent = navigator.userAgent.toLowerCase();
        return {
            version : ( userAgent.match(/.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/) || [0, '0'] )[1],
            safari : /webkit/.test(userAgent),
            opera : /opera/.test(userAgent),
            msie : /msie/.test(userAgent) && !/opera/.test(userAgent),
            mozilla : /mozilla/.test(userAgent) && !/(compatible|webkit)/.test(userAgent),
            is_ie8 : !!userAgent.match(/msie 8.0/),
            is_ie9 : !!userAgent.match(/msie 9.0/),
            is_ie10 : !!userAgent.match(/msie 10.0/),
            is_wechat : !!userAgent.match(/micromessenger/),
            is_rtl : $('body').css('direction') === 'rtl'
        }
    };

    /*
     * 提示信息
     * @params word  String 提示信息
     * */
    Util.splash = function(resp, append_callback) {
        let obj_resp = Util.toJson(resp);
        let obj_data = {
            _callback : '',
            _show : 'tip',
            _time : 0
        };
        let obj_init = {
            message : 'No Message Send By Server!',
            status : 1
        };

        obj_resp = $.extend(obj_init, obj_resp);
        if (typeof obj_resp.data !== 'undefined') {
            obj_data = $.extend(obj_data, obj_resp.data);
        }
        if (obj_data._show === 'tip') {
            obj_data._time = parseInt(obj_data._time) ? parseInt(obj_data._time) : 0;
            let jump_time;
            if (!obj_data._time && obj_data._location) {
                jump_time = 800;
            }
            if (!obj_data._time && obj_data._reload) {
                jump_time = 800;
            }
            if (!obj_data._time && obj_data._reload_opener) {
                jump_time = 800;
            }
            if (typeof window.mobile !== 'undefined') {
                layer.msg(obj_resp.message, {
                    time : 3000
                })
            } else {
                setTimeout(function() {
                    if (obj_resp.status === 0) {
                        // success icon
                        // layer.msg(obj_resp.message, {icon : 1});
                        layer.msg(obj_resp.message);
                    } else {
                        // fail icon
                        // layer.msg(obj_resp.message, {icon : 2});
                        layer.msg(obj_resp.message);
                    }
                }, jump_time);
            }
        }

        if (obj_data._show === 'dialog') {
            delete obj_resp._show;
            let conf  = {};
            let title = !conf.hasOwnProperty('title') ? resp.message : conf.title;
            let content;
            if (obj_data._append) {
                content = obj_data._append;
            } else {
                content = title
            }
            layer.open({
                title : title,
                content : content,
                shadeClose : true
            });
            return false;
        }

        if (obj_data.show === 'callback' || obj_data.callback) {
            let func = obj_data.callback;
            setTimeout(function() {
                eval(func + ";");
            }, obj_data.time);
        }

        if (obj_data._reload) {
            let $winPjax = window.$('form[data-pjax]');
            if ($winPjax.length) {
                $winPjax.submit();
            } else {
                setTimeout(function() {
                    if (Util.browser().is_wechat) {
                        window.location.search = '?v=' + Date.now();
                    } else {
                        let $reload = $('#filter-box-reload');
                        if ($reload.length){
                            $reload.trigger('click');
                        } else {
                            window.location.reload()
                        }
                    }
                }, obj_data.time);
                return;
            }
        }
        if (obj_data._top_reload) {
            if ($('#filter-box-reload').length) {
                $('#filter-box-reload').trigger('click')
                return;
            }
            if (typeof top.window.layui !== 'undefined' && typeof top.window.layui.admin !== 'undefined') {
                top.window.layui.admin.events.refresh();
            } else {
                if (typeof top.window.$ != 'undefined') {
                    let $topPjax = top.window.$('form[data-pjax]');
                    if ($topPjax.length) {
                        $topPjax.submit();
                    } else {
                        setTimeout(function() {
                            top.window.location.reload()
                        }, obj_data.time);
                    }
                } else {
                    setTimeout(function() {
                        top.window.location.reload()
                    }, obj_data.time);
                }
            }
        }

        if (obj_data._location) {
            setTimeout(function() {
                window.location.href = obj_data._location;
            }, obj_data.time);
        }

        if (obj_data._top_location) {
            setTimeout(function() {
                top.window.location.href = obj_data._top_location;
            }, obj_data.time);
        }

        if (obj_data._reload_opener) {
            setTimeout(function() {
                if (typeof top.layui !== 'undefined' && typeof top.layui.admin !== 'undefined') {
                    top.layui.admin.refresh();
                } else {
                    top.location.reload();
                }
            }, obj_data._time);
        }

        if (obj_data._iframe_close) {
            setTimeout(function() {
                let opener = Util.opener(obj_data._iframe_close);
                opener.iframe.close();
            }, obj_data._time);
        }

        if (obj_data._captcha_reload) {
            $('.J_captcha').trigger('click');
        }

        if (obj_data._pjax) {
            let $topPjax = top.window.$('form[data-pjax]');
            if ($topPjax.length) {
                $topPjax.submit();
            } else {
                $('form[data-pjax]').submit();
            }
        }

        if (typeof append_callback === 'function') {
            append_callback(obj_resp);
        }
    };
    /**
     * 字串转 json
     * @param resp
     * @returns {*}
     */
    Util.toJson = function(resp) {
        let objResp;
        if (typeof resp === 'object') {
            objResp = resp;
        } else {
            if ($.trim(resp) === '') {
                objResp = {};
            } else {
                objResp = $.parseJSON(resp);
            }
        }
        return objResp;
    };

    /**
     * 获取 openner
     * @param workspace
     * @returns {*}
     */
    Util.opener = function(workspace) {
        let opener = top.frames[workspace];
        if (typeof opener === 'undefined') {
            opener = top;
        }
        return opener;
    };

    /**
     * 按钮交互
     * @param btn_selector
     * @param data
     * @param error_submit
     */
    Util.buttonInteraction = function(btn_selector, data, error_submit) {
        let objData;
        if (typeof data == 'undefined' || !isNaN(parseInt(data))) {
            $(btn_selector).attr('disabled', true);
            if (!isNaN(parseInt(data))) {
                let time = parseInt(data);
                setTimeout(function() {
                    $(btn_selector).attr('disabled', false);
                }, time * 1000);
            }
        }
        objData = Util.toJson(data);
        if (objData.status == 'error') {
            $(btn_selector).attr('disabled', false);
            if (typeof error_submit != 'undefined') {
                $(btn_selector).html(error_submit);
            }
        }
    };


    /**
     * 事件请求, 使用post 方法
     * @param $this
     * @param splash_func
     * @returns {boolean}
     */
    Util.requestEvent = function($this, splash_func) {
        // confirm
        let str_confirm = $this.attr('data-confirm');
        if (str_confirm === 'true') {
            str_confirm = '您确定删除此条目 ?';
        }
        if (str_confirm) {
            if (!confirm(str_confirm)) {
                layer.closeAll();
                return false;
            }
        }
        let append = $this.attr('data-append');
        let data   = Util.appendToObj(append);

        let condition_str = $this.attr('data-condition');
        let condition     = Util.conditionToObj(condition_str);
        for (let i in data) {
            if (condition.hasOwnProperty(i) && !data.hasOwnProperty(i)) {
                splash_func({
                    'status' : 1,
                    'message' : condition[i]
                });
                return false;
            }
        }

        let update = $this.attr('data-update');
        if (update) {
            data._update = update;
        }

        // do request
        let href = $this.attr('href');
        if (!href) {
            href = $this.attr('data-url');
        }
        data._token = Util.csrfToken();
        $.post(href, data, splash_func);
    };

    /**
     * 获取页面中的 csrf token
     * @returns {*|jQuery}
     */
    Util.csrfToken = function() {
        return $('meta[name="csrf-token"]').attr('content');
    };


    /**
     * 追加元素到对象
     * @param append
     * @returns {{}}
     */
    Util.appendToObj = function(append) {
        let data = {};
        if (append) {
            let appends = [append];
            if (append.indexOf(',') >= 0) {
                appends = append.split(',');
            }
            for (let i in appends) {
                let item = appends[i];
                let re   = /(.*)\((.*)\)/;
                let m;

                if (( m = re.exec(item) ) !== null) {
                    if (m.index === re.lastIndex) {
                        re.lastIndex++;
                    }
                }

                if (m[1].indexOf('checked') >= 0 && m[1].indexOf('radio') < 0) {
                    let id_array = [];
                    $(m[1]).each(function() {
                        id_array.push($(this).val());//向数组中添加元素
                    });
                    data[m[2]] = id_array;//将数组元素连接起来以构建一个字符串
                } else {
                    data[m[2]] = $(m[1]).val();
                }

            }
        }
        return data;
    };


    /**
     * 条件转换
     * @param append
     * @returns {{}}
     */
    Util.conditionToObj = function(append) {
        let data = {};
        if (append) {
            let appends = append.split(',');
            for (let i in appends) {
                let item = appends[i];
                let re   = /(.*):(.*)/;
                let m;
                if (( m = re.exec(item) ) !== null) {
                    if (m.index === re.lastIndex) {
                        re.lastIndex++;
                    }
                    data[m[1]] = m[2];
                }
            }
        }
        return data;
    };

    /**
     * 对象转换成url地址
     * @param obj
     * @param url
     * @returns {*}
     */
    Util.objToUrl = function(obj, url) {
        let str = "";
        for (let key in obj) {
            if (str != "") {
                str += "&";
            }
            str += key + "=" + obj[key];
        }
        if (typeof url != 'undefined') {
            return url.indexOf('?') >= 0 ? url + '&' + str : url + '?' + str;
        } else {
            return str;
        }
    };

    /**
     * 预览图像地址
     * @param imgSrc
     * @param w
     * @returns {boolean}
     */
    Util.imagePopupShow = function(imgSrc, w) {
        if (!imgSrc) {
            Util.splash({
                status : 1,
                message : '没有图像文件'
            });
            return false;
        }
        Util.imageSize(imgSrc, _popup_show);

        /**
         * imgObj.width   imgObj.height  imgObj.url
         * @param imgObj
         * @private
         */
        function _popup_show(imgObj) {
            let _w = imgObj.width;
            let _h = imgObj.height;
            if (typeof w != 'undefined' && imgObj.width > w) {
                _w = w;
                _h = parseInt(_w * imgObj.height / imgObj.width);
            }
            let imgStr = '<img src="' + imgObj.url + '" width="' + _w + '" height="' + _h + '" />';
            layer.open({
                title : '图片预览',
                content : imgStr,
                area : [( _w + 40 ) + 'px', ( _h + 80 ) + 'px']
            });
        }
    };


    /**
     * 计算图片的大小
     * @param sUrl
     * @param fCallback
     */
    Util.imageSize = function(sUrl, fCallback) {
        let img = new Image();
        img.src = sUrl + '?t=' + Math.random();    //IE下，ajax会缓存，导致onreadystatechange函数没有被触发，所以需要加一个随机数
        if (Util.browser().msie) {
            img.onreadystatechange = function() {
                if (this.readyState == "loaded" || this.readyState == "complete") {
                    fCallback({width : img.width, height : img.height, url : sUrl});
                }
            };
        } else if (Util.browser().mozilla || Util.browser().safari || Util.browser().opera) {
            img.onload = function() {
                fCallback({width : img.width, height : img.height, url : sUrl});
            };
        } else {
            fCallback({width : img.width, height : img.height, url : sUrl});
        }
    };

    /**
     * 通过 post 的方法异步读取数据
     * @param targetPhp
     * @param queryString
     * @param success
     * @param method
     */
    Util.makeRequest = function(targetPhp, queryString, success, method) {
        if (typeof queryString === 'string') {
            queryString += queryString.indexOf('&') < 0
                ? '_token=' + Util.csrfToken()
                : '&_token=' + Util.csrfToken();
        }
        if (typeof queryString === 'object') {
            queryString['_token'] = Util.csrfToken();
        }
        if (typeof queryString === 'undefined') {
            queryString = {
                '_token' : Util.csrfToken()
            }
        }
        if (typeof success === 'undefined') {
            success = Util.splash;
        }
        if (typeof method === 'undefined') {
            method = 'post';
        }
        $.ajax({
            async : false,
            cache : false,
            type : method,
            url : targetPhp,
            data : queryString,
            success : function(data) {
                let obj_data = Util.toJson(data);
                success(obj_data);
            }
        });
    };

    /**
     * 验证配置
     * @param rules
     * @url https://jqueryvalidation.org/validate/
     * @url https://jqueryvalidation.org/valid/
     * @url https://vadikom.com/demos/poshytip/
     */
    Util.validateConfig = function(rules) {
        let config = {
            ignore : '.ignore,[contenteditable=\'true\']',
            // debug : true,
            submitHandler : function(form) {
                layer.load(3, {
                    shade : [0.03, '#000000']
                });
                $(form).ajaxSubmit({
                    success : function(resp) {
                        layer.closeAll();
                        Util.splash(resp);
                    }
                });
            },
            // errorClass : 'error',
            // validClass : 'valid',
            onkeyup : function(element) {
                // console.log('on-keyup');
                let elem = $(element);
                elem.valid();
            },
            onfocusout : function(element) {
                // console.log('on-foucus-out');
                let elem = $(element);
                elem.valid();
            },
            highlight : function(element, errorClass, validClass) {
                $(element).closest('.layui-form-auto-field').addClass('layui-form-error');
            },
            unhighlight : function(element) {
                $(element).closest('.layui-form-auto-field').removeClass('layui-form-error');
            },
            success : function(label, element) {
                let elem = $(element);
                elem.poshytip('disable');
                elem.poshytip('destroy');
            },
            errorPlacement : function(error, element) {
                let elem = $(element);
                if (elem.prop('type') === 'file' || elem.prop('type') === 'textarea') {
                    elem = $(element).parents('.layui-form-auto-field');
                    if (!elem) {
                        alert(error);
                        return;
                    }
                }
                if (!error.is(':empty')) {
                    if (elem.data('start-poshy') && error.text()) {
                        elem.poshytip('update', error.text());
                    } else {
                        elem.poshytip({
                            className : 'tip-yellowsimple',
                            showTimeout : 0,
                            showOn : 'hover',
                            content : error,
                            alignTo : 'target',
                            alignX : "inner-left",
                            // alignY : aY,
                            offsetX : 5,
                            offsetY : 5
                        });
                        elem.poshytip('show');
                    }
                    elem.data('start-poshy', error.text());

                } else {
                    elem.poshytip('disable');
                    elem.poshytip('destroy');
                    elem.data('start-poshy', '');
                }
            }
        }

        return $.extend(config, rules);
    };

    /**
     * 获取当前视窗的大小
     * To get the correct viewport width
     * based on  http://andylangton.co.uk/articles/javascript/get-viewport-size-javascript/
     * @returns {{width: *, height: *}}
     */
    Util.getViewport = function() {
        let e = window,
            a = 'inner';
        if (!( 'innerWidth' in window )) {
            a = 'client';
            e = document.documentElement || document.body;
        }

        return {
            width : e[a + 'Width'],
            height : e[a + 'Height']
        };
    };


    /**
     * 检测给定的字串是否是 Url
     * @param str
     * @returns {boolean}
     */
    Util.isUrl = function(str) {
        let pattern = new RegExp("^(https?:\\/\\/)?" + // protocol
            "((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|" + // domain name
            "((\\d{1,3}\\.){3}\\d{1,3}))" + // OR ip (v4) address
            "(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*" + // port and path
            "(\\?[;&a-z\\d%_.~+=-]*)?" + // query string
            "(\\#[-a-z\\d_]*)?$", 'i'); // fragment locater
        return pattern.test(str);
    };

    /**
     * 判定是否是邮箱
     * @param str
     * @returns {boolean}
     */
    Util.isEmail = function(str) {
        let reg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,8}){1,2})$/;
        return reg.test(str);
    };

    /**
     * 判定是否为手机号码
     * @param str
     * @returns {boolean|Array|{index: number, input: string}}
     */
    Util.isMobile = function(str) {
        let phone_number = str.replace(/\(|\)|\s+|/g, "");
        return phone_number.length > 10 && phone_number.match(/^(\d{1,5}\-)?1[3|4|5|6|8|7|9][0-9]\d{4,8}$/);
    };

    /**
     * 按钮倒计时工具
     * @param btn_selector
     * @param str
     * @param time
     * @param end_str
     */
    Util.countdown = function(btn_selector, str, time, end_str) {
        let count      = time;
        let handlerCountdown;
        let $btn       = $(btn_selector);
        let displayStr = typeof end_str != 'undefined' ? end_str : $btn.text();

        handlerCountdown = setInterval(_countdown, 1000);
        $btn.attr("disabled", true);

        function _countdown() {
            let count_str = str.replace(/\{time\}/, count);
            $btn.text(count_str);
            if (count == 0) {
                $btn.text(displayStr).removeAttr("disabled");
                clearInterval(handlerCountdown);
            }
            count--;
        }
    };

    /**
     * 生成随机字符
     * @param length
     * @returns {string}
     */
    Util.random = function(length) {
        if (typeof length == 'undefined' || parseInt(length) == 0) {
            length = 18;
        }
        let chars = "abcdefhjmnpqrstuvwxyz23456789ABCDEFGHJKLMNPQRSTUVWYXZ";
        let str   = '';
        for (let i = 0; i < length; i++) {
            str += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return str;
    };

    /**
     * 方便添加维护类
     * @returns {{hasClass: *, addClass: *, removeClass: *, toggleClass: toggleClass, has: *, add: *, remove: *, toggle: toggleClass}}
     */
    Util.classie = function() {
        function classReg(className) {
            return new RegExp("(^|\\s+)" + className + "(\\s+|$)");
        }

        // classList support for class management
        // altho to be fair, the api sucks because it won't accept multiple classes at once
        let hasClass, addClass, removeClass;

        if ('classList' in document.documentElement) {
            hasClass    = function(elem, c) {
                return elem.classList.contains(c);
            };
            addClass    = function(elem, c) {
                elem.classList.add(c);
            };
            removeClass = function(elem, c) {
                elem.classList.remove(c);
            };
        } else {
            hasClass    = function(elem, c) {
                return classReg(c).test(elem.className);
            };
            addClass    = function(elem, c) {
                if (!hasClass(elem, c)) {
                    elem.className = elem.className + ' ' + c;
                }
            };
            removeClass = function(elem, c) {
                elem.className = elem.className.replace(classReg(c), ' ');
            };
        }

        function toggleClass(elem, c) {
            let fn = hasClass(elem, c) ? removeClass : addClass;
            fn(elem, c);
        }

        return {
            // full names
            hasClass : hasClass,
            addClass : addClass,
            removeClass : removeClass,
            toggleClass : toggleClass,
            // short names
            has : hasClass,
            add : addClass,
            remove : removeClass,
            toggle : toggleClass
        };
    };


    /**
     * 计算对象的长度
     * @param obj
     * @returns {number}
     */
    Util.objSize = function(obj) {
        let count = 0;

        if (typeof obj == "object") {

            if (Object.keys) {
                count = Object.keys(obj).length;
            } else if (window._) {
                count = _.keys(obj).length;
            } else if (window.$) {
                count = $.map(obj, function() {
                    return 1;
                }).length;
            } else {
                for (let key in obj) if (obj.hasOwnProperty(key)) count++;
            }

        }

        return count;
    };

    /**
     * 重新载入当前页面
     */
    Util.refresh = function() {
        top.window.location.reload();
    };

    Util.opener = function(workspace) {
        let opener = top.frames[workspace];
        if (typeof opener == 'undefined') {
            opener = top;
        }
        return opener;
    };

    /**
     * 执行一次动画
     * @param selector
     * @param animation_name
     */
    Util.animate = function(selector, animation_name) {
        let animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
        $(selector).addClass('animated ' + animation_name).one(animationEnd, function() {
            $(this).removeClass('animated ' + animation_name);
        });
    };

    /**
     * 全屏
     * @param ele
     */
    Util.fullScreen = function(ele) {
        let element;
        if (typeof ele == 'undefined') {
            element = document.documentElement;
        } else {
            element = document.getElementById(ele);
        }
        if (element.requestFullscreen) {
            element.requestFullscreen();
        } else if (element.mozRequestFullScreen) {
            element.mozRequestFullScreen();
        } else if (element.webkitRequestFullscreen) {
            element.webkitRequestFullscreen();
        } else if (element.msRequestFullscreen) {
            element.msRequestFullscreen();
        }
    };

    /**
     * 退出全屏
     */
    Util.exitFullScreen = function() {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        }
    };


    /**
     * 检查浏览器是否支持 local 存储
     * @returns {boolean}
     */
    Util.localStorageSupport = function() {
        return ( ( 'localStorage' in window ) && window['localStorage'] !== null )
    };

    /**
     * 获取 Url 参数
     * @param paramName
     * @returns {string}
     */
    Util.getUrlParameter = function(paramName) {
        let searchString   = window.location.search.substring(1),
            i, val, params = searchString.split("&");

        for (i = 0; i < params.length; i++) {
            val = params[i].split("=");
            if (val[0] == paramName) {
                return unescape(val[1]);
            }
        }
        return '';
    };

    /**
     * 获取当前视窗的大小
     * To get the correct viewport width
     * based on  http://andylangton.co.uk/articles/javascript/get-viewport-size-javascript/
     * @returns {{width: *, height: *}}
     */
    Util.getViewport = function() {
        let e = window,
            a = 'inner';
        if (!( 'innerWidth' in window )) {
            a = 'client';
            e = document.documentElement || document.body;
        }

        return {
            width : e[a + 'Width'],
            height : e[a + 'Height']
        };
    };

    /**
     * 是否是触摸设备
     * check for device touch support
     * @returns {boolean}
     */
    Util.isTouchDevice = function() {
        try {
            document.createEvent("TouchEvent");
            return true;
        } catch (e) {
            return false;
        }
    };


    /**
     * 获取唯一ID
     * @param prefix
     * @returns {string}
     */
    Util.getUniqueId = function(prefix) {
        let _pre = ( typeof prefix == 'undefined' ) ? 'prefix_' : prefix;
        return _pre + Math.floor(Math.random() * ( new Date() ).getTime());
    };

    /**
     * 即时搜索
     * @param options
     */
    Util.holmes = function(options) {

        if (typeof options != 'object') {
            throw new Error('The options need to be given inside an object like this:\nholmes({\n\tfind:".result",\n\tdynamic:false\n});\n see also https://haroen.me/holmes/doc/module-holmes.html');
        }

        // if options.find is missing, the searching won't work so we'll thrown an exceptions
        if (typeof options.find == 'undefined') {
            throw new Error('A find argument is needed. That should be a querySelectorAll for each of the items you want to match individually. You should have something like: \nholmes({\n\tfind:".result"\n});\nsee also https://haroen.me/holmes/doc/module-holmes.html');
        }

        start();

        // start listening
        function start() {

            // setting default values
            if (typeof options.input == 'undefined') {
                options.input = 'input[type=search]';
            }
            if (typeof options.placeholder == 'undefined') {
                options.placeholder = false;
            }
            if (typeof options.class == 'undefined') {
                options.class = {};
            }
            if (typeof options.class.visible == 'undefined') {
                options.class.visible = false;
            }
            if (typeof options.class.hidden == 'undefined') {
                options.class.hidden = 'hide';
            }
            if (typeof options.dynamic == 'undefined') {
                options.dynamic = false;
            }
            if (typeof options.contenteditable == 'undefined') {
                options.contenteditable = false;
            }

            // find the search and the elements
            var search         = document.querySelector(options.input);
            var elements       = document.querySelectorAll(options.find);
            var elementsLength = elements.length;

            // create a container for a placeholder
            if (options.placeholder) {
                var placeholder = document.createElement('div');
                placeholder.classList.add(options.class.hidden);
                placeholder.innerHTML = options.placeholder;
                elements[0].parentNode.appendChild(placeholder);
            }

            // if a visible class is given, give it to everything
            if (options.class.visible) {
                var i;
                for (i = 0; i < elementsLength; i++) {
                    elements[i].classList.add(options.class.visible);
                }
            }

            // listen for input
            $(options.input).bind('input propertychange', function() {

                // by default the value isn't found
                var found = false;

                // search in lowercase
                var searchString;
                if (options.contenteditable) {
                    searchString = search.textContent.toLowerCase();
                } else {
                    searchString = search.value.toLowerCase();
                }

                // if the dynamic option is enabled, then we should query
                // for the contents of `elements` on every input
                if (options.dynamic) {
                    elements       = document.querySelectorAll(options.find);
                    elementsLength = elements.length;
                }

                // loop over all the elements
                // in case this should become dynamic, query for the elements here
                var i;
                for (i = 0; i < elementsLength; i++) {

                    // if the current element doesn't containt the search string
                    // add the hidden class and remove the visbible class
                    if (elements[i].textContent.toLowerCase().indexOf(searchString) === -1) {
                        elements[i].classList.add(options.class.hidden);
                        if (options.class.visible) {
                            elements[i].classList.remove(options.class.visible);
                        }
                        // else
                        // remove the hidden class and add the visible
                    } else {
                        elements[i].classList.remove(options.class.hidden);
                        if (options.class.visible) {
                            elements[i].classList.add(options.class.visible);
                        }
                        // the element is now found at least once
                        found = true;
                    }
                }
                // if the element wasn't found
                // and a placeholder is given,
                // stop hiding it now
                if (!found && options.placeholder) {
                    placeholder.classList.remove(options.class.hidden);
                    // otherwise hide it again
                } else {
                    placeholder && placeholder.classList.add(options.class.hidden);
                }
            });
        }
    }

} )();

/**
 * 根据参数名获取对应的url参数
 * @param {string} name 要取的值key
 * @returns {string|null}
 */
function getQueryString(name) {
    let reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    let r   = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]);
    return null;
}
