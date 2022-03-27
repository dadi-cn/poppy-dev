<div class="layui-card">
    {{--  标题以及工具  --}}
    <div class="layui-card-header">
        {{ $title }}
        {{--显示工具--}}
        {!! $grid->renderHeaderTools() !!}

        <div class="pull-right">
            {!! $grid->renderQuickButton() !!}
        </div>
        @if ($grid->isShowExporter())
            {!! $grid->renderExportButton() !!}
        @endif
    </div>

    <div class="layui-card-body">

        {{-- 首选范围 --}}
        @if($scopes->count())
            <div class="layui-tab" style="margin-bottom: 10px;">
                <ul class="layui-tab-title">
                    @foreach($scopes as $scope)
                        {!! $scope->render() !!}
                    @endforeach
                </ul>
            </div>
        @endif

        {{--  显示查询条件  --}}
        {!! $grid->renderFilter() !!}

        {{-- Layui Table--}}
        <table class="layui-hide" id="{!! $id !!}" lay-filter="{!! $id !!}-filter"></table>
    </div>
</div>
<script type="text/html" id="{!! $filter_id !!}-toolbar">
<div class="layui-btn-container">
    {!! $grid->renderBatchActions() !!}
</div>
</script>

<script>
layui.table.render($.extend({!! $lay !!}, {
    // 返回的数据去做解析
    request : {
        limitName : 'pagesize'
    },
    limit : {!! $grid->getPerPage() !!},
    // 使用后端排序
    autoSort : false,
    id : '{!! $filter_id !!}-table',
    loading : true,
    toolbar : '#{!! $filter_id !!}-toolbar',
    defaultToolbar : ['filter', 'print'],
    even : true,
    parseData : function(resp) {
        return {
            code : resp.status,
            msg : resp.message,
            count : resp.data.pagination.total,
            data : resp.data.list
        };
    }
}));
$('#{!! $filter_id !!}-search').on('click', function() {
    let values = $('#{!! $filter_id !!}-form').serializeArray();
    let query  = {
        _query : 1
    };
    $.each(values, function(i, field) {
        query[field.name] = field.value;
    });
    layui.table.reload('{!! $filter_id !!}-table', {
        page : {
            curr : 1 //重新从第 1 页开始
        },
        where : query
    });
    return false;
});
$('#{!! $filter_id !!}-reload').on('click', function() {
    layui.table.reload('{!! $filter_id !!}-table', true);
    return false;
});
$('#{!! $filter_id !!}-reset').on('click', function() {
    $('#{!! $filter_id !!}-form').resetForm();
    layui.table.reload('{!! $filter_id !!}-table', {
        page : {
            curr : 1 //重新从第 1 页开始
        },
        where : {
            _query : 1
        }
    });
    return false;
});

// 监听排序事件
// https://www.layui.com/doc/modules/table.html#onsort
layui.table.on('sort({!! $id !!}-filter)', function(obj) {
    layui.table.reload('{!! $filter_id !!}-table', {
        initSort : obj,
        where : {
            _field : obj.field,
            _order : obj.type,
            _query : 1
        }
    });
});

layui.table.on('toolbar({!! $id !!}-filter)', function(obj) {
    if (obj.event.indexOf('LAYTABLE') > -1) {
        return false;
    }
    let url = $(this).attr('data-url');
    if (!url) {
        layui.layer.msg('输入要处理的地址')
        return false;
    }
    let status = layui.table.checkStatus(obj.config.id);
    let data   = status.data;
    if (!data.length) {
        layui.layer.msg('你尚未选中数据, 请选择');
        return false;
    }

    // confirm
    let str_confirm = $(this).attr('data-confirm');
    console.log(str_confirm);
    if (str_confirm && !confirm(str_confirm)) return false;

    let ids = [];
    for (let i in data) {
        let id = data[i]['{!! $model_pk !!}'];
        if (!id) {
            layui.layer.msg('数据中无主键, 无法进行删除操作')
            return false;
        }
        ids.push(data[i]['{!! $model_pk !!}']);
    }

    Util.makeRequest(url, {
        {!! $model_pk !!} : ids
    }, function(data) {
        Util.splash(data);
    });
});

// 注：edit是固定事件名，test是table原始容器的属性 lay-filter="对应的值"
// obj.value 得到修改后的值
// obj.field 当前编辑的字段名
// obj.data 所在行的所有相关数据
layui.table.on('edit({!! $id !!}-filter)', function(obj) {
    if (!obj.data['{!! $model_pk !!}']) {
        Util.splash({
            status : 1,
            message : '尚未定义/返回主键, 无法使用编辑功能'
        })
        return obj;
    }
    Util.makeRequest('{!! $url_base !!}', {
        '_edit' : 1,
        '_field' : obj.field,
        '_value' : obj.value,
        '_pk' : obj.data['{!! $model_pk !!}']
    })
});

layui.table.on('tool({!! $id !!}-filter)', function(obj) {
    if (!obj.data['{!! $model_pk !!}']) {
        Util.splash({
            status : 1,
            message : '尚未定义/返回主键, 无法使用编辑功能'
        })
        return obj;
    }
    let field   = this.getAttribute('data-field');
    let type    = this.getAttribute('data-type');
    let value;
    let checked = $(this).prop('checked');
    if (type === 'Y/N') {
        value = checked ? 'Y' : 'N';
    } else {
        value = checked ? '1' : '0';
    }
    Util.makeRequest('{!! $url_base !!}', {
        '_edit' : 1,
        '_field' : field,
        '_value' : value,
        '_pk' : obj.data['{!! $model_pk !!}']
    })
});
</script>