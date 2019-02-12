
[![Join the chat at https://gitter.im/poppy-framework/Lobby](https://badges.gitter.im/poppy-framework/Lobby.svg)](https://gitter.im/poppy-framework/Lobby?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

English Document[Click Here](../README.md);
Build 文档 [点击这里](../docs/build.md);

## Agamotto

Agamotto是DateTime的一个简单的PHP API扩展，是Carbon的亲戚, 这个库的目标是创建一个显示本地化日期的解决方案
并引入更健壮的特性。

这个库是Date库的镜像名称:

[Date](https://github.com/jenssegers/date)
[Carbon](https://github.com/briannesbitt/carbon)

## 命令行/Console

### 创建模块

创建一个 Poppy 模块并启动它. 

```
$ php artisan poppy:make {slug} [-Q|--quick]
```

模块文件树: 

```
├── configurations        # 配置文件
├── docs                  # 文档
├── resources             
│   ├── lang              # 语言文件
│   │   └── zh            # 语言文件夹
│   └── views             # blade 模板
└── src
    ├── classes
    ├── database
    │   ├── factories
    │   ├── migrations
    │   └── seeds
    ├── events
    ├── http
    │   ├── request
    │   │   ├── api
    │   │   ├── backend
    │   │   └── web
    │   └── routes
    ├── listeners
    └── models
```
### 列出 Modules

列出所有的应用模块

```
$ php artisan poppy:list

+------+--------+--------+-------------------------------------------------------+---------+
| #    | Name   | Slug   | Description                                           | Status  |
+------+--------+--------+-------------------------------------------------------+---------+
| 9001 | System | system | This is the description for the poppy Backend module. | Enabled |
| 9001 | Slt    | slt    | Sour Lemon Team                                       | Enabled |
+------+--------+--------+-------------------------------------------------------+---------+
```

### 启用/禁用模块

```
$ php artisan poppy:enable {slug}
$ php artisan poppy:disable {slug}
```

### 优化模块

模块优化, 清空生成的缓存等操作

```
$ php artisan poppy:optimize
```

### Poppy 文档

使用 php-cs-fixer 修复代码风格, .php_cs 样式在根目录文件夹中.
```
$ php artisan poppy:doc {phpcs/cs} 
```

使用 phplint 检测代码错误
```
$ php artisan poppy:doc {phplint/lint} 
```

使用 Sami 生成 php 文档, 可以找到 modules 文件夹下的所有 php 文件

```
$ php artisan poppy:doc {sami/php} 
```

显示当前 tail 日志命令

```
$ php artisan poppy:doc {log} 
```

### Poppy 数据库管理

```
poppy:migrate           Run the database migrations for a specific or all modules
poppy:migrate:refresh   Reset and re-run all migrations for a specific or all modules
poppy:migrate:reset     Rollback all database migrations for a specific or all modules
poppy:migrate:rollback  Rollback the last database migrations for a specific or all modules
poppy:migration {slug}  Create a new module migration file
```

## Poppy 生成器

生成器工具

```
php artisan poppy:command {slug} {name}
php artisan poppy:controller {slug} {api/web} {name}
php artisan poppy:middleware {slug} {name}
php artisan poppy:model {slug} {name}
php artisan poppy:policy {slug} {name}
php artisan poppy:provider {slug} {name}
php artisan poppy:request {slug} {name}
php artisan poppy:seed {slug} {name}
php artisan poppy:seeder {slug} {name}
php artisan poppy:test {slug} {name}
```


## 事件

```
// Locale Changed
Events\LocaleChanged($locale)

// Module Maked
Events\PoppyMake($slug)
```

## Helpers

```
ArrayHelper
CacheHelper
ContentHelper
EnvHelper
FileHelper
HtmlHelper
ImageHelper
CookieHelper
RouterHelper
SearchHelper
StrHelper
TimeHelper
TreeHelper
UtilHelper
WebHelper
```

## 解析器

支持 Xml,Ini,Yaml

## Blade 语法

```
@poppy
// You Can check if module is exist and enabled.
@endpoppy
```

## 鸣谢

- [Yaml](http://nodeca.github.io/js-yaml/)
- [EloquentFilter](https://github.com/Tucker-Eric/EloquentFilter)
- [Sami](https://github.com/FriendsOfPHP/Sami) 