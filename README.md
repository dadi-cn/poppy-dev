
[![Join the chat at https://gitter.im/poppy-framework/Lobby](https://badges.gitter.im/poppy-framework/Lobby.svg)](https://gitter.im/poppy-framework/Lobby?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

### 代码验证

通过 php-cs-fixer 来进行php 格式检查, 使用下面的命令获取执行代码
```
$ php artisan poppy:doc cs
```

### 规范检测

使用命令来检查事件是否符合规范

```
php artisan poppy:check 
```


### 使用的组件

```
jenssegers/date
    继承自 Carbon 的多语言时间支持
```
