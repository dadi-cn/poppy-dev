# Framework : README(En)


[![Join the chat at https://gitter.im/poppy-framework/Lobby](https://badges.gitter.im/poppy-framework/Lobby.svg)](https://gitter.im/poppy-framework/Lobby?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

中文文档查看[这里](./docs/README_Zh-cn.md);
Build Document [Click Here](./docs/build.md);

## Agamotto

Agamotto is a simple PHP API extension for DateTime, and is the cousin of Carbon. 
The goal of this library is to create a solution for displaying localized dates 
and introducing more robust features.

This library acts as an umbrella for the Date library:
[Date](https://github.com/jenssegers/date)
[Carbon](https://github.com/briannesbitt/carbon)¶¶

## Console

### Make Module

Create a new Poppy module and bootstrap it.

```
$ php artisan poppy:make {slug} [-Q|--quick]
```

The file tree lists:

```
├── configurations        # configuration
├── docs                  # document
├── resources             
│   ├── lang              # lang file
│   │   └── zh            # language folder
│   └── views             # view template
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
### List Modules

List all application modules

```
$ php artisan poppy:list

+------+--------+--------+-------------------------------------------------------+---------+
| #    | Name   | Slug   | Description                                           | Status  |
+------+--------+--------+-------------------------------------------------------+---------+
| 9001 | System | system | This is the description for the poppy Backend module. | Enabled |
| 9001 | Slt    | slt    | Sour Lemon Team                                       | Enabled |
+------+--------+--------+-------------------------------------------------------+---------+
```

### Enable/Disable Module

```
$ php artisan poppy:enable {slug}
$ php artisan poppy:disable {slug}
```

### Optimize Module

```
$ php artisan poppy:optimize
```

### Poppy Database Manager

```
poppy:migrate           Run the database migrations for a specific or all modules
poppy:migrate:refresh   Reset and re-run all migrations for a specific or all modules
poppy:migrate:reset     Rollback all database migrations for a specific or all modules
poppy:migrate:rollback  Rollback the last database migrations for a specific or all modules
poppy:migration {slug}  Create a new module migration file
poppy:migration {slug} CreateDemoTable
                        Create a table for module migration
```

## Poppy Generators

Generator Tools

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


### generate command
```
php artisan poppy:command {slug} {name}
```

### generate controller
```
php artisan poppy:controller {slug} {api/web} {name}
```

### generate middleware
```
php artisan poppy:middleware {slug} {name}
```

### generate model
```
php artisan poppy:model {slug} {name}
```

### Generate event

Generate event for module
```
php artisan poppy:model {slug} {name}
```

### Generate listener

Generate listener for module
```
$ php artisan poppy:model {slug} {name}
--event : EventName, With `\` before Event means Global Event else module event in folder `listeners`
```

### generate policy
```
php artisan poppy:policy {slug} {name}
```

### generate provider
```
php artisan poppy:provider {slug} {name}
```

### generate request
```
php artisan poppy:request {slug} {name}
```

### generate seeder
```
php artisan poppy:seeder {slug} {name}
```

### run seed
```
php artisan poppy:seed
```

### generate test
```
php artisan poppy:test
```

## Events

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

## Parse

Support Xml,Ini,Yaml

## Blade 

```
@poppy
// You Can check if module is exist and enabled.
@endpoppy
```


## Thanks To

- [Yaml](http://nodeca.github.io/js-yaml/)
- [EloquentFilter](https://github.com/Tucker-Eric/EloquentFilter)
- [Sami](https://github.com/FriendsOfPHP/Sami) 