<div class="pull-right ml8">
	{!! app('form')->select('_export_', [
	'all' => '导出所有',
	'current' => '导出当前页',
	'select' => '导出选中行',
], null, [
	'lay-filter' => '_export_'
]) !!}
	<script>
	$(function() {
		layui.form.render('select', 'f_export_')
	})
	</script>
</div>