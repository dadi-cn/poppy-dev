## Code Specification

### PHP-CS-Fixer

[PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) is a tool to automatically fix PHP Coding Standards issues, We use it for Code Specification.

### 测试用例完善并且通过


### 注释完善并且通过

注释使用 `modules/system` 模块来检测

```
$ php artisan system:inspect class > fp.txt
```

### 代码清理过

```
ide:clean code
``` 

### 不要出现 package 标签

```
@package
```