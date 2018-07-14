<!DOCTYPE HTML>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>@if (isset($_title) && $_title) {!! $_title !!} - @endif</title>
    @section('head-meta') @show
    @section('head-css') @show
    @section('head-js') @show
</head>
<body class="@yield('body-class')">

@yield('body-main')

</body>
</html>